<?php

//client id and client secret
$client = '{SmartApp client id}';
$secret = '{SmartApp client secret}';

if(isset($argv[1])) {
	
	//$_REQUEST['access_token'] = $argv[1];
	
}

//hardcode the full url to redirect to this file
$Appurl = "{your server path}/alarm_sensor_status.php";

//STEP 1 - Get Access Code
if(!isset($_REQUEST['code']) && !isset($_REQUEST['access_token']))
{
	//Depending on your account, you may need to change the SmartThings URL
	header( "Location: https://graph-na02-useast1.api.smartthings.com/oauth/authorize?response_type=code&client_id=" . urlencode($client) . "&scope=app&redirect_uri=" . urlencode($Appurl) ) ;
}
//STEP 2 - Use Access Code to claim Access Token
else if(isset($_REQUEST['code']))
{
	$code = $_REQUEST['code'];
	$page = "https://graph-na02-useast1.api.smartthings.com/oauth/token?grant_type=authorization_code&client_id=".$client."&client_secret=".$secret."&redirect_uri=".$url."&code=".$code."&scope=app";

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL,            $page );
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt($ch, CURLOPT_POST,           0 );
	curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: application/json')); 

	$response =  json_decode(curl_exec($ch),true);

	curl_close($ch);

	if(isset($response['access_token']))
	{
		//Redirect to self with access token for step 3 for ease of bookmarking
		header( "Location: ?access_token=".$response['access_token'] ) ;
	}
	else
	{
		print "error requesting access token...";
		print_r($response);
	}

}
//Step 3 - Lookup Endpoint and write out urls
else if(isset($_REQUEST['access_token']))
{
	
	$STurl = "https://graph.api.smartthings.com/api/smartapps/endpoints/$client?access_token=".$_REQUEST['access_token'];
	
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL,            $STurl );
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt($ch, CURLOPT_POST,           0 );
	curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: application/json')); 

	$theEndpoints =  json_decode(curl_exec($ch),true);

	curl_close($ch);
	
	print "<html><head><style>h3{margin-left:10px;}a:hover{background-color:#c4c4c4;} a{border:1px solid black; padding:5px; margin:5px;text-decoration:none;color:black;border-radius:5px;background-color:#dcdcdc}</style></head><body>";


	print "<i>Save the above URL (access_token) for future reference.</i>";
	print " <i>Right Click on buttons to copy link address.</i>";
	

//GET SENSORS
		$sensorUrl = $theEndpoints[uri] . "/contacts";
		$access_key = $_REQUEST['access_token'];

		$ch = curl_init($sensorUrl);
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Authorization: Bearer ' . $access_key ) );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($ch, CURLOPT_POST,           0 );

		$resp =  curl_exec($ch);
		curl_close($ch);

		$respData = json_decode($resp,true);
		
		if(count($respData) > 0) print "<h2>Contact Sensors</h2>";
		else
			print "No sensors found";


		//let's show each of the switches
		foreach($respData as $i => $contact)
		{
			$label = $contact['label'] != "" ? $contact['label'] : "Unlabeled Contact Sensor";

			print " <h3>$label</h3>";

			$onUrl = $theEndpoints[uri] . "/contacts" . $contact['id'] . "/refresh?access_token=" . $_REQUEST['access_token'];
			$access_key = $_REQUEST['access_token'];

			$ch = curl_init($onUrl);
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Authorization: Bearer ' . $access_key ) );
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt($ch, CURLOPT_POST,           0 );
	
			$respData = json_decode(curl_exec($ch),true);	
			
			sleep(12);

			print "<a target='cmd' href='$onUrl'>" . $contact['value']['value'] . "</a>";
		}
}


?>