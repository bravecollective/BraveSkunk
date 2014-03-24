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
if( $rights < 1 )
{
	header( "Location: /index.php" );
	exit();
}

if( isset( $_POST["keyid"] ) && isset( $_POST["vCode"] ) && !empty( $_POST["keyid"] ) && !empty( $_POST["vCode"] ) )
{
	$id = ValID( $_POST["keyid"] );
	$vCode = ValCode( $_POST["vCode"] );
	if( $id == NULL || $vCode == NULL )
	{
		exit( "Invalid API Key." );
	}
	AddAPIKey( $id, $vCode );
	header( "Location: /index.php" );
	exit();
}

include( "header.html" );

print( "<div class=\"container\">\n" );
print( "<div class=\"page-header\"><h1>Spais-R-Us</h1></div>\n" );
print( "<h4>Submit a new API Key</h4>\n" );


print( "<div class=\"list-group clearfix\">\n" );
print( "<div class=\"list-group-item media-list clearfix\">\n" );
print( "<form style=\"text-align: center\" method=\"post\" action=\"submit.php\">\n" );
print( "<li><input class=\"\" type=\"text\" name=\"keyid\" autofocus placeholder=\"API Key ID\"></li>\n" );
print( "<li><input type=\"text\" name=\"vCode\" placeholder=\"API vCode\"></li>\n" );
print( "<button type=\"submit\">Submit</button>\n" );
print( "</form>\n" );

print( "</div>\n" );
print( "</div>\n" );

include( "footer.html" );

?>
