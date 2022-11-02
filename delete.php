<html>


<head>
	<title>ZaggoNet Editor</title>
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
</head>


<body>

<section class="section">
<div class="container">

<h1 class='title'>Delete</h1>
<?php

generateNavBar();
echo("<br><br>");


if (isset($_POST['filepath'])) {

  $filepath = $_POST['filepath'];

  if (is_numeric($filepath)) {
	  $cronjob =  getRecurringCalls()[$filepath];
	  $cron = new Crontab();
	  if ($cron->removeJob($cronjob) === false) {
		  echo("<p class='has-text-danger'>Error deleting recurring call: $cronjob </p>");
	  }
	  else {
		   echo("<p>You deleted this recurring call:<br><br> $cronjob </p>");
	  }
  }
  else {
	  $source_file = $_POST['filepath'];
	  $filename = pathinfo($source_file, PATHINFO_BASENAME);;

	  $destination_subdir = '/backups/misc/';
	  $destination_dir = getcwd() . $destination_subdir;
	  mkdir($destination_dir);
	  $destination_path = $destination_dir . '/' . $filename;
	  rename($source_file, $destination_path);

	  echo("<p>You deleted: $source_file </p>");
	  echo("<p>I hope you meant to do that! </p><br>");
	  echo("<p class='has-text-danger'>Note that if you have custom extensions configured for this file they will no longer work! You should remove them. </p><br>");
	  echo("<p>File has been moved to: </p> <p> <a href='./$destination_subdir'>$destination_subdir</a> </p> in case you need to recover it.");

	}
}

else {
	echo "No file specified";
}

echo("<br><br><a href='files.php'>Go back</a>")

?>

</div>
</section>


</body>
</html>
