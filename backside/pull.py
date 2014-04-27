#!/usr/bin/python
import sys
from pymongo import MongoClient
import requests
import xml.etree.ElementTree as ET

client = MongoClient()
apikeys = client.braveskunk.apikeys.find()

# List of All Alliances
Alliances = {}
# List of Alliances found in mails
Allies = {}
# List of Corps found in mails
Corporations = {}
# List of Characters found in mails
Characters = {}
# List of Mailing Lists from characters
MailingLists = {}
mails = []


##### Grab data from DB *****
#
# Obtain Corporation IDs and names from MongoDB
def GetAlliesFromDB():
	global Allies
	if( client.braveskunk.mailallies.count() > 0 ):
		rows = client.braveskunk.mailallies.find()
		for row in rows:
			Allies[row["id"]] = { "name": row["name"], "ticker": row["ticker"] }

def GetCorpsFromDB():
	global Corporations
	if( client.braveskunk.corporations.count() > 0 ):
		rows = client.braveskunk.corporations.find()
		for row in rows:
			Corporations[row["id"]] = { "name": row["name"], "ticker": row["ticker"], "parentID": row["parentID"] }

# Obtain Character IDs and names from MongoDB
def GetCharsFromDB():
	global Characters
	if( client.braveskunk.characters.count() > 0 ):
		rows = client.braveskunk.characters.find()
		for row in rows:
			Characters[row["id"]] = { "name": row["name"], "parentID": row["parentID"] }

# Obtain Mailing List IDs and names from MongoDB
def GetMailListsFromDB():
	global MailingLists
	if( client.braveskunk.maillists.count() > 0 ):
		rows = client.braveskunk.maillists.find()
		for row in rows:
			MailingLists[row["id"]] = row["name"]

##### Grab data from Eve API *****
#
# Obtain Alliance info from Eve API
def GetAlliances():
	global Alliances
	r = requests.get( "https://api.eveonline.com/eve/AllianceList.xml.aspx" )
	allyxml = ET.fromstring( r.content )
	allylist = allyxml.findall( ".//*[@allianceID]" )
	for ally in allylist:
		id = int( ally.attrib["allianceID"] )
		name = ally.attrib["name"]
		ticker = ally.attrib["shortName"]
		Alliances[id] = { "name": name, "ticker": ticker }

# Obtain Mailing List name from Eve API
def GetMailingLists( reqStrings ):
	global MailingLists
	for key in reqStrings:
		r = requests.get( "https://api.eveonline.com/char/mailinglists.xml.aspx?keyID=" + key["keyid"] + "&vCode=" + key["vCode"] + "&characterID=" + key["charID"] )
		results = ET.fromstring( r.content )
		for row in results.iter( "row" ):
			id = int( row.attrib["listID"] )
			name = row.attrib["displayName"]
			MailingLists[id] = name

# Takes API keyID and vCodes and discovers associated character IDs for future API queries
def GetRequestStrings( apikeys ):
	reqStrings = []
	for key in apikeys:
		r = requests.get( "https://api.eveonline.com/account/APIKeyInfo.xml.aspx?keyID=" + key["keyid"] + "&vCode=" + key["vCode"] )
		results = ET.fromstring( r.content )
		for character in results.iter( "row" ):
			reqStrings.append( dict( keyid=key["keyid"], vCode=key["vCode"], charID=character.attrib["characterID"] ) )
	return reqStrings

# Obtains Corporation info from the Eve API
def FetchCorpData( id ):
	global Corporations

	r = requests.get( "https://api.eveonline.com/corp/CorporationSheet.xml.aspx?corporationID=" + str( id ) )
	results = ET.fromstring( r.content )

	name = results.findall( ".//corporationName" )[0].text
	ticker = results.findall( ".//ticker" )[0].text
	test = results.findall( ".//allianceID" )
	if( test ):
		parentID = test[0].text
	else:
		parentID = "None"
	Corporations[id] = { "name": name, "ticker": ticker, "parentID": parentID }

# Obtains Character info from the Eve API
def FetchCharData( id ):
	global Characters

	r = requests.get( "https://api.eveonline.com/eve/CharacterInfo.xml.aspx?characterID=" + str( id ) )
	results = ET.fromstring( r.content )

	name = results.findall( ".//characterName" )[0].text
	test = results.findall( ".//allianceID" )
	if( test ):
		parentID = test[0].text
	else:
		parentID = results.findall( ".//corporationID" )[0].text
	Characters[id] = { "name": name, "parentID": parentID }

