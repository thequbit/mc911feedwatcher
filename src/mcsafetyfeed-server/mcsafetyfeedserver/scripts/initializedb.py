import os
import sys
import transaction

from sqlalchemy import engine_from_config

from pyramid.paster import (
    get_appsettings,
    setup_logging,
    )

from pyramid.scripts.common import parse_vars

from ..models import (
    DBSession,
    #MyModel,
    Base,
    AgencyTypes,
    Agencies,
    )


def usage(argv):
    cmd = os.path.basename(argv[0])
    print('usage: %s <config_uri> [var=value]\n'
          '(example: "%s development.ini")' % (cmd, cmd))
    sys.exit(1)


def main(argv=sys.argv):
    if len(argv) < 2:
        usage(argv)
    config_uri = argv[1]
    options = parse_vars(argv[2:])
    setup_logging(config_uri)
    settings = get_appsettings(config_uri, options=options)
    engine = engine_from_config(settings, 'sqlalchemy.')
    DBSession.configure(bind=engine)
    Base.metadata.create_all(engine)
    #with transaction.manager:
    #    model = MyModel(name='one', value=1)
    #    DBSession.add(model)

    with transaction.manager:
        with open('agencies.csv','r') as f:
            agencies = f.read().split('\n')

        for agency in agencies:
            if agency.strip() != '':
                # agencyid, shortname, longname, type, description, websiteurl
                parts = agency.split('\t')
                agency_type = AgencyTypes.get_from_code(DBSession, parts[3])
                a = Agencies(
                    agency_code = parts[1],
                    agency_name = parts[2],
                    type_id = agency_type.id,
                    description = parts[4],
                    website = parts[5],
                )
                DBSession.add(a)
                transaction.commit()
