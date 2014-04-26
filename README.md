BraveSkunk
==========

Dependencies:
requires bravecollective's php-api lib.

Setup:
python script in "backside" should be setup as a cron job no more frequently than every 15 minutes due to EVE API caching.
files in "public" should be located in your web server's htdocs or similiar directory.

Settings: The settings.php file has several options that must be set for the application to work.
1) "$application_id" is the id number of the Skunk app in Core.  This is obtained only after you've registered the application.
2) "$public_key" is the public key of Core.
3) "$private_key" is the private key of the Skunk app, usually generated via the php-api scripts.
4) "$core_url" is the full url to Core's API endpoint.
5) "$skunk_host" is the full dns url for your Skunk host.
