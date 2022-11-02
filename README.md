# README #

## What the heck is Zaggonet?

Zaggonet is a web frontend for Asterisk PBX that allows you to take control incoming and outgoing calls of a hardware landline telephone. You can set up custom phone number behaviors (audio, text-to-speech), schedule calls, and place calls to connected phones. Great for escape rooms, art projects, home automation, and good old fashioned pranks.

It is named after the project I used it on. An art installation for a fake pizza parlor: Zaggo's Pizza.

This project is based on the excellent work of Playful Technology, particularly this video series: https://www.youtube.com/watch?v=kbODHbJyEX4

#### Features

-  Uploading mp3/wav files and setting up custom phone numbers that will play them when dialed
-  Triggering calls to physical phones: generated text-to-speech audio, sending audio files, and random audio
-  Scheduling future calls to connected phones: one-time, or cron-style
-  Manual online editing of extensions_custom.conf file for maximum flexibility (and potential to screw things up!)

## Installation

### Raspbx / VOIP Adapter

There are plenty of tutorials on how to do this that will explain it better, but here's the gist:

- Start with a clean raspbx image installed to a raspberry pi: http://www.raspberry-asterisk.org/downloads/ , connect it to your wifi/ethernet connection, and boot it up
- Follow post install instructions here (just up to 3. Basic Setup is probably fine): http://www.raspberry-asterisk.org/documentation/#nextsteps
- Login to raspbx by pointing a web browser to it's IP address.
- From the FreePBX interface, add some PJSIP extensions for your hardware phones.
- Configure a VOIP adapter to point to the raspbx server. Recommended devices: Linksys PAP2T, OBi 110. Others will work, but some require unlocking and possibly messing around with serial connections (Vonage units, for example)
- Connect a landline phone to the VOIP adapter
- It would probably be a good idea to set up a software VOIP on your computer for testing.

I would recommend watching this series for more details: https://www.youtube.com/watch?v=kbODHbJyEX4

### Zaggonet

- Login to the raspbx device as root / raspberry
- Clone this zaggonet repo down the /var/www/html/ on your asterisk server, you should then have a directory called /var/www/html/zaggonet:
```
cd /var/www/html/
git clone <this_repo_url>
```
- Run the setup script:
```
cd /var/www/html/zaggonet
./setup.sh
```

### Launch

Point your web browser to http://< ip_of_your_raspbx_server >/zaggonet
Default un/pw is: admin/zaggtastic

Add your configured phones in the call creator screen, by clicking the link under the select phone dropbox.

### Optional settings

- You may need to mess with vars.php to match your specific setup as well. A default raspbx setup should be fine though
- Default admin password is admin/zaggtastic, you can change this by creating a vars_override.php file and adding the following:
`<?php $valid_passwords = array ("<new_username>" => "<new_password>");
?>`
- If you want to clear out the file backup folders on a schedule to save disk space, add something like the following to cron (deletes audio and extension conf backups older than 30 days)
```
@daily find /var/www/html/zaggonet/backups/misc/* -mtime +30 -type f -delete
@daily find /var/www/html/zaggonet/backups/extconf/* -mtime +30 -type f -delete
```
