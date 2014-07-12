import os
from time import strftime
import datetime
import urllib

from xml.dom.minidom import parseString as parse_xml

from omgeo import Geocoder

import transaction

from models import (
    DBSession,
    Users,
    AgencyTypes,
    Agencies,
    IncidentTypes,
    Groups,
    GroupIncidentTypes,
    Statuses,
    Incidents,
    APICalls,
    CurrentIncidents,
    Runs,
)

_geoheader = '<rss version="2.0" xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" xmlns:atom="http://www.w3.org/2005/Atom">'
_geofooter = '</rss>'

def text_from_tag(tag, dom):
    try:
    #if True:
        string = dom.getElementsByTagName(tag)[0].toxml()
        string = string.replace('<{0}>'.format(tag),'').replace('</{0}>'.format(tag),'')
    except:
        string = None
    return string

def geocode_address(short_address):

    # https://pypi.python.org/pypi/python-omgeo

    print "Geocoding '{0}' ...".format(short_address)

    g = Geocoder()

    formed_address = "{0}, NY, USA".format(short_address)

    result = g.geocode(formed_address)
    canidates = [c.__dict__ for c in result["candidates"]]

    #print canidates

    lat = lng = full_address = None
    success = False
    if len(canidates) != 0:
        lat = canidates[0]['x']
        lng = canidates[0]['y']
        full_address = canidates[0]['match_addr']
        success = True    

    return lat,lng,full_address,success

def get_rss_feed_as_dict(url):

    """
    Get the RSS feed from the web, and return a dict of it's contents.
    """

    # download the RSS feed and prase to a list of dom objects
    xml_string = urllib.urlopen(url).read()
    xml_dom = parse_xml(xml_string)
    items = xml_dom.getElementsByTagName('item')

    print "Parsing {0} items ...".format(len(items))

    # process each item
    items_dict_list = []
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
        source_lng = text_from_tag('geo:lng',item_dom)
      
        # parse fields
        incident_text = title.split(' at ')[0]
        short_address =  title.split(' at ')[1]
        incident_datetime = datetime.datetime.strptime(pub_date_time[:-6],'%a, %d %b %Y %X')
        status_text = description.split(',')[0].split('Status:')[1]
        guid = description.split(',')[1].split('ID:')[1]

        #print "title: {0}\nlink: {1}\npub_date_time:{2}\ndescription:{3}\nguid: {4}\nsource_lat: {5}\nsource_lng: {6}\n\n".format(title,link,pub_date_time,description,guid,source_lat,source_lng)
 
        geocode_lat,geocode_lng,full_address,geocode_success = geocode_address(short_address)

        # make dict, and add to list to return
        item_dict = {
            'incident_text': title.split(' at ')[0],
            'short_address': title.split(' at ')[1],
            'incident_datetime': datetime.datetime.strptime(pub_date_time[:-6],'%a, %d %b %Y %X'),
            'status_text': description.split(',')[0].split('Status:')[1],
            'guid': description.split(',')[1].split('ID:')[1],
            'source_lat': source_lat,
            'source_lng': source_lng,
            'geocode_lat': geocode_lat,
            'geocode_lng': geocode_lng,
            'full_address': full_address,
            'geocode_success': geocode_success,
        }
        items_dict_list.append(item_dict)

    return items_dict_list

def push_items(run_id,items):

    new_incidents = False

    for item in items:

        if not Incidents.check_exists(item.guid, item.status_text):

            geocode_lat,geocode_lng,full_address,geocode_success = geocode_address(short_address)

            Incidents.add_incident(
                session = DBSession,
                run_id = run_id,
                status_text = item.status_text,
                short_address = item.short_address,
                guid = item.guid,
                incident_text = item.incident_text,
                incident_datetime = item.incident_datetime,
                source_lat = item.source_lat,
                geocode_lat = geocode_lat,
                geocode_lng = geocode_lng,
                full_address = full_address,
                geocode_success = geocode_success,
            )

            new_incidents = True

    return new_incidents

if __name__ == '__main__':

    print "Downloading RSS feed and parsing ..."

    success = True
    error_text = ''

    run = Runs.new_run(DBSession)

    url = "http://www2.monroecounty.gov/etc/911/rss.php"

    items = get_rss_feed_as_dict(url)

    print "Pushing items to database ..."

    new_incidents = push_items(run_id, items)

    print "Updating run ..."

    Runs.update_run(run, successful, error_text, new_incidents)

    print "Done."
