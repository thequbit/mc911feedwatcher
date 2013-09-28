import sys
import time

import _mysql as mysql

import simplejson

import urllib2
import urllib
from xml.dom.minidom import parseString

import datetime

_xmlsourceurl = "http://www2.monroecounty.gov/etc/911/rss.php"
_geoheader = '<rss version="2.0" xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" xmlns:atom="http://www.w3.org/2005/Atom">'
_geofooter = '</rss>'

#
# code via Ralph Bean (github.com/ralphbean) from:
#   https://github.com/ralphbean/monroe/blob/master/wsgi/tg2app/tg2app/scrapers/propertyinfo.py
#
def geocode(address):
    # TODO -- a more open way of doing this.
    # Here we have to sleep 1 second to make sure google doesn't scold us.
    time.sleep(2)
    vals = {'address': address, 'sensor': 'false'}
    qstr = urllib.urlencode(vals)
    reqstr = "http://maps.google.com/maps/api/geocode/json?%s" % qstr
    return simplejson.loads(urllib.urlopen(reqstr).read())

def pulldata(_json):
    fulladdress = _json['results'][0]['formatted_address']
    lat = _json['results'][0]['geometry']['location']['lat']
    lng = _json['results'][0]['geometry']['location']['lng']
    zipcode = ""

    for comp in _json['results'][0]['address_components']:
        if comp['types'][0] == "postal_code":
            zipcode = comp['long_name']
            break

    print "Address decoded: '{0}'".format(fulladdress)

    retval = (fulladdress,lat,lng,zipcode)
    return retval

def decode_month(month):

	return {
		"jan": 1,
		"feb": 2,
		"mar": 3,
		"apr": 4,
		"may": 5,
		"jun": 6,
		"jul": 7,
		"aug": 8,
		"sep": 9,
		"oct": 10,
		"nov": 11,
		"dec": 12,
	}[month.lower()]

def remove_xml_tags(string):
	
	# remove tags from string
	string = string.replace('<item>','')
        string = string.replace('</item>','')
	string = string.replace('<title>','')
	string = string.replace('</title>','')
	string = string.replace('<link>','')
        string = string.replace('</link>','')
	string = string.replace('<pubDate>','')
        string = string.replace('</pubDate>','')
	string = string.replace('<description>','')
        string = string.replace('</description>','')
	string = string.replace('<guid>','')
        string = string.replace('</guid>','')

	return string

def get_xml_file():
	
	print "Getting XML file from monroecounty.gov ..."

	# pull the file from the web
	file = urllib2.urlopen(_xmlsourceurl)
	
	# read the file contents
	data = file.read()

        print data
	
	# close the stream
	file.close()

	print "... Done"

	# return the text string
	return data

def get_mysql_credentials():
        # read in credentials file
        lines = tuple(open('mysqlcreds.txt', 'r'))

        # return the tuple of the lines in the file
        #
        # host
        # dbname
        # username
        # password
        #
        return lines

def update_current_incidents(incidentids):

	print "\tUpdating the list of currently open incidents ..."

	# now we need to do two things:
        # 1: if an incidentid in the passed in list isn't in the currentincidents table we need to add it
        # 2: if an incidentid in the currentincidents table is not in the passed in list, then we need to remove it
        #    and add an entree into the incidents table with status CLOSED

	# get our db info from our local file
        dbcreds = get_mysql_credentials()

        # decode responce
        host = dbcreds[0].rstrip()
        dbname = dbcreds[1].rstrip()
        username = dbcreds[2].rstrip()
        password = dbcreds[3].rstrip()

        # connect to our database
        database = mysql.connect(host=host,user=username,passwd=password,db=dbname)

	# get the list of current incidentid's
	query = 'SELECT incidentid FROM currentincidents'
	database.query(query)
	dbresult=database.store_result()
        #data=dbresult.fetchall()
	
	print "\t\tAdding any new incidentsid's to the current list ..."

	# create a list of the currentids
	curids = []
	#while True:
	#	row = dbresult.fetch_row()
	#	curids.append(row[0])

	for row in dbresult.fetch_row(maxrows=0):
		curids.append(row[0])
		#print "\t\t\tID = {0}".format(row[0])

	# go through our scraped ids, and test to see if they are in the current id's table
	for incidentid in incidentids:

		# test to see if the scraped incidentid is in the currentid table
		if any(incidentid in s for s in curids) == False:
			print "\t\t\tNew ID! Adding id = {0}".format(incidentid)
			
			# we didn't find the id in the list, so we need to add it to the current list
			query = 'INSERT INTO currentincidents (incidentid) VALUES("{0}")'.format(incidentid)
			database.query(query)
		else:
			#nothing to do, it's already in the list
			print "\t\t\tID = {0} already in current incident list.".format(incidentid)	

	# go through our currentids and if there is one that doesn't exist within our scraped id's, we need to
	# remove it, and then add a status in the table of incidents
	#
	# note: this list of currnet id's was grabed from the database before we inserted the
	# new id's, so there is no concerns about stale data.
	for cid in curids:
		
		if any(cid in s for s in incidentids) == False:
			print "\t\t\tIncident not found, closing."

			# removing the incidentid from the current table since it is no longer there
			query = 'DELETE FROM currentincidents WHERE incidentid="{0}"'.format(cid)
			database.query(query)

			# add CLOSING status to the incidents table
			query = 'INSERT INTO incidents (event, address, pubdate, pubtime, status, itemid, scrapedatetime) VALUES("","","","","CLOSED","{0}","{1}"'.format(cid,currenttime.strftime("%Y-%m-%d %H:%M"))
			database.query(query)

	print "\t\t... Done"

	print "\t... Done"

