module.exports = {
	all: {
		files: {
			'js/indows-azure-storage-media-browser.min.js': ['js/indows-azure-storage-media-browser.js'],
			'js/indows-azure-storage-admin.min.js': ['js/indows-azure-storage-admin.js'],
		},
		options: {
			banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
			' * <%= pkg.homepage %>\n' +
			' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +

			' */\n',
			mangle: {
				except: ['jQuery']
			}
		}
	}
};
