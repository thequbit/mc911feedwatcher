import os
from time import strftime
import datetime
import urllib
from time import sleep
import time
import json

from xml.dom.minidom import parseString as parse_xml

from omgeo import Geocoder as Geocoder2

from pygeocoder import Geocoder

import transaction

from sqlalchemy import create_engine

from mcsafetyfeedserver.models import (
    Base,
    DBSession,
    Users,
    AgencyTypes,
    Agencies,
    DispatchTypes,
    Groups,
    GroupDispatchTypes,
    Statuses,
    Incidents,
    IncidentsDispatches,
    Dispatches,
    APICalls,
    CurrentDispatches,
    Runs,
)

engine = create_engine('mysql://mcsafetyfeeduser:password123%%%@lisa.duffnet.local/mcsafetyfeeddb')
DBSession.configure(bind=engine)
Base.metadata.bind = engine

_geoheader = '<rss version="2.0" xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" xmlns:atom="http://www.w3.org/2005/Atom">'
_geofooter = '</rss>'

_geofence = {
  'latmax': 43.5,
  'latmin': 42.5,
  'lngmax': -77.0,
  'lngmin': -78.5,
}

def text_from_tag(tag, dom):
    try:
    #if True:
        string = dom.getElementsByTagName(tag)[0].toxml()
        string = string.replace('<{0}>'.format(tag),'').replace('</{0}>'.format(tag),'').strip()
    except Exception, ex:
        string = None
    return string

def geocode_address_old(short_address):

    # https://pypi.python.org/pypi/python-omgeo

    g = Geocoder2([['omgeo.services.MapQuest', {'settings': {'api_key': 'Fmjtd%7Cluurn96rl9%2Cbl%3Do5-9w80gr'}}]])

    formed_address = "{0}, new york".format(short_address.lower())

    formed_address = formed_address.replace('/w ',' and west ').replace('/e ',' and east ').replace('/n ',' and north ').replace('/s ', ' and south ')
    formed_address = formed_address.replace('/', ' and ')

    print "Geocoding '{0}' ...".format(formed_address)

    result = g.geocode(formed_address)

    print result

    canidates = [c.__dict__ for c in result["candidates"]]

    lat = lng = full_address = None
    success = False
    if len(canidates) != 0:
        lat = canidates[0]['y']
        lng = canidates[0]['x']
        full_address = canidates[0]['match_addr']
        success = True    

    return lat,lng,full_address,success

def geocode_address(short_address):

    # https://pypi.python.org/pypi/python-omgeo

    formed_address = "{0}, new york".format(short_address.lower())

    #formed_address = formed_address.replace('/w ',' and west ').replace('/e ',' and east ').replace('/n ',' and north ').replace('/s ', ' and south ')
    formed_address = formed_address.replace('/', ' and ')
    formed_address = formed_address.replace('nb ','').replace('sb ','')

    print "Geocoding '{0}' ...".format(formed_address)

    results = Geocoder.geocode(formed_address)

    #print results

    lat = results[0].coordinates[0]
    lng = results[0].coordinates[1]

    full_address = str(results[0])



    success = results.valid_address

    sleep(.5)

    return lat,lng,full_address,success


