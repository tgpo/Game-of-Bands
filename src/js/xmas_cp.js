$(document).ready(function(){
    // Creator only.. won't work for non-creator.
    $('#charity_nomination').on('click','input.save_charity',function(){
	if(!confirm("This step is only available once, you can't easily change your mind, \n"
		+ "please ensure you have researched your position and are sure this charity is the one.")){
	    return false;
	}
	var parent = $(this).parent();
	if($('#existing_id').length){
	    data =  {
		    'existing_id': $('#existing_id:selected').val()
	    };
	}else{
	    data =  {
		    'name': $('#nominate_name').val(),
		    'locality': $('#nominate_locality').val(),
		    'email': $('#nominate_email').val(),
		    'id': $('#nominate_id').val()
	    };
	}
	$.ajax({
	    type: "GET",
	    url: '/src/xmas.php?type=jsonsetcharity',
	    data: data,
	    success: function(r){
		parent.append('<span>Saved.</span>.');
	    }
	});
    });
    $('#teamacp').on('click','input.save_team',function(){
	// Get all the rows and save each team members new role.
	//TODO:
    });
    $('#teamacp').on('click','input#save_team_name',function(){
	$.ajax({
	    type: "GET",
	    url: '/src/xmas.php?type="jsonrenameteam',
	    data: {newname: $('#team_name').val()},
	    success: function(r){

	    },
	});
    });

    $('#teamacp').on('click','input.approve_member',function(){
	var bandit_id = $(this).data('id');
	$.ajax({
	    type: "GET",
	    url: '/src/xmas.php?type=jsonapprovemember',
	    data: {bandit: bandit_id},
	    success: function(r){
		console.log("Team member Approved.");
	    },
	});
    });
});