module.exports = {
	options: {
		banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
		' * <%=pkg.homepage %>\n' +
		' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +

		' */\n'
	},
	minify: {
		expand: true,

		cwd: 'css/',
		src: ['css/windows-azure-storage.css'],

		dest: 'css/',
		ext: '.min.css'
	}
};
