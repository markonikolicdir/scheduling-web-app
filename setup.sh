#!/usr/bin/env bash

./bin/console doctrine:database:create
./bin/console make:migration
./bin/console doctrine:migrations:migrate