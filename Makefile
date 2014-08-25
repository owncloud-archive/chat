build_dir=build/
app_name=chat

all:

clean:
	rm -rf $(build_dir)

dist: clean
	mkdir -p $(build_dir)
	git archive HEAD --format=zip --prefix=$(app_name)/ > $(build_dir)$(app_name).zip

test: php-unit js-unit

php-unit:
	phpunit -c tests/phpunit.xml --testsuite app --coverage-clover=coverage.clover

js-unit

