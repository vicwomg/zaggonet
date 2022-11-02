<html lang="en">
<head>
	<title>ZaggoNet Outgoing Calls</title>
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


	function backupExtConf() {
		global $extconf_backup_subdir;
		global $custom_extensions_file;
		$backupdir = getcwd() . $extconf_backup_subdir . "/";
		//be sure to backup that file!
    if (! file_exists($backupdir) ) {
  	  mkdir($backupdir,0755,true);
    }
  	$backupfilename = sprintf('/extensions_custom-%s.conf.txt',time());
  	$backupfilepath = $backupdir . $backupfilename;

  	if (!copy($custom_extensions_file, $backupfilepath)) {
  	    echo "Error backing up extensions_custom.conf file to: $backupfilepath! \n";
  	}
  	else {
  		echo "Backed up old extensions_custom.conf file to: <a href='.$extconf_backup_subdir/$backupfilename'>$backupfilename</a>\n";
  	}
	}
	?>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="./css/bulma.min.css">
    <link rel="stylesheet" href="./css/jquery.timepicker.css">
    <link rel="stylesheet" href="./css/jquery-ui.min.css">

    <script type="text/javascript" src="./js/scripts.js"></script>
    <script type="text/javascript" src="./js/jquery-1.12.4.min.js"></script>
    <script type="text/javascript" src="./js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="./js/jquery.timepicker.js"></script>

    <script>

    function generateFields() {
	    var calltype = $("#calltype").find(':selected').attr('value');
			switch(calltype) {
				case 'playback':
					$("#file").removeClass( "is-hidden" );
					$("#data").addClass( "is-hidden" );
					$("#voice-options").addClass( "is-hidden" );
					break;
				case 'saymessage':
					$("#file").addClass( "is-hidden" );
					$("#data").removeClass( "is-hidden" );
					$("#voice-options").removeClass( "is-hidden" );
					break;
				case 'randomcall':
					$("#file").addClass( "is-hidden" );
					$("#data").addClass( "is-hidden" );
					$("#voice-options").addClass( "is-hidden" );
					break;
				case 'saydigits':
					$("#file").addClass( "is-hidden" );
					$("#data").removeClass( "is-hidden" );
					$("#voice-options").addClass( "is-hidden" );
					break;
				case 'fetchurl':
					$("#file").addClass( "is-hidden" );
					$("#data").removeClass( "is-hidden" );
					$("#voice-options").addClass( "is-hidden" );
					break;
				default:
					$("#file").addClass( "is-hidden" );
					$("#data").addClass( "is-hidden" );
					$("#voice-options").addClass( "is-hidden" );
					break;
			}
    }

  $( function() {
    generateFields();

    $( "#calltype" ).change(function() {
	    generateFields();
		});

     $('#extension-text').bind('input propertychange', function() {
		    $('#save-file').removeAttr("disabled");
	    });

	 $('#extension-input').bind('input propertychange', function() {
		    $('#generate-dialplan').removeAttr("disabled");
	    });


  } );
    </script>


</head>




<body>

<section class="section">
<div class="container">
<h1 class="title">ZaggoNet&trade; Outgoing</h1>
<?php generateNavBar(); ?>

<br>
<?php

//handle editing of file
if (isset($_POST['file-contents'])) {
	$data = $_POST['file-contents'];

	backupExtConf();
  echo("<div class='box'>");
	$result = file_put_contents($custom_extensions_file, $data);
		if(! $result === FALSE){
		    echo("<p class='has-text-success'>File edit saved!</p>");
		    $result=system("asterisk -rx 'dialplan reload'");
		}
		else {
		    echo("<p class='has-text-danger'>Error while saving!</p><br>");
		}
		echo("</div>");
  }

?>
<?php
//handle generating dialplan code
if (!empty($_POST['extension']) && isset($_POST['calltype'])) {

  $extension=processExtensionString($_POST['extension']);

  switch($_POST['calltype']){
  	case('playback'):
	  $template = "; dial %s to play the file: %s\n[from-internal-custom]\n exten => %s,1,Answer()\n same => n,Playback(\"%s\")\n same => n,Hangup()";
    echo $_POST['audio_path'];
		$code=sprintf($template, $_POST['extension'], $_POST['audio_path'], $extension, $_POST['audio_path']);
	  break;
	case('saydigits'):
	  $template = "; dial %s to say the following digits: %s\n[from-internal-custom]\n exten => %s,1,Answer()\n same => n,SayDigits(%s)\n same => n,Hangup()";
	  $code=sprintf($template, $_POST['extension'],$_POST['data'], $extension, $_POST['data']);
	  break;
	case('saymessage'):
	  $template = "; dial %s to speak the following generated message: \n[from-internal-custom]\n exten => %s,1,Answer()\n same => n,agi(picotts.agi, \"%s\", %s, any, %s)\n same => n,Hangup()";
	  $code=sprintf($template, $_POST['extension'], $extension, $_POST['data'], $_POST['voice'], $_POST['speed']);
	  break;
	case('fetchurl'):
	  $template = "; dial %s to activate the following URL: %s\n[from-internal-custom]\n exten => %s,1,Answer()\n same => n,Set(foo=\${CURL(%s)})\n same => n,Hangup()";
	  $code=sprintf($template, $_POST['extension'],$_POST['data'], $extension, $_POST['data']);
	  break;
  }


	$data = $code . "\n\n" . file_get_contents($custom_extensions_file);

	echo("<div class='box'>");
	backupExtConf();
	$result = file_put_contents($custom_extensions_file, $data);
	if(! $result === FALSE){
			echo("<p class='has-text-success'>Custom extension behavior added!</p>");
			$result=system("asterisk -rx 'dialplan reload'");
	}
	else {
			echo("<p class='has-text-danger'>Error while saving!</p><br>");
	}
  echo('<p class="has-text-success">Added the following to extensions_custom.conf file: </font><br>');
  echo('<textarea rows="6" cols="80">');
  echo($code);
  echo('</textarea>');
  echo('<br></p>');
	echo("</div>");
}
?>

