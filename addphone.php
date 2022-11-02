<html lang="en">
<head>
	<title>ZaggoNet Phones</title>
	<?php
	include 'vars.php';

	$valid_users = array_keys($valid_passwords);

	$user = $_SERVER['PHP_AUTH_USER'];
	$pass = $_SERVER['PHP_AUTH_PW'];

	$validated = (in_array($user, $valid_users)) && ($pass == $valid_passwords[$user]);

	if (!$validated) {
	  header('WWW-Authenticate: Basic realm="My Realm"');
	  header('HTTP/1.0 401 Unauthorized');
	  die ("Not authorized. Beat it, buster.");
	  }
	?>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./css/bulma.min.css">
    <script type="text/javascript" src="./js/scripts.js"></script>
    <script type="text/javascript" src="./js/jquery-1.12.4.min.js"></script>

    <script>

    $( function() {
	    $('#phone-number').change(function() {
		    $('#add-button').removeAttr("disabled");
	    });

	    $('#extension-selector').change(function() {
		    $('#delete-button').removeAttr("disabled");
	    });
    } );

    </script>
</head>




<body>

<section class="section">
<div class="container">
<h1 class="title">ZaggoNet&trade; Phones</h1>
<?php generateNavBar(); ?>

<br>
<?php


//handle editing of phones
if (isset($_POST['phonenumber']) && isset($_POST['friendlyname'] )) {
	$phonenumber = $_POST['phonenumber'];
	$friendlyname = $_POST['friendlyname'];

	$new_array = array($phonenumber => $friendlyname);
	//combine into existing extensions
	if ($extensions) {
		$merged_array = $extensions + $new_array;
	}
	else {
		$merged_array = $new_array;
	}
	$data = serialize($merged_array);

	$result = file_put_contents($phones_file, $data);


	echo("<div class='box'>");
	if(! $result === FALSE){
	    echo(sprintf("<p class='has-text-success'>Phone added: %s - %s</p>", $phonenumber, $friendlyname));
	    //refresh extensions
	    $extensions = unserialize(file_get_contents($phones_file));
	}
	else {
	    echo("<p class='has-text-danger'>Error while saving!</p><br>");
	}
	echo("</div>");

}

//handle deleting of phones
if ( isset( $_POST['ext'] ) ) {
	$phonenumber = $_POST['ext'];

	unset($extensions[$phonenumber]);
	$data = serialize($extensions);

	$result = file_put_contents($phones_file, $data);

	echo("<div class='box'>");
	if(! $result === FALSE){
	    echo(sprintf("<p class='has-text-dange'>Phone deleted: %s</p>", $phonenumber));
	    //refresh extensions
	    $extensions = unserialize(file_get_contents($phones_file));
	}
	else {
	    echo("<p class='has-text-danger'>Error while saving!</p><br>");
	}
	echo("</div>");

}

?>

<h2 class="subtitle">Phones</h2>
<div class='box has-background-white-bis'>
<p>Add available hardware/software VOIP phones on your network here and they will show up in the dropboxes in incoming call creator.</p><br>
<form action="addphone.php" method="post">
	<div class="field">
		<label class="label">Phone Number:</label>
		<input id="phone-number" class="input" type="text" name="phonenumber">
		<p class="help">Example: "5551234" (no dashes)</p>
	</div>
	<div class="field">
		<label class="label">Friendly Name:</label>
		<input class="input" type="text" name="friendlyname">
		<p class="help">Example: "Office Line 1"</p>
	</div>

	<div class='control'>
		<button class="button is-primary" id="add-button" type="submit" name="submit" disabled>Add</button>
	</div>
</form>
<form action="addphone.php" method="post">

	<div class='field'>
	    <label class="label">Current available phones:</label>

	    <?php

	    generateExtensionSelector($extensions);
	    ?>
		    <p class="help">Select a phone to delete (can't be undone!)</p>
	</div>

	<div class='control'>
		<button class="button is-danger" id="delete-button" type="submit" name="submit" disabled onclick="return confirm('Are you sure you want to delete a phone? (Cannot be undone)');" >Delete</button>
	</div>
</form>
</div>


</div>
</section>
</body></html>
