<?php

if( $_SERVER["PHP_SELF"] == "/settings.php" )
{
	header( "Location: http://www.google.com" );
	exit();
}

// Application ID
$application_id = '5328f12458b70047ebb9d377';

// Core public key
$public_key = '7d0a166c4f904acb0f68378b3397a2a7cdb81a37fa619fc935315292976f433495a2e7c6011ef49479fb90d8ced4450c68a6990656bc8ec5cacc9f0adcc70e5b';

// App private key
$private_key = 'ac1937dab292e243518c3b1a1381a97385bbbb25d3a2099a7e9d2895d738771';

// Core url
$core_url = 'http://core.braveineve.com/api';

// DNS name of Skunk host
$skunk_host = 'http://ec2-54-148-216-14.us-west-2.compute.amazonaws.com/';

?>
