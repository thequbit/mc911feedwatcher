from pyramid.config import Configurator
from sqlalchemy import engine_from_config

from .models import (
    DBSession,
    Base,
    )


def main(global_config, **settings):
    """ This function returns a Pyramid WSGI application.
    """
    engine = engine_from_config(settings, 'sqlalchemy.')
    DBSession.configure(bind=engine)
    Base.metadata.bind = engine
    config = Configurator(settings=settings)
    config.include('pyramid_chameleon')
    config.add_static_view('static', 'static', cache_max_age=3600)

    #config.add_route('home', '/')

    #config.add_route('system_status', 'system_status.json')

    config.add_route('home', '/')
    config.add_route('feed', '/feed')
    config.add_route('accidents', '/accidents')
    config.add_route('agencies', '/agencies')
    config.add_route('browse', '/browse')
    config.add_route('search', '/search')
    config.add_route('about', '/about')
    config.add_route('status','/status')

    config.add_route('status.json','status.json')
    config.add_route('dispatches.json','dispatches.json')
    config.add_route('dispatch_types.json', 'dispatch_types.json')


    config.scan()
    return config.make_wsgi_app()
