$(document).ready(function(){

    $('#bestOfVoting .button').click(function(event){
        event.stopPropagation();

        //Song Nomination
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
    
    $('#bestOfVoting .listen').click(function(event){

        SC.oEmbed($(this).attr('data-url'), {
            color: "000000",
            auto_play: true,
            maxheight: 166,
            maxwidth: 400
        },
        document.getElementById("soundcloudBlock"));
        
        
        $.getJSON("/src/songInfo.php", { song: $(this).attr('data-id') } )
            .done(function( data ) {
                $('#titleBlock').text(data.name);
                
                $('#lyricsBlock').html(data.lyricsheet);
                $('#roundBlock').text('Round ' + data.round + ': ' + data.theme);
                $('#bandBlock .lyrics').text(data.banditLyrics);
                $('#bandBlock .music').text(data.banditMusic);
                $('#bandBlock .vocals').text(data.banditVocals);
            });
        
        if($('#votingWidget').is(":hidden")) {
            $('#votingWidget').slideDown();
        }
        
        return false;
    });
    

});