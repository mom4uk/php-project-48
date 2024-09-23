start:
	bin/gendiff.php

validate:
	composer validate

lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin