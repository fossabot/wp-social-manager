/* eslint-env node */
module.exports = function(grunt) {

	'use strict';
	var stage = grunt.option('stage') || 'release',

		adminDirCSS = './admin/css/',
		adminDirJS = './admin/js/',
		publicDirCSS = './public/css/',
		publicDirJS = './public/js/',

		csssrc = [{
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
				'!*-rtl.css',
			],
			ext: '.min.css'
		}],

		csssrcRTL = [{
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
		}],


		jssrc = [{
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
		}];

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

		// Run tasks whenever watched files change.
		watch: {
			scripts: {
				files: [
					adminDirJS + '*.js',
					publicDirJS + '*.js',
					'!' + adminDirJS + '*.min.js',
					'!' + publicDirJS + '*.min.js'
				],
				tasks: ['scripts:dev'],
				options: {
					interrupt: true,
				},
			},
			styles: {
				files: [
					adminDirCSS + '*.css',
					publicDirCSS + '*.css',
					'!' + adminDirCSS + '*.min.css',
					'!' + adminDirCSS + '*.min-rtl.css',
					'!' + publicDirCSS + '*.min.css',
					'!' + publicDirCSS + '*.min-rtl.css'
				],
				tasks: ['styles:dev'],
				options: {
					interrupt: true,
				},
			},
			readme: {
				files: ['readme.txt'],
				tasks: ['shell:readme'],
				options: {
					interrupt: true,
				},
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
			reg: {
				files: [{
					expand: true,
					cwd: adminDirCSS,
					dest: adminDirCSS,
					ext: '-rtl.css',
					src: [
						'*.css',
						'!*-rtl.css',
						'!*.min.css'
					]
				}, {
					expand: true,
					cwd: publicDirCSS,
					dest: publicDirCSS,
					ext: '-rtl.css',
					src: [
						'*.css',
						'!*-rtl.css',
						'!*.min.css'
					]
				}]
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
				src: [
					'*.php', // Include all files
					'**/*.php', // Include all files
					'!includes/bb-metabox/**', // Exclude sub-modules/
					'!includes/bb-metabox-extend/**', // Exclude sub-modules/
					'!includes/wp-settings/**', // Exclude sub-modules/
					'!<%= pkg.name %>/**', // Exclude build/
					'!build/**', // Exclude build/
					'!node_modules/**', // Exclude node_modules/
					'!tests/**' // Exclude tests/
				],
				expand: true
			}
		},

		'string-replace': {
			version: {
				files: {
					'./readme.txt': './readme.txt',
					'./<%= pkg.name %>.php': './<%= pkg.name %>.php',
					'./includes/class-plugin.php': './includes/class-plugin.php'
				},
				options: {
					replacements: [{
						pattern: /\Stable tag: (.*)/g,
						replacement: 'Stable tag: <%= pkg.version %>'
					}, {
						pattern: /\Version: (.*)/g,
						replacement: 'Version: <%= pkg.version %>'
					},{
						pattern: /\protected \$version = (.*)/g,
						replacement: 'protected $version = \'<%= pkg.version %>\';'
					}, {
						pattern: /\Requires at least: (.*)/g,
						replacement: 'Requires at least: <%= pkg.wordpress.requires_at_least %>'
					}, {
						pattern: /\Tested up to: (.*)/g,
						replacement: 'Tested up to: <%= pkg.wordpress.tested_up_to %>'
					}]
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
				files: [{
					expand: true,
					cwd: './build/',
					src: ['**'],

					// When the .zip file is uncompressed (e.g. 'ninecodes-social-media').
					dest: './<%= pkg.name %>/'
				}, ]
			},
		},

		// Clean files and folders.
		clean: {
			build: ['./build/'],
			zip: ['./<%= pkg.name %>*.zip']
		},

		// Deploys a build directory to the WordPress SVN repo.
		wp_deploy: {
			release: {
				options: {
					plugin_slug: '<%= pkg.name %>',
					build_dir: 'build',
					assets_dir: 'wp-assets'
				}
			},

			// Only commit the assets directory.
			assets: {
				options: {
					plugin_slug: '<%= pkg.name %>',
					build_dir: 'build',
					assets_dir: 'wp-assets',
					deploy_trunk: false

				}
			},

			// Only deploy to trunk (e.g. when only updating the 'Tested up to' value and not deploying a release).
			trunk: {
				options: {
					plugin_slug: '<%= pkg.name %>',
					build_dir: 'build',
					assets_dir: 'wp-assets',
					deploy_tag: false
				}
			}
		}
	});

	// Load tasks
	grunt.loadNpmTasks('grunt-shell');
	grunt.loadNpmTasks('grunt-checktextdomain');
	grunt.loadNpmTasks('grunt-eslint');
	grunt.loadNpmTasks('grunt-rtlcss');
	grunt.loadNpmTasks('grunt-string-replace');
	grunt.loadNpmTasks('grunt-wp-deploy');

	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-contrib-compress');
	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-contrib-watch');

	// Register grunt default tasks.
	grunt.registerTask('default', [
		'wordpress',
		'styles:dev',
		'scripts:dev',
		'watch'
	]);

	// Build the plugin.
	grunt.registerTask('build', [
		'clean:zip',
		'wordpress',
		'styles:prod',
		'scripts:prod',
		'copy:build'
	]);

	// Build and package the plugin.
	grunt.registerTask('package', [
		'build',
		'compress:build',
		'clean:build'
	]);

	/**
	 * ==================================================
	 * Register Test specific tasks
	 * ==================================================
	 */
	grunt.registerTask('test', [
		'shell:phpunit'
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
	grunt.registerTask('styles:prod', [
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
		'uglify:dev'
	]);

	// "Production" stage.
	grunt.registerTask('scripts:prod', [
		'eslint',
		'uglify:build'
	]);

	/**
	 * ==================================================
	 * Register WordPress specific tasks
	 * ==================================================
	 */

	// Check and compile WordPress files.
	grunt.registerTask('wordpress', [
		'version',
		'shell:readme',
		'shell:phpunit',
		'checktextdomain'
	]);

	// Check and compile WordPress files.
	grunt.registerTask('version', [
		'string-replace:version',
		'shell:readme'
	]);

	// Deploy to WordPress.org repository.
	grunt.registerTask('deploy', [
		'build',
		'wp_deploy:' + stage,
		'clean:build'
	]);
};