def process_rss_feed(run_id,url):

    """
    Get the RSS feed from the web, and return a dict of it's contents.
    """

    successful = True

    # download the RSS feed and prase to a list of dom objects
    xml_string = urllib.urlopen(url).read()
    xml_dom = parse_xml(xml_string)
    items = xml_dom.getElementsByTagName('item')

    print "Parsing {0} items ...".format(len(items))

    # process each item
    new_dispatch_guids = []
    new_dispatch_count = 0
    for item in items:
        
        # pre-process xml 
        xml = item.toxml()
        xml = xml.replace('<item>','').replace('</item>','')
        xml = xml.replace('\t','').replace('\n\n','\n').replace('\r','')
        xml = "{0}\n{1}\n{2}".format(_geoheader,xml,_geofooter)

        # conver to dom object for querying
        item_dom = parse_xml(xml)

        # generate fields that will be used to result dict
        title = text_from_tag('title',item_dom)
        link = text_from_tag('link',item_dom)
        pub_date_time = text_from_tag('pubDate',item_dom) # .replace('-','-0') 
        description = text_from_tag('description',item_dom)
        source_lat = text_from_tag('geo:lat',item_dom)
        source_lng = text_from_tag('geo:long',item_dom)
      
        # parse fields
        dispatch_text = title.split(' at ')[0].strip()
        short_address =  title.split(' at ')[1].strip()
        dispatch_datetime = datetime.datetime.strptime(pub_date_time[:-6],'%a, %d %b %Y %X')
        status_text = description.split(',')[0].split('Status:')[1].strip()
        guid = description.split(',')[1].split('ID:')[1].strip()

        #print "Working on: [{0}] : {2} : '{1}'".format(guid, title, status_text)

        # add guid to list of new guids seen
        new_dispatch_guids.append(guid)

        # see if the dispatch already exists within the database, and if it doesn't add it
        exists = Dispatches.check_exists(DBSession, guid, status_text)
        if not exists:

            print "GUID: {0}, Status: {1} - does not exist within database, adding.".format(guid, status_text)

            geocode_lat,geocode_lng,full_address,geocode_successful = geocode_address(short_address)
            #geocode_lat = 0; geocode_lng = 0; full_address = ''; geocode_successful = False 

            # need to check to make sure that we geo-coded correctly.  This is a sanity check
            # to make sure we are within monroe county.
            if geocode_successful == True \
                    and (geocode_lat > _geofence['latmax'] \
                    or geocode_lat < _geofence['latmin'] \
                    or geocode_lat < _geofence['lngmin'] \
                    or geocode_lng > _geofence['lngmax']):
                #geocode_lat = 0; geocode_lng = 0; full_address = '';
                geocode_successful = False;

            # create the dispatch in the database
            dispatch = Dispatches.add_dispatch(
                session = DBSession,
                run_id = run_id,
                status_text = status_text,
                short_address = short_address,
                guid = guid,
                dispatch_text = dispatch_text,
                dispatch_datetime = dispatch_datetime,
                source_lat = source_lat,
                source_lng = source_lng,
                geocode_lat = geocode_lat,
                geocode_lng = geocode_lng,
                full_address = full_address,
                geocode_successful = geocode_successful,
            )

            # inc our count of new dispatches
            new_dispatch_count += 1

    # get list of current dispatches (not closed)
    current_dispatch_guids = CurrentDispatches.get_current_dispatch_guids(DBSession)

    #print "Current Dispatches:"
    #for g in current_dispatch_guids:
    #    print "\t%s" % g

    # keep track of the guis we have just removed so we don't re-add them
    removed_guids = []

    # keep track of the number of dispatches that we close
    closed_dispatch_count = 0

    # check to see if there are any guis that are in the current dispatches list
    # but are not within the RSS feed, and close them
    for current_dispatch_guid in current_dispatch_guids:
        if not current_dispatch_guid in new_dispatch_guids:
            CurrentDispatches.remove_current_dispatch(
                session = DBSession,
                guid = current_dispatch_guid,
            )
            Dispatches.close_dispatch(
                session = DBSession,
                run_id = run_id,
                guid = current_dispatch_guid,
            )
            removed_guids.append(current_dispatch_guid)
            closed_dispatch_count += 1
            print "Removed '{0}' from the current dispatch list".format(current_dispatch_guid)

    # see if there are any new dispatches that need to be added to the current 
    # dispatches list, and add them.
    for new_dispatch_guid in new_dispatch_guids:
        if not new_dispatch_guid in current_dispatch_guids and \
                not new_dispatch_guid in removed_guids:
            CurrentDispatches.add_current_dispatch(
                session = DBSession,
                guid = new_dispatch_guid,
            )
            print "Added '{0}' to the current dispatch list".format(new_dispatch_guid)

    return new_dispatch_count, closed_dispatch_count, successful

if __name__ == '__main__':

    error_text = ''
    url = "http://www2.monroecounty.gov/etc/911/rss.php"

    if True:
    #while(True):

        print "Attempting to process RSS feed ..."

        run = Runs.new_run(DBSession)
 
        #start_time = time.time()
        new_dispatch_count, closed_dispatch_count, successful = process_rss_feed(run.id,url)
        #time_taken = time.time() - start_time

        print "Successfull added {0} new dispatches and closed {1} existing dispatches.".format(new_dispatch_count, closed_dispatch_count)

        new_dispatches = False
        if new_dispatch_count > 0:
            new_dispatches = True
        Runs.update_run(DBSession, run, successful, error_text, new_dispatches)

        #rest_time = 30

        # see if we need to wait
        #wait_time = rest_time - time_taken
        #if wait_time > 0:
        #    print "Waiting {0} seconds ...".format(wait_time)
        #    sleep(wait_time)
