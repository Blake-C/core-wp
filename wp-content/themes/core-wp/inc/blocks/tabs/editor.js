/* global wp */
;(function (blocks, element, blockEditor, components, i18n) {
	var el = element.createElement
	var Fragment = element.Fragment
	var __ = i18n.__
	var useBlockProps = blockEditor.useBlockProps
	var InnerBlocks = blockEditor.InnerBlocks
	var InnerBlocksContent = InnerBlocks.Content
	var InspectorControls = blockEditor.InspectorControls
	var PanelBody = components.PanelBody
	var ToggleControl = components.ToggleControl

	var editorNoticeStyle = {
		borderBottom: '2px solid var(--wp--preset--color--primary, #1779ba)',
		color: '#757575',
		fontSize: '0.75rem',
		letterSpacing: '0.06em',
		paddingBottom: '0.5rem',
		paddingTop: '0.25rem',
		textTransform: 'uppercase',
	}

	blocks.registerBlockType('core-wp/tabs', {
		icon: el(
			'svg',
			{ xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 24 24', 'aria-hidden': true, focusable: false },
			el('path', { d: 'M3 9h18v12H3zM3 4h7v5H3zM12 4h7v5h-7z' })
		),
		edit: function (props) {
			var attributes = props.attributes
			var setAttributes = props.setAttributes
			var mobileAccordion = attributes.mobileAccordion || false
			var mobileSingleOpen = attributes.mobileSingleOpen || false

			var blockProps = useBlockProps({ className: 'tabs tabs--editor' })

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __('Responsive behaviour', 'core_wp'), initialOpen: true },
						el(ToggleControl, {
							label: __('Convert to accordion on mobile', 'core_wp'),
							help: __(
								'Below 640 px the tab nav is hidden and each tab becomes a collapsible accordion item.',
								'core_wp'
							),
							checked: mobileAccordion,
							onChange: function (val) {
								setAttributes({ mobileAccordion: val })
							},
						}),
						mobileAccordion
							? el(ToggleControl, {
									label: __('One item open at a time', 'core_wp'),
									help: __('Opening an item automatically closes the others.', 'core_wp'),
									checked: mobileSingleOpen,
									onChange: function (val) {
										setAttributes({ mobileSingleOpen: val })
									},
								})
							: null
					)
				),
				el(
					'div',
					blockProps,
					el('p', { style: editorNoticeStyle }, __('Tabs — add or reorder Tab Items below', 'core_wp')),
					el(InnerBlocks, {
						allowedBlocks: ['core-wp/tab-item'],
						template: [
							['core-wp/tab-item', {}],
							['core-wp/tab-item', {}],
						],
						templateLock: false,
					})
				)
			)
		},
		save: function () {
			return el(InnerBlocksContent, null)
		},
	})
})(wp.blocks, wp.element, wp.blockEditor, wp.components, wp.i18n)
