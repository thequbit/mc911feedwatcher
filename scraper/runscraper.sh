#!/bin/bash

# make sure we have our bash env setup correct
#export WORKON_HOME=/home/ubuntu/.Envs
#source /etc/bash_completion.d/virtualenvwrapper

export WORKON_HOME=~/Envs
source /usr/local/bin/virtualenvwrapper.sh

# enter virt env
workon mcsafetyfeed

# run our script
cd /home/ubuntu/mcsafetyfeed/scraper && python scraper.py

# leave virt env
deactivate

