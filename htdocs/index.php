<?php

include_once( "functions.php" );

session_start();
$sid = session_id();

if( !CheckSession( $sid ) )
{
	header( "Location: /login.php" );
	exit();
}

include( "header.html" );

$m = new MongoClient();
$cursor = $m->braveskunk->mails->find()->sort( array( "date" => -1 ) );

print( "<div class=\"container\">\n" );
print( "<div class=\"page-header\"><h1>Spais-R-Us</h1></div>\n" );

$rights = GetRights( $sid );
if( $rights == 1 || $rights == 2 )
{
	print( "<ul class=\"nav nav-pills pull-right\">\n" );
	if( $rights == 1 )
	{
		print( "<li><a href=\"/submit.php\"><i class=\"fa fa-arrow-circle-o-right fa-lg\"></i>Submit an API Key</a></li>\n" );
	}
	if( $rights == 2 )
	{
		print( "<li><a href=\"/manage.php\"><i class=\"fa fa-arrow-circle-o-right fa-lg\"></i>Manage API Keys</a></li>\n" );
	}
	print( "</ul>\n" );
}

print( "<h4>Currently known EVE-Mails</h4>\n" );

print( "<div class=\"list-group media-list clearfix\">\n" );

foreach( $cursor as $doc )
{
	$sender = $m->braveskunk->characters->findOne( array( "id" => (int)$doc["sender"] ) )["name"];
	$parent = $m->braveskunk->characters->findOne( array( "id" => (int)$doc["sender"] ) )["parentID"];
	$ticker = $m->braveskunk->alliances->findOne( array( "id" => (int)$parent ) )["ticker"];
	if( !$ticker )
	{
		$ticker = $m->braveskunk->corporations->findOne( array( "id" => (int)$parent ) )["ticker"];
	}
	if( $ticker != "" )
	{
		$ticker = "&#60" . $ticker . "&#62";
	}
	print( "<ul class=\"list-group-item media thread\">\n" );
	print( "<b>" . $doc["date"] . "</b>\n" );
	print( "<a href=\"message.php?message=" . $doc["id"] . "\">" . $ticker . " " . $sender . " - " . $doc["title"] . "</a><br>\n" );
	print( "</ul>\n" );
}

print( "</div>\n" );

unset( $m );

include( "footer.html" );

?>
