# SmartThings-Total-Connect-Sensor-Update
SmartThings SmartApp and files to get near real-time monitoring of TotalConnect Alarm system sensors

#Installation
Install the smart app via the IDE, go into its settings and enable OAuth, and note the client ID/secret for use in the PHP file. 
Upload the PHP file to your own server (internet accessible), and run it from your browser. It will ask you to authorize the app, select your sensors and then generate your Access token which you will need for all the other API calls. THen you can setup a cron tab on your server to run the script frequently. 
