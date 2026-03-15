/**
 * Webpack Config
 *
 * @link http://css-max.com/getting-started-with-webpack/
 * @link https://shellmonger.com/2016/01/26/using-eslint-with-webpack/
 *
 * Eslint config for module loaders:
 * @link https://github.com/eslint/eslint/issues/4787
 */
import path from 'path'
import scriptsList from './theme_components/js/scripts-list.js'

const __dirname = path.resolve(path.dirname(''))

const webpackConfig = env => {
	return {
		mode: 'production',
		entry: scriptsList,
		output: {
			path: path.resolve(__dirname, env.output), // eslint-disable-line no-undef
			filename: 'bundle.[name].js',
		},
		devtool: 'source-map',
		stats: {
			/**
			 * @link https://webpack.js.org/configuration/stats/
			 */
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
					exclude: /node_modules/,
					options: {
						presets: [
							[
								'@babel/preset-env',
								{
									modules: false,
									// debug: true,
									useBuiltIns: 'usage',
									corejs: 3,
								},
							],
						],
					},
				},
			],
		},
	}
}

export default webpackConfig
