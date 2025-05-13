#!/bin/bash
php7.4  app/console  cache:clear --env=prod -vvv;chown -R www-data:www-data app

