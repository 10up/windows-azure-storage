module.exports = {
	main: {
		options: {
			mode: 'zip',
			archive: './release/drweil.<%= pkg.version %>.zip'
		},
		expand: true,
		cwd: 'release/<%= pkg.version %>/',
		src: ['**/*'],
		dest: 'drweil/'
	}
};