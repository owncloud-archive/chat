module.exports = function(grunt) {

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		uglify: {
			options: {
				banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n'
			},
			build: {
				files: {
					'build/chat/js/chat.min.js': [
						'js/**/*js',
						'!js/vendor/**/*.js'
					],
					'build/chat/js/vendor/angular.js' : [
						'js/vendor/anguler.js'
					],
					'build/chat/js/vendor/angular-santitize.js' : [
						'js/vendor/angular-santitize.js'
					],
					'build/chat/js/vendor/jquery.autosize.js' : [
						'js/vendor/jquery.autosize.js'
					],
					'build/chat/js/vendor/cache.js' : [
						'js/vendor/cache.js'
					],
					'build/chat/js/vendor/time.js' : [
						'js/vendor/time.js'
					],

					'build/chat/js/vendor/rangyinputs.js' : [
						'js/vendor/anguler.js'
					],
				}
			}
		}
	});

	// Load the plugin that provides the "uglify" task.
	grunt.loadNpmTasks('grunt-contrib-uglify');

	// Default task(s).
	grunt.registerTask('default', ['uglify']);

};