(function($){
	$(document).ready(function(){
	
	$(".sortTable").tablesorter(); 

	/* Original voting setup for bestOf2013 */
	$('#bestOfVoting .button').click(function(event){
		event.stopPropagation();

		// Song Nomination
		var catagory = $(this).attr('rel');
		var vote = $(this).attr('data-id');

		$(this).parents('ul').addClass('done').find('.done').removeClass('done');

		$(this).addClass('done');

		$.ajax({
			url: '/src/bestOfProcess.php',
			data: {voteSong: 'voteSong', catagory: catagory, vote: vote},
			type: 'post',
			success: function(output) {
			}
		});

		return false;
	});
	
	/* setup voting system */
	GOB.voting_template = $('<span class="upvotebutton">&nbsp;</span>');//&#8683; // ugly arrow
	GOB.vote_types = {
               'lyrics': 'bestLyricist',
               'music' : 'bestMusician',
               'vocals': 'bestVocalist',
               'track' : 'bestSong',
               'xmas'  : 'bestXmasSong',
			};
	
	GOB.logged_in = /reddit_session=/.test(document.cookie); 
	
	var selected_url = '';
	var widget = false; 
	
	var destroy_player = function(){
		$('#votingWidget').hide("slow");// Begin closing animation.
		// Ideally, if the "exact same" song was re-clicked, we should just simply re-display it.. and then toggle audio
		//widget.pause(); // Will unfortunately only pause when actually playing.. if it hasn't loaded yet, this won't do anything. feck
		
		// Completely Destroy the widget.. and rebuild it
		// Build SoundCloud HTML5 iframe
		$('#soundcloudBlock').empty();
		$('<iframe>', {
			   src: 'https://w.soundcloud.com/player/?url=', // It doesn't like the url, it doesn't like a blank url, frankly its fuckin annoying!
			   id:  'sc-widget',
			   frameborder: 0,
			   scrolling: 'no',
			   height: '166',
			   width: "100%",
		}).appendTo(document.getElementById("soundcloudBlock")); // When called again, will destroy existing player and rebuild.
		
		// Make a new widget.. ffs
		widget = SC.Widget(document.getElementById('sc-widget'));
	}
	
	
	// Build SoundCloud HTML5 iframe
	destroy_player(); // I know.. 

	// The widget is listening for these events.. lets use em for something.
	widget
	.bind(SC.Widget.Events.READY, 	function() {console.log("Widget: Activate!");	widget.play(); })
	.bind(SC.Widget.Events.PLAY, 	function() {console.log("Sound.. hope your speakers are on!.");})
    .bind(SC.Widget.Events.FINISH,  function() {console.log("Song has finished, was that good?");})
	.bind(SC.Widget.Events.PAUSE, 	function() {console.log("Toggling play state.");})
	.bind(SC.Widget.Events.ERROR,	function() {console.log("Something b0rked..");})
	.bind(SC.Widget.Events.DOWNLOAD,function() {console.log("Bandit downloading.. ");});
	
	// Enable moving widget around page, actual widget, so contents may change, but widget position remains
	//$('#votingWidget').drags(); // Hmm.. scroll is disabled when this is active, mousewheel works, but.. not elegant.
	
	// TODO: Enable .resize() binding so widget moves if you scale the page.. it does move.. but not properly.
	
	// Sanity check the input to the json request.. we can only accept an integer
	var isInt = function(n) { return n === +n && n === (n|0); };
	
	/* 
	 * Configure the Song widget when its link is clicked.
	 * Override the normal "link" function of the song links.
	 * Provide voting widgets if voting is active for the song.
	 */ 
	$('#songtable').on('click', 'a.song',function(e){ // hehe, A song has selector "a.song".. love it.			
		// Stop the link from being all "linky"
		e.stopPropagation();
		e.preventDefault();
		
		// Clear any previous voting notifications.
		$('#vote-notification').empty();
		
		if(typeof SC === 'undefined'){ 
			console.log("Soundcloud not loaded, unable to load player.");
		}else{
			selected_url = $(this).data('url');
			if(selected_url.length < 10){//"soundcloud" = 10 characters, if its not even that.. well..
				console.log("Url is too short or is otherwise invalid. Not creating a SoundCloud player.");
			}else{
				widget.load(selected_url, { // Tell the widget what URL we want to play
					auto_play: true,
					sharing: true,
					liking: true,
					show_artwork: false, 
				});
			}
		}
		get_song_details($(this).attr('data-id'));
		$('#votingWidget').show();
	});
	
	/* Enable the [Exit] function */
	$('#votingWidget #close-sc').on('click',function(e){
		e.preventDefault();// This prevents the page scrolling-back-to-the-top. Because the anchor isn't referenced anywhere.
		e.stopPropagation();
		destroy_player();
	});
	
	var actor_link = function(a,b,c){// type, name, pretty-name
		$('#bandBlock .' + a).html($('<a>').attr('href','/bandit/' + b).text(b).attr('title','View ' + c + ' page'));
	}
	
	var currentdate = new Date(); 
	var datetime = currentdate.getDate() + "/"
                + (currentdate.getMonth()+1)  + "/" 
                + currentdate.getFullYear() + " @ "  
                + currentdate.getHours() + ":"  
                + currentdate.getMinutes() + ":" 
                + currentdate.getSeconds();
        datetime=JSON.stringify(datetime);
	
	var get_song_details = function(song_id){
		if(!isInt(parseInt(song_id))){
			console.log("Something b0rked, we can't read this song-id as a number.. try a different song?");
		}else{
			// Due to aggressive static caching, this shouldn't take long.
			$.ajax({
			    type: 'GET',
			    url: "/src/songInfo.php?cf",
			    data: { song : song_id },
			    dataType: 'json',
			    contentType: 'application/json',
			    success : function(data){
			    	// Populate the template with the retrieved data.
					$('#titleBlock').text(data.name);
					$('#lyricsBlock').html(data.lyricsheet);
					$('#roundBlock').text('Round ' + data.round + ': ' + data.theme);

					actor_link('lyrics',data.banditLyrics,'Lyricists');
					actor_link('music',data.banditMusic,'Musicians');
					actor_link('vocals',data.banditVocals,'Vocalists');
					
					create_voting_buttons(data,song_id,$(this).attr('id'));
				},
				error: function(r){
					destroy_player(); // With no data, we can't rely on song-url either.. 
					alert(r.msg);
		         }
			});
		}
	}
	
	var create_voting_buttons = function(data,song_id,elem){
		// Server determines if voting is active or not for this track.
		if(data.votable == 1){
			var id_counter = 1;
			// Create voting buttons for Track, Lyricist, Musician, Vocalist
			$.each(GOB.vote_types, function(key,value){
				var vote_type = value;
				var selector = (vote_type == 'bestSong') ? '#titleBlock' : '#bandBlock .' + key;
				var text = (vote_type == 'bestSong') ? 'this song.' : 'this artist.';
				var classes = 'new-vote';
				if(typeof data.voted !== 'undefined'){ // Show a different icon if already voted.
					if($.inArray(vote_type,data.voted) !== -1){ classes = 'voted'; text = 'You have already voted for this. ;-)';}
				}
				$(selector)
				.append(GOB.voting_template
						.clone(true)
						.attr('id','vote-button-' + id_counter)
						.attr('title','Vote for ' + text) // setup hover-text
						.addClass(classes)
						.click(function(){// Activate the button
							send({
								type: vote_type,
								song: song_id,
								round: data.round,
								element_id: elem
							});
				})); 
				id_counter++;
			});
			say("Voting is active, Click green to vote!");
		}
	}

	// Send vote to server.
	var send = function(vote){
		$.ajax({	// Feel free to send votes, they are only counted when voting is active though.
			url: '/src/bestOfProcess.php',
			data: {vote: JSON.stringify(vote)}, 
			type: 'POST',
			success: function(s) {
				say(s.msg.replace(/best/i, 'Best '));
				if(typeof s.element_id !== 'undefined'){
					$('#'+s.element_id).addClass('voted');
				}
			},
			error: function(r){
				say("Doh!: " + r.msg);
				if(typeof r.element_id !== 'undefined'){
					$('#'+r.element_id).addClass('error');
				}
	         }
		});
	}

	var say = function(text){
		console.log(text);
		$('#vote-notification').html('<span class="message">' + text + '</span>');
	}
	
	// Resume previous action (Useful if user was forced to login)
	if(typeof GOB.previous !== 'undefined'){
		// TODO: re-click whatever link was clicked before..
	}
	console.log("GOB JS Loaded.");
	});
})(jQuery);
/** 
 * Enables dragging things around @ will, from: http://css-tricks.com/snippets/jquery/draggable-without-jquery-ui/ 
 * Usage: $(selector).drags();
 */