def update_address(fulladdress,lat,lng,zipcode,itemid):
        print "\tPushing Decoded Address to Database ..."

	# get our db info from our local file
        dbcreds = get_mysql_credentials()

        # decode responce
        host = dbcreds[0].rstrip()
        dbname = dbcreds[1].rstrip()
        username = dbcreds[2].rstrip()
        password = dbcreds[3].rstrip()

        # connect to our database
        database = mysql.connect(host=host,user=username,passwd=password,db=dbname)
	
        # update all entries with the itemid with the address information
        query = 'UPDATE incidents SET fulladdress = "{0}", lat = "{1}", lng = "{2}", zipcode = "{3}" WHERE itemid = "{4}"'.format(fulladdress,lat,lng,zipcode,itemid)
        database.query(query)

	print "\tDone."

def push_to_database(event, address, pubdate, pubtime, status, itemid):

	print "\tPushing Indident to Database ..."

	# get our db info from our local file
        dbcreds = get_mysql_credentials()

        # decode responce
        host = dbcreds[0].rstrip()
        dbname = dbcreds[1].rstrip()
        username = dbcreds[2].rstrip()
        password = dbcreds[3].rstrip()

        # connect to our database
        database = mysql.connect(host=host,user=username,passwd=password,db=dbname)

	# get the current time to record as the date and time of the scaping
	currenttime = datetime.datetime.now()

	#
	# we need to check to see if we have already placed this item into the DB
	#

	# generate query, and get the number of rows returned
        print "\t\tChecking for item '{0}' ...".format(itemid)
	query = 'SELECT count(*) FROM incidents WHERE itemid="{0}" and status="{1}"'.format(itemid,status)
	database.query(query)
	dbresult=database.store_result()
        (count,),=dbresult.fetch_row()
	
	#
	# if the number of rows returned is zero, insert the item into the db, else do nothing.
	#
	if count == "0":
		
		print "\t\tDecoding Agency ID from 4 leter code."

		# pull off first four letters from itemid, this is the agency shortname
		agencyShortName = itemid[0:4]

		# decode the agency
		query = 'SELECT agencyid FROM agencies WHERE shortname = "{0}"'.format(agencyShortName)
		database.query(query)
		dbresult=database.store_result()
		(agencyID,), = dbresult.fetch_row()

		print "\t\tInserting into database - item not yet in database."

		# generate query
		query = 'INSERT INTO incidents (event, address, pubdate, pubtime, status, itemid, scrapedatetime, agencyid) VALUES("{0}","{1}","{2}","{3}","{4}","{5}","{6}",{7})'.format(event, address, pubdate, pubtime, status, itemid, currenttime.strftime("%Y-%m-%d %H:%M"), agencyID)

		# execute query
		database.query(query)

		# see if we can decode our address
		_json = geocode(address)

		# see if we were successful
		if _json['status'] == 'OK':
			fulladdress,lat,lng,zipcode = pulldata(_json)
			update_address(fulladdress,lat,lng,zipcode,itemid)
		else:
			print "[WARNING] Address could not be decoded!" 

	else:	
		print "\t\tSkipping Item - item already in database."

	#
	# EVENT
	#

	# we need to see if the event type already exists in our list of event types
	query = 'SELECT count(*) FROM eventtypes where eventtype="{0}"'.format(event.lower())
	database.query(query)
	dbresult=database.store_result()
        (eventcount,),=dbresult.fetch_row()
		
	# test to see if we have already recorded this type
	if eventcount == "0":
		print '\t\tAdding new event to DB: "{0}"'.format(event.lower());
			
		# add the event type to the list of event types
		query = 'INSERT INTO eventtypes (eventtype) VALUES("{0}")'.format(event.lower());
		database.query(query)

	#
	# STATUS
	#

	# we need to see if the status type already exists in our list of event types
        query = 'SELECT count(*) FROM statustypes where statustype="{0}"'.format(status)
        database.query(query)
        dbresult=database.store_result()
        (statuscount,),=dbresult.fetch_row()

        # test to see if we have already recorded this type
        if statuscount == "0":
                print '\t\tAdding new status to DB: "{0}"'.format(status);

                # add the event type to the list of event types
                query = 'INSERT INTO statustypes (statustype) VALUES("{0}")'.format(status);
                database.query(query)


	print "\t... Done"

