<?php

if( !empty( $_POST ) )
{
	if( $_POST["keyid"] and $_POST["vCode"] )
		AddAPIKey( $_POST["keyid"], $_POST["vCode"] );
	else
	{
		header( "Location: error.php?error=apikey" );
	}
}
else
{
	main();
}

function AddAPIKey( $keyid, $vCode )
{
	$row = array( "keyid" => $keyid, "vCode" => $vCode );
	$m = new MongoClient();
	$doc = $m->braveskunk->apikeys->findOne( $row );
	if( !$doc )
	{
		$result = $m->braveskunk->apikeys->insert( $row );
	}
	else
	{
		header( "Location: error.php?error=exists" );
	}
}

function main()
{
	include( "header.html" );

	print( "<div class=\"frame\">\n" );
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
}
?>
