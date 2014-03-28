<?php

include_once( "functions.php" );

session_start();
$sid = session_id();

if( !CheckSession( $sid ) )
{
	header( "Location: /index.php" );
	exit();
}

$rights = GetRights( $sid );
if( $rights < 2 )
{
	header( "Location: /index.php" );
	exit();
}

date_default_timezone_set( "UTC" );

include( "header.html" );

print( "<div class=\"container\">\n" );
print( "<div class=\"page-header\"><h1>Spais-R-Us</h1></div>\n" );


print( "<h4>Logs</h4>\n" );

print( "<div class=\"list-group media-list clearfix\">\n" );

$m = new MongoClient();
$cursor = NULL;

if( isset( $_GET["address"] ) )
{
	$address = filter_var( $_GET["address"], FILTER_VALIDATE_IP, array( "options" => array( "default" => NULL ) ) );
	if( $address == NULL )
	{
		header( "Location: /index.php" );
		exit();
	}
	$cursor = $m->braveskunk->logs->find( array( "address" => $address ) )->sort( array( "time" => -1 ) );
}
elseif( isset( $_GET["user"] ) )
{
	$user = filter_var( $_GET["user"], FILTER_VALIDATE_REGEXP, array( "options" => array( "default" => NULL, "regexp" => "/^[[:alnum:] ]+$/" ) ) );
	if( $user == NULL )
	{
		header( "Location: /index.php" );
		exit();
	}
	$cursor = $m->braveskunk->logs->find( array( "user" => $user ) )->sort( array( "time" => -1 ) );
}
else
{
	$cursor = $m->braveskunk->logs->find()->sort( array( "time" => -1 ) );
}

foreach( $cursor as $doc )
{
	print( "<div class=\"list-group-item media thread\">\n" );
	print( "<li>\n" );
	print( date( "Y M j G:i:s", $doc["time"] ) . " - <a href=\"/logs.php?address=" . $doc["address"] ."\">" . $doc["address"] . "</a> - <a href=\"/logs.php?user=". $doc["user"] . "\">" . $doc["user"] . "</a> - " . $doc["url"] . "\n" );
	print( "</li>\n" );
	print( "</div>\n" );
}

print( "</div>\n" );

include( "footer.html" );

?>
