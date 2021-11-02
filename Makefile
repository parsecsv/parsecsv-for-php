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

# Requires:
# - Docker: https://docker.com
# - act: https://github.com/nektos/act
local-ci:
	act -P ubuntu-latest=shivammathur/node:latest -W .github/workflows/ci.yml

.SILENT:
.PHONY: test phpunit-dep local-ci
