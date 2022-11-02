<html>

<head>
	<title>ZaggoNet Incoming</title>
	<?php
	include 'vars.php';
	include 'ChronSchedule.php';

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
    <link rel="stylesheet" href="./css/jquery.timepicker.css">
    <link rel="stylesheet" href="./css/jquery-ui.min.css">

    <script type="text/javascript" src="./js/scripts.js"></script>
    <script type="text/javascript" src="./js/jquery-1.12.4.min.js"></script>
    <script type="text/javascript" src="./js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="./js/jquery.timepicker.js"></script>
<!--     <script type="text/javascript" src="./js/cronstrue.min.js"></script> -->

    <script type="text/javascript" src="./js/cron/jquery-cron-min.js"></script>
    <link type="text/css" href="./js/cron/jquery-cron.css" rel="stylesheet" />

    <script>

    function generateFields() {
	    var calltype = $("#calltype").find(':selected').attr('value');
			switch(calltype) {
				case 'playback':
					$("#file").removeClass( "is-hidden" );
					$("[name='file']").prop('disabled',false);
					$("#data").addClass( "is-hidden" );
					$("#data-text").prop('disabled',true);
					$("#voice-options").addClass( "is-hidden" );
					$("[name='voice']").prop('disabled',true);
					$("[name='speed']").prop('disabled',true);
					break;
				case 'saymessage':
					$("#file").addClass( "is-hidden" );
					$("[name='file']").prop('disabled',true);
					$("#data").removeClass( "is-hidden" );
					$("#data-text").prop('disabled',false);
					$("#voice-options").removeClass( "is-hidden" );
					$("[name='voice']").prop('disabled',false);
					$("[name='speed']").prop('disabled',false);
					break;
				case 'randomcall':
					$("#file").addClass( "is-hidden" );
					$("[name='file']").prop('disabled',true);
					$("#data").addClass( "is-hidden" );
					$("#data-text").prop('disabled',true);
					$("#voice-options").addClass( "is-hidden" );
					$("[name='voice']").prop('disabled',true);
					$("[name='speed']").prop('disabled',true);
					break;
				case 'saydigits':
					$("#file").addClass( "is-hidden" );
					$("[name='file']").prop('disabled',true);
					$("#data").removeClass( "is-hidden" );
					$("#data-text").prop('disabled',false);
					$("#voice-options").addClass( "is-hidden" );
					$("[name='voice']").prop('disabled',true);
					$("[name='speed']").prop('disabled',true);
					break;
				default:
					$("#file").removeClass( "is-hidden" );
					$("[name='file']").prop('disabled',false);
					$("#data").addClass( "is-hidden" );
					$("#data-text").prop('disabled',true);
					$("#voice-options").addClass( "is-hidden" );
					$("[name='voice']").prop('disabled',true);
					$("[name='speed']").prop('disabled',true);
					break;
			}
    }

  $( function() {
    generateFields();
    $( "#datepicker" ).datepicker();
    $('#basicExample').timepicker();

    $("#advanced-options").hide();

    $(".show_hide").on("click", function () {
        var txt = $("#advanced-options").is(':visible') ? 'More Options' : 'Less Options';
        $(".show_hide").text(txt);
        /* $(this).next('.content').slideToggle(200); */
        $("#advanced-options").slideToggle(200);
    });

    $( "#calltype" ).change(function() {
	    generateFields();
		});

	$( "#call" ).change(function() {
		var callpath = $("#call").find(':selected').attr('value');
		var contents = decodeURIComponent($("#call").find(':selected').attr('contents'));
		$("#call-contents").removeClass( "is-hidden" );
		$("#contents").html( contents );
		$('#delete-scheduled-call-button').removeAttr("disabled");
		});

	 $('#extension-selector').change(function() {
	 		$('#submit-button').removeAttr("disabled");
	    });

	 $('form').submit(function(e){
		 var emptyinputs = $(this).find('input').filter(function(){
			 return !$.trim(this.value).length;  // get all empty fields
			 }).prop('disabled',true);
	        $('#repeating-scheduler').find('select').prop('disabled',true);
		});

	$('#repeating-scheduler').hide();

	$('#repeating-scheduler-link').on("click", function () {
	    var txt = $("#scheduled-call-time").is(':visible') ? 'I want to create a one-time call...' : 'I want to create a repeating call...';
        $("#repeating-scheduler-link").text(txt);
		$('#repeating-scheduler').slideToggle(200);
		$('#scheduled-call-time').slideToggle(200);
	});

	$('#cron-selector').cron({
		onChange: function() {
        	$('#cronvalue').attr('value',($(this).cron("value")));
        },
        initial: " ",
        customValues: {
        "--select--" : " ",
        }
     }

	); // apply cron with default options



  } );
    </script>

