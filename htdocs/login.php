<?php

include_once( "functions.php" );

session_start();
$sid = session_id();

if( CheckSession( $sid ) )
{
	header( "Location: /index.php" );
	exit();
}

// include composer autoloader
require('vendor/autoload.php');
define('USE_EXT', 'GMP');

// API Keys
include_once( "settings.php" );

// API Class Setup
$api = new Brave\API('https://core.braveineve.com/api', $application_id, $private_key, $public_key);

// API Call Args
$info_data = array(
	'success' => 'http://ec2-54-201-154-61.us-west-2.compute.amazonaws.com/step2.php',
	'failure' => 'http://ec2-54-201-154-61.us-west-2.compute.amazonaws.com/failure.php'
);
$result = $api->core->authorize($info_data);

// Redirect back to the auth platform for user authentication approval
header("Location: ".$result->location);
exit();
