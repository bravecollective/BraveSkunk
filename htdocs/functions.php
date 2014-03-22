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

?>
