<html>

<head>
	<title>ZaggoNet</title>
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
	
	<h1 class="title">ZaggoNet&trade;</h1>
	
	<?php 
	generateNavBar();
	?>
	
	<div class="column">
	<h2 class='subtitle'>Oh, hello.</h2>
	<p>Welcome to ZaggoNet, your one-stop telecom shop.</p><br>
	</div>
	
	<div class="box has-background-white-bis">
	<?php
	
	$str   = @file_get_contents('/proc/uptime');
	$num   = floatval($str);
	$secs  = fmod($num, 60); $num = (int)($num / 60);
	$mins  = $num % 60;      $num = (int)($num / 60);
	$hours = $num % 24;      $num = (int)($num / 24);
	$days  = $num;
	
	echo("<h2 class='subtitle'>Stats</h2>");
	echo("<list>");
	echo("<li>Uptime: <b>" . $days . " days, " . $hours . " hours, and " . $mins . " minutes.</b></li>");
	
	echo("<li>Load Average: <b>" . get_server_cpu_usage() . "%</b>. </li>");
	echo("<li>Memory Utilization: <b>" . get_server_memory_usage() . "%</b></li>");
	echo("<li>Free Disk Space: <b>" . disk_free_space("/") . " bytes</b>. </li>");
	echo("</list>");
	
	?>
	</div>
	
	    </div>
	</section>
</body>

</html>
