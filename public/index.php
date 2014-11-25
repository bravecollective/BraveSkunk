<?php

include_once( "functions.php" );

session_start();
$sid = session_id();

if( !CheckSession( $sid ) )
{
	header( "Location: /login.php" );
	exit();
}

LogAccess( $sid );
$rights = GetRights( $sid );

include( "header.html" );

$m = new MongoClient();

print( "<div class=\"container\">\n" );
print( "<div class=\"page-header\"><h1>Spais-R-Us</h1></div>\n" );

//if( $rights == 0 || $rights == 1 || $rights == 2 )
if( $rights == 2 )
{
	print( "<ul class=\"nav nav-pills pull-right\">\n" );
//	if( $rights == 0 || $rights == 1 )
//	{
//		print( "<li><a href=\"/submit.php\"><i class=\"fa fa-arrow-circle-o-right fa-lg\"></i>Submit an API Key</a></li>\n" );
//	}
	if( $rights == 2 )
	{
		print( "<li><a href=\"/manage.php\"><i class=\"fa fa-arrow-circle-o-right fa-lg\"></i>Manage API Keys</a></li>\n" );
	}
	print( "</ul>\n" );
}

print( "<h4>Currently known EVE-Mails</h4>\n" );

print( "<div>\n" );

print( "<table class=\"table table-hover\">\n" );
print( "<thead>\n" );
print( "<tr>\n" );
print( "<th>Alliance</th>\n" );
print( "<th>Subject</th>\n" );
print( "<th><div class=\"pull-right\">Sent</div></th>\n" );
print( "</tr>\n" );
print( "</thead>\n" );
print( "<tbody style=\"background-color: rgba( 0, 0, 0, 0.5 );\">\n" );

$cursor = $m->braveskunk->mails->find()->sort( array( "date" => -1 ) );
foreach( $cursor as $doc )
{
	$to = $m->braveskunk->mailallies->findOne( array( "id" => (int)$doc["receiver"] ) )["name"];
	if( !$to )
	{
		$to = $m->braveskunk->corporations->findOne( array( "id" => (int)$doc["receiver"] ) )["name"];
	}

	if( $to != "" )
	{
		print( "<tr style=\"cursor: pointer;\" onclick=\"document.location='/message.php?message=" . $doc["id"]. "'\">\n" );
		print( "<td style=\"vertical-align: middle;\"><b>" . $to . "</b></td>\n" );
		print( "<td style=\"vertical-align: middle; width: 60%\">" . $doc["title"] . "</td>\n" );
		print( "<td style=\"vertical-align: middle;\"><div class=\"pull-right\"><b>" . $doc["date"] . "</b></div></td>\n" );
		print( "</tr>\n" );
	}
}

print( "</tbody>\n" );
print( "</table>\n" );

print( "</div>\n" );

unset( $m );

include( "footer.html" );

?>
