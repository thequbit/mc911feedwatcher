import sys

import _mysql as mysql

import urllib
import urllib2

import time
import simplejson

import threading

def geocodetor(address):
    proxy = urllib2.ProxyHandler({'http': '127.0.0.1:8118'})
    opener = urllib2.build_opener(proxy)
    urllib2.install_opener(opener)
    vals = {'address': address, 'sensor': 'false'}
    qstr = urllib.urlencode(vals)
    response = simplejson.loads(urllib2.urlopen("http://maps.google.com/maps/api/geocode/json?%s" % qstr).read()) # address=Los+Angeles&sensor=false").read()
    return response

#
# code via Ralph Bean (github.com/ralphbean) from:
#   https://github.com/ralphbean/monroe/blob/master/wsgi/tg2app/tg2app/scrapers/propertyinfo.py
#
#def geocode(address):
#    # TODO -- a more open way of doing this.
#    # Here we have to sleep 1 second to make sure google doesn't scold us.
#    #time.sleep(45)
#    vals = {'address': address, 'sensor': 'false'}
#    qstr = urllib.urlencode(vals)
#    reqstr = "http://maps.google.com/maps/api/geocode/json?%s" % qstr
#    return simplejson.loads(urllib.urlopen(reqstr).read())

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

    #print "[INFO   ] Address decoded: '{0}'".format(fulladdress)

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
    #print "[INFO   ] Checking if '{0}' is in database ...".format(address)

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
    #print "[INFO   ] Pushing Address Data to Database ..."

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
    #print "[INFO   ] Updating Address for Item ID: {0}".format(itemid)

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
    #print "[INFO   ] Getting all addresses from database ..."

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

def dodecode(items):
    for item in items:
        itemid,addr = item
        exists = check_address(addr)
        if exists == False:
            _addr = "{0}, NY".format(addr)
            _json = geocodetor(_addr)
            status = _json['status']
            if status == "ZERO_RESULTS":
                print "[WARNING] No Results for Address `{0}`".format(_addr)
            else:
                while status != "OK":
                    print "[INFO    ] GEO ERROR.  json = {0}".format(_json)
                    time.sleep(1)
                    _json = geocodetor(addr)
                    status = _json['status']
                fulladdress,lat,lng,zipcode,streetnumber,route,locality = pulldata(_json)
                push_address(addr,fulladdress,lat,lng,zipcode,streetnumber,route,locality)
                update_address(fulladdress,lat,lng,zipcode,itemid)
                print "[INFO    ] Address decoded: '{0}'".format(fulladdress)
        else:
            print "[SKIPPED] Address skipped, already in database."

def splitlist(alist, wanted_parts=1):
    length = len(alist)
    return [ alist[i*length // wanted_parts: (i+1)*length // wanted_parts]
             for i in range(wanted_parts) ]

# From http://stackoverflow.com/a/9790882/145400
def jointhreads(threads):
    for t in threads:
        while t.isAlive():
            t.join(5)

def main(argv):
    print "Starting Application.\n"

    items = get_addresses()

    print "[INFO   ] Working on {0} addresses ...".format(len(items))

    tcount = 16
    itemchunks = splitlist(items,tcount)

    threads = []
    for ichunk in itemchunks:
        thr = threading.Thread(target=dodecode,args=[ichunk])
        thr.deamon = True
        thr.start()
        threads.append(thr)

    try:
        jointhreads(threads)
    except KeyboardInterrupt:
        print "\nKeyboardInterrupt caught.  Killing threads."

    print "Exiting Application.\n"

if __name__ == '__main__': sys.exit(main(sys.argv))
