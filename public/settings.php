<?php

if( $_SERVER["PHP_SELF"] == "/settings.php" )
{
	header( "Location: http://www.google.com" );
	exit();
}

// Application ID
$application_id = '<INSERT YOUR APP ID HERE>';

// Core public key
$public_key = '<INSERT CORE'S PUBLIC KEY HERE>';

// App private key
$private_key = '<INSERT SKUNK'S PRIVATE KEY HERE>';

// Core url
$core_url = '<INSERT URL OF CORE API HERE>';

// DNS name of Skunk host
$skunk_host = '<INSERT SKUNK'S URL HERE>';

?>
