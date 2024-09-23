install:
	composer install

start:
	bin/gendiff.php

validate:
	composer validate

lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin

tests: 
	composer exec --verbose phpunit tests

.PHONY: tests