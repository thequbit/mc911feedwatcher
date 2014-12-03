import os
import datetime

import transaction

from sqlalchemy import (
    Column,
    Index,
    Integer,
    Text,
    Boolean,
    Float,
    DateTime,
    Date,
    ForeignKey,
    cast,
    desc,
    )

from sqlalchemy import update, func, DATE

from sqlalchemy.ext.declarative import declarative_base

from sqlalchemy.orm import (
    scoped_session,
    sessionmaker,
    )

from zope.sqlalchemy import ZopeTransactionExtension

DBSession = scoped_session(sessionmaker(extension=ZopeTransactionExtension(), expire_on_commit=False))
Base = declarative_base()


#class MyModel(Base):
#    __tablename__ = 'models'
#    id = Column(Integer, primary_key=True)
#    name = Column(Text)
#    value = Column(Integer)
#
#Index('my_index', MyModel.name, unique=True, mysql_length=255)

class Users(Base):

    """
    Holds users. This is simply to allow for registering for an API key.
    """

    __tablename__ = 'users'
    id = Column(Integer, primary_key=True)
    first = Column(Text)
    last = Column(Text)
    email = Column(Text)
    organization = Column(Text)
    api_key = Column(Text)
    verified = Column(Boolean)

class AgencyTypes(Base):

    """
    Holds the different types of agencies (such as Fire(F), Police(P), EMS(E).
    """

    __tablename__ = 'agency_types'
    id = Column(Integer, primary_key=True)
    code = Column(Text)
    description = Column(Text)

    @classmethod
    def get_from_code(cls, session, code):
        with transaction.manager:
            agency_type = session.query(
                AgencyTypes,
            ).filter(
                code == code,
            ).first()
            if agency_type == None:
                agency_type = AgencyTypes.create_new_agency_type(session, code, '')
        return agency_type

    @classmethod
    def create_new_agency_type(cls, session, code, description):
        with transaction.manager:
            agency_type = cls(
                code = code,
                description = description,
            )
        return agency_type

class Agencies(Base):

    """
    Holds a list of all of the agencies within the county
    """

    __tablename__ = 'agencies'
    id = Column(Integer, primary_key=True)
    agency_code = Column(Text)
    agency_name = Column(Text)
    type_id = Column(Integer, ForeignKey('agency_types.id'))
    description = Column(Text)
    website = Column(Text)

    @classmethod
    def get_from_guid(cls, session, guid):
        with transaction.manager:
            agency = session.query(
                Agencies,
            ).filter(
                Agencies.agency_code == guid[:4], #first four leters are agency code
            ).first()
            if agency == None:
                agency_type = AgencyTypes.get_from_code(session, guid[3])
                agency = Agencies.create_new_agency(session, guid[:4], '', agency_type.id, '', '')
        return agency

    @classmethod
    def create_new_agency(cls, session, agency_code, agency_name,
            type_id, description, website):
        with transaction.manager:
            agency = cls(
                agency_code = agency_code,
                agency_name = agency_name,
                type_id = type_id,
                description = description,
                website = website,
            )
        return agency

    @classmethod
    def get_all(cls, session):
        with transaction.manager:
            agencies = session.query(
                Agencies.agency_code,
                Agencies.agency_name,
                Agencies.description,
                Agencies.website,
                AgencyTypes.code,
                AgencyTypes.description,
            ).join(
                AgencyTypes, AgencyTypes.id == Agencies.type_id,
            ).order_by(
                Agencies.agency_code,    
            ).all()
        return agencies

