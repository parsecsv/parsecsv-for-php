COMPOSER_BIN_DIR := vendor/bin
PHPUNIT_ARGS = -c tests/phpunit.xml

test: phpunit-dep
	${COMPOSER_BIN_DIR}/phpunit ${PHPUNIT_ARGS}

phpunit-dep:
	test -f ${COMPOSER_BIN_DIR}/phpunit || ( \
		echo "phpunit is required to run tests." \
			"Please run: composer install" >&2 && \
		exit 1 \
	)

.SILENT:
.PHONY: test phpunit-dep
