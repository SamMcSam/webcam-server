#! /bin/bash

#raspistill -o screenshots/`date +%Y%m%d%H%M%S`.jpg;

wget http://localhost:8080/?action=snapshot -O /var/www/camera/screenshots/`date +%Y%m%d%H%M%S`.jpg;
