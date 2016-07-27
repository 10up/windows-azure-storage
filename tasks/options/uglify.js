module.exports = {
	all: {
		files: {
			'js/windows-azure-storage-media-browser.min.js': ['js/windows-azure-storage-media-browser.js'],
			'js/windows-azure-storage-admin.min.js': ['js/windows-azure-storage-admin.js'],
		},
		options: {
			banner: '',
			mangle: {
				except: ['jQuery']
			}
		}
	}
};
