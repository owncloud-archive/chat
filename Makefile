build_dir=../build/
app_name=chat
files_to_delete = ("bower.json" ".bowerrc")

all:

clean:
	rm -rf $(build_dir)

dist: clean
	sh build.sh

test: php-unit js-unit

php-unit:
	phpunit -c tests/phpunit.xml --testsuite app --coverage-clover=coverage.clover

js-unit:
	./node_modules/karma/bin/karma start karma.conf.js

travis-install-dep:
	sudo apt-get -y install nodejs
	npm install

