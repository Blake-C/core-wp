const isDocker = process.env.APP_ENV === 'docker'
const port = parseInt(process.env.BROWSER_SYNC_PORT || (isDocker ? '3000' : '3010'))

module.exports = {
	proxy: {
		target: isDocker ? 'http://nginx' : 'http://localhost',
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
	// Only watch the frontend stylesheet. Watching all *.min.css files causes
	// browser-sync to fire injection events for editor-styles and login-admin,
	// which have no <link> on frontend pages. Those fall back to a full page
	// reload, which cancels the in-flight global-styles fetch and leaves it
	// stuck as "pending" in DevTools.
	files: ['assets/css/global-styles.min.css'],
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
