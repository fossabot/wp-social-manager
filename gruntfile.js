/* eslint-env node */
module.exports = function (grunt) {

	'use strict';

	grunt.initConfig({

		/**
		 * Extract the package.json,
		 *
		 * @see {@link https://gruntjs.com/api/grunt.file#grunt.file.readjson|grunt.file Tutorial}
		 * @type {Object}
		 */
		pkg: grunt.file.readJSON('package.json'),

		/**
		 * Defines the project working directories
		 *
		 * @type {Object}
		 */
		dir: {
			pluginPath: '/srv/www/wordpress-develop/public_html/src/wp-content/plugins/<%= pkg.name %>',
			adminCSS: 'admin/css/',
			adminJS: 'admin/js/',
			publicCSS: 'public/css/',
			publicJS: 'public/js/',
		},

		/**
		 * Define PHP source files
		 *
		 * @type {Array}
		 */
		php: [
			'*.php',
			'**/*.php',
			'!docs/**',
			'!includes/ogp/**',
			'!includes/bb-metabox/**',
			'!includes/bb-metabox-extend/**',
			'!includes/wp-settings/**',
			'!<%= pkg.name %>/**',
			'!build/**',
			'!node_modules/**',
			'!tests/**'
		],

		/**
		 * Run command line on shell
		 *
		 * @type {Object}
		 */
		shell: {
			readme: {
				command: 'cd ./dev-lib && ./generate-markdown-readme' // Generate the readme.md
			},
			phpunit: {
				command: 'vagrant ssh -c "cd <%= dir.pluginPath %> && phpunit"'
			}
		},

		/**
		 * Run QUnit test
		 *
		 * @see {@link https://github.com/gruntjs/grunt-contrib-qunit}
		 * @type {Object}
		 */
		qunit: {
			all: ['./tests/qunit/**/*.html']
		},

		/**
		 * Run tasks when files has changed
		 *
		 * @see {@link https://github.com/gruntjs/grunt-contrib-watch}
		 * @type {Object}
		 */
		watch: {
			options: {
				interrupt: true
			},
			scripts: {
				files: [
					'<%= dir.publicJS %>**/*.js',
					'<%= dir.adminJS %>**/*.js',
					'!**/*.min.js',
				],
				tasks: ['scripts:dev'],
			},
			styles: {
				files: [
					'<%= dir.publicCSS %>**/*.less',
					'<%= dir.adminCSS %>**/*.less',
				],
				tasks: ['styles:dev'],
			},
			readme: {
				files: ['readme.txt'],
				tasks: ['shell:readme'],
			},
			textDomain: {
				files: '<%= php %>',
				tasks: [
					'newer:checktextdomain',
					'makepot'
				],
			}
		},

		/**
		 * JavaScript linting with ESLint.
		 *
		 * @see {@link https://github.com/sindresorhus/grunt-eslint}
		 * @type {Object}
		 */
		eslint: {
			options: {
				fix: true
			},
			target: [
				'<%= dir.publicJS %>**/*.js',
				'<%= dir.adminJS %>**/*.js',
				'!<%= dir.publicJS %>**/*.min.js',
				'!<%= dir.adminJS %>**/*.min.js',
			]
		},

		/**
		 * Minify JavaScript files files
		 *
		 * @see {@link https://github.com/gruntjs/grunt-contrib-uglify}
		 * @type {Object}
		 */
		uglify: {
			options: {
				preserveComments: false,
			},
			files: [{
				'<%= dir.adminJS %>scripts.min.js': [
					'<%= dir.adminJS %>*.js',
					'!<%= dir.adminJS %>*.min.js',
				],
				'<%= dir.publicJS %>app.min.js': [
					'<%= dir.publicJS %>*.js',
					'!<%= dir.publicJS %>*.min.js',
				],
				'<%= dir.publicJS %>scripts.min.js': [
					'<%= dir.publicJS %>*.js',
					'!<%= dir.publicJS %>*.min.js',
				],
			}],

			dev: {
				options: {
					sourceMap: true
				},
				files: '<%= uglify.files %>'
			},
			build: {
				files: '<%= uglify.files %>'
			}
		},

		/**
		 * Compile LESS files
		 *
		 * @see {@link https://github.com/gruntjs/grunt-contrib-less}
		 * @type {Object}
		 */
		less: {
			options: {
				plugins: [
					require('less-plugin-group-css-media-queries'), // eslint-disable-line global-require
					new(require('less-plugin-clean-css'))({ // eslint-disable-line global-require
						compatibility: 'ie9'
					}),
					new(require('less-plugin-autoprefix'))({ // eslint-disable-line global-require
						browsers: [
							'last 2 version',
							'> 1%',
							'ie >= 9',
							'ie_mob >= 10',
							'ff >= 30',
							'chrome >= 34',
							'safari >= 7',
							'opera >= 23',
							'ios >= 7',
							'android >= 4',
							'bb >= 10'
						]
					}),
				]
			},

			dev: {
				options: {
					compress: false,
					sourceMap: true,
				},
				files: [{
					'<%= dir.publicCSS %>style.css': [
						'<%= dir.publicCSS %>*.less'
					],
					'<%= dir.adminCSS %>style.css': [
						'<%= dir.adminCSS %>*.less'
					]
				}]
			},
		},

		/**
		 * Transforming CSS LTR to RTL.
		 *
		 * @see {@link https://github.com/MohammadYounes/grunt-rtlcss}
		 * @type {Object}
		 */
		rtlcss: {
			options: {
				map: false,
				saveUnmodified: false
			},
			target: {
				files: [{
					expand: true,
					cwd: '<%= dir.adminCSS %>',
					dest: '<%= dir.adminCSS %>',
					ext: '-rtl.css',
					src: ['*.css', '!*-rtl.css']
				}, {
					expand: true,
					cwd: '<%= dir.publicCSS %>',
					dest: '<%= dir.publicCSS %>',
					ext: '-rtl.css',
					src: ['*.css', '!*-rtl.css']
				}]
			}
		},

		/**
		 * Add text domain to PHP files.
		 *
		 * @see {@link https://github.com/cedaro/grunt-wp-i18n}
		 * @type {Object}
		 */
		addtextdomain: {
			target: {
				options: {
					textdomain: '<%= pkg.name %>', // Project text domain.
				},
				files: {
					src: '<%= php %>'
				}
			}
		},

		/**
		 * Check textdomain errors.
		 *
		 * @see {@link https://github.com/stephenharris/grunt-checktextdomain}
		 * @type {Object}
		 */
		checktextdomain: {
			options: {
				text_domain: '<%= pkg.name %>',
				keywords: [
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'esc_attr__:1,2d',
					'esc_attr_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'_ex:1,2c,3d',
					'_n:1,2,4d',
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d'
				]
			},
			files: {
				src: '<%= php %>',
				expand: true
			}
		},

		/**
		 * Create .pot files for i18n.
		 *
		 * @see {@link https://github.com/cedaro/grunt-wp-i18n}
		 * @type {Object}
		 */
		makepot: {
			target: {
				options: {
					cwd: './',
					type: 'wp-plugin',
					domainPath: './languages',
					updateTimestamp: false,
					mainFile: '<%= pkg.name %>.php',
					potFilename: '<%= pkg.name %>.pot',
					potHeaders: {
						'poedit': true, // Includes common Poedit headers.
						'x-poedit-keywordslist': true // Include a list of all possible gettext functions.
					},
					include: [
						'admin/.*',
						'public/.*',
						'includes/.*',
						'widgets/.*',
						'<%= pkg.name %>.php'
					],
					exclude: [
						'.js',
						'admin/js/.*',
						'public/js/.*',
						'node_modules/.*',
						'build/.*',
						'dev-lib/.*',
						'includes/ogp/.*',
						'includes/bb-metabox/.*',
						'includes/bb-metabox-extend/.*',
						'includes/includes/wp-settings/.*'
					],
					processPot: function (pot) {

						var translation,
							excluded_meta = [
								'Plugin Name of the plugin/theme',
								'Plugin URI of the plugin/theme',
								'Author of the plugin/theme',
								'Author URI of the plugin/theme'
							];

						for (translation in pot.translations['']) {
							if ('undefined' !== typeof pot.translations[''][translation].comments.extracted) {
								if (excluded_meta.indexOf(pot.translations[''][translation].comments.extracted) >= 0) {
									console.log('Excluded meta: ' + pot.translations[''][translation].comments.extracted);
									delete pot.translations[''][translation];
								}
							}
						}

						pot.headers['report-msgid-bugs-to'] = 'https://github.com/ninecodes/social-manager/issues';
						pot.headers['last-translator'] = 'NineCodes <admin@ninecodes.com>';
						pot.headers['language-team'] = 'NineCodes <admin@ninecodes.com>';

						return pot;
					}
				}
			}
		},

		/**
		 * Replace string in the file.
		 *
		 * @type {Object}
		 */
		replace: {
			version: {
				src: [
					'./readme.txt',
					'./composer.json',
					'./<%= pkg.name %>.php',
					'./includes/class-plugin.php',
				],
				overwrite: true,
				replacements: [{
					from: /Stable tag: (.*)/g,
					to: 'Stable tag: <%= pkg.version %>'
				}, {
					from: /Version: (.*)/g,
					to: 'Version: <%= pkg.version %>'
				}, {
					from: /protected \$version = (.*)/g,
					to: 'protected $version = \'<%= pkg.version %>\';'
				}, {
					from: /Requires at least: (.*)/g,
					to: 'Requires at least: <%= pkg.wordpress.requires_at_least %>'
				}, {
					from: /Description: (.*)/g,
					to: 'Description: <%= pkg.description %>'
				}, {
					from: /\'WordPress\' => \'(.*)\'/g,
					to: '\'WordPress\' => \'<%= pkg.wordpress.requires_at_least %>\''
				}, {
					from: /Tested up to: (.*)/g,
					to: 'Tested up to: <%= pkg.wordpress.tested_up_to %>'
				},
				{
					from: /\"version\": \"(.*)\"/g,
					to: '"version": "<%= pkg.version %>"'
				},
				{
					from: /public \$version\s=\s\'(.*)\'/g,
					to: 'public \$version = \'<%= pkg.version %>\''
				}]
			}
		},

		/**
		 * Build a deploy-able plugin.
		 *
		 * @see {@link https://github.com/gruntjs/grunt-contrib-copy}
		 * @type {Object}
		 */
		copy: {
			build: {
				src: [
					'*.php',
					'admin/**',
					'public/**',
					'widgets/**',
					'includes/**',
					'languages/**',
					'readme.txt',
					'!**/*.less',
					'!**/*.map',
					'!**/changelog.md',
					'!**/readme.md',
					'!**/README.md',
					'!**/contributing.md'
				],
				dest: './build/',
				expand: true,
				dot: false
			}
		},

		/**
		 * Compress files and folders into a .zip file.
		 *
		 * @see {@link https://github.com/gruntjs/grunt-contrib-compress}
		 * @type {Object}
		 */
		compress: {
			build: {
				options: {
					archive: '<%= pkg.name %>.<%= pkg.version %>.zip'
				},
				files: [{
					expand: true,
					cwd: './build/',
					src: ['**'],

					// When the .zip file is uncompressed (e.g. 'ninecodes-social-media').
					dest: './<%= pkg.name %>/'
				}]
			}
		},

		/**
		 * Remove files and folders
		 *
		 * @see {@link https://github.com/gruntjs/grunt-contrib-clean}
		 * @type {Object}
		 */
		clean: {
			build: ['./build/'],
			zip: ['./<%= pkg.name %>*.zip']
		},

		/**
		 * Desktop notification
		 *
		 * @see {@link https://github.com/dylang/grunt-notify}
		 * @type {Object}
		 */
		notify_hooks: {
			options: {
				enabled: true,
				max_jshint_notifications: 5,
				title: "Social Manager",
				success: true,
				duration: 3
			}
		}
	});

	// Load tasks
	grunt.loadNpmTasks('grunt-shell');
	grunt.loadNpmTasks('grunt-checktextdomain');
	grunt.loadNpmTasks('grunt-eslint');
	grunt.loadNpmTasks('grunt-rtlcss');
	grunt.loadNpmTasks('grunt-text-replace');
	grunt.loadNpmTasks('grunt-wp-i18n');
	grunt.loadNpmTasks('grunt-notify');
	grunt.loadNpmTasks('grunt-newer');
	grunt.loadNpmTasks('grunt-contrib-qunit');
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-contrib-compress');
	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-contrib-watch');

	grunt.task.run('notify_hooks');

	// Register grunt default tasks.
	grunt.registerTask('default', [
		'styles:dev',
		'scripts:dev',
		'wordpress',
		'watch'
	]);

	// Version bump.
	grunt.registerTask('version', [
		'replace:version',
		'shell:readme'
	]);

	// Build the plugin.
	grunt.registerTask('build', [
		'clean:build',
		'clean:zip',
		'styles',
		'scripts',
		'wordpress',
		'version',
		'copy:build'
	]);

	// Build and package the plugin into a .zip file.
	grunt.registerTask('build:package', [
		'build',
		'compress:build',
		'clean:build'
	]);

	/**
	 * ==================================================
	 * Register Test specific tasks
	 * ==================================================
	 */

	// Run "phpunit" in Vagrant container.
	grunt.registerTask('phpunit', [
		'shell:phpunit',
	]);

	// Run Unit Test.
	grunt.registerTask('test', [
		'phpunit',
		'qunit'
	]);

	// Run Lint.
	grunt.registerTask('lint', [
		'newer:eslint'
	]);

	/**
	 * ==================================================
	 * Register JavaScript specific tasks
	 * ==================================================
	 */

	// "Development" stage.
	grunt.registerTask('styles:dev', [
		'newer:less:dev',
		'newer:rtlcss',
	]);

	// "Build/Production" stage.
	grunt.registerTask('styles', [
		'newer:less',
		'newer:rtlcss',
	]);

	/**
	 * ==================================================
	 * Register JavaScript specific tasks
	 * ==================================================
	 */

	// "Development" stage.
	grunt.registerTask('scripts:dev', [
		'newer:eslint',
		'newer:uglify:dev'
	]);

	// "Build/Production" stage.
	grunt.registerTask('scripts', [
		'newer:eslint',
		'newer:uglify:build'
	]);

	/**
	 * ==================================================
	 * Register WordPress specific tasks
	 * ==================================================e
	 */

	// Check and compile WordPress files.
	grunt.registerTask('wordpress', [
		'phpunit',
		'newer:addtextdomain',
		'newer:checktextdomain',
		'version',
		'makepot'
	]);
};
