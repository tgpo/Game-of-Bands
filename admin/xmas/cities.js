$(document).ready(function(){
	var make_edit_row = function(type,obj){
		if(type == 'city'){
				obj.append('<tr class="new_city" ><td><input type="text" class="new_city_name" style="width: 300px;"></input></td>'
						+'<td><input type="text" class="ptid" style="width:30px;"></input></td>'
						+'<td><input type="text" class="mtid" style="width:30px;"></input></td>'
						+'<td><input type="text" class="new_subreddit" style="width:60px;" ></input></td>'
						+'<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>');
		}else{
				obj.append('<tr class="new_template"><td>&nbsp;</td>'
						+'<td><input type="text" class="new_title"></input></td>'
						+'<td><textarea class="new_text"></textarea></td><td>&nbsp;</td></tr>');
		}
	}
	var make_data_row = function(obj,data){
		if(data.type == 'city'){
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
		}else{
			obj.append('<tr data-id="'
					+ data.id +'" data-type="template"><td>'
					+ data.id +'</td><td><input type="text" class="title" value="'
					+ data.title +'"></input></td><td><input type="text" class="text" value="'
					+ data.text + '"></input></td><td><input type="button" class="save_row" value="Save" />&nbsp;<input type="button" class="delete_row" value="X" /></td></tr>'
				);
		}
	}
	console.log("Found " + cities_data.length + " cities");
	// Convert those arrays into table rows.
	var city_table = $('<tbody>');
	$.each(cities_data,function(key,c){
		c.type='city';
		//console.log(c);
		make_data_row(city_table,c);
	});
	// Add an edit row
	make_edit_row('city',city_table);
	$('#cities').append(city_table);
	
	console.log("Found " + templates_data.length + " templates");
	var templates_table = $('<tbody>');
	$.each(templates_data,function(key,t){
		t.type='template';
		//console.log(t);
		make_data_row(templates_table,t);
	}); 
	// Add an edit row
	make_edit_row('template',templates_table);
	$('#templates').append(templates_table);

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
	
	// Create action for save buttons. //TODO: auto-save
	$('input.save_row').click(function(){
		var r = $(this).closest('tr');
		var row = {};
		row.id = r.data('id');
		row.type = r.data('type');
		console.log("SAVE PUSHED type:" + row.type + ",id:"+row.id);
		if(row.type=='template'){
			row.title = r.find('.title').val();
			row.text = r.find('.text').val();
		}else{
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
		}
		//TODO: Send row data to server.. 
		console.log(row);
		$.ajax({
			type: "POST",
			url: "xmas/json.php?type=update_row",
			data: info,
			success: function(r){
				var id = r.element_id;
				console.log("Update success.. //TODO: Notify mod"); //TODO: Notify mod
			},
			error: function(xhr){
				console.log("Update failure.  //TODO: Notify mod");
				console.log(xhr); //TODO: Notify mod
			},
		});
	});
	$('input.new').click(function(){ 
		// Find our new row, get the data, change inputs to text.
		var type = $(this).data('type'); // template||city
		var this_row = $('.new_' + type);
		var new_row = this_row.clone(); //duplicate empty row
		var info = {};
		info.type = type;
		if(type == "city"){
			info.name = this_row.find('.new_city_name').val();
			info.post_template_id = this_row.find('.ptid').val();
			info.message_template_id = this_row.find('.mtid').val();
			info.subreddit = this_row.find('.new_subreddit').val();
			info.lat = this_row.data('lat');
			info.lng = this_row.data('lng');
			var onSuccess = function(id){
				info.id = id;
				make_data_row($('#cities tr:last'),info);
			}
		}else{
			info.title = this_row.find('.new_title').val();
			info.text = this_row.find('.new_text').val();
			var onSuccess = function(id){
				info.id=id;
				make_data_row($('#templates tr:last'),info);
			}
		} 
		console.log(info);
		$.ajax({
			type: "POST",
			url: "xmas/json.php?type=" + type,
			data: info,
			success: function(r){
				var id = r.element_id;
				onSuccess(r.element_id);//note, calls the functions defined above.
				make_edit_row(type,this_row.parent());
			},
			error: function(xhr){
				console.log(xhr);
			},
		});
	});

	// Create delete action
	$('#citiespage').on('click', '.delete_row' ,function(){
			var row = $(this).closest('tr');
			var id = row.data('id');
			var type = row.data('type');
      if (!confirm("Delete this " + type + "? Seriously?")){
      	return false;
      }
			$.ajax({
				type: "POST",
				url: "xmas/json.php?type=delete",
				data: {'type': type, 'id':id},
				success: function(){
						row.remove();
				},
			});
	});
	// Save concept button
	$('#save_concept').click(function(){
		$.ajax({
			type: "POST",
			url: "xmas/json.php?type=concept",
			data: {text: JSON.stringify($('#concept').val())},
			success: function(response){
				$('#save_concept').before('Concept Saved! <a href="/admin/index.php?view=clearcache" title="Anonymous users will not see your changes until this is cleared, or 24 hours have passed.">Clear Cache</a>');
			},
			error: function(xhr){
				console.log(xhr);
			},
		});
	});
	
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
	
/* TURN OFF THE DATABINDING>> BECAUSE REASONS.
	var redditRenderer = function (instance, td, row, col, prop, value, cellProperties) {
	  Handsontable.renderers.TextRenderer.apply(this, arguments);
	  if($(td).text().length){
		  var contents = $(td).text();
			$(td).html($('<a>').attr('href','http://reddit.com/r/' + contents).text(contents));
	  }
	};

	var postRenderer = function (instance, td, row, col, prop, value, cellProperties) {
		Handsontable.renderers.TextRenderer.apply(this, arguments);
		if($(td).text().length>1){
			// Make link to post/message
			$(td).html($('<a>').attr('href',$(td).text()).text("[-View-]"));
		}else{
			$(td).html($('<a>').text('[Create]'));
		}
	};

	var template_ids = [];
	for(i=0; i< templates_data.length; i++){ template_ids[i] = templates_data[i].id; }

    // Databind cities
    $('#cities').handsontable({
    	  data: cities_data,
    	  width: 600,
    	  height: 300,
    	  fillHandle: "vertical",
    	  fixedRowsTop: 0,
    	  contextMenu: true,
    	  columnSorting: true,
    	  colWidths: [10, 50, 20, 20, 40, 50, 50],
    	  colHeaders: ['id','City', 'Template', 'Teams', 'Subreddit','Messaged','Post'],
    	  columns: [
    	    {data: "id", type: 'numeric', readOnly: true},
    	    {data: "name", type: 'text', readOnly: true},
    	    {data: "template_id", type: 'dropdown', source: template_ids },
    	    {data: "team_count", type: 'numeric', readOnly: true},
    	    {data: "subreddit", renderer: redditRenderer},
    	    {data: "messaged_mods", renderer: postRenderer},
    	    {data: "post", renderer: postRenderer},
    	  ],
         
          afterChange: function (change, source) {
              if(source == 'loadData') return;
            if ($('#cities').parent().parent().find('input[name=autosave]').is(':checked')) {
			     		 $.ajax({
			     	        url: "/admin/xmas/json.php?type=changed",
			     	        dataType: 'json',
			     	        type: "POST",
			     	        data : {changes: change}, //contains changed cells' data
			     	        success: function (data) {
			     	          console.log('Autosaved (' + change.length + ' cell' + (change.length > 1 ? 's' : '') + ')');
			     	        },
			     	        error: function(data){
											console.log(data);
			     	        },
			     	     });
            }
          },

    	});

	if($('#cities').parent().parent().find('input[name=autosave]').is(':checked')){
		console.log("Autosaving enabled.");
	}
    
    /* Databind templates
    $('#templates').handsontable({
    	data: templates_data,
    	minSpareRows: 1,
    	columnSorting: true,
    	columns: [
    	          {data: 'id',type: 'numeric', readOnly: true},
    	          {data: 'title',type: 'text'},
    	          {data: 'text', type: 'text'},
    	          ],
    	colHeaders: ['id','Title','Text'],
    });*/
});
