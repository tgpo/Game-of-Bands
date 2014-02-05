$(document).ready(function(){
    $('#bestOf .close').click(function(){
    	$('#bestOf').slideUp();

    });

    if($('#songtable .round .round').text() > 9 && $('#songtable .round .round').text() < 36) {
        $('#bestOf').slideDown();
    }

    $('#bestOf .button').click(function(event){
        event.stopPropagation();

        //Song Nomination
        var catagory = $(this).attr('rel');
        var nomination = $(this).attr('data-id');

        $(this).addClass('done');
        $(this).parent().addClass('done');

        $.ajax({
            url: '/src/bestOfProcess.php',
            data: {nominateSong: 'nominateSong', catagory: catagory, nomination: nomination},
            type: 'post',
            success: function(output) {
            }
        });

        return false;
    });

});