</head>


<body>


	<section class="section">


	<div class="container">

	<h1 class="title">ZaggoNet&trade; Incoming</h1>

<?php
	generateNavBar();
	?>

	<br>
<h2 class='subtitle'>Incoming Call Creator</h2>
<div class="box has-background-white-bis">


<p>Send or schedule a call to the selected phone.</p> <br>



	<form action="initiatecall.php" method="get">

	<div class="field">
	<label class="label">Phone Extension</label>
	<?php generateExtensionSelector($extensions); ?>
	<p class="help">Add more phones <a href='addphone.php'>here</a></p>
	</div>

	<div class="field">
		<label class="label">Call Type</label>
		<div class="select">
			<select id="calltype" name="calltype">
			  <option value="playback">Play Audio file</option>
			  <option value="randomcall">Randomly-Selected Call</option>
			  <option value="saymessage">Say Message (in robo-voice)</option>
			  <option value="saydigits">Say Digits (pre-recorded voice)</option>
			</select>
		</div>
	</div>

	<div id="voice-options" class="box" >
		<div id="voice" class="field ">
			<label class="label">Language</label>
			<div class="select">
				<select name="voice">
				  <option value="">--select voice--</option>
				  <option value="en-US">English (American)</option>
				  <option value="en-GB">English (British)</option>
				  <option value="es-ES">Spanish</option>
				  <option value="fr-FR">French</option>
				  <option value="it-IT">Italian</option>
				  <option value="de-DE">German</option>
				</select>
			</div>
			<p class="help">Default: English (American)</a></p>
		</div>
		<div id="speed" class="field ">
			<label class="label">Voice speed</label>
			<div class="select">
				<select name="speed">
				  <option value="">--select speed--</option>
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
			<p class="help">Default: 100% speed</a></p>
		</div>
	</div>

	<div id="data" class="field">
		<label class="label">Data</label>
		<textarea class="textarea" id="data-text" name="data" rows="3" cols="50"></textarea>
		<p class="help">Content of the call. Must be numbers for 'Say Digits'.</p>
	</div>

	<div id="file" class="field">
		 <label class="label">File</label>
		    <div class="select ">
			  <select name="file"   >
			    echo("<option value=''>--select file--</option>");
				<?php
				$scanned_directory = array_slice(scandir($random_call_dir), 2);
				foreach ($scanned_directory as &$value) {
				 $filename = pathinfo($value, PATHINFO_FILENAME);
				 $path = $random_call_subdir . "/" . $filename;
				 $path = htmlspecialchars($path, ENT_QUOTES);
				 echo("<option value='$path'>$path</option>");
				}

				$scanned_directory = array_slice(scandir($custom_call_dir), 2);
				foreach ($scanned_directory as &$value) {
				 $filename = pathinfo($value, PATHINFO_FILENAME);
				 $path = $custom_call_subdir . "/" . $filename;
				 $path = htmlspecialchars($path, ENT_QUOTES);
				 echo("<option value='$path'>$path</option>");
				}
				?>
			  </select>
		    </div>
		    <p class="help">Add more files in the <a href='./files.php'>File Manager</a> section</p>
		</div>

	<div class="field">
	<a class="show_hide has-text-link" data-content="toggle-text">More Options</a>
	</div>

	<div id="advanced-options" class="content box">

		<div class="field">
			<label class="label">Ring time</label>
		    <input type="text" name="waittime" autocomplete="off"></input> <br>
		    <p class="help">Time (in seconds) to continue ringing before canceling call (default 45)</p>
		</div>



		<div class="field" id='scheduled-call-time'>
			<label class="label">Scheduled call time</label>
			<p class="help">Schedule this call on... </p>
			Date: <input type="text" name="date" id="datepicker" autocomplete="off"><br>
			Time: <input type="text" name="time" class="time ui-timepicker-input" id="basicExample" autocomplete="off">
			<p class="help">Note: time can be manually typed in (ex. 9:37pm)</p>
			</div>

		<div class="field" id="repeating-scheduler">
		<label class="label">Repeating scheduler</label>
		<p class="help">Make this call repeat...</p>
		<div id='cron-selector'></div>
		<p class="help">For more advanced scheduling, you can manually edit the cron code below. See <a target="_blank" href='https://www.tutorialspoint.com/unix_commands/crontab.htm'>crontab tutorial</a></p>
		Cron code: <input type="text" name="cronvalue" id="cronvalue" autocomplete="off"></input>
		</div>
		<a class="help" id="repeating-scheduler-link">I want to create a repeating call...</a>



		<div class="field">
		    <label class="label">Debugging</label>
			    <input type="checkbox" name="dontsend"> Do not place call (just show callfile contents, URL, and crontab example)</input>
		</div>
	</div>

	<div class="field control">

		<button type="submit" class="button is-primary" id="submit-button" disabled>Submit</button>

	</div>


	</form>


