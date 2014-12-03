#!/bin/bash

# make sure we have our bash env setup correct
export WORKON_HOME=/home/administrator/.virtualenvs
source /etc/bash_completion.d/virtualenvwrapper

# enter virt env
workon monroe911

# run our script
cd /home/administrator/dev/mc911feedwatcher/src/mcsafetyfeed-server && python mcsafetyfeed-scraper-v2.py

# leave virt env
deactivate

