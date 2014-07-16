
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

@view_config(route_name='home')
def home(request):

    return Response('<html><body>hi.</body></html>')

@view_config(route_name='status.json')
def status(request):

    """
    Returns the status of the system
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

