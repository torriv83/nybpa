SHELL := /bin/bash

debug:
	echo $$SHELL
	echo ./sail

bash:
	docker-compose exec laravel.test bash

start:
	sh vendor/bin/sail up -d

stop:
	sh vendor/bin/sail stop

down:
	sh vendor/bin/sail down

set-permissions:
	chmod +x merge-and-deploy.sh

deploy: set-permissions
	./merge-and-deploy.sh

build:
	sh vendor/bin/sail bun run build

buildf:
	sh vendor/bin/sail artisan filament:assets

test:
	sh vendor/bin/sail pest --parallel

install:
	sh vendor/bin/sail composer install
	sh vendor/bin/sail bun install

update:
	sh vendor/bin/sail composer update
	bun update
	git add -u
	git commit -m "bun and composer update"

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
phpstan:
	./vendor/bin/sail exec laravel.test bash -c "./vendor/bin/phpstan analyse"

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
