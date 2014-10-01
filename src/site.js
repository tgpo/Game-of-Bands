$(document).ready(function(){

    $('#bestOfVoting .button').click(function(event){
        event.stopPropagation();

        // Song Nomination
        var catagory = $(this).attr('rel');
        var vote = $(this).attr('data-id');

        $(this).addClass('done');
        $(this).parents('ul').addClass('done');

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
            maxwidth: 200
        },
        document.getElementById("votingWidget"));
        
        alert($(this).attr('data-id'));
        
        $.get("/src/songInfo.php", { song: $(this).attr('data-id') } )
            .done(function( data ) {
                alert( "Lyrics Loaded: " + data.lyrics);
            });
        
        if($('#votingWidget').is(":hidden")) {
            $('#votingWidget').slideDown();
        }
        
        return false;
    });
    

});