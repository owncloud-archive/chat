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
						'js/src/och/chat.och.js',
						'js/src/och/chat.och.api.js',
						'js/src/och/chat.och.on.js',
						'js/src/och/chat.och.util.js',
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
					'js/vendor/rangyinputs.js' : [ 'js/src/bower_components/rangyinputs-jquery-src/index.js']
				}
			}
		}
	});

	// Load the plugin that provides the "uglify" task.
	grunt.loadNpmTasks('grunt-contrib-uglify');

	// Default task(s).
	grunt.registerTask('default', ['uglify']);

};