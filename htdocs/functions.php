<?php

function CheckSession( $sid )
{
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
		unset( $m );
		return false;
	}
	return true;
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


?>
