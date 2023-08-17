SHELL := /bin/bash

debug:
	echo $$SHELL
	echo ./sail


deploy:
	./merge-and-deploy.sh

build:
	sh vendor/bin/sail npm run build

test:
	sh vendor/bin/sail pest

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

lint:
	sh vendor/bin/sail ./vendor/bin/phpcs

clean:
	rm -rf build/ dist/

##fresh-install: install migrate
