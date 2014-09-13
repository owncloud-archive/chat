build_dir=build/
app_name=chat

all:

clean:
	rm -rf $(build_dir)

dist: clean
	mkdir $(build_dir)
	git archive HEAD --format=zip --prefix=$(app_name)/ > $(build_dir)$(app_name).zip
	unzip $(build_dir)/$(app_name).zip -d $(build_dir)
	rm -rf $(build_dir)/$(app_name).zip
	rm -rf $(build_dir)/chat/js/*
	rm -rf $(build_dir)/chat/template/scripts.php
	mv $(build_dir)/chat/template/scripts.php.build $(build_dir)/chat/template/scripts.php
	grunt

test: php-unit js-unit

php-unit:
	phpunit -c tests/phpunit.xml --testsuite app --coverage-clover=coverage.clover

js-unit:


