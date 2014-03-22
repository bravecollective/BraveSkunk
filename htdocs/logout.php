<?php

include_once( "functions.php" );

session_start();
$sid = session_id();

if( CheckSession( $sid ) )
{
	$m = new MongoClient();
	$row = array( "session" => $sid );
	$doc = $m->braveskunk->sessions->remove( $row );
	session_destroy();
	header( "Location: http://www.google.com" );
	exit();
}
else
{
	exit( "You weren't logged in." );
}