class DispatchTypes(Base):

    """
    Holds the different types of event types (such as barking dogs, or MVA)
    """

    __tablename__ = 'dispatch_types'
    id = Column(Integer, primary_key=True)
    dispatch_text = Column(Text)
    description = Column(Text)

    @classmethod
    def get_from_dispatch_text(cls, session, dispatch_text):
        with transaction.manager:
            dispatch_type = session.query(
                DispatchTypes,
            ).filter(
                DispatchTypes.dispatch_text == dispatch_text,
            ).first()
            if dispatch_type == None:
                dispatch_type = DispatchTypes.create_new_dispatch_type(session, dispatch_text, '')
        return dispatch_type

    @classmethod
    def create_new_dispatch_type(cls, session, dispatch_text, description):
        with transaction.manager:
            dispatch_type = cls(
                dispatch_text = dispatch_text,
                description = description,
            )
            session.add(dispatch_type)
            transaction.commit()
        return dispatch_type

    @classmethod
    def get_all(cls, session):
        with transaction.manager:
            dispatch_types = session.query(
                DispatchTypes.id,
                DispatchTypes.dispatch_text,
                DispatchTypes.description,
            ).filter(
            ).all()
        return dispatch_types

class Groups(Base):

    """
    Holds the definition of a group, which has multiple dispatch types within it. This is
    to group like dispatches together such as car accidents or animal disruptance.
    """
  
    __tablename__ = 'groups'
    id = Column(Integer, primary_key=True)
    name = Column(Text)
    description = Column(Text)

    @classmethod
    def get_from_dispatch_text(cls, session, dispatch_text):
        with transaction.manager:
            group = session.query(
                Groups,
            ).join(
                Groups, GroupDispatchTypes.id,
                DispatchTypes, GroupDispatchTypes.dispatch_type_id,
            ).filter(
                GroupDispatchTypes.dispatch_text == dispatch_text,
            ).first()
        return group

class GroupDispatchTypes(Base):

    """
    Connects dispatch types to groups.
    """

    __tablename__ = 'group_dispatch_types'
    id = Column(Integer, primary_key=True)
    group_id = Column(Integer, ForeignKey('groups.id'))
    dispatch_type_id = Column(Integer, ForeignKey('dispatch_types.id'))

class Statuses(Base):

    """
    Holds the different statuses that an dispatch can have (WAITING, DISPATCHED, etc).
    """

    __tablename__ = 'statuses'
    id = Column(Integer, primary_key=True)
    status_text = Column(Text)
    description = Column(Text)

    @classmethod
    def create_new_status(cls, session, status_text, description):
        with transaction.manager:
            status = cls(
                status_text = status_text,
                description = description,
            )
            session.add(status)
            transaction.commit()
        return status

    @classmethod
    def get_from_status_text(cls, session, status_text):
        with transaction.manager:
            status = session.query(
                Statuses,
            ).filter(
                Statuses.status_text == status_text,
            ).first()
            if status == None:
                status = Statuses.create_new_status(session, status_text, '')
        return status

class Incidents(Base):

    """
    Unique events that may have multiple dispatches.  Matched using time, address, 
    and dispatch type.
    """

    __tablename__ = 'incidents'
    id = Column(Integer, primary_key=True)
    incident_datetime = Column(DateTime)
    group_id = Column(Integer, ForeignKey('groups.id'))

    @classmethod
    def create_new_incident(cls, session, dispatch_text):
        with transaction.manager:
            group = Groups.get_from_dispatch_text(session, dispatch_text)
            incident = cls(
                incident_datetime = datetime.datetime.now(),
                group_id = group.id,
            )
            session.add(incident)
            transaction.commit()
        return incident

class IncidentsDispatches(Base):

    """
    Connections Incidents to Dispatches
    """

    __tablename__ = 'incidents_dispatches'
    id = Column(Integer, primary_key=True)
    incident_id = Column(Integer, ForeignKey('incidents.id'))
    dispatch_id = Column(Integer, ForeignKey('dispatches.id'))

