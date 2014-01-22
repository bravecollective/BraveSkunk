<?php

if( !empty( $_GET ) )
{
	if( $_GET["message"] )
	{
		DisplayMessage( $_GET["message"] );
	}
	else
	{
		main();
	}
}
else
{
	main();
}

function DisplayMessage( $id )
{
	include( "header.html" );

	$m = new MongoClient();
	$doc = $m->braveskunk->mails->findOne( array( "id" => $id ) );
	$sender = $m->braveskunk->characters->findOne( array( "id" => (int)$doc["sender"] ) )["name"];
	$rcvrs = "";
	if( strpos( $doc["receiver"], "," ) !== false )
	{
		$temp = explode( ",", $doc["receiver"] );
		for( $i = 0; $i < count( $temp ); $i++ )
		{
			$name = $m->braveskunk->alliances->findOne( array( "id" => (int)$temp[$i] ) )["name"];
			$type = 0;
			if( !$name )
			{
				$name = $m->braveskunk->corporations->findOne( array( "id" => (int)$temp[$i] ) )["name"];
				$type = 1;
				if( !$name )
				{
					$name = $m->braveskunk->characters->findOne( array( "id" => (int)$temp[$i] ) )["name"];
					$type = 2;
					if( !$name )
					{
						$name = $m->braveskunk->maillists->findOne( array( "id" => (int)$temp[$i] ) )["name"];
						$type = 3;
					}
				}
			}
			if( $rcvrs == "" )
			{
				$rcvrs = "To: ";
				if( $type == 0 )
				{
					$rcvrs = $rcvrs . "<a href=\"allyinfo.php?id=". $temp[$i] ."\">" . $name . "</a>";
				}
				if( $type == 1 )
				{
					$rcvrs = $rcvrs . "<a href=\"corpinfo.php?id=". $temp[$i] ."\">" . $name . "</a>";
				}
				if( $type == 2 )
				{
					$rcvrs = $rcvrs . "<a href=\"charinfo.php?id=". $temp[$i] ."\">" . $name . "</a>";
				}
				if( $type == 3 )
				{
					$rcvrs = $rcvrs . "<a href=\"mlinfo.php?id=". $temp[$i] ."\">" . $name . "</a>";
				}
			}
			else
			{
				if( $type == 0 )
				{
					$rcvrs = $rcvrs . ", <a href=\"allyinfo.php?id=". $temp[$i] ."\">" . $name . "</a>";
				}
				if( $type == 1 )
				{
					$rcvrs = $rcvrs . ", <a href=\"corpinfo.php?id=". $temp[$i] ."\">" . $name . "</a>";
				}
				if( $type == 2 )
				{
					$rcvrs = $rcvrs . ", <a href=\"charinfo.php?id=". $temp[$i] ."\">" . $name . "</a>";
				}
				if( $type == 3 )
				{
					$rcvrs = $rcvrs . ", <a href=\"mlinfo.php?id=". $temp[$i] ."\">" . $name . "</a>";
				}
			}
		}
	}
	else
	{
		$name = $m->braveskunk->alliances->findOne( array( "id" => (int)$doc["receiver"] ) )["name"];
		$rcvrs = "To: <a href=\"allyinfo.php?id=" . $doc["receiver"] . "\">" . $name . "</a>";
		if( !$name )
		{
			$name = $m->braveskunk->corporations->findOne( array( "id" => (int)$doc["receiver"] ) )["name"];
			$rcvrs = "To: <a href=\"corpinfo.php?id=" . $doc["receiver"] . "\">" . $name . "</a>";
			if( !$name )
			{
				$name = $m->braveskunk->characters->findOne( array( "id" => (int)$doc["receiver"] ) )["name"];
				$rcvrs = "To: <a href=\"charinfo.php?id=" . $doc["receiver"] . "\">" . $name . "</a>";
				if( !$name )
				{
					$name = $m->braveskunk->maillists->findOne( array( "id" => (int)$doc["receiver"] ) )["name"];
					$rcvrs = "To: <a href=\"mlinfo.php?id=" . $doc["receiver"] . "\">" . $name . "</a>";
				}
			}
		}
	}

	print( "<div class=\"frame\">\n" );
	print( "<div class=\"center\"><b>" . $doc["date"] . " - " . $doc["title"] . "</b></div>\n" );
	print( "From: <a href=\"charinfo.php?id=" . $doc["sender"] . "\">" . $sender . "</a><br>\n" );
	print( $rcvrs );
	print( "<br>\n" );
	print( strip_tags( $doc["body"], "<br>" ) . "\n" );
	print( "</div>\n" );

	include( "footer.html" );
}

function main()
{
	include( "header.html" );

	$m = new MongoClient();
	$cursor = $m->braveskunk->mails->find()->sort( array( "date" => -1 ) );

	print( "<div class=\"center\"><b>BraveSkunk v0.1</b></div>\n" );
	print( "<div class=\"frame\">\n" );

	foreach( $cursor as $doc )
	{
		$sender = $m->braveskunk->characters->findOne( array( "id" => (int)$doc["sender"] ) )["name"];
		$parent = $m->braveskunk->characters->findOne( array( "id" => (int)$doc["sender"] ) )["parentID"];
		$ticker = $m->braveskunk->alliances->findOne( array( "id" => (int)$parent ) )["ticker"];
		if( !$ticker )
		{
			$ticker = $m->braveskunk->corporations->findOne( array( "id" => (int)$parent ) )["ticker"];
		}
		if( $ticker != "" )
		{
			$ticker = "&#60" . $ticker . "&#62";
		}
		print( "<div>\n" );
		print( "<b>" . $doc["date"] . "</b>\n" );
		print( "<a href=\"?message=" . $doc["id"] . "\">" . $ticker . " " . $sender . " - " . $doc["title"] . "</a><br>\n" );
		print( "</div>\n" );
	}

	print( "</div>\n" );

	include( "footer.html" );
}

?>
