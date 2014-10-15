<?php
	$email_to="jimmy@lounge.com.au"; // bookings@lounge.com.au is the original address
	$email_subject="New Booking from Website";
	
	function clean_string($string) {
 
      $bad = array("content-type","bcc:","to:","cc:","href");
 
      return str_replace($bad,"",$string);
 
    }
	
	$first_name = $_POST['firstName'];
	$last_name = $_POST['lastName'];
	$contact_number = $_POST['contactNumber'];
	$pax = $_POST['pax'];
	$date = $_POST['date'];
	
	$time = $_POST['hour'];
	$time .= ":";
	$time .= $_POST['min'];
	$time .= "pm";
	
	$ios_confirmation_link = "<p><a href='sms:" . $contact_number . ";body=Hi " . $first_name .". Your booking at Lounge on " . $date . " at " . $time . " has been confirmed. If you have additional requirements or would like to alter your booking, please give us a call on (03) 9663 2916.' >iOS: Confirm this booking with " . $first_name . "</a></p>";
	$android_confirmation_link = "<p><a href='sms:" . $contact_number . "?body=Hi " . $first_name .". Your booking at Lounge on " . $date . " at " . $time . " has been confirmed. If you have additional requirements or would like to alter your booking, please give us a call on (03) 9663 2916.' >Android: Confirm this booking with " . $first_name . "</a></p>";
	
	$email_message .= "<html><body>";
	$email_message .= "<p><strong>You have received a new booking!</strong></p>";
	$email_message .= "<p><strong>Name: </strong>".clean_string($first_name) . " " . clean_string($last_name) . "<br />";
	$email_message .= "<strong>Contact Number: </strong>".clean_string($contact_number)."<br />";
	$email_message .= "<strong>Pax: </strong>".clean_string($pax)."<br />";
	$email_message .= "<strong>Date: </strong>".clean_string($date)."<br />";
	$email_message .= "<strong>Time: </strong>".clean_string($time)."</p>";
	$email_message .= "<p>Please use the link below to confirm this booking with " . $first_name . ".</p>";
	
	$email_message .= $ios_confirmation_link;
	$email_message .= $android_confirmation_link;
	
	$email_message .= "</body></html>";
	
	$email_headers  = 'MIME-Version: 1.0' . "\r\n";
	$email_headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	// $email_headers .= 'To: Mary <mary@example.com>, Kelly <kelly@example.com>' . "\r\n";
	$email_headers .= 'From: Lounge // GSK Bookings <bookings@lounge.com.au>' . "\r\n";
	// $email_headers .= 'Cc: jimmy@lounge.com.au' . "\r\n";
	// $email_headers .= 'Bcc: jimmy@lounge.com.au' . "\r\n";
    
    mail($email_to, $email_subject, $email_message, $email_headers);

	echo "<p>Thanks " . $first_name . "! Your booking request has been sent.</p>";

	echo "<p>Name: ".clean_string($first_name) . " " . clean_string($last_name) . "<br />";
	echo "Contact Number: ".clean_string($contact_number)."<br />";
	echo "Pax: ".clean_string($pax)."<br />";
	echo "Date: ".clean_string($date)."<br />";
	echo "Time: ".clean_string($time).".</p>";
	
	echo "<p>You will receive an SMS confirmation from our staff within 24hrs.</p>";
	echo "<p>If you have additional requirements or would like to alter your booking, please give us a call on (03) 9663 2916.</p>"

?>

  