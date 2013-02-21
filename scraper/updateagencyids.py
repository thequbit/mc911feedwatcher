import sys
import _mysql as mysql

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


def main(argv):

	print "Application Started."

	print "Connecting to Database ..."

	# get our db info from our local file
        dbcreds = get_mysql_credentials()

        # decode responce
        host = dbcreds[0].rstrip()
        dbname = dbcreds[1].rstrip()
        username = dbcreds[2].rstrip()
        password = dbcreds[3].rstrip()

        # connect to our database
        database = mysql.connect(host=host,user=username,passwd=password,db=dbname)


	print "Done!"

	print "Pulling All Incidents itemdi's From the Database ..."

	query = 'SELECT itemid FROM incidents'
	database.query(query)
	dbresult=database.store_result()
        (rowData,), = dbresult.fetch_row()

	print type(rowData)
	#print rowData

	print "Done!"

	print "Iterating Through List and Updating AgencyID's ..."

	itemCount = 0

	# loop through the rows
	while True:
	#if True:
		# get the itemid
		(itemID,), = dbresult.fetch_row()
		
		#print "ItemID = {0}".format(itemID)

		# see if we are done
		if not itemID:
			print "\tDone with list, {0} items processed".format(itemCount)
			break

		agencyShortName = itemID[0:4]

		query = 'SELECT agencyid FROM agencies WHERE shortname = "{0}"'.format(agencyShortName)
		database.query(query)
        	agencyResult=database.store_result()
	        (agencyID,), = agencyResult.fetch_row()

		#print "Shortname '{0}' decoded as AgencyID '{1}'".format(agencyShortName,agencyID)

		query = 'UPDATE incidents SET agencyid = "{0}" WHERE itemid = "{1}"'.format(agencyID, itemID)
		database.query(query)

		#print "ItemID '{0}' updated with AgencyID '{1}'".format(itemID, agencyID)

		itemCount += 1

		if itemCount % 400 == 0:
			print "{0}%".format((float(itemCount) / float(39000.0)) * float(100))

	print "Done!"

	print "Application Closing."

if __name__ == '__main__': sys.exit(main(sys.argv))
