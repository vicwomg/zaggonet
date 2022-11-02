<html>


<head>
	<title>ZaggoNet Uploader</title>
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

<h1 class='title'>ZaggoNet Upload</h1>

<?php

generateNavBar();
echo("<br><br>");


    $currentDir = getcwd();

    //put file in correct dir.
    if ($_POST["dir"] == "random") {
      $uploadDirectory = $random_call_dir;
    }
    else if($_POST["dir"] == "custom") {
        $uploadDirectory = $custom_call_dir;
    }
    else {
	    echo "ERROR: unrecognized dir in POST";
    }

		if (! file_exists($uploadDirectory) ) {
			mkdir($uploadDirectory,0755,true);
		}

    $errors = []; // Store all foreseen and unforseen errors here

    $fileExtensions = ['wav','mp3']; // Get all the file extensions

    $fileName = $_FILES['myfile']['name'];
    $fileSize = $_FILES['myfile']['size'];
    $fileTmpName  = $_FILES['myfile']['tmp_name'];
    $fileType = $_FILES['myfile']['type'];
    $fileExtension = strtolower(end(explode('.',$fileName)));

    $uploadPath = $uploadDirectory . basename($fileName);

    if (isset($_POST['submit'])) {

        if (! in_array($fileExtension,$fileExtensions)) {
            $errors[] = "Either you didn't specify a file to upload or it wasn't one of the supported file formats: " . implode( ', ' , $fileExtensions) . "<br>";
        }

        if ($fileSize > 50000000) {
            $errors[] = "Files more than 50mb are not allowed";
        }

        if (empty($errors)) {
            // echo($fileTmpName . '<br>');
            // echo($uploadPath . '<br>');
            $didUpload = move_uploaded_file($fileTmpName, $uploadPath);

            if ($didUpload) {
                echo "<p class='has-text-success'>The file ''" . basename($fileName) . "'' has been uploaded.</p>";
								if (file_exists($eyed3_path)) {
									$output = shell_exec("$eyed3_path --remove-all --remove-all-images --remove-all-objects '$uploadPath'");
									echo "<p class='has-text-success'>Stripped id3 tags from: ''$fileName'</p>";
								}
								else {
									echo "<p class='has-text-danger'>Was not able to strip id3 tags: '$eyed3_path' not found. Some audio files won't play in asterisk if they contain id3/image metadata. Run 'sudo pip install eyeD3' to fix this error.</p>";
								}
            } else {
                echo "<p class='has-text-danger'>An error occurred somewhere. Try again or contact the admin.</p>";
            }
        } else {
            foreach ($errors as $error) {
                echo $error;
            }
        }
    }

  echo("<br><br><a href='files.php'>Go back</a>")
?>

</div>
</section>

</body></html>
