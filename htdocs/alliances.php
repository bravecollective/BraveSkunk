<?php

include_once( "functions.php" );

session_start();
$sid = session_id();

if( !CheckSession( $sid ) )
{
	header( "Location: /index.php" );
	exit();
}

LogAccess( $sid );

include( "header.html" );

$m = new MongoClient();

print( "<div class=\"container\">\n" );
print( "<div class=\"page-header\"><h1>Spais-R-Us</h1></div>\n" );

print( "<h4>Currently known Alliances</h4>\n" );

print( "<table class=\"table table-hover\">\n" );
print( "<thead>\n" );
print( "<tr>\n" );
print( "<th>Alliance Name</th>\n" );
print( "<th>Ticker</th>\n" );
print( "<th><div class=\"pull-right\"># of Mails</div></th>\n" );
print( "</tr>\n" );
print( "</thead>\n" );
print( "<tbody style=\"background-color: rgba( 0, 0, 0, 0.5 );\">\n" );

$cursor = $m->braveskunk->mailallies->find()->sort( array( "name" => 1 ) );
foreach( $cursor as $doc )
{
	print( "<tr style=\"cursor: pointer;\" onclick=\"document.location='/allyinfo.php?id=" . $doc["id"]. "'\">\n" );
	print( "<td style=\"vertical-align: middle;\"><b>" . $doc["name"] . "</b></td>\n" );
	print( "<td style=\"vertical-align: middle;\"><b>" . $doc["ticker"] . "</b></td>\n" );
	$n = $m->braveskunk->mails->count( array( "receiver" => (int)$doc["id"] ) );
	print( "<td style=\"vertical-align: middle;\"><div class=\"pull-right\"><b>" . $n . "</b></div></td>\n" );
	print( "</tr>\n" );
}

print( "</tbody>\n" );
print( "</table>\n" );

print( "</div>\n" );

unset( $m );

include( "footer.html" );

?>
