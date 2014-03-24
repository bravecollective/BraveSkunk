<?php

include_once( "functions.php" );

session_start();
$sid = session_id();

if( !CheckSession( $sid ) )
{
        header( "Location: /index.php" );
        exit();
}

if( !isset( $_GET["id"] ) || empty( $_GET["id"] ) )
{
	header( "Location: /index.php" );
	exit();
}

$id = ValID( $_GET["id"] );
if( $id == NULL )
{
	header( "Location: /index.php" );
	exit();
}

include( "header.html" );

$m = new MongoClient();
$name = $m->braveskunk->characters->findOne( array( "id" => (int)$id ) )["name"];

print( "<div class=\"container\">\n" );
print( "<div class=\"page-header\"><h1>Spais-R-Us <small>EVE-Mails for " . $name . "</small></h1></div>\n" );
print( "<div class=\"list-group media-list clearfix\">\n" );

if( $m->braveskunk->apikeys->findOne( array( "charID" => (int)$id ) ) == null )
{
	print( "<div><i>** No API key for this character is available. **</i></div>\n" );
}

print( "<div class=\"container\">\n" );
print( "<h4>Outbox</h4>\n" );
$cursor = $m->braveskunk->mails->find( array( "sender" => (int)$id ) )->sort( array ( "date" => -1 ) );
foreach( $cursor as $doc )
{
	print( "<ul class=\"list-group-item media thread\">\n" );
	print( "<b>" .$doc["date"] . "</b>\n" );
	print( "<a href=\"message.php?message=" . $doc["id"] . "\">" . $doc["title"] . "</a>\n" );
	print( "</ul>\n" );
}
print( "</div>\n" );

print( "<div class=\"container\">\n" );
print( "<h4>Inbox</h4>\n" );
$cursor = $m->braveskunk->mails->find( array( "receiver" => array( '$regex' => new MongoRegex( "/.*" . $id . ".*/" ) ) ) )->sort( array ( "date" => -1 ) );
foreach( $cursor as $doc )
{
	print( "<ul class=\"list-group-item media thread\">\n" );
	print( "<b>" .$doc["date"] . "</b>\n" );
	print( "<a href=\"message.php?message=" . $doc["id"] . "\">" . $doc["title"] . "</a><br>\n" );
	print( "</ul>\n" );
}

print( "</div>\n" );
print( "</div>\n" );

include( "footer.html" );

?>
