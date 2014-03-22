<?php

include_once( "functions.php" );

session_start();
$sid = session_id();

if( !CheckSession( $sid ) )
{
	header( "Location: /login.php" );
	exit();
}

$m = new MongoClient();
$session = $m->braveskunk->sessions->findOne( array( "session" => $sid ) );

include( "header.html" );

$m = new MongoClient();
$cursor = $m->braveskunk->mails->find()->sort( array( "date" => -1 ) );

print( "<div class=\"center\"><b>BraveSkunk v0.1</b></div>\n" );
print( "<div class=\"center\"><table width=\"100%\"><tr><td align=\"left\">Currently logged in as: <b>" . $session["name"] . "</b></td><td align=\"right\"><a href=\"logout.php\">Logout</a></td></tr></table></div>\n" );
print( "<div class=\"frame\">\n" );

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
	print( "<div>\n" );
	print( "<b>" . $doc["date"] . "</b>\n" );
	print( "<a href=\"message.php?message=" . $doc["id"] . "\">" . $ticker . " " . $sender . " - " . $doc["title"] . "</a><br>\n" );
	print( "</div>\n" );
}

print( "</div>\n" );

unset( $m );

include( "footer.html" );

?>
