<?php

if( !isset( $_GET["id" ) || empty( $_GET["id"] )
{
	header( "Location: index.php" );
	exit();
}

$id = $_GET["id"];

include( "header.html" );

$m = new MongoClient();
$name = $m->braveskunk->characters->findOne( array( "id" => (int)$id ) )["name"];

print( "<div class=\"frame\">\n" );
print( "<div class=\"center\"><b>" . $name . "</b>" );
if( $m->braveskunk->apikeys->findOne( array( "charID" => (int)$id ) ) == null )
{
	print( " <i>** No API key for this character is available. **</i></div>\n" );
}
print( "<div class=\"frame\">\n" );
print( "<div class=\"center\">Outbox</div>\n" );
$cursor = $m->braveskunk->mails->find( array( "sender" => (int)$id ) )->sort( array ( "date" => -1 ) );
foreach( $cursor as $doc )
{
	print( "<b>" .$doc["date"] . "</b>\n" );
	print( "<a href=\"message.php?message=" . $doc["id"] . "\">" . $doc["title"] . "</a><br>\n" );
}
print( "</div>\n" );
print( "<div class=\"frame\">\n" );
print( "<div class=\"center\">Inbox</div>\n" );
$cursor = $m->braveskunk->mails->find( array( "receiver" => array( '$regex' => new MongoRegex( "/.*" . $id . ".*/" ) ) ) )->sort( array ( "date" => -1 ) );
foreach( $cursor as $doc )
{
	print( "<b>" .$doc["date"] . "</b>\n" );
	print( "<a href=\"message.php?message=" . $doc["id"] . "\">" . $doc["title"] . "</a><br>\n" );
}
print( "</div>\n" );
print( "</div>\n" );

include( "footer.html" );

?>
