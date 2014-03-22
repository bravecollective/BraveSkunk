<?php

if( !isset( $_GET["id"] ) || empty( $_GET["id"] ) )
{
	header( "Location: index.php" );
	exit();
}

$id = $_GET["id"];

include( "header.html" );

$m = new MongoClient();
$name = $m->braveskunk->alliances->findOne( array( "id" => (int)$id ) )["name"];

print( "<div class=\"frame\">\n" );
print( "<div class=\"center\">" . $name . "</div>\n" );
print( "<div class=\"frame\">\n" );
print( "<div class=\"center\">Inbox</div>\n" );
$cursor = $m->braveskunk->mails->find( array( "receiver" => (int)$id ) )->sort( array ( "date" => -1 ) );
foreach( $cursor as $doc )
{
	print( "<b>" .$doc["date"] . "</b>\n" );
	print( "<a href=\"message.php?message=" . $doc["id"] . "\">" . $doc["title"] . "</a><br>\n" );
}
print( "</div>\n" );
print( "</div>\n" );
print( "</div>\n" );

include( "footer.html" );

?>
