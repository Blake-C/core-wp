const isDocker = process.env.APP_ENV === 'docker'
const port = parseInt(process.env.BROWSER_SYNC_PORT || (isDocker ? '3000' : '3010'))

module.exports = {
	proxy: {
		target: isDocker ? 'http://wordpress' : 'http://localhost',
		proxyOptions: isDocker
			? {
					changeOrigin: false,
					headers: { host: 'localhost' },
				}
			: {},
	},
	host: isDocker ? '0.0.0.0' : 'localhost',
	port: port,
	ui: { port: port + 1 },
	files: ['assets/css/**/*.min.css'],
	open: false,
	notify: false,
	rewriteRules: isDocker
		? [
				{
					match: /http:\/\/localhost\//g,
					replace: 'http://localhost:3000/',
				},
			]
		: [],
}
