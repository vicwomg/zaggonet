#!/bin/sh

# Set dir permissions properly
echo "ZAGGONET: Setting zaggonet directory permissions to asterisk:asterisk"
chown -R asterisk:asterisk ../zaggonet

echo
# Install tts dependencies
echo "ZAGGONET: Installing libttspico dependencies..."
sudo apt-get update
sudo apt-get install libttspico0
sudo apt-get install libttspico-utils
echo
echo "ZAGGONET: Downloading picotts.agi script to /var/lib/asterisk/agi-bin/ ..."
URL="https://raw.githubusercontent.com/stevenmirabito/asterisk-picotts/master/picotts.agi"
wget -O /var/lib/asterisk/agi-bin/picotts.agi ${URL}
echo
echo "ZAGGONET: Setting excecutable bits to: /var/lib/asterisk/agi-bin/picotts.agi ..."
chmod +x /var/lib/asterisk/agi-bin/picotts.agi

echo
echo "ZAGGONET: Installing eyeD3 (for stripping id3 tags)..."
sudo apt-get install python-pip
sudo pip install eyeD3

# Adding required extensions_custom.conf modifications
echo
echo "ZAGGONET: Adding custom tts dialplan to extensions_custom.conf ..."
EXT_CUSTOM_PATH="/etc/asterisk/extensions_custom.conf"
EXT_CUST="\n\n; DO NOT DELETE THIS SECTION BELOW\n; Required for sending automated voice calls\n[say-message]\n exten => s,1,Answer()\n same => n,agi(picotts.agi, \"\${MESSAGE}\", \"\${VOICE}\", any, \"\${SPEED}\"\n same => n,Hangup()\n\n"
echo ${EXT_CUST} >> ${EXT_CUSTOM_PATH}
