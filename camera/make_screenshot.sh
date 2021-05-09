#! /bin/bash

#raspistill -o screenshots/`date +%Y%m%d%H%M%S`.jpg;

wget http://localhost:8080/?action=snapshot -O screenshots/`date +%Y%m%d%H%M%S`.jpg;
