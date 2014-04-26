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

LogAccess( $sid );

include( "header.html" );

$m = new MongoClient();
$name = $m->braveskunk->corporations->findOne( array( "id" => (int)$id ) )["name"];

print( "<div class=\"container\">\n" );
print( "<div class=\"page-header\"><h1>Spais-R-Us <small>EVE-Mails for " . $name . "</small></h1></div>\n" );

print( "<table class=\"table table-hover\">\n" );
print( "<thead>\n" );
print( "<tr>\n" );
print( "<th>Subject</th>\n" );
print( "<th><div class=\"pull-right\">Sent</div></th>\n" );
print( "</tr>\n" );
print( "</thead>\n" );
print( "<tbody style=\"background-color: rgba( 0, 0, 0, 0.5 );\">\n" );

$cursor = $m->braveskunk->mails->find( array( "receiver" => (int)$id ) )->sort( array ( "date" => -1 ) );
foreach( $cursor as $doc )
{
	print( "<tr style=\"cursor: pointer;\" onclick=\"document.location='/message.php?message=" . $doc["id"]. "'\">\n" );
	print( "<td style=\"vertical-align: middle; width: 60%\">" . $doc["title"] . "</td>\n" );
	print( "<td style=\"vertical-align: middle;\"><div class=\"pull-right\"><b>" . $doc["date"] . "</b></div></td>\n" );
	print( "</tr>\n" );
}

print( "</tbody>\n" );
print( "</table>\n" );

print( "</div>\n" );

include( "footer.html" );

?>
