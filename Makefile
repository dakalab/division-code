.PHONY: all
all: help

NGINX_PHP_IMG=dakalab/nginx-php

.PHONY: help
help:
	###########################################################
	# bench                - run benchmarks
	# phpcs                - run style fixer
	# phpunit              - run unit tests
	############################################################
	@echo "Enjoy!"


.PHONY: bench
bench:
	docker run --rm -it -v "${PWD}:/app" ${NGINX_PHP_IMG} php bench.php

.PHONY: phpcs
phpcs:
	docker run --rm -it -v "${PWD}:/app" ${NGINX_PHP_IMG} composer phpcs

.PHONY: phpunit
phpunit:
	docker run --rm -it -v "${PWD}:/app" ${NGINX_PHP_IMG} composer phpunit
