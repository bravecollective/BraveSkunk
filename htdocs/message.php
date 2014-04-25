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

if( !isset( $_GET["message"] ) || $_GET["message"] == NULL )
{
	header( "Location: /index.php" );
	exit();
}

$id = ValID( $_GET["message"] );
if( $id == NULL )
{
	header( "Location: /index.php" );
	exit();
}

LogAccess( $sid );

include( "header.html" );

$m = new MongoClient();
$doc = $m->braveskunk->mails->findOne( array( "id" => $id ) );
$sender = $m->braveskunk->characters->findOne( array( "id" => (int)$doc["sender"] ) )["name"];
$rcvrs = "";

if( strpos( $doc["receiver"], "," ) !== false )
{
	$temp = explode( ",", $doc["receiver"] );
	for( $i = 0; $i < count( $temp ); $i++ )
	{
		$name = $m->braveskunk->alliances->findOne( array( "id" => (int)$temp[$i] ) )["name"];
		$type = 0;
		if( !$name )
		{
			$name = $m->braveskunk->corporations->findOne( array( "id" => (int)$temp[$i] ) )["name"];
			$type = 1;
			if( !$name )
			{
				$name = $m->braveskunk->characters->findOne( array( "id" => (int)$temp[$i] ) )["name"];
				$type = 2;
				if( !$name )
				{
					$name = $m->braveskunk->maillists->findOne( array( "id" => (int)$temp[$i] ) )["name"];
					$type = 3;
				}
			}
		}
		if( $rcvrs == "" )
		{
			$rcvrs = "To: ";
		}
		else
		{
			$rcvrs = $rcvrs . ", ";
		}
		switch( $type )
		{
			case 0:	$rcvrs = $rcvrs . "<a href=\"allyinfo.php?id=". $temp[$i] ."\">" . $name . "</a>";
				break;
			case 1:	$rcvrs = $rcvrs . "<a href=\"corpinfo.php?id=". $temp[$i] ."\">" . $name . "</a>";
				break;
			case 2:	$rcvrs = $rcvrs . "<a href=\"charinfo.php?id=". $temp[$i] ."\">" . $name . "</a>";
				break;
			case 3:	$rcvrs = $rcvrs . "<a href=\"mlinfo.php?id=". $temp[$i] ."\">" . $name . "</a>";
				break;
		}
	}
}
else
{
	$name = $m->braveskunk->alliances->findOne( array( "id" => (int)$doc["receiver"] ) )["name"];
	$rcvrs = "To: <a href=\"allyinfo.php?id=" . $doc["receiver"] . "\">" . $name . "</a>";
	if( !$name )
	{
		$name = $m->braveskunk->corporations->findOne( array( "id" => (int)$doc["receiver"] ) )["name"];
		$rcvrs = "To: <a href=\"corpinfo.php?id=" . $doc["receiver"] . "\">" . $name . "</a>";
		if( !$name )
		{
			$name = $m->braveskunk->characters->findOne( array( "id" => (int)$doc["receiver"] ) )["name"];
			$rcvrs = "To: <a href=\"charinfo.php?id=" . $doc["receiver"] . "\">" . $name . "</a>";
			if( !$name )
			{
				$name = $m->braveskunk->maillists->findOne( array( "id" => (int)$doc["receiver"] ) )["name"];
				$rcvrs = "To: <a href=\"mlinfo.php?id=" . $doc["receiver"] . "\">" . $name . "</a>";
			}
		}
	}
}

print( "<div class=\"container\">\n" );
print( "<div class=\"page-header\"><h1>Spais-R-Us <small>EVE-Mail \"" . $doc["date"] . " - " . $doc["title"] . "\"</small></h1></div>\n" );
print( "<div class=\"list-group media-list clearfix\">\n" );
print( "<div class=\"list-group-item media thread\">\n" );

print( "From: <a href=\"charinfo.php?id=" . $doc["sender"] . "\">" . $sender . "</a><br>\n" );
if( $rights == 2 )
{
	print( $rcvrs ."<br>\n" );
}
$body = strip_tags( $doc["body"], "<br>" );
if( $rights != 2 )
{
	$body = preg_replace( "/(<br>To:)[[:alpha:], ]*(<br>)/", "<br>", $body );
}
print( $body . "\n" );
print( "</div>\n" );
print( "</div>\n" );

include( "footer.html" );

?>
