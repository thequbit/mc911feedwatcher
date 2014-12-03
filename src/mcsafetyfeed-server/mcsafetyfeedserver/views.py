
import os
import json
import datetime

from pyramid.response import Response
from pyramid.view import view_config

from sqlalchemy.exc import DBAPIError

from .models import (
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


system_status = {
    'launch_time': str(datetime.datetime.now()),
    'alive': True
}

#@view_config(route_name='home')
#def home(request):
#
#    return Response('<html><body>hi.</body></html>')

@view_config(route_name='home', renderer='templates/index.mak')
def home(request):

    return {}

@view_config(route_name='feed', renderer='templates/feed.mak')
def feed(request):

    if True:

        start = 0
        try:
            start = int(request.GET['start'])
        except:
            pass

        count = 100
        try:
            count = int(request.GET['count'])
        except:
            pass

        dispatches = Dispatches.get_by_date(
            session = DBSession,
            target_datetime = datetime.datetime.now(),
            start = start,
            count = count,
        )

        ret_dispatches = []
        for short_address, guid, dispatch_datetime, source_lat, source_lng, \
                geocode_lat, geocode_lng, geocode_successful, status_text, \
                status_description, agency_name, agency_description, \
                agency_website, dispatch_id, dispatch_text, \
                dispatch_description in dispatches:
            ret_dispatches.append({
                'short_address': short_address,
                'guid': guid,
                'dispatch_datetime': str(dispatch_datetime),
                'source_lat': source_lat,
                'source_lng': source_lng,
                'geocode_lat': geocode_lat,
                'geocode_lng': geocode_lng,
                'geocode_successful': geocode_successful,
                'status_text': status_text,
                'status_description': status_description,
                'agency_name': agency_name,
                'agency_description': agency_description,
                'agency_website': agency_website,
                'dispatch_id': dispatch_id,
                'dispatch_text': dispatch_text,
                'dispatch_description': dispatch_description,
            })

        dispatch_count, = Dispatches.get_count_by_date(
            session = DBSession,
            target_datetime = datetime.datetime.now(),
        )

    return {'dispatches': ret_dispatches, 'start': start, 'count': count, 'dispatch_count': dispatch_count}

@view_config(route_name='accidents', renderer='templates/accidents.mak')
def accidents(request):

    return {}

@view_config(route_name='agencies', renderer='templates/agencies.mak')
def agencies(request):

    if True:
    #try:

        _agencies = Agencies.get_all(
            session = DBSession,
        )

        agencies = []
        for agency_code, agency_name, agency_description, agency_website, \
                agency_type_code, agency_type_description in _agencies:
            agencies.append({
                'agency_code': agency_code,
                'agency_name': agency_name,
                'description': agency_description,
                'website': agency_website,
                'code': agency_type_code,
                'description': agency_type_description,
            })

    #except:
    #    pass

    return {'agencies': agencies}

@view_config(route_name='browse', renderer='templates/browse.mak')
def browse(request):

    return {}

@view_config(route_name='search', renderer='templates/search.mak')
def search(request):

    return {}

@view_config(route_name='about', renderer='templates/about.mak')
def about(request):

    return {}

@view_config(route_name='status', renderer='templates/status.mak')
def status(request):

    return {}

@view_config(route_name='status.json')
def status_json(request):

    """ Returns the status of the system
    """

    response = {'success': False}
    try:
        response['dispatch_count'] = Dispatches.get_count(DBSession)
        response['current_dispatch_count'] = CurrentDispatches.get_count(DBSession)
        response['run_count'] = Runs.get_count(DBSession)
        response['system_status'] = system_status

        response['success'] = True

    except:
        pass

    resp = json.dumps(response)
    return Response(resp,content_type="application/json")

@view_config(route_name='dispatch_types.json')
def dispatch_types(request):

    response = {'success': False}

    #try:
    if True:

        dispatch_types = DispatchTypes.get_all(
            session = DBSession,
        )

        ret_dispatch_types = []
        for dispatch_type_id, dispatch_type_dispatch_text, \
                dispatch_type_description in dispatch_types:
            ret_dispatch_types.append({
                'id': dispatch_type_id,
                'text': dispatch_type_dispatch_text,
                'description': dispatch_type_description,
            })

        response['dispatch_types'] = ret_dispatch_types

        response['success'] = True

#    except:
#        pass

    resp = json.dumps(response)
    return Response(resp,content_type="application/json")


@view_config(route_name='dispatches.json')
def dispatches(request):

    """ Returns a list of todays dispatches
    """

    response = {'success': False}

    if True:
#    try:

        start = 0
        try:
            start = int(request.GET['start'])
        except:
            pass

        count = 100
        try:
            count = int(request.GET['count'])
        except:
            pass
 
        dispatches = Dispatches.get_by_date(
            session = DBSession,
            target_datetime = datetime.datetime.now(),
            start = start,
            count = count,
        )


        ret_dispatches = []
        for short_address,guid,dispatch_datetime,source_lat,source_lng, \
                geocode_lat,geocode_lng,geocode_successful,status_text, \
                status_description,agency_name,agency_description, \
                agency_website, dispatch_type_id, dispatch_text, \
                dispatch_description in dispatches:
            ret_dispatches.append({
                'short_address': short_address,
                'guid': guid,
                'dispatch_datetime': str(dispatch_datetime),
                'source_lat': source_lat,
                'source_lng': source_lng,
                'geocode_lat': geocode_lat,
                'geocode_lng': geocode_lng,
                'geocode_successful': geocode_successful,
                'status_text': status_text,
                'status_description': status_description,
                'agency_name': agency_name,
                'agency_description': agency_description,
                'agency_website': agency_website,
                'dispatch_type_id': dispatch_type_id,
                'dispatch_text': dispatch_text,
                'dispatch_description': dispatch_description,
            })

        response['dispatches'] = ret_dispatches

        dispatch_count, = Dispatches.get_count_by_date(
            session = DBSession,
            target_datetime = datetime.datetime.now(),
        )

        response['dispatch_count'] = dispatch_count

        response['success'] = True

#    except:
#        pass

    resp = json.dumps(response)
    return Response(resp,content_type="application/json")


