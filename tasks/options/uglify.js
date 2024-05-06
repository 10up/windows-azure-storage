module.exports = {
	all: {
		files: {
			'js/windows-azure-storage-media-browser.min.js': ['js/windows-azure-storage-media-browser.js'],
			'js/windows-azure-storage-admin.min.js': ['js/windows-azure-storage-admin.js'],
			'js/windows-azure-storage-media-replace.min.js': ['js/windows-azure-storage-media-replace.js'],
		},
		options: {
			banner: '',
			mangle: {
				reserved: ['jQuery']
			}
		}
	}
};
