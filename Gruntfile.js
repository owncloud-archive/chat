module.exports = function(grunt) {

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		uglify: {
			options: {
				banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n'
			},
			build: {
				options: {
					mangle: false,
				},
				files: {
					'js/main.min.js': [
						'js/src/chat.js',
						'js/src/**/*.js',
						'!js/src/bower_components/**/*.js',
						'!js/src/vendor/**/*.js'
					],
					'js/vendor/angular.js' : [ 'js/src/bower_components/angular/angular.js'	],
					'js/vendor/angular-enhance-text.js': [ 'js/src/bower_components/angular-enhance-text/build/angular-enhance-text.js'	],
					'js/vendor/angular-sanitize.js' : ['js/src/bower_components/angular-sanitize/angular-sanitize.js' ],
					'js/vendor/jquery-autosize.js' : [ 'js/src/bower_components/jquery-autosize/jquery.autosize.js'	],
					'js/vendor/applycontactavatar.js' : ['js/src/vendor/applycontactavatar.js'],
					'js/vendor/cache.js'  : [ 'js/src/vendor/cache.js' ],
					'js/vendor/time.js' : [ 'js/src/vendor/time.js'	],
					'js/vendor/rangyinputs.js' : [ 'js/src/bower_components/rangyinputs-jquery-src/index.js'],
					'js/vendor/moment.js' : [ 'js/src/bower_components/moment/moment.js'],
					'js/vendor/strophe.js' : [ 'js/src/bower_components/strophejs/strophe.js']

				}
			}
		},
		cssmin: {
			combine: {
				files: {
					'css/main.min.css': ['css/src/*.css']
				}
			}
		},
		copy: {
			main: {
				src: 'js/src/bower_components/strophejs/strophe.js',
				dest: 'js/vendor/strophe.js'
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.registerTask('default', ['uglify', 'cssmin']);

};