build_dir=../build/
app_name=chat
files_to_delete = ("bower.json" ".bowerrc")

all:

dist:
	sh build.sh

test: php-unit js-unit

php-unit:
	phpunit -c tests/phpunit.xml --testsuite app --coverage-clover=coverage.clover

js-unit:
	./node_modules/grunt-cli/bin/grunt karma

travis-install-dep:
	sudo apt-get -y install nodejs
	npm install