</div>



<h2 class='subtitle'>Scheduled Calls</h2>

    <div class="box has-background-white-bis">
	<p >List of current scheduled calls in the queue in order of creation date. You can add these above using the 'More Options' section.</p><br>

    <form action="delete.php" method="post">

    	<div class="field">
		    <label class="label">Delete Call</label>

			<div class="select">
		       <select id='call' name="filepath">
		       echo("<option value=''>--select scheduled call--</option>");
		       <?php
		$scanned_directory = array_slice(scandir($asterisk_outgoing_call_dir), 2);
		foreach ($scanned_directory as &$value) {
		 	$path = $asterisk_outgoing_call_dir . "/" . $value;
		 	$modifiedtime = date ("m/d/Y h:ia", filemtime($path));
		 	$contents = htmlspecialchars(file_get_contents($path), ENT_QUOTES);
		 	echo("<option contents='$contents' value='$path'>One-time: $modifiedtime</option>");
		}
		$recurring = getRecurringCalls();
		$index = 0;
		foreach ($recurring as $value) {
			$croncode = explode("    ", $value)[0];
			$cs = CronSchedule::fromCronString($croncode);
			$nlcron = $cs->asNaturalLanguage();
			echo("<option class='cron' contents=\"$value\" value='$index'>Recurring: $nlcron</option>");
			$index +=  1;
		}
		?>
		    	</select>
		    </div>
		    <p class="help">Select a call to delete (can't be undone!)</p>

    	</div>

    	<div id="call-contents" class="field is-hidden">
    	    <label class="label ">Call Contents</label>
    	    <textarea class="textarea has-text-grey-dark has-background-light" id="contents"  rows="4" ></textarea>

    	</div>

	    <div class="control">
	    	<button class='button is-danger' id="delete-scheduled-call-button" type="submit" onclick="return confirm('Are you sure you want to delete this scheduled call? (Cannot be undone)');" disabled>Delete this scheduled call</button>
	    </div>
    </form>
</div>



	</div>


	</section>


</body></html>
