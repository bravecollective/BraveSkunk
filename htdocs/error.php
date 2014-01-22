<?php

if( !empty( $_GET ) )
{
	if( $_GET["error"] )
	{
		DisplayError( $_GET["error"] );
	}
}
else
{
	main();
}

function DisplayError( $error )
{
	include( "header.html" );

	if( $error == "apikey" )
	{
		print( "<div>You apparently forgot something important while submitting a new apikey. <a href=\"submit.php\">Try again?</a></div>\n" );
	}

	if( $error == "exists" )
	{
		print( "<div>That apikey already exists in the database. <a href=\"submit.php\">Try again?</a></div>\n" );
	}

	include( "footer.html" );
}

function main()
{
	include( "header.html" );

	print( "<div>How did you get here?</div>\n" );

	include( "footer.html" );
}

?>
