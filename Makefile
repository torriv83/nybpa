SHELL := /bin/bash

debug:
	echo $$SHELL
	echo ./sail


deploy:
	./merge-and-deploy.sh

build:
	sh vendor/bin/sail npm run build

build-filament:
	sh vendor/bin/sail artisan filament:assets

test:
	sh vendor/bin/sail pest --parallel

install:
	sh vendor/bin/sail composer install
	sh vendor/bin/sail npm install

update:
	sh vendor/bin/sail composer update
	sh vendor/bin/sail npm update
	git add composer.lock package-lock.json
	git commit -m "npm and composer update"

clear-cache:
	sh vendor/bin/sail artisan cache:clear
	sh vendor/bin/sail artisan view:clear
	sh vendor/bin/sail artisan config:clear
	sh vendor/bin/sail artisan event:clear
	sh vendor/bin/sail artisan route:clear



migrate:
	sh vendor/bin/sail artisan migrate

seed:
	sh vendor/bin/sail artisan db:seed

clean:
	rm -rf build/ dist/

routelist:
	sh vendor/bin/sail artisan route:list

##fresh-install: install migrate

##Filament

f-r:
	sh vendor/bin/sail artisan make:filament-resource

f-rm:
	sh vendor/bin/sail artisan make:filament-relation-manager

f-wt:
	sh vendor/bin/sail artisan make:filament-widget --table

f-wc:
	sh vendor/bin/sail artisan make:filament-widget --chart

f-ws:
	sh vendor/bin/sail artisan make:filament-widget --stats-overview

f-wcustom:
	sh vendor/bin/sail artisan make:filament-widget
