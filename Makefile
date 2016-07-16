PHP_BIN      := php
COMPOSER_BIN := composer

.PHONY: composer-install-dev tests tests-coverage

composer-install-dev:
	$(COMPOSER_BIN) install --optimize-autoloader

tests: composer-install-dev
	$(PHP_BIN) ./vendor/phpunit/phpunit/phpunit -c .

tests-coverage: composer-install-dev
	$(PHP_BIN) ./vendor/phpunit/phpunit/phpunit -c . --coverage-html coverage