<?php

if( !empty( $_GET ) )
{
	if( $_GET["id"] )
	{
		DisplayInfo( $_GET["id"] );
	}
}
else
{
	main();
}

function DisplayInfo( $id )
{
	include( "header.html" );

	$m = new MongoClient();
	$name = $m->braveskunk->maillists->findOne( array( "id" => (int)$id ) )["name"];

	print( "<div class=\"frame\">\n" );
	print( "<div class=\"center\">" . $name . "</div>\n" );
	print( "<div class=\"frame\">\n" );
	print( "<div class=\"center\">Inbox</div>\n" );
	$cursor = $m->braveskunk->mails->find( array( "receiver" => array( '$regex' => new MongoRegex( "/.*" . $id . ".*/" ) ) ) )->sort( array ( "date" => -1 ) );
	foreach( $cursor as $doc )
	{
		print( "<b>" .$doc["date"] . "</b>\n" );
		print( "<a href=\"index.php?message=" . $doc["id"] . "\">" . $doc["title"] . "</a><br>\n" );
	}
	print( "</div>\n" );
	print( "</div>\n" );

	include( "footer.html" );
}

function main()
{
	include( "header.html" );

	print( "<div>How did you get here?</div>\n" );

	include( "footer.html" );
}

?>
