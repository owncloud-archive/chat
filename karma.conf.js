// Karma configuration
// Generated on Mon Sep 15 2014 12:59:56 GMT+0200 (CEST)

module.exports = function(config) {
	config.set({

		// base path that will be used to resolve all patterns (eg. files, exclude)
		basePath: '',


		// frameworks to use
		// available frameworks: https://npmjs.org/browse/keyword/karma-adapter
		frameworks: ['jasmine'],


		// list of files / patterns to load in the browser
		files: [
			'js/test/mocks/OC.js',
			'vendor/angular/angular.js',
			'vendor/angular-sanitize/angular-sanitize.js',
			'vendor/angular-enhance-text/build/angular-enhance-text.js',
			'vendor/angular-mocks/angular-mocks.js',
			'vendor/angular-resource/angular-resource.js',
			'vendor/jquery/dist/jquery.js',
			'vendor/jquery-autosize/jquery.autosize.js',
			'vendor/rangyinputs-jquery-src/index.js',
			'vendor/emojione/lib/js/emojione.min.js',
			'js/src/app/**/*.js',
			'js/src/app/*.js',
			'js/src/vendor/*.js',
			'js/src/vendor/**/*.js',
			'js/src/app/*.js',
			'js/src/*.js',
			'js/test/*.js',
			'js/test/**/*.js',
		],


		// list of files to exclude
		exclude: [
		],


		// preprocess matching files before serving them to the browser
		// available preprocessors: https://npmjs.org/browse/keyword/karma-preprocessor
		preprocessors: {
		},


		// test results reporter to use
		// possible values: 'dots', 'progress'
		// available reporters: https://npmjs.org/browse/keyword/karma-reporter
		reporters: ['progress'],


		// web server port
		port: 9876,


		// enable / disable colors in the output (reporters and logs)
		colors: true,


		// level of logging
		// possible values: config.LOG_DISABLE || config.LOG_ERROR || config.LOG_WARN || config.LOG_INFO || config.LOG_DEBUG
		logLevel: config.LOG_INFO,


		// enable / disable watching file and executing tests whenever any file changes
		autoWatch: true,


		// start these browsers
		// available browser launchers: https://npmjs.org/browse/keyword/karma-launcher
		browsers: ['Firefox'],


		// Continuous Integration mode
		// if true, Karma captures browsers, runs the tests and exits
		singleRun: true
  });
};