def parse_xml_file(data):

	print "\tParsing XML stream ..."

	# parse the xml file
	dom = parseString(data)



	print "\t... Done"

	# return the dom structure
	return dom

def push_success(success, successtext):

	currenttime = datetime.datetime.now()

	# get our db info from our local file
        dbcreds = get_mysql_credentials()

        # decode responce
        host = dbcreds[0].rstrip()
        dbname = dbcreds[1].rstrip()
        username = dbcreds[2].rstrip()
        password = dbcreds[3].rstrip()

        # connect to our database
        database = mysql.connect(host=host,user=username,passwd=password,db=dbname)

	if success:
		success = "1"
	else:
		success = "0"

	# enter the run into the database
	query = 'INSERT INTO runs (runsuccess,errtext,rundatetime) VALUES("{0}","{1}","{2}")'.format(success, successtext,currenttime.strftime("%Y-%m-%d %H:%M"))
	database.query(query)

	print "Pushing success to database."

def main(argv):

	print "Scraper Launched."

	success = True
	successtext = "";

	try:

		# we first need to pull the latest xml file
		xmlString = get_xml_file()

		# parse the xml file
		xmldom = parse_xml_file(xmlString)

		# pull the item array
		items = xmldom.getElementsByTagName('item')

		# create an array to place all of our incidentid's in
		incidentids = []

		# iterate through the items in the xml array
		for item in items:

			# get the xml from the item
			xml = item.toxml()

			# prettify the xml
			xml = xml.replace('\t','').replace('\n\n','\n')
			xml = "{0}\n{1}\n{2}".format(_geoheader,xml,_geofooter)

			# parse the xml into a dom
			itemdom = parseString(xml)

			# parse contents out of each tag
			title = remove_xml_tags(itemdom.getElementsByTagName('title')[0].toxml())
			pubdatetime = remove_xml_tags(itemdom.getElementsByTagName('pubDate')[0].toxml())
			description = remove_xml_tags(itemdom.getElementsByTagName('description')[0].toxml())

			# further parse strings
			event = title.split(' at ')[0]
			address = title.split(' at ')[1]
			pubdate = pubdatetime.split(', ')[1][0:11]
			pubtime = pubdatetime.split(', ')[1][12:20]
			status = description.split(', ')[0][8:]
			itemid = description.split(', ')[1][4:]

			# decode date to make it 'mysql friendly'
			pubdate = "{0}-{1}-{2}".format(pubdate.split(' ')[2],decode_month(pubdate.split(' ')[1]),pubdate.split(' ')[0])

			#print "Item:"
			#print '\tTitle: "%s"' % title
			#print '\tEvent: "%s"' % event
			#print '\tAddress: "%s"' % address
			#print '\tPublication DateTime: "%s"' % pubdatetime
			#print '\tPublication Date: "%s"' % pubdate
			#print '\tPublication Time: "%s"' % pubtime
			#print '\tDescription: "%s"' % description
			#print '\tStatus: "%s"' % status
			#print '\tItem ID: "%s"' % itemid
	
			# push results to database
			push_to_database(event, address, pubdate, pubtime, status, itemid)
	
			# see if we can decode our address
			#_json = geocode(address)
	
			# see if we were successful
			#if _json['status'] == 'OK':
			#	fulladdress,lat,lng,zipcode = pulldata(_json)
			#	update_address(fulladdress,lat,lng,zipcode,itemid)
			#else:
			#	print "[WARNING] Address could not decoded!"

			# add the itemid to the list of incidentids
			incidentids.append(itemid)


	except:
		print "ERROR!"
		success = False
		sucesstext = ""

	# TODO: fix this ... still not working correctly.

	# update the list of current incidents
	
#update_current_incidents(incidentids) 

	# push success to db
	push_success(success,successtext)

	print "Scraper Finished."

if __name__ == '__main__': sys.exit(main(sys.argv))



