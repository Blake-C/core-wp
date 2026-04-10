/* global wp */
;(function (blocks, element, blockEditor, components, i18n) {
	var el = element.createElement
	var Fragment = element.Fragment
	var __ = i18n.__
	var useBlockProps = blockEditor.useBlockProps
	var InspectorControls = blockEditor.InspectorControls
	var PanelBody = components.PanelBody
	var ToggleControl = components.ToggleControl
	var TextControl = components.TextControl

	var previewStyle = {
		fontSize: 'inherit',
		margin: '0',
	}

	blocks.registerBlockType('core-wp/copyright', {
		edit: function (props) {
			var attributes = props.attributes
			var setAttributes = props.setAttributes
			var blockProps = useBlockProps()

			// Build the preview text, mirroring the PHP render logic
			var year = new Date().getFullYear()
			var siteName = wp.data.select('core').getSite() ? wp.data.select('core').getSite().title : ''

			var parts = []
			if (attributes.prefixText && attributes.prefixText.trim()) {
				parts.push(attributes.prefixText.trim())
			}
			parts.push('\u00a9 ' + year)
			if (attributes.showSiteName && siteName) {
				parts.push(siteName)
			}
			if (attributes.suffixText && attributes.suffixText.trim()) {
				parts.push(attributes.suffixText.trim())
			}

			var previewText = parts.join(' ')

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __('Copyright Settings', 'core_wp'), initialOpen: true },
						el(TextControl, {
							label: __('Prefix text', 'core_wp'),
							help: __('Appears before the © symbol.', 'core_wp'),
							value: attributes.prefixText,
							onChange: function (val) {
								setAttributes({ prefixText: val })
							},
						}),
						el(ToggleControl, {
							label: __('Show site name', 'core_wp'),
							checked: attributes.showSiteName,
							onChange: function (val) {
								setAttributes({ showSiteName: val })
							},
						}),
						el(TextControl, {
							label: __('Suffix text', 'core_wp'),
							help: __('Appears after the year and site name.', 'core_wp'),
							value: attributes.suffixText,
							onChange: function (val) {
								setAttributes({ suffixText: val })
							},
						})
					)
				),
				el('div', blockProps, el('p', { style: previewStyle }, previewText))
			)
		},
	})
})(wp.blocks, wp.element, wp.blockEditor, wp.components, wp.i18n)
