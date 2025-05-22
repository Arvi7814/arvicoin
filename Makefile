include .env

restart: down build up

build:
	docker-compose build

up:
	docker-compose up -d

down:
	docker-compose down --remove-orphans
	
login-php:
	docker-compose exec -u $(shell id -u):$(shell id -g) php-cli bash

login-node:
	docker-compose exec -u $(shell id -u):$(shell id -g) node sh

db-restore:
	cat ./docker/storage/arvicoin.sql | docker-compose exec -T db /usr/bin/mysql -u ${DB_USERNAME} --password=${DB_PASSWORD} ${DB_DATABASE}

db-dump:
	docker-compose exec db /usr/bin/mysqldump -u ${DB_USERNAME} --password=${DB_PASSWORD} ${DB_DATABASE} > ./docker/dump.sql

show-log:
	tail -n 500 ./storage/logs/laravel.log

clear-log:
	echo "" > ./storage/logs/laravel.log

hotfix:
	git add . && git commit -m 'hotfix' && git push
