module.exports = function(grunt) {

	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		uglify: {
			options: {
				banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n',
				beautify: false,
				mangle: false
			},
			build: {
				files: {
					'js/app.min.js': [
						'js/app/src/**/*.js',
						'js/app/src/*.js',
						'js/src/**/*.js',
						'js/src/*.js',
					],
					'js/integrated.min.js': [
						'js/integrated/src/**/*.js',
						'js/integrated/src/*.js',
						'js/src/**/*.js',
						'js/src/*.js',
					],
					'js/admin.min.js' : [
						'js/admin/src/*.js'
					],
					'js/error.min.js' : [
						'js/error/src/*.js'
					],
					'vendor/all.min.js' : [
						'vendor/angular/angular.min.js',
						'vendor/angular-enhance-text/build/angular-enhance-text.min.js',
						'vendor/angular-resource/angular-resource.min.js',
						'vendor/angular-sanitize/angular-sanitize.min.js',
						'vendor/jquery-autosize/jquery.autosize.min.js',
						'vendor/moment/min/moment.min.js',
						'vendor/rangyinputs-jquery-src/index.js',
						'vendor/strophe/strophe.min.js',
						'vendor/emojione/lib/js/emojione.min.js',
					]
				}
			}
		},
		cssmin: {
			combine: {
				files: {
					'css/main.min.css': [
						'css/src/*.css',
						'!css/admin',
						'!css/integrated'
					],
					'css/admin.min.css' : [
						'css/admin/src/*.css'
					],
					'css/integrated.min.css' : [
						'css/integrated/src/*.css'
					],
					'css/error.min.css' : [
						'css/error/src/*.css'
					]
				}
			}
		},	
		
		karma: {
			integrated: {
				options: {
					files: [
						'vendor/jquery/dist/jquery.js',
						'vendor/jquery-autosize/jquery.autosize.js',
						'js/test/mocks/OC.js',
						'vendor/all.min.js',
						'vendor/angular-mocks/angular-mocks.js',
						'js/integrated.min.js',
						'js/test/**/*.js',
					],
					frameworks: [
						'jasmine'
					],
					reporters: [
						'progress'
					],
					port: 9876,
					browsers: [
						'Firefox'
					],
					singleRun: true,
				}
			},
			app: {
				options: {
					files: [
						'vendor/jquery/dist/jquery.js',
						'vendor/jquery-autosize/jquery.autosize.js',
						'js/test/mocks/OC.js',
						'vendor/all.min.js',
						'vendor/angular-mocks/angular-mocks.js',
						'js/app.min.js',
						'js/test/**/*.js',
					],
					frameworks: [
						'jasmine'
					],
					reporters: [
						'progress'
					],
					port: 9876,
					browsers: [
						'Firefox'
					],
					singleRun: true
				}
			}
		},
		htmlmin: {
			dist: {
				options: {
					removeComments: true,
					collapseWhitespace: true
				},
				files: {
					'integrated.min.html': 'integrated.html'
				}
			}
		},
		copy: {
			main: {
				files: [
					{
						expand: true, src: ['./**'], dest: 'dist/'
					}
				]
			}
		},
		clean : [
			"dist/upload.sh",
			"dist/composer.json",
			"dist/composer.lock",
			"dist/vendor/emojione/assets/svg",
			"dist/vendor/emojione/assets/png",
			"dist/.npm",
			"dist/.node-gyp",
			"dist/node_modules",
			"dist/.bowerrc",
			"dist/bower.json",
			"dist/.git*",
			"dist/.scrutinizer.yml",
			"dist/.travis.yml",
			"dist/Gruntfile.js",
			"dist/karma.conf.js",
			"dist/Makefile",
			"dist/package.json",
			"dist/build.sh",
			"dist/tests",
		],
		compress : {
			main: {
				options: {
					"archive": "build.zip"
				},
				"files": [
					{expand: true,src: ['**'], cwd: "dist", dest: 'chat/'}
				]
			}
		},
		watch: {
			files: ['./**.*', '!./**.min.*'],
			tasks: ['uglify', 'cssmin', 'htmlmin']
		},
		phpunit: {
			unit: {
				dir: './tests/unit/app',
				dir: './tests/unit/controller',
				options: {
					bootstrap: './tests/unit/autoloader.php'
				}
			},
			integration: {
				dir: './tests/integration/lib/och/db',
				options: {
					bootstrap: '../../lib/base.php'
				}
			},
			options: {
				bin: './vendor/bin/phpunit',
				noConfiguration: true
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-contrib-compress');
	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-contrib-htmlmin');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-phpunit');
	grunt.loadNpmTasks('grunt-karma');
	grunt.registerTask('default', ['uglify', 'cssmin', 'htmlmin']);
	grunt.registerTask('dist', ['uglify', 'cssmin', 'htmlmin', 'copy', 'clean', 'compress']);
	grunt.registerTask('tests', ['karma', 'phpunit']);
};