# Obtain mails from Eve API using previously built reqStrings, it then processes them, adding data to global dicts as needed
def GetMails( reqStrings ):
	global Allies
	global Alliances
	global mails
	apimails = []
	apibodies = []

	for key in reqStrings:
		r = requests.get( "https://api.eveonline.com/char/MailMessages.xml.aspx?keyID=" + key["keyid"] + "&vCode=" + key["vCode"] + "&characterID=" + key["charID"] )
		apimails = ET.fromstring( r.content )

		messages = []
		for message in apimails.iter( "row" ):
			messages.append( message.attrib )

		msglist = ""
		for i in range( 0, len( messages ) ):
			if( msglist == "" ):
				msglist = messages[i]["messageID"]
			else:
				msglist = msglist + "," + messages[i]["messageID"]

		r = requests.get( "https://api.eveonline.com/char/MailBodies.xml.aspx?keyID=" + key["keyid"] + "&vCode=" + key["vCode"] + "&characterID=" + key["charID"] + "&ids=" + msglist )
		apibodies = ET.fromstring( r.content )

		for message in messages:
			# Translate ID strings to ints
			message["senderID"] = int( message["senderID"] )
			if( message["toCorpOrAllianceID"] != "" ):
				message["toCorpOrAllianceID"] = int( message["toCorpOrAllianceID"] )

			# Add data to global dicts if it doesn't exist

			# Character from senderID
			if( message["senderID"] not in Characters ):
				FetchCharData( message["senderID"] )

			# Corporation from toCorpOrAllianceID
			if( ( message["toCorpOrAllianceID"] != "" ) and ( message["toCorpOrAllianceID"] not in Alliances ) and ( message["toCorpOrAllianceID"] not in Corporations ) ):
				FetchCorpData( message["toCorpOrAllianceID"] )

			# Character(s) from toCharacterIDs
			if( message["toCharacterIDs"] != "" ):
				if( message["toCharacterIDs"].find( "," ) == -1 ):
					message["toCharacterIDs"] = int( message["toCharacterIDs"] )
					if( message["toCharacterIDs"] not in Characters ):
						FetchCharData( message["toCharacterIDs"] )
				else:
					list = message["toCharacterIDs"].split( "," )
					for entry in list:
						entry = int( entry )
						if( entry not in Characters ):
							FetchCharData( entry )

			# Start building the mail dict
			sendChar = message["senderID"]

			if( message["toCorpOrAllianceID"] != "" ):
				rcvr = message["toCorpOrAllianceID"]
			elif( message["toCharacterIDs"] != "" ):
				rcvr = message["toCharacterIDs"]
			elif( message["toListID"] != "" ):
				rcvr = message["toListID"]
			else:
				rcvr = "Nobody"

			# Determine if this mail's destination is an Alliance and store it
			if( rcvr in Alliances and rcvr not in Allies ):
				Allies[rcvr] = { "name": Alliances[rcvr]["name"], "ticker": Alliances[rcvr]["ticker"] }

			bodytext = apibodies.findall( ".//*[@messageID=\'" + message["messageID"] + "\']" )[0].text
			mails.append( dict( id=message["messageID"], sender=sendChar, date=message["sentDate"], receiver=rcvr, title=message["title"], body=bodytext ) )

##### Add data to DB #####
#
# Adds all interesting information to DB for future usage.
def AddToDB():
	global Alliances
	global Allies
	global Corporations
	global Characters
	global MailingLists
	global mails

	client.braveskunk.alliances.drop()
	for key, value in Alliances.items():
		row = { "id": key, "name": value["name"], "ticker": value["ticker"] }
		insert_id = client.braveskunk.alliances.insert( row )

	for key, value in Allies.items():
		row = { "id": key, "name": value["name"], "ticker": value["ticker"] }
		insert_id = client.braveskunk.mailallies.insert( row )

	for key, value in Corporations.items():
		row = { "id": key, "name": value["name"], "ticker": value["ticker"], "parentID": value["parentID"] }
		test = client.braveskunk.corporations.find_one( row )
		if( test == None ):
			insert_id = client.braveskunk.corporations.insert( row )

	for key, value in Characters.items():
		row = { "id": key, "name": value["name"], "parentID": value["parentID"] }
		test = client.braveskunk.characters.find_one( row )
		if( test == None ):
			insert_id = client.braveskunk.characters.insert( row )

	for key, value in MailingLists.items():
		row = { "id": key, "name": value }
		test = client.braveskunk.maillists.find_one( row )
		if( test == None ):
			insert_id = client.braveskunk.maillists.insert( row )

	for mail in mails:
		test = client.braveskunk.mails.find_one( mail )
		if( test == None ):
			insert_id = client.braveskunk.mails.insert( mail )

def main():
	GetAlliances()
	GetCorpsFromDB()
	GetCharsFromDB()
	GetMailListsFromDB()

	reqStrings = GetRequestStrings( apikeys )
	GetMailingLists( reqStrings )
	GetMails( reqStrings )
	AddToDB()

if __name__ == "__main__":
	sys.exit( main() )
