/* global wp */
;(function (blocks, element, blockEditor, components, i18n) {
	var el = element.createElement
	var Fragment = element.Fragment
	var __ = i18n.__
	var useBlockProps = blockEditor.useBlockProps
	var InnerBlocks = blockEditor.InnerBlocks
	var InspectorControls = blockEditor.InspectorControls
	var MediaUploadCheck = blockEditor.MediaUploadCheck
	var MediaUpload = blockEditor.MediaUpload
	var InnerBlocksContent = InnerBlocks.Content
	var PanelBody = components.PanelBody
	var TextControl = components.TextControl
	var ToggleControl = components.ToggleControl
	var Button = components.Button

	var titleInputStyle = {
		background: 'transparent',
		border: 'none',
		borderBottom: '1px solid #ddd',
		flex: '1',
		fontSize: '1rem',
		fontWeight: '600',
		outline: 'none',
		padding: '0.5rem 0',
		width: '100%',
	}

	var headerPreviewStyle = {
		alignItems: 'center',
		borderBottom: '1px solid #e0e0e0',
		display: 'flex',
		gap: '0.5rem',
		padding: '0.75rem 0',
	}

	var iconPreviewStyle = {
		color: 'var(--wp--preset--color--primary, #1779ba)',
		flexShrink: '0',
		fontSize: '1.25rem',
		lineHeight: '1',
		userSelect: 'none',
	}

	var contentAreaStyle = {
		borderLeft: '3px solid var(--wp--preset--color--primary, #1779ba)',
		paddingLeft: '1rem',
	}

	blocks.registerBlockType('core-wp/accordion-item', {
		icon: el(
			'svg',
			{ xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 24 24', 'aria-hidden': true, focusable: false },
			el('path', { d: 'M3 4h13v5H3zM17 4l3 2.5-3 2.5zM3 12h13v2H3zM3 16h9v2H3z' })
		),
		edit: function (props) {
			var attributes = props.attributes
			var setAttributes = props.setAttributes
			var title = attributes.title || ''
			var imageId = attributes.imageId || 0
			var imageUrl = attributes.imageUrl || ''
			var imageAlt = attributes.imageAlt || ''
			var defaultOpen = attributes.defaultOpen || false

			var blockProps = useBlockProps({ className: 'accordion__item' })

			function onSelectImage(media) {
				setAttributes({
					imageId: media.id,
					imageUrl: media.url,
					imageAlt: media.alt || '',
				})
			}

			function onRemoveImage() {
				setAttributes({ imageId: 0, imageUrl: '', imageAlt: '' })
			}

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __('Item Settings', 'core_wp'), initialOpen: true },
						el(ToggleControl, {
							label: __('Open by default', 'core_wp'),
							checked: defaultOpen,
							onChange: function (val) {
								setAttributes({ defaultOpen: val })
							},
						})
					),
					el(
						PanelBody,
						{ title: __('Image (split layout)', 'core_wp'), initialOpen: false },
						el(
							'p',
							{ style: { fontSize: '12px', color: '#757575', marginTop: 0 } },
							__('Used when the parent accordion has 50/50 split enabled.', 'core_wp')
						),
						imageUrl
							? el(
									'div',
									{ style: { marginBottom: '12px' } },
									el('img', {
										src: imageUrl,
										alt: imageAlt,
										style: { display: 'block', height: 'auto', marginBottom: '8px', width: '100%' },
									}),
									el(
										Button,
										{ isDestructive: true, variant: 'link', onClick: onRemoveImage },
										__('Remove image', 'core_wp')
									)
								)
							: null,
						el(
							MediaUploadCheck,
							null,
							el(MediaUpload, {
								onSelect: onSelectImage,
								allowedTypes: ['image'],
								value: imageId,
								render: function (ref) {
									return el(
										Button,
										{ variant: 'secondary', onClick: ref.open },
										imageId ? __('Replace image', 'core_wp') : __('Select image', 'core_wp')
									)
								},
							})
						)
					)
				),
				el(
					'div',
					blockProps,
					el(
						'div',
						{ style: headerPreviewStyle },
						el('input', {
							type: 'text',
							placeholder: __('Accordion title\u2026', 'core_wp'),
							value: title,
							style: titleInputStyle,
							onChange: function (e) {
								setAttributes({ title: e.target.value })
							},
						}),
						el('span', { style: iconPreviewStyle, 'aria-hidden': 'true' }, '+')
					),
					el(
						'div',
						{ style: contentAreaStyle },
						el(InnerBlocks, {
							allowedBlocks: ['core/paragraph', 'core/heading', 'core/list', 'core/image', 'core/quote'],
							template: [['core/paragraph', { placeholder: __('Accordion content\u2026', 'core_wp') }]],
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
