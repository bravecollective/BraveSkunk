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
	AddAPIKey( $_POST["keyid"], $_POST["vCode"] );
	header( "Location: /index.php" );
	exit();
}

include( "header.html" );

print( "<div class=\"container\">\n" );
print( "<div class=\"page-header\"><h1>Spais-R-Us <small>Submit a new API Key</small></h1></div>\n" );

print( "<form method=\"post\" action=\"submit.php\">\n" );
print( "<table>\n" );
print( "<tr>\n" );
print( "<td>Key ID:</td>\n" );
print( "<td><input name=\"keyid\" autofocus></td>\n" );
print( "</tr>\n" );
print( "<tr>\n" );
print( "<td>vCode:</td>\n" );
print( "<td><input name=\"vCode\"></td>\n" );
print( "</tr>\n" );
print( "<tr>\n" );
print( "<td></td>\n" );
print( "<td class=\"center\"><input type=\"submit\" value=\"Submit\"></td>\n" );
print( "</tr>\n" );
print( "</table>\n" );
print( "</form>\n" );

print( "</div>\n" );

include( "footer.html" );

?>
