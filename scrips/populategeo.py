import sys

import _mysql as mysql

import urllib

import time
import simplejson


#
# code via Ralph Bean (github.com/ralphbean) from:
#   https://github.com/ralphbean/monroe/blob/master/wsgi/tg2app/tg2app/scrapers/propertyinfo.py
#
def geocode(address):
    # TODO -- a more open way of doing this.
    # Here we have to sleep 1 second to make sure google doesn't scold us.
    #time.sleep(45)
    vals = {'address': address, 'sensor': 'false'}
    qstr = urllib.urlencode(vals)
    reqstr = "http://maps.google.com/maps/api/geocode/json?%s" % qstr
    return simplejson.loads(urllib.urlopen(reqstr).read())

def pulldata(_json):
    fulladdress = _json['results'][0]['formatted_address']
    lat = _json['results'][0]['geometry']['location']['lat']
    lng = _json['results'][0]['geometry']['location']['lng']

    zipcode = ""
    streetnumber = ""
    route = ""
    locality = ""

    for comp in _json['results'][0]['address_components']:
        if comp['types'][0] == "postal_code":
            zipcode = comp['long_name']
            break

    for comp in _json['results'][0]['address_components']:
        if comp['types'][0] == "street_number":
            streetnumber = comp['long_name']
            break

    for comp in _json['results'][0]['address_components']:
        if comp['types'][0] == "route":
            route = comp['long_name']
            break

    for comp in _json['results'][0]['address_components']:
        if comp['types'][0] == "locality":
            locality = comp['long_name']
            break

    print "[INFO   ] Address decoded: '{0}'".format(fulladdress)

    retval = (fulladdress,lat,lng,zipcode,streetnumber,route,locality)
    return retval

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

def check_address(address):
    print "[INFO   ] Checking if '{0}' is in database ...".format(address)

     # get our db info from our local file
    dbcreds = get_mysql_credentials()

    # decode responce
    host = dbcreds[0].rstrip()
    dbname = dbcreds[1].rstrip()
    username = dbcreds[2].rstrip()
    password = dbcreds[3].rstrip()

    # connect to our database
    database = mysql.connect(host=host,user=username,passwd=password,db=dbname)

    query = 'SELECT COUNT(addressid) FROM addresses WHERE rawaddress = "{0}"'.format(address)
    database.query(query)
    dbresult=database.store_result()
    (addresscount,),=dbresult.fetch_row()

    if int(addresscount) == 0:
        exists = False
    else:
        exists = True

    return exists

def push_address(rawaddress,fulladdress,lat,lng,zipcode,streetnumber,route,locality):
    print "[INFO   ] Pushing Address Data to Database ..."

     # get our db info from our local file
    dbcreds = get_mysql_credentials()

    # decode responce
    host = dbcreds[0].rstrip()
    dbname = dbcreds[1].rstrip()
    username = dbcreds[2].rstrip()
    password = dbcreds[3].rstrip()

    # connect to our database
    database = mysql.connect(host=host,user=username,passwd=password,db=dbname)

    query = 'INSERT INTO addresses(rawaddress,fulladdress,lat,lng,zipcode,streetnumber,route,locality) VALUES("{0}","{1}","{2}","{3}","{4}","{5}","{6}","{7}")'.format(rawaddress,fulladdress,lat,lng,zipcode,streetnumber,route,locality)
    database.query(query)

def update_address(fulladdress,lat,lng,zipcode,itemid):
    print "[INFO   ] Updating Address for Item ID: {0}".format(itemid)

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

def get_addresses():
    print "[INFO   ] Getting all addresses from database ..."

    # get our db info from our local file
    dbcreds = get_mysql_credentials()

    # decode responce
    host = dbcreds[0].rstrip()
    dbname = dbcreds[1].rstrip()
    username = dbcreds[2].rstrip()
    password = dbcreds[3].rstrip()

    # connect to our database
    database = mysql.connect(host=host,user=username,passwd=password,db=dbname)

    query = "SELECT DISTINCT itemid, address FROM incidents GROUP BY itemid"
    database.query(query)
    dbresult=database.store_result()

    addresses = []

    for row in dbresult.fetch_row(maxrows=0):
        addresses.append((row[0],row[1]))

    return addresses

def main(argv):
    print "Starting Application.\n"

    items = get_addresses()

    for item in items:
        #print "{0} {1}".format(type(item),item)
        #break
        itemid,addr = item
        exists = check_address(addr)
        if exists == False:
            _json = geocode(addr)
            if _json['status'] == "OK":
                fulladdress,lat,lng,zipcode,streetnumber,route,locality = pulldata(_json)
                push_address(addr,fulladdress,lat,lng,zipcode,streetnumber,route,locality)
                update_address(fulladdress,lat,lng,zipcode,itemid)
                print "[SUCCESS] Address decoded and loaded successfully."
            else:
                print "[WARNING] Address not decoded."
            print "[INFO   ] Waiting 45 seconds so google doesn't deny us ..."
            time.sleep(45)
        else:
            print "[SKIPPED] Address skipped, already in database."

    print "Exiting Application.\n"

if __name__ == '__main__': sys.exit(main(sys.argv))
