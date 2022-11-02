<html>
<script type="text/javascript" src="./js/jquery-1.12.4.min.js"></script>
<script>
var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
};

$( function() {
  var dontsend = getUrlParameter('dontsend');
  if (dontsend == "on") {

  }
  else {
    window.location = "./dialer.php";
  }
} );
</script>

<?php

include 'vars.php';

// grab the query strings
$extension = $_GET['ext'];
$waittime = $_GET['waittime'];

switch($_GET['calltype']){

  case 'saydigits':
    $data = str_replace(array("\r","\n"),'',$_GET['data']); //strip newlines
    $application='SayDigits';
    $calldata = "Channel: PJSIP/$extension\n" .
    "Application: $application\n" .
    "WaitTime: $waittime\n" .
    "Data: $data\n";
    break;

  case 'saymessage':
    $data = str_replace(array("\r","\n"),'',$_GET['data']); //strip newlines
    $voice = $_GET['voice'];
    $speed = $_GET['speed'];
    $application='say-message';
    $calldata = "Channel: PJSIP/$extension\n" .
    "Set: MESSAGE=$data\n" .
    "Set: VOICE=$voice\n" .
    "Set: SPEED=$speed\n" .
    "WaitTime: $waittime\n" .
    "Context: $application\n" .
    "Extension: s\n";
    break;

  case 'randomcall':
    //convert contents to an array, remove the non-filenames (. , ..)
    $scanned_directory = array_slice(scandir($random_call_dir), 2);
    //print_r($scanned_directory);
    $random_choice = $scanned_directory[mt_rand(0,count($scanned_directory)-1)];
    //remove any extensions, Playback rejects them
    $filename = pathinfo($random_choice, PATHINFO_FILENAME);

    $application='Playback';
    $data = $random_call_subdir . '/' . $filename;
    $calldata = "Channel: PJSIP/$extension\n" .
    "Application: $application\n" .
    "WaitTime: $waittime\n" .
    "Data: $data\n";
    break;

  case 'playback':
    $data = $_GET['file'];
    $application='Playback';
    $calldata = "Channel: PJSIP/$extension\n" .
    "Application: $application\n" .
    "WaitTime: $waittime\n" .
    "Data: $data\n";
    break;

}

echo("<p>Callfile Contents: </p>");
echo(nl2br($calldata));

$full_url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// handle scheduled calls
if ( $_GET['date'] && $_GET['time'] ) {
    $datetime = $_GET['date'] . " " . $_GET['time'];
    $scheduledtime = strtotime($datetime);
	echo "<p>Scheduled for: </p><p>" . $datetime . " " . $scheduledtime . "</p>";
}

if (! isset($_GET['dontsend'])) {

	if (isset($_GET['cronvalue'])) {
		$cronvalue = $_GET['cronvalue'];
		//echo("$cronvalue <br> ");
		$url = removeParam($full_url,'cronvalue');
		//echo("$url <br>  <br> ");
		$url_cron_escaped = str_replace('%','\%',nl2br($url));
		$url_cron_escaped = str_replace("'","\'",$url_cron_escaped);
		$cron_command = $cronvalue . "    /usr/bin/curl -X GET '$url_cron_escaped'";


		$cron = new Crontab();
		$cron->addJob($cron_command);
		//print_r($cron->getJobs());
		echo("Repeating call generated: $cron_command <br> ");

	}
	else {
	    $filename = sprintf("%s%s.call", $callfile_prefix, time());
		$callfile = $asterisk_spool_tmp_dir . "/" . $filename;
		$call = fopen($callfile, 'w') or die("Can't open file");
		$bytes = fwrite($call, $calldata);
		fclose($call);
		if ($scheduledtime) {
		//scheduled calls are processed by asterisk by
		//setting modified date to desired scheduled time and moving them over to the outgoing dir
			touch($callfile,$scheduledtime);
		}
		system(sprintf("mv %s %s", $callfile, $asterisk_outgoing_call_dir . "/"));
	}
}

else {
    //output extra debugging stuff is dontsend is set

    //$full_url_strip_dontcall = str_replace('&dontsend=on', '', $full_url);
    $full_url_strip_dontcall = removeParam($full_url,'dontsend');
	echo("<p>URL: </p>");
	echo(nl2br($full_url_strip_dontcall));
/*

    echo("<p>Crontab string: </p>");
    echo("curl -X GET '" . str_replace('%','\%',nl2br($full_url_strip_dontcall))) . "'";
*/
}

echo("<p><a href='dialer.php'>&lt; Go Back</a></p>");

?>

</html>
