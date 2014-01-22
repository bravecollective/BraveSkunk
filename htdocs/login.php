<?php

include( "functions.php" );

if( !empty( $_POST ) )
{
	if( $_POST["uid"] and $_POST["pswd"] )
	{
		$uid = $_POST["uid"];
		$pswd = $_POST["pswd"];
		Login( $uid, $pswd );
	}
}
else
{
	main();
}

function Login( $uid, $pswd )
{
	$date = "Date: " . GetNow();
	print( $date );
}

function main()
{
	include( "header.html" );

	print( "<div class=\"frame\">\n" );
	print( "<form method=\"post\" action=\"login.php\">\n" );
	print( "<table>\n");
	print( "<tr>\n" );
	print( "<td>Username:</td>\n" );
	print( "<td><input name=\"uid\" autofocus></td>\n" );
	print( "</tr>\n" );
	print( "<tr>\n" );
	print( "<td>Password:</td>\n" );
	print( "<td><input type=\"password\" name=\"pswd\"></td>\n" );
	print( "</tr>\n" );
	print( "<tr>\n" );
	print( "<td></td>\n" );
	print( "<td class=\"center\"><input type=\"submit\" value=\"Submit\"></td>\n" );
	print( "</tr>\n" );
	print( "</table>\n" );
	print( "</form>\n" );
	print( "</div>\n" );

	include( "footer.html" );
}

?>
