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

if( isset( $_POST["objID"] ) && !empty( $_POST["objID"] ) )
{
	LogAccess( $sid );
	$id = ValCode( $_POST["objID"] );
	if( $id == NULL )
	{
		exit( "Invalid object id." );
	}
	DelAPIKey( $id );
	header( "Location: /manage.php" );
	exit();
}

LogAccess( $sid );

include( "header.html" );

print( "<div class=\"container\">\n" );
print( "<div class=\"page-header\"><h1>Spais-R-Us</h1></div>\n" );

print( "<h4>Manage current API Keys</h4>\n" );

print( "<div class=\"list-group media-list clearfix\">\n" );

$m = new MongoClient();
$cursor = $m->braveskunk->apikeys->find();
foreach( $cursor as $doc )
{
	print( "<div class=\"list-group-item media thread\">\n" );
	print( "<ul class=\"nav nav-pills pull-right\">\n" );
	print( "<li>\n" );
	print( "<form method=\"post\" action=\"manage.php\">\n" );
	print( "<input type=\"hidden\" name=\"objID\" value=\"" . (string)$doc["_id"] . "\">\n" );
	print( "<button type=\"submit\"><i class=\"fa fa-trash-o fa-lg\"></i>Delete</button>\n" );
	print( "</form>\n" );
	print( "</li>\n" );
	print( "</ul>\n" );

	print( "<li><b>Key ID:</b> " . $doc["keyid"] . "</li>\n" );
	print( "<li><b>vCode:</b> " . $doc["vCode"] . "</li>\n" );
	print( "</div>\n" );
}

print( "</div>\n" );

include( "footer.html" );

?>
