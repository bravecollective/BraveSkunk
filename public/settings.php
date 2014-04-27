<?php

if( $_SERVER["PHP_SELF"] == "/settings.php" )
{
	header( "Location: http://www.google.com" );
	exit();
}

// Application ID
$application_id = '5359ccf0d18eab0845644dc5';

// Core public key
$public_key = 'a8dc3ae2101fcf4e8fd45195e6c6101efde1325ebd004d787b30de22ef885bb3d5e2edcb1cb00b699fda46f63226720032317c070ff82f82fa1c69e38a914272';

// App private key
$private_key = '9fc41e50b6a4767b6e09e2912c1f38bb32000b8d994d97f85a823fc41f95b108';

// Core url
$core_url = 'http://192.168.1.50:8080/api';

// DNS name of Skunk host
$skunk_host = 'http://192.168.1.50/';

?>
