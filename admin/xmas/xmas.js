$(document).ready(function(){
	/** 
	 * Global Xmas Javascript *
	 * Some of this should possibly be merged into admin.js
	 */
	var _isDirty = false;
	$("input[type='text'],textarea").change(function(){
	  _isDirty = true;
	});
	window.onbeforeunload = function(e) {
		  e = e || window.event;  
		  var text = "You have unsaved changes. You sure?";
		  if (_isDirty) {
		    // For IE and Firefox
		    if (e) {
		      e.returnValue = text;
		    }
		    // For Safari
		    return text;
		  }
		};
	var send = function(type,data,callback){
		console.log('AJAX: ' + type);
		$.ajax({
			type: "POST",
			url: "xmas/json.php?type=" + type,
			data: data,
			success: function(r){
				console.log("AJAX -> Success.");
				if(typeof callback !== 'undefined'){
					callback(r);
				}
			},
			error: function(r){
				console.log("AJAX -> Failure.");
				if(typeof callback !== 'undefined'){
					callback(r,true); 
				}
			},
		});
	}
	// Create action for save buttons.
	$('table').on('click','save_row',function(){
		var r = $(this).closest('tr');
		var row = {};
		row.id = r.data('id');
		row.type = r.data('type');

		switch(row.type){
			case 'template':
				row.title = r.find('.title').val();
				row.text = r.find('.text').val();
				break;
			case 'city':
				var city = get_city(row.id);
				if(!city){
					console.log("Did we already have one of these?");
				}
				row.name = city.name;
				row.post_template_id = r.find('.ptid').val();
				row.message_template_id = r.find('.mtid').val();
				row.subreddit = (city.subreddit.length) ? city.subreddit : r.find('.subreddit').val();
				row.messaged_mods = (city.messaged_mods.length) ? city.messaged_mods : r.find('.messaged').val();
				row.post = (city.post.length) ? city.post : r.find('.post').val();
				break;
			case 'team':
			case 'charity':
			default: console.log("WUT?");
		}
		send('update_row',row);
	});
	
	$('input.new').click(function(){ 
		// Find our new row, get the data, change inputs to text.
		var type = $(this).data('type'); // template||city etc
		var this_row = $('.new_' + type);
		var new_row = this_row.clone(); //duplicate empty row
		var info = {};
		info.type = type;
		switch(info.type){
		case 'city':
			info.name = this_row.find('.new_city_name').val();
			info.post_template_id = this_row.find('.ptid').val();
			info.message_template_id = this_row.find('.mtid').val();
			info.subreddit = this_row.find('.new_subreddit').val();
			info.lat = this_row.data('lat');
			info.lng = this_row.data('lng');
			var onSuccess = function(id){
				info.id = id.element_id;
				make_city_data_row($('#cities tr:last'),info);
			}
			break;
		case 'template':
			info.title = this_row.find('.new_title').val();
			info.text = this_row.find('.new_text').val();
			var onSuccess = function(id){
				info.id=id.element_id;
				make_template_data_row($('#templates tr:last'),info);
			}
			break;
		case 'team':
		case 'charity':
		default:
		} 
		send(type,info,onSuccess);
		make_edit_row(type,this_row.parent());
	});

	// Create delete action
	$('table').on('click', '.delete_row' ,function(){
			var row = $(this).closest('tr');
			var id = row.data('id');
			var type = row.data('type');
	      if (!confirm("Delete this " + type + "? Seriously?")){
	      	return false;
	      }
	      send('delete',{'type': type, 'id':id},function(){
	    	  row.remove(); 
	      });
	});
	// Save fragment button
	$('body').on('click','input.fragment_save',function(){
		_isDirty = false;
		var div = $(this).closest('div');
		var file = div.find('h3').text();
		var text = div.find('textarea').val();
		send('fragment',{'text': text,fragment: file},function(r){
			div.append('Saved! <a href="/admin/clearcache" title="Anonymous users will not see your changes..">Clear Cache</a>');
		} );
	});
	
	
/**************************************************************** CITIES */
	if(typeof cities_data !== 'undefined'){
		var make_city_edit_row = function(obj){		
			obj.append('<tr class="new_city" ><td><input type="text" class="new_city_name" style="width: 300px;"></input></td>'
					+'<td><input type="text" class="ptid" style="width:30px;"></input></td>'
					+'<td><input type="text" class="mtid" style="width:30px;"></input></td>'
					+'<td><input type="text" class="new_subreddit" style="width:60px;" ></input></td>'
					+'<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>');
		}
	
		var make_city_data_row = function(obj,data){
			var post = (data.post.length > 1) 
			? '<a href="' + data.post + '">[-View Post-]</a>'
			: '<input type="button" class="post" data-id="' + data.id + '" value="Create"/>';
			var message = (data.messaged_mods.length > 1) 
					? '<a href="' + data.post + ' title="View this post">Read</a>' 
					: '<input type="button" class="messaged" data-template="' + data.template_id + '" value="Post">';
			var reddit = (data.subreddit.length > 1)
					? '<a href="http://reddit.com/r/' + data.subreddit + '" title="Visit subreddit" class="subreddit">' + data.subreddit +'</a>'
					: '<input type="text" style="width:60px;" class="subreddit">' + data.subreddit + '</input>';
			obj.append('<tr data-id="' 
				+ data.id + '" class="name" data-type="city"><td><a href="/xmas/city/' 
				+ data.id +'">'
				+ data.name +'</a></td><td><input type="text" class="ptid" style="width:30px;" value="'
				+ data.post_template_id + '"></input></td><td><input type="text" class="mtid" style="width:30px;" value="'
				+ data.message_template_id + '"></input></td><td>'
				+ reddit + '</td><td>'
				+ message + '</td><td>'
				+ post + '</td><td><input type="button" class="save_row" value="Save" />&nbsp;<input type="button" class="delete_row" value="X" /></td><td>'
				+ data.team_count +'</td></tr>');
		}
	
		console.log("Found " + cities_data.length + " cities");
		// Convert those arrays into table rows.
		var city_table = $('<tbody>');
		$.each(cities_data,function(key,c){
			c.type='city';
			//console.log(c);
			make_city_data_row(city_table,c);
		});
		// Add an edit row
		make_city_edit_row(city_table);
		$('#cities').append(city_table);
	
		function get_city(id){
			var r = false;
			$.each(cities_data,function(key,city){
				if(city.id == id){
					r = city;
					return false;
				}
			});
			return r;
		}
		function update_city(id,obj){
			var city = get_city(id);
			$.extend(city,obj); //TODO: test.. this seems too easy.
		}
		
	//Autocomplete for the city creation 
	    $(".new_city_name").geocomplete()
	      .bind("geocode:result", function(event, result){
	          lat = result.geometry.location.k;
	          lng = result.geometry.location.B; //Why these variables Googs??
	          var nam = result.formatted_address;
	          console.log("Result: " + nam);
	          $('#cities tr.new_city').data('lat',lat).data('lng',lng);
	          console.log("LAT: " + lat + ", LNG: " + lng);
	      })
	      .bind("geocode:error", function(event, status){
	    	  console.log("ERROR: " + status);
	      });
	}
	
	/************************************************ TEMPLATES *************/
	if(typeof templates_data !== 'undefined'){
		var make_template_edit_row = function(obj){
			obj.append('<tr class="new_template"><td>&nbsp;</td>'
					+'<td><input type="text" class="new_title"></input></td>'
					+'<td><textarea class="new_text"></textarea></td><td>&nbsp;</td></tr>');
		}
		var make_template_data_row = function(obj,data){
			obj.append('<tr data-id="'
					+ data.id +'" data-type="template"><td>'
					+ data.id +'</td><td><input type="text" class="title" value="'
					+ data.title +'"></input></td><td><input type="text" class="text" value="'
					+ data.text + '"></input></td><td><input type="button" class="save_row" value="Save" />&nbsp;<input type="button" class="delete_row" value="X" /></td></tr>'
				);		
		}
		console.log("Found " + templates_data.length + " templates");
		var templates_table = $('<tbody>');
		$.each(templates_data,function(key,t){
			make_template_data_row(templates_table,t);
		}); 
		// Add an edit row
		make_template_edit_row(templates_table);
		$('#templates').append(templates_table);
	}
	/************************************************ CHARITIES **************/
	// Delete function
	$('#charity_list').on('click','a.delete',function(){
		var name = $(this).nearest('li').data('name');
		var id = $(this).nearest('li').data('id');
		confirm("You sure you want to delete team: " + name + " ?");
	
		console.log('Deleting ' + name);
		$.ajax({
			type: "POST",
			url: "xmas/json.php?type=delete",
			data: {type: 'charity',id: id},
			success: function(r){
				console.log("Removed.");
			},
			error: function(xhr){
				console.log(xhr); //TODO: Notify mod
			},
		});
	});
});