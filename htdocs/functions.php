<?php

function GetNow()
{
	$curTime = new DateTime( "NOW", new DateTimeZone( "UTC" ) );
	$dateStr = str_replace( "+0000", "UTC", $curTime->format( DateTime::RFC1123 ) );
	return $dateStr;
}

?>
