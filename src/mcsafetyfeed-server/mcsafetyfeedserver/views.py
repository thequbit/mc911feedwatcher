
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

#@view_config(route_name='home', )
#def home(request):
#
#    return {}

@view_config(route_name='status.json')
def status(request):

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

@view_config(route_name='dispatches.json')
def dispatches(request):

    """ Returns a list of todays dispatches
    """

    response = {'success': False}

    if True:
#    try:

        dispatches = Dispatches.get_by_date(
            session = DBSession,
            target_datetime = datetime.datetime.now()
        )


        ret_dispatches = []
        for short_address,guid,dispatch_datetime,source_lat,source_lng, \
                geocode_lat,geocode_lng,geocode_successful,status_text, \
                status_description,agency_name,agency_description, \
                agency_website,dispatch_text,dispatch_description \
                in dispatches:
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
                'dispatch_text': dispatch_text,
                'dispatch_description': dispatch_description,
            })

        response['dispatches'] = ret_dispatches
        response['success'] = True

#    except:
#        pass

    resp = json.dumps(response)
    return Response(resp,content_type="application/json")


#@view_config(route_name='dispatches')
#def status(request):
#
#    """
#    return the dispatches for the date requested 
#    """
#
#    resp = json.dumps({})
#
#    return Response(resp,content_type='application/json')

#
#@view_config(route_name='home', renderer='templates/mytemplate.pt')
#def my_view(request):
#    try:
#        one = DBSession.query(MyModel).filter(MyModel.name == 'one').first()
#    except DBAPIError:
#        return Response(conn_err_msg, content_type='text/plain', status_int=500)
#    return {'one': one, 'project': 'mcsafetyfeed-server'}
#

#conn_err_msg = """\
#Pyramid is having a problem using your SQL database.  The problem
#might be caused by one of the following things:
#
#1.  You may need to run the "initialize_mcsafetyfeed-server_db" script
#    to initialize your database tables.  Check your virtual
#    environment's "bin" directory for this script and try to run it.
#
#2.  Your database server may not be running.  Check that the
#    database server referred to by the "sqlalchemy.url" setting in
#    your "development.ini" file is running.
#
#After you fix the problem, please restart the Pyramid application to
#try it again.
#"""

