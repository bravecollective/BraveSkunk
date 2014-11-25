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
if( !defined( "USE_EXT" ) )
{
	define('USE_EXT', 'GMP');
}

// API Keys
include_once( "settings.php" );

// API Class Setup
$api = new Brave\API( $core_url, $application_id, $private_key, $public_key);

// API Call Args
$info_data = array(
	'success' => $skunk_host . '/step2.php',
	'failure' => $skunk_host . '/failure.php'
);
$result = $api->core->authorize($info_data);

var_dump( $api );
var_dump( $info_data );
var_dump( $result );

// Redirect back to the auth platform for user authentication approval
header("Location: ".$result->location);
exit();
