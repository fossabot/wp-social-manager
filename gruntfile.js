/* eslint-env node */
module.exports = function(grunt) {

	'use strict';

	var adminDirCSS = './admin/css/',
		adminDirJS = './admin/js/',
		publicDirCSS = './public/css/',
		publicDirJS = './public/js/',

		csssrc = [
			{
				expand: true,
				cwd: adminDirCSS,
				dest: adminDirCSS,
				src: [
					'*.css',
					'!*.min.css',
					'!*-rtl.css'
				],
				ext: '.min.css'
			}, {
				expand: true,
				cwd: publicDirCSS,
				dest: publicDirCSS,
				src: [
					'*.css',
					'!*.min.css',
					'!*-rtl.css'
				],
				ext: '.min.css'
			}, {
				expand: true,
				cwd: './includes/customize/css/',
				dest: './includes/customize/css/',
				src: [
					'*.css',
					'!*.min.css',
					'!*-rtl.css'
				],
				ext: '.min.css'
			}
		],

		csssrcRTL = [
			{
				expand: true,
				cwd: adminDirCSS,
				dest: adminDirCSS,
				src: [
					'*.css',
					'*.min.css',
					'!*-rtl.css'
				],
				ext: '.min-rtl.css'
			}, {
				expand: true,
				cwd: publicDirCSS,
				dest: publicDirCSS,
				src: [
					'*.css',
					'*.min.css',
					'!*-rtl.css'
				],
				ext: '.min-rtl.css'
			}
		],

		jssrc = [
			{
				expand: true,
				cwd: adminDirJS,
				dest: adminDirJS,
				src: [
					'*.js',
					'!*.min.js'
				],
				ext: '.min.js'
			}, {
				expand: true,
				cwd: publicDirJS,
				dest: publicDirJS,
				src: [
					'*.js',
					'!*.min.js'
				],
				ext: '.min.js'
			}, {
				expand: true,
				cwd: './includes/customize/js/',
				dest: './includes/customize/js/',
				src: [
					'*.js',
					'!*.min.js'
				],
				ext: '.min.js'
			}
		],

		phpsrc = [
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
		];

	grunt.initConfig({

		pkg: grunt.file.readJSON('package.json'),

		config: {
			plugin_path: '/srv/www/wordpress-default/public_html/wp-content/plugins/<%= pkg.name %>' // Plugins path in VVV.
		},

		// Shell actions.
		shell: {
			readme: {
				command: 'cd ./dev-lib && ./generate-markdown-readme' // Generate the readme.md
			},
			phpunit: {
				command: 'vagrant ssh -c "cd <%= config.plugin_path %> && phpunit"'
			}
		},

		// Run Qunit test.
		qunit: {
			all: [ './tests/qunit/**/*.html' ]
		},

		// Run tasks whenever watched files change.
		watch: {

			scripts: {
				files: [
					adminDirJS + '*.js',
					publicDirJS + '*.js',
					'./includes/customize/js/*.js',
					'!' + adminDirJS + '*.min.js',
					'!' + publicDirJS + '*.min.js',
					'!./includes/customize/js/*.min.js'
				],
				tasks: ['scripts:dev'],
				options: {
					interrupt: true
				}
			},

			styles: {
				files: [
					adminDirCSS + '*.css',
					publicDirCSS + '*.css',
					'./includes/customize/css/*.css',
					'!' + adminDirCSS + '*.min.css',
					'!' + adminDirCSS + '*.min-rtl.css',
					'!' + publicDirCSS + '*.min.css',
					'!' + publicDirCSS + '*.min-rtl.css',
					'!./includes/customize/css/*.min.css',
					'!./includes/customize/css/*.min-rtl.css'
				],
				tasks: ['styles:dev'],
				options: {
					interrupt: true
				}
			},

			readme: {
				files: ['readme.txt'],
				tasks: ['shell:readme'],
				options: {
					interrupt: true
				}
			},

			textDomain: {
				files: phpsrc,
				tasks: [
					'checktextdomain',
					'makepot'
				],
				options: {
					interrupt: true
				}
			}
		},

		// JavaScript linting with ESLint.
		eslint: {
			options: {
				fix: true
			},
			target: [
				adminDirJS + '*.js',
				publicDirJS + '*.js'
			]
		},

		// Minify .js files.
		uglify: {
			dev: {
				options: {
					preserveComments: false,
					sourceMap: true
				},
				files: jssrc
			},
			build: {
				options: {
					preserveComments: false
				},
				files: jssrc
			}
		},

		// Minify .css files.
		cssmin: {
			dev: {
				options: {
					sourceMap: true
				},
				files: csssrc
			},
			rtl: {
				files: csssrcRTL
			},
			build: {
				files: csssrc
			}
		},

		// Transforming CSS LTR to RTL.
		rtlcss: {
			options: {
				map: false,
				saveUnmodified: false
			},
			target: {
				files: [
					{
						expand: true,
						cwd: adminDirCSS,
						dest: adminDirCSS,
						ext: '-rtl.css',
						src: ['*.css', '!*-rtl.css', '!*.min.css']
					}, {
						expand: true,
						cwd: publicDirCSS,
						dest: publicDirCSS,
						ext: '-rtl.css',
						src: ['*.css', '!*-rtl.css', '!*.min.css']
					}
				]
			}
		},

		// Add text domain to PHP files.
		addtextdomain: {
			target: {
				options: {
					textdomain: '<%= pkg.name %>', // Project text domain.
				},
				files: {
					src: phpsrc
	            }
	        }
	    },

		// Check textdomain errors.
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
				src: phpsrc,
				expand: true
			}
		},

		// Create .pot files for i18n.
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
					processPot: function(pot) {

						var translation,
							excluded_meta = [
								'Plugin Name of the plugin/theme',
								'Plugin URI of the plugin/theme',
								'Author of the plugin/theme',
								'Author URI of the plugin/theme'
							];

						for ( translation in pot.translations[''] ) {
							if ( 'undefined' !== typeof pot.translations[''][ translation ].comments.extracted ) {
								if ( excluded_meta.indexOf( pot.translations[''][ translation ].comments.extracted ) >= 0 ) {
									console.log( 'Excluded meta: ' + pot.translations[''][ translation ].comments.extracted );
									delete pot.translations[''][ translation ];
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

		'string-replace': {
			version: {
				files: {
					'./readme.txt': './readme.txt',
					'./composer.json': './composer.json',
					'./<%= pkg.name %>.php': './<%= pkg.name %>.php',
					'./includes/class-plugin.php': './includes/class-plugin.php'
				},
				options: {
					replacements: [
						{
							pattern: /\Stable tag: (.*)/g,
							replacement: 'Stable tag: <%= pkg.version %>'
						}, {
							pattern: /\Version: (.*)/g,
							replacement: 'Version: <%= pkg.version %>'
						}, {
							pattern: /\protected \$version = (.*)/g,
							replacement: 'protected $version = \'<%= pkg.version %>\';'
						}, {
							pattern: /\Requires at least: (.*)/g,
							replacement: 'Requires at least: <%= pkg.wordpress.requires_at_least %>'
						}, {
							pattern: /\Description: (.*)/g,
							replacement: 'Description: <%= pkg.description %>'
						}, {
							pattern: /\'WordPress\' => \'(.*)\'/g,
							replacement: '\'WordPress\' => \'<%= pkg.wordpress.requires_at_least %>\''
						}, {
							pattern: /\Tested up to: (.*)/g,
							replacement: 'Tested up to: <%= pkg.wordpress.tested_up_to %>'
						},
						{
							pattern: /\"version\": \"(.*)\"/g,
							replacement: '"version": "<%= pkg.version %>"'
						}
					]
				}
			}
		},

		// Build a deploy-able plugin.
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

		// Compress files and folders.
		compress: {
			build: {
				options: {
					archive: '<%= pkg.name %>.<%= pkg.version %>.zip'
				},
				files: [
					{
						expand: true,
						cwd: './build/',
						src: ['**'],

						// When the .zip file is uncompressed (e.g. 'ninecodes-social-media').
						dest: './<%= pkg.name %>/'
					}
				]
			}
		},

		// Clean files and folders.
		clean: {
			build: ['./build/'],
			zip: ['./<%= pkg.name %>*.zip']
		}
	});

	// Load tasks
	grunt.loadNpmTasks('grunt-shell');
	grunt.loadNpmTasks('grunt-checktextdomain');
	grunt.loadNpmTasks('grunt-eslint');
	grunt.loadNpmTasks('grunt-rtlcss');
	grunt.loadNpmTasks('grunt-string-replace');
	grunt.loadNpmTasks('grunt-wp-i18n');
	grunt.loadNpmTasks('grunt-contrib-qunit');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-contrib-compress');
	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-contrib-watch');

	// Register grunt default tasks.
	grunt.registerTask('default', [
		'styles:dev',
		'scripts:dev',
		'wordpress',
		'watch'
	]);

	// Version bump.
	grunt.registerTask('version', [
		'string-replace:version',
		'shell:readme'
	]);

	// Build the plugin.
	grunt.registerTask('build', [
		'clean:build',
		'clean:zip',
		'styles:build',
		'scripts:build',
		'wordpress',
		'version',
		'copy:build'
	]);

	// Build and package the plugin.
	grunt.registerTask('build:package', [
		'build',
		'compress:build',
		'clean:build'
	]);

	// Run test unit.
	grunt.registerTask('test', [
		'shell:phpunit',
		'qunit'
	]);

	/**
	 * ==================================================
	 * Register JavaScript specific tasks
	 * ==================================================
	 */

	// "Development" stage.
	grunt.registerTask('styles:dev', [
		'rtlcss',
		'cssmin:dev',
		'cssmin:rtl'
	]);

	// "Production" stage.
	grunt.registerTask('styles:build', [
		'rtlcss',
		'cssmin:build',
		'cssmin:rtl'
	]);

	/**
	 * ==================================================
	 * Register JavaScript specific tasks
	 * ==================================================
	 */

	// "Development" stage.
	grunt.registerTask('scripts:dev', [
		'eslint',
		'qunit',
		'uglify:dev'
	]);

	// "Production" stage.
	grunt.registerTask('scripts:build', [
		'eslint',
		'qunit',
		'uglify:build'
	]);

	/**
	 * ==================================================
	 * Register WordPress specific tasks
	 * ==================================================e
	 */

	// Check and compile WordPress files.
	grunt.registerTask('wordpress', [
		'shell:phpunit',
		'addtextdomain',
		'checktextdomain',
		'version',
		'makepot'
	]);
};