<h2 class='subtitle'>Outgoing Call Behavior</h2>
<div class='box has-background-white-bis'>

<p>Set up a custom phone number that will play an audio file, recite text, or fetch a URL when dialed.</p><br>

<form action="outgoing.php" method="post">
	<div class="field">
		<label class="label">Target Phone Number</label>
		<input id="extension-input" class="input" type="text" name="extension">
		<p class="help">Example inputs: 1234, 555-1234, POPCORN, 1(800)FUNCHAT</p>
	</div>

	<div class="field">
		<label class="label">Behavior</label>
		<div class="select">
			<select id="calltype" name="calltype">
			  <option value="playback">Play Audio file</option>
			  <option value="saymessage">Say Message (in robo-voice)</option>
			  <option value="saydigits">Say Digits (pre-recorded voice)</option>
				<option value="fetchurl">Fetch URL (for activating remote APIs)</option>
			</select>
		</div>
	</div>

		<div id="voice-options" class="box" >
		<div id="voice" class="field ">
			<label class="label">Language</label>
			<div class="select">
				<select name="voice">
				  <option value="en-US">English (American)</option>
				  <option value="en-GB">English (British)</option>
				  <option value="es-ES">Spanish</option>
				  <option value="fr-FR">French</option>
				  <option value="it-IT">Italian</option>
				  <option value="de-DE">German</option>
				</select>
			</div>
		</div>
		<div id="speed" class="field ">
			<label class="label">Voice speed</label>
			<div class="select">
				<select name="speed">
				  <option value="1">100% speed</option>
				  <option value="0.90">90% speed</option>
				  <option value="0.80">80% speed</option>
				  <option value="0.70">70% speed</option>
				  <option value="0.60">60% speed</option>
				  <option value="0.50">50% speed</option>
				  <option value="0.40">40% speed</option>
				  <option value="0.30">30% speed</option>
				  <option value="0.20">20% speed</option>
				  <option value="0.10">10% speed</option>
				</select>
			</div>
		</div>
	</div>

	<div id="data" class="field">
		<label class="label">Data</label>
		<textarea class="textarea" name="data" rows="3" cols="50"></textarea>
		<p class="help">Data of the call. Say Message: text string, Say Digits: numerical string (# and * ok), Fetch URL: valid URL beginning with http:// or https:// </p>
	</div>

	<div id="file" class="field">
		<label class="label">Available Audio Files</label>
		<div class="select ">
			<select   name="audio_path">
			<?php
			$scanned_directory = array_slice(scandir($custom_call_dir), 2);
			foreach ($scanned_directory as &$value) {
			 $filename = pathinfo($value, PATHINFO_FILENAME);
			 $path = $custom_call_subdir . "/" . $filename;
			 echo("<option value=\"$path\">$path</option>");
			}
			$scanned_directory = array_slice(scandir($random_call_dir), 2);
			foreach ($scanned_directory as &$value) {
			 $filename = pathinfo($value, PATHINFO_FILENAME);
			 $path = $random_call_subdir . "/" . $filename;
			 echo("<option value=\"$path\">$path</option>");
			}
			?>
			</select>
		</div>
	</div>
	<div class="control">
		<button class="button is-primary" type="submit" id="generate-dialplan" name="generate" disabled>Add</button>
	</div>
</form>

</div>

<h2 class="subtitle">Advanced Extension Editor</h2>
<div class='box has-background-white-bis'>
<p>This section edits the Asterix PBX custom extensions file for setting up more advanced outgoing call behavior (phone trees, scripts, etc). You can delete previously added outgoing call behaviors here. You may want to read up on <a  target="_blank" href='http://www.asteriskdocs.org/en/3rd_Edition/asterisk-book-html-chunk/asterisk-DP-Basics-SECT-1.html'>dialplan syntax</a> before you mess with this, or you could do some damage. </p><br>

<form action="outgoing.php" method="post">

	<div class='field'>
	    <label class="label">Edit extensions_custom.conf:</label>
		<textarea class="textarea has-text-black has-background-grey-light is-family-monospace is-size-7" id="extension-text" name="file-contents"  rows="25" ><?php
		// Open the file to get existing content
		$current = file_get_contents($custom_extensions_file);
		echo($current);
	?></textarea>
		<p class="help">Oh boy, I sure hope you know what you're doing...</p>
	</div>

	<div class'level'>
	<div class='control level-left'>
		<button class="button is-danger" type="submit" id="save-file" name="submit" onclick="return confirm('Are you sure you want to save the extensions_custom.conf file?');" value="Save and Reload" disabled>Save and Reload</button>
	</div>
	<?php
	  echo("<a class='level-right' href='.$extconf_backup_subdir?C=M;O=D'>File backups</a>");
	?>

	</div>

</form>
</div>


</div>
</section>
</body></html>