(function($) {
    $.fn.drags = function(opt) {

        opt = $.extend({handle:"",cursor:"move"}, opt);

        if(opt.handle === "") {
            var $el = this;
        } else {
            var $el = this.find(opt.handle);
        }

        return $el.css('cursor', opt.cursor).on("mousedown", function(e) {
            if(opt.handle === "") {
                var $drag = $(this).addClass('draggable');
            } else {
                var $drag = $(this).addClass('active-handle').parent().addClass('draggable');
            }
            var z_idx = $drag.css('z-index'),
                drg_h = $drag.outerHeight(),
                drg_w = $drag.outerWidth(),
                pos_y = $drag.offset().top + drg_h - e.pageY,
                pos_x = $drag.offset().left + drg_w - e.pageX;
            $drag.css('z-index', 1000).parents().on("mousemove", function(e) {
                $('.draggable').offset({
                    top:e.pageY + pos_y - drg_h,
                    left:e.pageX + pos_x - drg_w
                }).on("mouseup", function() {
                    $(this).removeClass('draggable').css('z-index', z_idx);
                });
            });
            e.preventDefault(); // disable selection
        }).on("mouseup", function() {
            if(opt.handle === "") {
                $(this).removeClass('draggable');
            } else {
                $(this).removeClass('active-handle').parent().removeClass('draggable');
            }
        });

    }
})(jQuery);
