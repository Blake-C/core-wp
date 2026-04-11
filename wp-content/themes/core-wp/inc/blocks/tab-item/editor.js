/* global wp */
;(function (blocks, element, blockEditor, components, i18n) {
	var el = element.createElement
	var Fragment = element.Fragment
	var __ = i18n.__
	var useBlockProps = blockEditor.useBlockProps
	var InnerBlocks = blockEditor.InnerBlocks
	var InnerBlocksContent = InnerBlocks.Content
	var TextControl = components.TextControl

	var itemWrapStyle = {
		borderBottom: '1px solid #e0e0e0',
		marginBottom: '0.5rem',
		paddingBottom: '0.75rem',
	}

	var tabLabelRowStyle = {
		alignItems: 'center',
		background: 'var(--wp--preset--color--primary, #1779ba)',
		borderRadius: '3px 3px 0 0',
		display: 'inline-flex',
		marginBottom: '0.5rem',
		padding: '0.25rem 0.75rem',
		position: 'relative',
	}

	var tabLabelInputStyle = {
		background: 'transparent',
		border: 'none',
		color: '#fff',
		fontSize: '0.875rem',
		fontWeight: '600',
		minWidth: '8rem',
		outline: 'none',
		padding: '0',
	}

	var contentAreaStyle = {
		border: '1px solid #e0e0e0',
		borderTop: '2px solid var(--wp--preset--color--primary, #1779ba)',
		padding: '1rem',
	}

	blocks.registerBlockType('core-wp/tab-item', {
		edit: function (props) {
			var attributes = props.attributes
			var setAttributes = props.setAttributes
			var title = attributes.title || ''

			var blockProps = useBlockProps({ style: itemWrapStyle })

			return el(
				Fragment,
				null,
				el(
					'div',
					blockProps,
					el(
						'div',
						{ style: tabLabelRowStyle },
						el('input', {
							type: 'text',
							'aria-label': __('Tab title', 'core_wp'),
							placeholder: __('Tab title\u2026', 'core_wp'),
							value: title,
							style: tabLabelInputStyle,
							onChange: function (e) {
								setAttributes({ title: e.target.value })
							},
						})
					),
					el(
						'div',
						{ style: contentAreaStyle },
						el(InnerBlocks, {
							allowedBlocks: [
								'core/paragraph',
								'core/heading',
								'core/list',
								'core/image',
								'core/quote',
								'core/video',
								'core/embed',
							],
							template: [['core/paragraph', { placeholder: __('Tab content\u2026', 'core_wp') }]],
							templateLock: false,
						})
					)
				)
			)
		},
		save: function () {
			return el(InnerBlocksContent, null)
		},
	})
})(wp.blocks, wp.element, wp.blockEditor, wp.components, wp.i18n)
