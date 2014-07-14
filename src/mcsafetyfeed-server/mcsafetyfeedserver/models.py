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
    ForeignKey,
    )

from sqlalchemy import update

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

class IncidentTypes(Base):

    """
    Holds the different types of event types (such as barking dogs, or MVA)
    """

    __tablename__ = 'incident_types'
    id = Column(Integer, primary_key=True)
    incident_text = Column(Text)
    description = Column(Text)

    @classmethod
    def get_from_incident_text(cls, session, incident_text):
        with transaction.manager:
            incident_type = session.query(
                IncidentTypes,
            ).filter(
                IncidentTypes.incident_text == incident_text,
            ).first()
            if incident_type == None:
                incident_type = IncidentTypes.create_new_incident_type(session, incident_text, '')
        return incident_type

    @classmethod
    def create_new_incident_type(cls, session, incident_text, description):
        with transaction.manager:
            incident_type = cls(
                incident_text = incident_text,
                description = description,
            )
            session.add(incident_type)
            transaction.commit()
        return incident_type

class Groups(Base):

    """
    Holds the definition of a group, which has multiple incident types within it. This is
    to group like incidents together such as car accidents or animal disruptance.
    """
  
    __tablename__ = 'groups'
    id = Column(Integer, primary_key=True)
    name = Column(Text)
    description = Column(Text)

class GroupIncidentTypes(Base):

    """
    Connects incident types to groups.
    """

    __tablename__ = 'group_incident_types'
    id = Column(Integer, primary_key=True)
    group_id = Column(Integer, ForeignKey('groups.id'))
    incident_type_id = Column(Integer, ForeignKey('incident_types.id'))

class Statuses(Base):

    """
    Holds the different statuses that an incident can have (WAITING, DISPATCHED, etc).
    """

    __tablename__ = 'statuses'
    id = Column(Integer, primary_key=True)
    status_text = Column(Integer)
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
    This is the table that holds all of the events that are gathered from the RSS feed.
    """

    __tablename__ = 'incidents'
    id = Column(Integer, primary_key=True)
    run_id = Column(Integer, ForeignKey('runs.id'))
    status_id = Column(Integer, ForeignKey('statuses.id'))
    short_address = Column(Text)
    guid = Column(Text)
    agency_id = Column(Integer, ForeignKey('agencies.id'))
    incident_type_id = Column(Integer, ForeignKey('incident_types.id'))
    incident_datetime = Column(DateTime)
    source_lat = Column(Float)
    source_lng = Column(Float)
    geocode_lat = Column(Float)
    geocode_lng = Column(Float)
    full_address = Column(Text)
    geocode_successful = Column(Boolean)

    @classmethod
    def check_exists(cls, session, guid, status_text):
        with transaction.manager:
            status = Statuses.get_from_status_text(session, status_text)
            q = session.query(
                Incidents,
            ).filter(
                Incidents.guid == guid,
                Incidents.status_id == status.id,
            )
            incident_exists = session.query(q.exists()).scalar()
        return incident_exists

    @classmethod
    def add_incident(cls, session, run_id, status_text, short_address,
            guid, incident_text, incident_datetime, source_lat, source_lng,
            geocode_lat, geocode_lng, full_address, geocode_successful):
        with transaction.manager:
            status = Statuses.get_from_status_text(session, status_text)
            agency = Agencies.get_from_guid(session, guid)
            incident_type = IncidentTypes.get_from_incident_text(session, incident_text)
            incident = cls(
                run_id = run_id,
                status_id = status.id,
                short_address = short_address,
                guid = guid,
                agency_id = agency.id,
                incident_type_id = incident_type.id,
                incident_datetime = incident_datetime,
                source_lat = source_lat,
                source_lng = source_lng,
                geocode_lat = geocode_lat,
                geocode_lng = geocode_lng,
                full_address = full_address,
                geocode_successful = geocode_successful,
            )
            session.add(incident)
            transaction.commit()
        return incident

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

class CurrentIncidents(Base):

    """
    This holds a list of the current incidents that are active. This is used to deturmine
    how long it takes different call types to be serviced by the calling agencies.
    """

    __tablename__ = 'current_incidents'
    id = Column(Integer, primary_key=True)
    incident_id = Column(Integer, ForeignKey('incidents.id'))

class Runs(Base):

    """
    Holds information about each scraper run.
    """

    __tablename__ = 'runs'
    id = Column(Integer, primary_key=True)
    successful = Column(Boolean)
    error_text = Column(Text)
    run_datetime = Column(DateTime)
    new_incidents = Column(Boolean)

    @classmethod
    def new_run(cls, session):
        with transaction.manager:
            run = cls(
                successful = False, #successful,
                error_text = None, #error_text,
                run_datetime = datetime.datetime.now(),
                new_incidents = False, #new_incidents,
            )
            session.add(run)
            transaction.commit()
        return run

    @classmethod
    def update_run(cls, session, run, successful, error_text, new_incidents):
        with transaction.manager:
            run = update(Runs).where(
                Runs.id == run.id
            ).values(
                successful = successful,
                error_text = error_text,
                new_incidents = new_incidents,
            )
            transaction.commit()
        return run



