import path from 'path'
import { fileURLToPath } from 'url'
import scriptsList from './theme_components/js/scripts-list.js'

const __dirname = path.dirname(fileURLToPath(import.meta.url))

const webpackConfig = env => {
	const mode = env.mode || 'production'
	return {
		mode,
		entry: scriptsList,
		output: {
			path: path.resolve(__dirname, env.output),
			filename: 'bundle.[name].js',
			// Clean output directory before each build (replaces manual rm -rf in scripts.sh).
			clean: true,
		},
		// Full source maps for production (debuggable in DevTools without exposing source).
		// Cheap module maps in development — much faster rebuilds, still line-accurate.
		devtool: mode === 'production' ? 'source-map' : 'cheap-module-source-map',
		stats: {
			assets: true,
			builtAt: false,
			entrypoints: false,
			hash: false,
			modules: false,
		},
		module: {
			rules: [
				{
					test: /\.(js|jsx)$/,
					loader: 'babel-loader',
					// Babel options are in babel.config.json — babel-loader picks it up automatically.
					exclude: /node_modules/,
				},
			],
		},
	}
}

export default webpackConfig
