module.exports = {
	options: {
		banner: ''
	},
	minify: {
		expand: true,

		cwd: 'css/',
		src: ['windows-azure-storage.css'],

		dest: 'css/',
		ext: '.min.css'
	}
};
