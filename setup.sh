#!/usr/bin/env bash

#./bin/console doctrine:database:create
#./bin/console make:migration

# Migrira sve tabele i veze vezane za projekat
./bin/console doctrine:migrations:migrate

chmod 777 -R /var/www/html/var/log
./bin/console cache:clear
./bin/console cache:clear --env=prod