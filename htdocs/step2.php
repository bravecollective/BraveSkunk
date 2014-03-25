<?php

include_once( "functions.php" );

if( !isset( $_GET["token"] ) || $_GET["token"] == NULL )
{
	header( "Location: /login.php" );
	exit();
}

session_start();
$sid = session_id();

// include composer autoloader
require('vendor/autoload.php');
define('USE_EXT', 'GMP');

// API Keys
include_once( "settings.php" );

// Get Token from core. DO THIS WITH PROPER SECURITY WHEN DEVELOPING REAL APPLICATIONS
$token = ValCode( $_GET['token'] );
if( $token == NULL )
{
	exit( "Invalid token received." );
}

// API Class Setup
$api = new Brave\API('https://core.bravecollective.net/api', $application_id, $private_key, $public_key);

// Hit /info method and get data from token
$result = $api->core->info(array('token' => $token));
// Grab Character Name
$name = $result->character->name;
// Grab tags associated with said character for this app
$tags = $result->tags;
// Set expiry for time of page load + 1 week
$expiry = $_SERVER["REQUEST_TIME"] + 604800;
// Init rights variable
$rights = 0;

foreach( $tags as $right )
{
	$temp = explode( ".", $right );
	if( $temp[0] == "skunk" )
	{
		$right = $temp[1];
	}

	switch( $right )
	{
		case "member":	$val = 0;
				break;
		case "spai":	$val = 1;
				break;
		case "admin":	$val = 2;
				break;
		default:	$val = 0;
				break;
	}

	if( $val > $rights )
	{
		$rights = $val;
	}
}

// Build database insertion array
$row = array( "session" => $sid, "name" => $name, "rights" => $rights, "expiry" => $expiry );

$m = new MongoClient();

// Clean up all prior store sessions for this character
$cursor = $m->braveskunk->sessions->find( array( "name" => $name ) );
foreach( $cursor as $doc )
{
	$result = $m->braveskunk->sessions->remove( $doc );
}

// Add the newly created sessions for the returned character to the database.
$result = $m->braveskunk->sessions->insert( $row );
unset( $m );

header( "Location: /index.php" );
exit();

?>
