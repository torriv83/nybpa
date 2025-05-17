kind: pipeline
name: ci-cd
type: docker

steps:
  - name: prepare-environment
    image: laravelsail/php83-composer
    commands:
      - apt-get update && apt-get install -y unzip git zip curl libzip-dev libpng-dev libonig-dev libxml2-dev
      - curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
      - composer install --prefer-dist --no-interaction
      - npm ci

  - name: pint
    image: laravelsail/php83-composer
    commands:
      - composer install --no-interaction
      - ./vendor/bin/pint --test

  - name: phpstan
    image: laravelsail/php83-composer
    commands:
      - composer install --no-interaction
      - ./vendor/bin/phpstan analyse

  - name: pest-tests
    image: laravelsail/php83-composer
    environment:
      APP_ENV: testing
      APP_KEY:
        from_secret: APP_KEY
    commands:
      - composer install --no-interaction
      - php artisan test --parallel

  - name: build-assets
    image: node:20
    commands:
      - npm ci
      - npm run build

trigger:
  branch:
    include:
      - devtest
