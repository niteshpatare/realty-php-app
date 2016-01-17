<?php
	if (isset($_POST['send'])) {
		 $to = 'niteshp27@gmail.com'; // Use your own email address
		 $subject = 'Feedback from Sai Prasad Nivara Site';
		 $message .= 'Email: ' . $_POST['email'] . "\r\n\r\n";
		 $message .= 'Comments: ' . $_POST['comments'];
		 echo $message;
		 $success = mail($to, $subject, $message);
	}
?>

<form method="post" action="/enquireack.php"  enctype="multipart/form-data">

  <label for="name">Name:</label>
  <input type="text" name="name" id="name">


  <label for="email">Email:</label>
  <input type="email" name="email" id="email">


  <label for="comments">Comments:</label>
  <textarea name="comments" id="comments" rows="7" cols="30"></textarea><br>

  <input type="submit" name="send" value="Send Message">

</form>
***



<?php
	if (isset($_POST['send'])) {
		 $to = 'niteshp27@gmail.com'; // Use your own email address
		 $subject = 'Feedback from Sai Prasad Nivara Site';
		 $message .= 'Email: ' . $_POST['email'] . "\r\n\r\n";
		 $message .= 'Comments: ' . $_POST['comments'];
		 echo $message;
		 $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

		if ($email) {
			$headers = "From: $email\r\n";	
		    $headers .= "\r\nReply-To: $email";
			$success = mail($to, $subject, $message, $headers, 'niteshp27@gmail.com');		   
			
		}


		 
	}
?>

<form method="post" action="enquireack.php"  enctype="multipart/form-data">

  <label for="name">Name:</label>
  <input type="text" name="name" id="name">


  <label for="email">Email:</label>
  <input type="email" name="email" id="email">


  <label for="comments">Comments:</label>
  <textarea name="comments" id="comments" rows="7" cols="30"></textarea><br>

  <input type="submit" name="send" value="Send Message">

</form>

<?php if (isset($success) && $success) { ?>
<h1>Thank You</h1>
Your message has been sent.
<?php } else { ?>
<h1>Oops!</h1>
Sorry, there was a problem sending your message.
<?php } ?>