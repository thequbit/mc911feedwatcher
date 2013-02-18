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
	
	print "Starting application.";

	# pull from the database a list of all of the incidents to date
	
	print "Connecting to Database and pulling all incidents."

	# get our db info from our local file
        dbcreds = get_mysql_credentials()

        # decode responce
        host = dbcreds[0].rstrip()
        dbname = dbcreds[1].rstrip()
        username = dbcreds[2].rstrip()
        password = dbcreds[3].rstrip()

        # connect to our database
        database = mysql.connect(host=host,user=username,passwd=password,db=dbname)

        # generate query, and get the number of rows returned
        query = 'SELECT DISTINCT itemid FROM incidents'
        database.query(query)
        dbresult=database.store_result()
        #(count,),=dbresult.fetch_row()

	# get all of the incident itemid's from the result
	itemids = []
	for row in dbresult.fetch_row(maxrows=0):
		itemids.append(row[0])

	print "\tRetrieved {0} items".format(len(itemids))

	print "... Done."

	print "Generating list of unique agencies ..."

	agencies = []
	# iterate through and genereate a list of only uniuque agencies
	for itemid in itemids:
		# get short name of agency ( first four leters of the incident id )
		shortname = itemid[0:4]
		
		# see if we have added it already
		if any(shortname is a for a in agencies) == False:
			
			# need to add the new agency to the list of agencies
			print "\tNew Agency Found! Shortname = {0}".format(shortname)

			agencies.append(shortname)

	print "... Done."

	print "Pushing {0} agencies to database ...".format(len(agencies))

	for agency in agencies:
		query = 'INSERT INTO agencies (shortname,longname,description,websiteurl) VALUES("{0}","","","")'.format(agency)
		database.query(query)

	print "... Done."


if __name__ == '__main__': sys.exit(main(sys.argv))
