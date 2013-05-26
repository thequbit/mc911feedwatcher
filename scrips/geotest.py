import sys
import time
import urllib
import simplejson
import MySQLdb as mdb
import _mysql as mysql

#
# code via Ralph Bean (github.com/ralphbean) from:
#   https://github.com/ralphbean/monroe/blob/master/wsgi/tg2app/tg2app/scrapers/propertyinfo.py 
#
def geocode(address):
    # TODO -- a more open way of doing this.
    # Here we have to sleep 1 second to make sure google doesn't scold us.
    time.sleep(1)
    vals = {'address': address, 'sensor': 'false'}
    qstr = urllib.urlencode(vals)
    reqstr = "http://maps.google.com/maps/api/geocode/json?%s" % qstr
    return simplejson.loads(urllib.urlopen(reqstr).read())

def get_addresses(date):
    
    # fill in database stuff here
    host = ""
    username = ""
    password = ""
    database = ""

    # connect to the database
    con = mdb.connect(host=host, user=username, passwd=password, db=database)

    # via cursor, pull all addresses for the date
    with con:
        cur = con.cursor()
        cur.execute("SELECT DISTINCT itemid, address FROM incidents WHERE DATE(scrapedatetime) = \"{0}\"".format(date))
        rows = cur.fetchall()
        cur.close()

    # pull out the addresses
    _addresses = []
    for row in rows:
        itemid,address = row
        _addresses.append(address)
    
    return _addresses

def main(argv):
    
    print "Start.\n\n"

    addresses = get_addresses("2013-5-25")

    print "Processing {0} addresses ...".format(len(addresses))

    success = 0

    # geo decode all the addresses and count the success rate
    for address in addresses:
        print "processing '{0}' ...".format(address)
        _json = geocode(address)
        if _json['status'] == "OK":
           success += 1
        #break

    print "{0} out of {1} addresses decoded successfully.".format(success,len(addresses))

    print "\n\nDone."

if __name__ == '__main__': sys.exit(main(sys.argv))
