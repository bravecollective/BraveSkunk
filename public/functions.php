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
	require('vendor/autoload.php');
	if( !defined( "USE_EXT" ) )
	{
		define('USE_EXT', 'GMP');
	}
	include( "settings.php" );

	$m = new MongoClient();
	$session = $m->braveskunk->sessions->findOne( array( "session" => $sid ) );
	unset( $m );

	$api = new Brave\API($core_url, $application_id, $private_key, $public_key);
	$result = $api->core->info( array( 'token' => $session["token"] ) );
	$tags = $result->tags;
	$rights = -1;

	foreach( $tags as $right )
	{
		$temp = preg_split( "/\./", $right );
		if( $temp[0] == "skunk" )
		{
			$right = $temp[1];
		}

		switch( $right )
		{
			case "member":	$val = 0;
					break;
			case "spai":	$val = 1;
					break;
			case "admin":	$val = 2;
					break;
			default:	$val = -1;
					break;
		}

		if( $val > $rights )
		{
			$rights = $val;
		}
	}

	if( $rights == -1 )
	{
		header( "Location: /logout.php" );
		exit();
	}

	return( $rights );
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

function LogAccess( $sid )
{
	$m = new MongoClient();
	$session = $m->braveskunk->sessions->findOne( array( "session" => $sid ) );

	$row = array( "time" => $_SERVER["REQUEST_TIME"], "address" => $_SERVER["REMOTE_ADDR"], "url" => $_SERVER["REQUEST_URI"], "user" => $session["name"] );
	$result = $m->braveskunk->logs->insert( $row );
	unset( $result );
	unset( $session );
	unset( $m );
}

?>
