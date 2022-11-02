<html lang="en">

<head>
	<title>ZaggoNet Files</title>
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


	  function tableFromList($array, $columns) {
	       $column_number = 1;
	       $num_columns = $columns;
	       $html = "<table class='table is-size-7 is-bordered is-hoverable'>";
	       foreach($array as $row) {
	           if ($column_number == 1) {
		       		$html .= "<tr>";
		       }
		       $html .= "<td>" . $row . "</td>";
			   if ($column_number == $num_columns) {
		       		$html .= "<tr>";
		       		$column_number = 1;
		       }
		       else {
			       $column_number += 1;
		       }

			}
		   $html .= "</table>";
		   echo($html);
	   }

	?>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./css/bulma.min.css">
    <script type="text/javascript" src="./js/scripts.js"></script>
    <script type="text/javascript" src="./js/jquery-1.12.4.min.js"></script>

    <script>
    $( function() {
    	$("#delete-misc").hide();
	    $(".show_hide").on("click", function () {
	        var txt = $("#delete-misc").is(':visible') ? 'More Options' : 'Less Options';
	        $(".show_hide").text(txt);
	        /* $(this).next('.content').slideToggle(200); */
	        $("#delete-misc").slideToggle(200);
	    });

	    $("#delete-random").hide();
	    $(".show_hide_2").on("click", function () {
	        var txt = $("#delete-random").is(':visible') ? 'More Options' : 'Less Options';
	        $(".show_hide_2").text(txt);
	        /* $(this).next('.content').slideToggle(200); */
	        $("#delete-random").slideToggle(200);
	    });

	    $('#fileToUpload').change(function() {
		    $('#upload-custom-file').removeAttr("disabled");
	    });

	    $('#fileToUpload2').change(function() {
		    $('#upload-random-file').removeAttr("disabled");
	    });

	    $('#delete-random-selector').change(function() {
		    $('#delete-random-button').removeAttr("disabled");
	    });

	    $('#delete-misc-selector').change(function() {
		    $('#delete-misc-button').removeAttr("disabled");
	    });

    } );
    </script>
</head>


<body>

<section class="section">
<div class="container">
<h1 class="title">ZaggoNet&trade; Files</h1>
<?php
generateNavBar();

$custom_directory = array_slice(scandir($custom_call_dir), 2);
$random_directory = array_slice(scandir($random_call_dir), 2);

?>



<br>


<h2 class='subtitle'>Miscellaneous Audio Directory</h2>

    <div class="box has-background-white-bis">
	<p >General directory for miscellaneous audio files. </p><br>
	<label class="label">Files</label>
    <?php
	   tableFromList($custom_directory, 2);
    ?>
    <label class="label">Upload</label>
    <form action="upload.php" method="post" enctype="multipart/form-data">

        <input type="hidden" name="dir" value="custom">

        <div class="field">
        	<input class="button" type="file" name="myfile" id="fileToUpload">
        	<p class="help">Must be .mp3 or .wav and under 50MB in size. Avoid files with double-quotes or other unusual characters.</p>
        </div>

        <div class="field">
        	<button class="button is-primary" id="upload-custom-file" type="submit" name="submit" disabled>Upload</button>
        </div>
    </form>

    <form action="delete.php" method="post">
        <div class="field">
			<a class="show_hide has-text-link" data-content="toggle-text">More Options</a>
		</div>

		<div id="delete-misc">
	    	<div class="field">
			    <label class="label">Delete File</label>

				<div class="select">
			       <select id='delete-misc-selector' name="filepath">
			       echo("<option value=''>--select file--</option>");
			       <?php
			foreach ($custom_directory as &$value) {
			 $path = $custom_call_dir . "/" . $value;
			 echo("<option value='$path'>$value</option>");
			}
			?>
			    	</select>
			    </div>
			    <p class="help">Select a file to delete (can't be undone!)</p>

	    	</div>

		    <div class="control">
		    	<button id='delete-misc-button' class='button is-danger' type="submit" onclick="return confirm('Are you sure you want to delete this file? (Cannot be undone)');" disabled>Delete this file</button>
		    </div>

		    <div class="field">
		    <?php
	  echo("<a class='level-right' href='.$misc_backup_subdir?C=M;O=D'>File backups</a>");
	?>

		    </div>
		</div>
    </form>
</div>

  <h2 class='subtitle'>Random Audio Directory</h2>

    <div class="box has-background-white-bis">
    <p >Files placed in this directory will be randomly chosen when sending a "random-selected call" in the incoming call creator.</p><br>
    <label class="label">Files</label>
    <?php
	   tableFromList($random_directory, 2);
    ?>
    <label class="label">Upload</label>

    <form action="upload.php" method="post" enctype="multipart/form-data">

        <input type="hidden" name="dir" value="random">

        <div class="field">
        	<input class="button" type="file" name="myfile" id="fileToUpload2">
        	<p class="help">Must be .mp3 or .wav and under 50MB in size. Avoid files with double-quotes or other unusual characters.</p>
        </div>

        <div class="field">
        	<button class="button is-primary" type="submit" name="submit" id="upload-random-file" disabled>Upload</button>

        </div>
    </form>

    <form action="delete.php" method="post">
    	<div class="field">
			<a class="show_hide_2 has-text-link" data-content="toggle-text">More Options</a>
		</div>

		<div id="delete-random">

	    	<div class="field">
			    <label class="label">Delete File</label>

				<div id='delete-random-selector' class="select ">
			       <select  name="filepath">
			       echo("<option value=''>--select file--</option>");
			       <?php
			foreach ($random_directory as &$value) {
			 $path = $random_call_dir . "/" . $value;
			 echo("<option value=\"$path\">$value</option>");
			}
			?>
			    	</select>
			    </div>
			    <p class="help">Select a file to delete (can't be undone!)</p>

	    	</div>

		    <div class="control">
		    	<button id='delete-random-button' class='button is-danger' type="submit" onclick="return confirm('Are you sure you want to delete this file? (Cannot be undone)');" disabled>Delete this file</button>
		    </div>

		    <div class="field">
		    <?php
	  echo("<a class='level-right' href='.$misc_backup_subdir?C=M;O=D'>File backups</a>");
	?>

		    </div>
		</div>
    </form>
</div>

</section>

</body>
</html>
