/* eslint-env node */
module.exports = function( grunt ) {

	'use strict';

	var csssrc = [ {
			expand: true,
			cwd: './admin/css/',
			src: [
				'*.css',
				'!*.min.css'
			],
			dest: './admin/css/',
			ext: '.min.css'
		}, {
			expand: true,
			cwd: './public/css/',
			src: [
				'*.css',
				'!*.min.css'
			],
			dest: './public/css/',
			ext: '.min.css'
		} ],

		jssrc = [ {
			expand: true,
			cwd: './public/js/',
			src: [
				'*.js',
				'!*.min.js'
			],
			dest: 'public/js/',
			ext: '.min.js'
		}, {
			expand: true,
			cwd: './public/js/',
			src: [
				'*.js',
				'!*.min.js'
			],
			dest: 'public/js/',
			ext: '.min.js'
		} ];

	grunt.initConfig( {

		pkg: grunt.file.readJSON( 'package.json' ),

		// JavaScript linting with ESLint.
		eslint: {
			options: {
				fix: true
			},
			target: [
				'./public/js/*.js',
				'./admin/js/*.js'
			]
		},

		// Minify .js files.
		uglify: {
			options: {
				preserveComments: false
			},
			dev: {
				sourceMap: true,
				files: jssrc
			},
			build: {
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
			build: {
				files: csssrc
			}
		},

		// Check textdomain errors.
		checktextdomain: {
			options:{
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
				src:	[
					'*.php', // Include all files
					'**/*.php', // Include all files
					'!**/butterbean/**', // Exclude build/
					'!**/pepperplane/**', // Exclude build/
					'!build/**', // Exclude build/
					'!node_modules/**', // Exclude node_modules/
					'!tests/**' // Exclude tests/
				],
				expand: true
			}
		},

		// Build a deploy-able plugin
		copy: {
			build: {
				src: [
					'*.php',
					'admin/**',
					'public/**',
					'widgets/**',
					'includes/**',
					'readme.txt',
					'!**/*.map',
					'!**/changelog.md',
					'!**/readme.md',
					'!**/README.md',
					'!**/contributing.md'
				],
				dest: 'build',
				expand: true,
				dot: false
			}
		},

		// Shell actions
		shell: {
			readme: {
				command: 'cd ./dev-lib && ./generate-markdown-readme' // Generate the readme.md
			}
		}
	} );

	// Load tasks
	grunt.loadNpmTasks( 'grunt-shell' );
	grunt.loadNpmTasks( 'grunt-checktextdomain' );
	grunt.loadNpmTasks( 'grunt-eslint' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
	grunt.loadNpmTasks( 'grunt-contrib-copy' );

	// Register task to compile "readme.txt" to "readme.md"
	grunt.registerTask( 'readme', [
		'shell:readme'
	] );

	// Register WordPress specific tasks.
	grunt.registerTask( 'wordpress', [
		'readme',
		'checktextdomain'
	] );

	// Register stylesheet specific tasks in "development" stage.
	grunt.registerTask( 'styles:dev', [
		'cssmin:dev'
	] );

	// Register stylesheet specific tasks for "build".
	grunt.registerTask( 'styles:build', [
		'cssmin:build'
	] );

	// Register JavaScript specific tasks for "development" stage.
	grunt.registerTask( 'javascript:dev', [
		'eslint',
		'uglify:dev'
	] );

	// Register JavaScript specific tasks for "build" stage.
	grunt.registerTask( 'javascript:build', [
		'eslint',
		'uglify:build'
	] );

	// Register grunt default tasks.
	grunt.registerTask( 'default', [
		'wordpress',
		'styles:dev',
		'javascript:dev'
	] );

	// Build package.
	grunt.registerTask( 'build', [
		'wordpress',
		'styles:build',
		'javascript:build',
		'copy'
	] );
};
