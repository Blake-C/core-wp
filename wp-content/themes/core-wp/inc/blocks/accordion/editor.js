/* global wp */
;(function (blocks, element, blockEditor, components, i18n) {
	var el = element.createElement
	var Fragment = element.Fragment
	var __ = i18n.__
	var useBlockProps = blockEditor.useBlockProps
	var InnerBlocks = blockEditor.InnerBlocks
	var InspectorControls = blockEditor.InspectorControls
	var PanelBody = components.PanelBody
	var RadioControl = components.RadioControl
	var ToggleControl = components.ToggleControl
	var InnerBlocksContent = InnerBlocks.Content

	blocks.registerBlockType('core-wp/accordion', {
		edit: function (props) {
			var attributes = props.attributes
			var setAttributes = props.setAttributes
			var layout = attributes.layout || 'full'
			var floatDirection = attributes.floatDirection || 'left'
			var splitLayout = attributes.splitLayout || false
			var singleOpen = attributes.singleOpen || false

			var blockProps = useBlockProps({
				className: 'accordion' + (layout === 'float' ? ' accordion--float-' + floatDirection : splitLayout ? ' accordion--split' : ''),
			})

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __('Layout', 'core_wp'), initialOpen: true },
						el(RadioControl, {
							label: __('Block layout', 'core_wp'),
							selected: layout,
							options: [
								{ label: __('Full width', 'core_wp'), value: 'full' },
								{ label: __('Float', 'core_wp'), value: 'float' },
							],
							onChange: function (val) {
								setAttributes({ layout: val })
							},
						}),
						layout === 'float'
							? el(RadioControl, {
									label: __('Float direction', 'core_wp'),
									selected: floatDirection,
									options: [
										{ label: __('Left', 'core_wp'), value: 'left' },
										{ label: __('Right', 'core_wp'), value: 'right' },
									],
									onChange: function (val) {
										setAttributes({ floatDirection: val })
									},
								})
							: null,
						layout === 'full'
							? el(ToggleControl, {
									label: __('50/50 split with image', 'core_wp'),
									help: __(
										'Renders accordion on the left with a per-item image on the right. Set the image on each item.',
										'core_wp'
									),
									checked: splitLayout,
									onChange: function (val) {
										setAttributes({ splitLayout: val })
									},
								})
							: null,
						el(ToggleControl, {
							label: __('One item open at a time', 'core_wp'),
							help: __(
								'Opening any item automatically closes the others. Always on in split layout.',
								'core_wp'
							),
							checked: singleOpen || splitLayout,
							disabled: splitLayout,
							onChange: function (val) {
								setAttributes({ singleOpen: val })
							},
						})
					)
				),
				el(
					'div',
					blockProps,
					el(
						'div',
						{ className: 'accordion__items' },
						el(InnerBlocks, {
							allowedBlocks: ['core-wp/accordion-item'],
							template: [['core-wp/accordion-item', {}]],
							templateLock: false,
						})
					),
					splitLayout && layout === 'full'
						? el(
								'div',
								{ className: 'accordion__image-panel accordion__image-panel--editor-placeholder' },
								el('span', null, __('Image panel — set image on each item', 'core_wp'))
							)
						: null
				)
			)
		},
		save: function () {
			return el(InnerBlocksContent, null)
		},
	})
})(wp.blocks, wp.element, wp.blockEditor, wp.components, wp.i18n)
