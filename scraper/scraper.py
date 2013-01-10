import sys
import _mysql as mysql

import urllib2
from xml.dom.minidom import parseString

import datetime

_xmlsourceurl = "http://www.monroecounty.gov/etc/911/rss.php"
_geoheader = '<rss version="2.0" xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" xmlns:atom="http://www.w3.org/2005/Atom">'
_geofooter = '</rss>'

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
		"spt": 9,
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
	query = 'SELECT count(*) FROM incidents WHERE itemid="{0}" and status="{1}"'.format(itemid,status)
	database.query(query)
	dbresult=database.store_result()
        (count,),=dbresult.fetch_row()
	
	#
	# if the number of rows returned is zero, insert the item into the db, else do nothing.
	#
	if count == "0":
		
		print "\t\tInterting into database - item not yet in database."

		# generate query
		query = 'INSERT INTO incidents (event, address, pubdate, pubtime, status, itemid, scrapedatetime) VALUES("{0}","{1}","{2}","{3}","{4}","{5}","{6}")'.format(event, address, pubdate, pubtime, status, itemid, currenttime.strftime("%Y-%m-%d %H:%M"))

		# execute query
		database.query(query)
	
	else:
		# nothing to do here

		print "\t\tSkipping Item - item already in database."

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

	try:

		# we first need to pull the latest xml file
		xmlString = get_xml_file()

		# parse the xml file
		xmldom = parse_xml_file(xmlString)

		# pull the item array
		items = xmldom.getElementsByTagName('item')

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
		
			success = True
			successtext = ""

	except:
		print "ERROR!"
		success = False
		sucesstext = ""

	# push success to db
	push_success(success,successtext)

	print "Scraper Finished."

if __name__ == '__main__': sys.exit(main(sys.argv))


