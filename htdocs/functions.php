<?php

function CheckSession( $sid )
{
	if( ValCode( $sid ) == NULL )
	{
		header( "Location: http://www.google.com" );
		exit();
	}
	$m = new MongoClient();
	$session = $m->braveskunk->sessions->findOne( array( "session" => $sid ) );
	unset( $m );

	if( $session == NULL )
	{
		return false;
	}

	if( $session["expiry"] < $_SERVER["REQUEST_TIME"] )
	{
		$m = new MongoClient();
		$result = $m->braveskunk->sessions->remove( $session );
		unset( $result );
		unset( $m );
		return false;
	}
	return true;
}

function GetAllies()
{
	$m = new MongoClient();
	$allies = $m->braveskunk->mailallies->find();
	unset( $m );
	foreach( $allies as $doc )
	{
		print( "<li><a href=\"/allyinfo.php?id=" . $doc["id"] . "\">" . $doc["name"] . "</a></li>\n" );
	}
	unset( $allies );
}

function GetCorps()
{
	$m = new MongoClient();
	$corps = $m->braveskunk->corporations->find();
	unset( $m );
	foreach( $corps as $doc )
	{
		print( "<li><a href=\"/corpinfo.php?id=" . $doc["id"] . "\">" . $doc["name"] . "</a></li>\n" );
	}
	unset( $corps );
}

function GetLists()
{
	$m = new MongoClient();
	$lists = $m->braveskunk->maillists->find();
	unset( $m );
	foreach( $lists as $doc )
	{
		print( "<li><a href=\"/mlinfo.php?id=" . $doc["id"] . "\">" . $doc["name"] . "</a></li>\n" );
	}
	unset( $lists );
}

function GetName( $sid )
{
	$m = new MongoClient();
	$session = $m->braveskunk->sessions->findOne( array( "session" => $sid ) );
	unset( $m );

	return( $session["name"] );
}

function GetRights( $sid )
{
	$m = new MongoClient();
	$session = $m->braveskunk->sessions->findOne( array( "session" => $sid ) );
	unset( $m );

	return( $session["rights"] );
}

function ValID( $id )
{
	$tmp = filter_var( $id, FILTER_VALIDATE_INT, array( "options" => array( "default" => NULL ) ) );
	if( $tmp != NULL )
	{
		$tmp = (string)$tmp;
	}
	return $tmp;
}

function ValCode( $vCode )
{
	return( filter_var( $vCode, FILTER_VALIDATE_REGEXP, array( "options" => array( "default" => NULL, "regexp" => "/^[[:alnum:]]+$/" ) ) ) );
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
}

function DelAPIKey( $objID )
{
	$row = array( "_id" => new MongoId( $objID ) );
	$m = new MongoClient();
	$result = $m->braveskunk->apikeys->remove( $row );
	unset( $result );
	unset( $m );
}	

?>
