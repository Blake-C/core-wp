import js from '@eslint/js'
import globals from 'globals'
import eslintConfigPrettier from 'eslint-config-prettier'
import eslintPluginPrettierRecommended from 'eslint-plugin-prettier/recommended'

export default [
	js.configs.recommended,
	eslintConfigPrettier,
	eslintPluginPrettierRecommended,
	{
		rules: {
			'no-console': 'warn',
		},
		languageOptions: {
			ecmaVersion: 2020,
			sourceType: 'module',
			globals: {
				...globals.browser,
				...globals.jquery,
			},
		},
	},
]
