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
						'js/src/och/chat.och.utiljs',
						'js/src/**/*.js',
						'!js/src/vendor/**/*.js'
					],
					'js/vendor/angular.js' : [
						'js/src/vendor/angular.js'
					],
					'js/vendor/angular-santitize.js' : [
						'js/src/vendor/angular-sanitize.js'
					],
					'js/vendor/angular-enhance-text.js' : [
						'js/src/vendor/angular-enhance-text.js'
					],
					'js/vendor/applycontactavatar.js' : [
						'js/src/vendor/applycontactavatar.js'
					],
					'js/vendor/jquery.autosize.js' : [
						'js/src/vendor/jquery.autosize.js'
					],
					'js/vendor/cache.js' : [
						'js/src/vendor/cache.js'
					],
					'js/vendor/time.js' : [
						'js/src/vendor/time.js'
					],
					'js/vendor/rangyinputs.js' : [
						'js/src/vendor/rangyinputs.js'
					]
				}
			}
		}
	});

	// Load the plugin that provides the "uglify" task.
	grunt.loadNpmTasks('grunt-contrib-uglify');

	// Default task(s).
	grunt.registerTask('default', ['uglify']);

};