class Dispatches(Base):

    """
    This is the table that holds all of the events that are gathered from the RSS feed.
    """

    __tablename__ = 'dispatches'
    id = Column(Integer, primary_key=True)
    run_id = Column(Integer, ForeignKey('runs.id'))
    status_id = Column(Integer, ForeignKey('statuses.id'))
    short_address = Column(Text)
    guid = Column(Text)
    agency_id = Column(Integer, ForeignKey('agencies.id'))
    dispatch_type_id = Column(Integer, ForeignKey('dispatch_types.id'))
    dispatch_datetime = Column(DateTime)
    source_lat = Column(Float)
    source_lng = Column(Float)
    geocode_lat = Column(Float)
    geocode_lng = Column(Float)
    full_address = Column(Text)
    geocode_successful = Column(Boolean)

    @classmethod
    def get_count(cls, session):
        with transaction.manager:
            count = session.query(
                Dispatches,
            ).count()
        return count

    @classmethod
    def check_exists(cls, session, guid, status_text):
        with transaction.manager:
            status = Statuses.get_from_status_text(session, status_text)
            q = session.query(
                Dispatches,
            ).filter(
                Dispatches.guid == guid,
                Dispatches.status_id == status.id,
            )
            dispatch_exists = session.query(q.exists()).scalar()
        return dispatch_exists

    @classmethod
    def add_dispatch(cls, session, run_id, status_text, short_address,
            guid, dispatch_text, dispatch_datetime, source_lat, source_lng,
            geocode_lat, geocode_lng, full_address, geocode_successful):
        with transaction.manager:
            status = Statuses.get_from_status_text(session, status_text)
            agency = Agencies.get_from_guid(session, guid)
            dispatch_type = DispatchTypes.get_from_dispatch_text(session, dispatch_text)
            dispatch = cls(
                run_id = run_id,
                status_id = status.id,
                short_address = short_address,
                guid = guid,
                agency_id = agency.id,
                dispatch_type_id = dispatch_type.id,
                dispatch_datetime = dispatch_datetime,
                source_lat = source_lat,
                source_lng = source_lng,
                geocode_lat = geocode_lat,
                geocode_lng = geocode_lng,
                full_address = full_address,
                geocode_successful = geocode_successful,
            )
            session.add(dispatch)
            transaction.commit()
        return dispatch

    @classmethod
    def get_by_guid(cls, session, guid):
        with transaction.manager:
            dispatch = session.query(
                Dispatches,
            ).filter(
                Dispatches.guid == guid
            ).first()
        return dispatch

    @classmethod
    def close_dispatch(cls, session, run_id, guid):
        with transaction.manager:
            current_dispatch = Dispatches.get_by_guid(DBSession, guid)
            status = Statuses.get_from_status_text(session, 'CLOSED')
            agency = Agencies.get_from_guid(session, current_dispatch.guid)
            #dispatch_type = DispatchTypes.get_from_dispatch_text(session, dispatch_text) 
            dispatch = cls(
                run_id = run_id,
                status_id = status.id,
                short_address = current_dispatch.short_address,
                guid = current_dispatch.guid,
                agency_id = current_dispatch.agency_id,
                dispatch_type_id = current_dispatch.dispatch_type_id,
                dispatch_datetime = datetime.datetime.now(),
                source_lat = current_dispatch.source_lat,
                source_lng = current_dispatch.source_lng,
                geocode_lat = current_dispatch.geocode_lat,
                geocode_lng = current_dispatch.geocode_lng,
                full_address = current_dispatch.full_address,
                geocode_successful = current_dispatch.geocode_successful,
            )
            session.add(dispatch)
            transaction.commit()
        return dispatch

    @classmethod
    def get_by_date(cls, session, target_datetime, start, count):
        with transaction.manager:
            dispatches = session.query(
                Dispatches.short_address,
                Dispatches.guid,
                Dispatches.dispatch_datetime,
                Dispatches.source_lat,
                Dispatches.source_lng,
                Dispatches.geocode_lat,
                Dispatches.geocode_lng,
                Dispatches.geocode_successful,
                Statuses.status_text,
                Statuses.description,
                Agencies.agency_name,
                Agencies.description,
                Agencies.website,
                DispatchTypes.id,
                DispatchTypes.dispatch_text,
                DispatchTypes.description,
            ).outerjoin(
                Statuses,Dispatches.status_id == Statuses.id,
            ).outerjoin(
                Agencies,Dispatches.agency_id == Agencies.id,
            ).outerjoin(
                DispatchTypes,Dispatches.dispatch_type_id == DispatchTypes.id,
            ).filter(
                func.date(Dispatches.dispatch_datetime) == \
                    target_datetime.date(),
            ).order_by(
                desc(Dispatches.dispatch_datetime)
            ).offset(
                start
            ).limit(
                count
            ).all()
        return dispatches

    @classmethod
    def get_count_by_date(cls, session, target_datetime):
        with transaction.manager:
            count = session.query(
                func.count(Dispatches.id),
            ).filter(
                func.date(Dispatches.dispatch_datetime) == \
                    target_datetime.date(),
            #).order_by(
            #    desc(Dispatches.dispatch_datetime)
            ).first()

        return count

