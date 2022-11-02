<?php

//admin password
$valid_passwords = array ("admin" => "zaggtastic");

//custom extensions file
$custom_extensions_file = '/etc/asterisk/extensions_custom.conf';

//default asterisk sounds dir, include trailing "/"
$asterisk_sound_dir = "/var/lib/asterisk/sounds/en/";

//path to eyed3 tool for stripping id3 tags
$eyed3_path = "/usr/local/bin/eyeD3";

// Path of where you are storing the randomized call audio files. Needs to be a subdir
// of $asterisk_sound_dir
// These dirs also need to be set to chown asterisk and chmod 766 for uploading to work
$random_call_subdir = "custom/random";
$random_call_dir = "$asterisk_sound_dir" . "$random_call_subdir" . "/";
$custom_call_subdir = "custom/misc";
$custom_call_dir = "$asterisk_sound_dir" . "$custom_call_subdir" . "/";

//default asterisk outgoing call dir, include trailing "/" :
$asterisk_outgoing_call_dir = "/var/spool/asterisk/outgoing/";
// default asterisk spool tmp dir for storing call files before they are moved over
$asterisk_spool_tmp_dir = "/var/spool/asterisk/tmp/";
$callfile_prefix = "zaggocall-";

//backup directories
$misc_backup_subdir = "/backups/misc";
$extconf_backup_subdir = "/backups/extconf";

$phones_file = getcwd() . "/phones.conf";
$extensions = unserialize(file_get_contents($phones_file));

//Generates a selection box of available extensions
function generateExtensionSelector($extensions) {
	echo('<div class="select is-rounded" > <select id="extension-selector" name="ext">');
	echo(sprintf("<option value=''>--select phone--</option>"));
	foreach ($extensions as $key => $value) {
	  echo(sprintf("<option value='%s'>%s: %s</option>", $key, $value, $key));
	}
	echo('</select></div>');
}


// removes given get parameter from given url
function removeParam($url, $param) {
    $url = preg_replace('/(&|\?)'.preg_quote($param).'=[^&]*$/', '', $url);
    $url = preg_replace('/(&|\?)'.preg_quote($param).'=[^&]*&/', '$1', $url);
    return $url;
}

//Generates a selection box of all the files in specified directory/subdir combo
//see above definitions of these dirs
function generateFileSelector($dir,$subdir) {
  $scanned_directory = array_slice(scandir($dir), 2);
  foreach ($scanned_directory as &$value) {
    $filename = pathinfo($value, PATHINFO_FILENAME);
    $path = $subdir . "/" . $filename;
    echo("<option value='$path'>$value</option>");
  }
}

// Generates the navigation bar
function generateNavBar() {
    echo('<nav class="navbar" role="navigation" aria-label="main navigation">');
    echo('<div class="navbar-brand"> <a role="button" class="navbar-burger" data-target="navMenu" aria-label="menu" aria-expanded="false">
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
    </a></div>');
    echo('<div class="navbar-menu" id="navMenu">');
    echo('<div class="navbar-start">');
	echo("<a class='navbar-item' href='index.php'>Home</a>");
	//echo("<a class='navbar-item' href='audio.php'>Audio Dialer</a>");
	echo("<a class='navbar-item' href='dialer.php'>Incoming Calls</a>");
	//echo("<a class='navbar-item' href='scheduled.php'>Scheduled Calls</a>");
  echo("<a class='navbar-item' href='outgoing.php'>Outgoing Calls</a>");
  //echo("<a class='navbar-item' href='extension_editor.php'>Extension Editor</a>");
	echo("<a class='navbar-item' href='files.php'>File Manager</a>");

	echo('</div></div></nav>');
}


function get_server_memory_usage(){

    $free = shell_exec('free');
    $free = (string)trim($free);
    $free_arr = explode("\n", $free);
    $mem = explode(" ", $free_arr[1]);
    $mem = array_filter($mem);
    $mem = array_merge($mem);
    $memory_usage = $mem[2]/$mem[1]*100;

    return $memory_usage;
}

function get_server_cpu_usage(){

    $load = sys_getloadavg();
    return $load[0];

}

class Crontab {

    // In this class, array instead of string would be the standard input / output format.

    // Legacy way to add a job:
    // $output = shell_exec('(crontab -l; echo "'.$job.'") | crontab -');

    static private function stringToArray($jobs = '') {
        $array = explode("\r\n", trim($jobs)); // trim() gets rid of the last \r\n
        foreach ($array as $key => $item) {
            if ($item == '') {
                unset($array[$key]);
            }
        }
        return $array;
    }

    static private function arrayToString($jobs = array()) {
        $string = implode("\r\n", $jobs);
        return $string;
    }

    static public function getJobs() {
        $output = shell_exec('crontab -l');
        //echo("<textarea>$output</textarea>");
        return self::stringToArray($output);
    }

    static public function saveJobs($jobs = array()) {
        $output = shell_exec('echo "'.self::arrayToString($jobs).'" | crontab -');
        return $output;
    }

    static public function doesJobExist($job = '') {
        $jobs = self::getJobs();
        if (in_array($job, $jobs)) {
            return true;
        } else {
            return false;
        }
    }

    static public function addJob($job = '') {
        if (self::doesJobExist($job)) {
            return false;
        } else {
            $jobs = self::getJobs();
            $jobs[] = $job;
            return self::saveJobs($jobs);
        }
    }

    static public function removeJob($job = '') {
        if (self::doesJobExist($job)) {
            $jobs = self::getJobs();
            unset($jobs[array_search($job, $jobs)]);
            return self::saveJobs($jobs);
        } else {
            return false;
        }
    }

}

// Get recurring calls from the crontab
function getRecurringCalls() {
	$cron = new Crontab();
	$jobs = $cron->getJobs();
	$scheduled_calls = array();
	foreach ($jobs as $value) {
		if (strpos($value, 'initiatecall.php')) {
			$scheduled_calls[] = $value;
		}
	}
	return $scheduled_calls;
}

function processExtensionString($string) {
    //remove unwanted phone number chars
	$string = str_replace(")","",$string);
	$string = str_replace("(","",$string);
	$string = str_replace(" ","",$string);
	$string = str_replace("-","",$string);
	if (is_numeric($string)) {
		return $string;
	}
	else {
		$string = strtolower($string);
		$output = strtr($string,'abcdefghijklmnopqrstuvwxyz', '22233344455566677778889999');
		return $output;
	}

}

// For overriding values this file without messing with the git status (custom password)
include 'vars_override.php';

?>
