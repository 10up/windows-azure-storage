module.exports = {
	options: {
		stripBanners: true,
			banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
		' * <%= pkg.homepage %>\n' +
		' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +

		' */\n'
	},
	mediabrowser: {
		src: [
			'js/src/windows-azure-storage-media-browser.js'
		],
		dest: 'js/windows-azure-storage-media-browser.js'
	},
	admin: {
		src: [
			'js/src/windows-azure-storage-admin.js'
		],
		dest: 'js/windows-azure-storage-admin.js'
	}
};
