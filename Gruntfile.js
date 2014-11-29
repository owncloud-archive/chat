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
						'js/src/vendor/**/*.js'
					]
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
		concat : {
			dist : {
				src: [
					'js/src/chat.js',
					'js/src/**/*.js',
					'js/src/vendor/**/*.js'
				],
				dest : 'js/main.min.js'
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.registerTask('default', ['uglify', 'cssmin']);

};