class APICalls(Base):

    """
    This is to record all API calls made to access the database.
    """

    __tablename__ = 'api_calls'
    id = Column(Integer, primary_key=True)
    user_id = Column(Integer, ForeignKey('users.id'))
    api_call_datetime = Column(DateTime)
    query_time = Column(Float)
    api_call = Column(Text)
    api_version = Column(Integer)

    @classmethod
    def add_api_call(cls, session, api_key, query_time, api_call, api_version):
        with transaction.manager:
            user = Users.from_api_key(session, api_key)
            apicall = cls(
                user_id = user.api_key,
                api_call_datetime = datetime.datetime.now(),
                query_time = query_time,
                api_call = api_call,
                api_version = api_version,
            )
            session.add(apicall)
            transaction.commit()
        return apicall

class CurrentDispatches(Base):

    """
    This holds a list of the current dispatches that are active. This is used to deturmine
    how long it takes different call types to be serviced by the calling agencies.
    """

    __tablename__ = 'current_dispatches'
    id = Column(Integer, primary_key=True)
    #dispatch_id = Column(Integer, ForeignKey('dispatches.id'))
    guid = Column(Text)

    @classmethod
    def get_count(cls, session):
        with transaction.manager:
            count = session.query(
                CurrentDispatches,
            ).count()
        return count

    @classmethod
    def add_current_dispatch(cls, session, guid):
        with transaction.manager:
            current_dispatch = cls(
                guid = guid,
            )
            session.add(current_dispatch)
            transaction.commit()
        return current_dispatch

    @classmethod
    def get_current_dispatch_guids(cls, session):
        with transaction.manager:
            current_dispatches = session.query(
                CurrentDispatches,
            ).all()
            current_dispatch_guids = []
            for current_dispatch in current_dispatches:
                current_dispatch_guids.append(current_dispatch.guid)
        return current_dispatch_guids

    @classmethod
    def remove_current_dispatch(cls, session, guid):
        with transaction.manager:
            dispatch = session.query(
                CurrentDispatches,
            ).filter(
                CurrentDispatches.guid == guid,
            ).first()
            if guid != None:
                session.delete(dispatch)
                transaction.commit()

class Runs(Base):

    """
    Holds information about each scraper run.
    """

    __tablename__ = 'runs'
    id = Column(Integer, primary_key=True)
    successful = Column(Boolean)
    error_text = Column(Text)
    run_datetime = Column(DateTime)
    new_dispatches = Column(Boolean)

    @classmethod
    def get_count(cls, session):
        with transaction.manager:
            count = session.query(
                Runs,
            ).count()
        return count

    @classmethod
    def new_run(cls, session):
        with transaction.manager:
            run = cls(
                successful = False, #successful,
                error_text = None, #error_text,
                run_datetime = datetime.datetime.now(),
                new_dispatches = False, #new_dispatches,
            )
            session.add(run)
            transaction.commit()
        return run

    @classmethod
    def update_run(cls, session, run, successful, error_text, new_dispatches):
        with transaction.manager:
            run = update(Runs).where(
                Runs.id == run.id
            ).values(
                successful = successful,
                error_text = error_text,
                new_dispatches = new_dispatches,
            )
            transaction.commit()
        return run



