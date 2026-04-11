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
	var Button = components.Button

	var previewWrapStyle = {
		alignItems: 'center',
		display: 'flex',
		flexWrap: 'wrap',
		gap: '1rem',
	}

	var labelStyle = {
		fontSize: '0.875rem',
		fontWeight: '700',
		letterSpacing: '0.04em',
		textTransform: 'uppercase',
	}

	var linkStyle = {
		color: 'var(--wp--preset--color--primary, #000)',
		display: 'inline-flex',
		fontSize: '0.9rem',
		fontWeight: '600',
		textDecoration: 'none',
		cursor: 'default',
	}

	blocks.registerBlockType('core-wp/social-share', {
		icon: el(
			'svg',
			{ xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 24 24', 'aria-hidden': true, focusable: false },
			el('path', { d: 'M18 3a3 3 0 1 0 0 6 3 3 0 0 0 0-6zM6 9a3 3 0 1 0 0 6 3 3 0 0 0 0-6zM18 15a3 3 0 1 0 0 6 3 3 0 0 0 0-6z' }),
			el('path', { d: 'M8.7 10.7L15.3 7.3M8.7 13.3L15.3 16.7', fill: 'none', stroke: 'currentColor', strokeWidth: 1.5 })
		),
		edit: function (props) {
			var attributes = props.attributes
			var setAttributes = props.setAttributes
			var blockProps = useBlockProps()
			var customLinks = attributes.customLinks || []

			function updateCustomLink(index, field, value) {
				var newLinks = customLinks.map(function (link, i) {
					if (i !== index) return link
					return { text: link.text, url: link.url, [field]: value }
				})
				setAttributes({ customLinks: newLinks })
			}

			function addCustomLink() {
				setAttributes({ customLinks: customLinks.concat([{ text: '', url: '' }]) })
			}

			function removeCustomLink(index) {
				setAttributes({
					customLinks: customLinks.filter(function (_, i) {
						return i !== index
					}),
				})
			}

			// Build live preview children
			var previewChildren = [el('span', { key: 'label', style: labelStyle }, attributes.shareLabel || 'Share:')]

			if (attributes.linkedinEnabled) {
				previewChildren.push(
					el('span', { key: 'linkedin', style: linkStyle }, attributes.linkedinText || 'LinkedIn')
				)
			}
			if (attributes.twitterEnabled) {
				previewChildren.push(
					el('span', { key: 'twitter', style: linkStyle }, attributes.twitterText || 'X (Twitter)')
				)
			}
			if (attributes.facebookEnabled) {
				previewChildren.push(
					el('span', { key: 'facebook', style: linkStyle }, attributes.facebookText || 'Facebook')
				)
			}
			customLinks.forEach(function (link, i) {
				if (link.text) {
					previewChildren.push(el('span', { key: 'custom-' + i, style: linkStyle }, link.text))
				}
			})

			// Build custom links panel children
			var customLinkRows = customLinks.map(function (link, index) {
				return el(
					'div',
					{
						key: 'row-' + index,
						style: { marginBottom: '16px', paddingBottom: '16px', borderBottom: '1px solid #ddd' },
					},
					el(TextControl, {
						label: __('Link text', 'core_wp'),
						value: link.text,
						onChange: function (val) {
							updateCustomLink(index, 'text', val)
						},
					}),
					el(TextControl, {
						label: __('URL', 'core_wp'),
						value: link.url,
						onChange: function (val) {
							updateCustomLink(index, 'url', val)
						},
					}),
					el(
						Button,
						{
							isDestructive: true,
							variant: 'link',
							onClick: function () {
								removeCustomLink(index)
							},
						},
						__('Remove', 'core_wp')
					)
				)
			})

			customLinkRows.push(
				el(Button, { key: 'add', variant: 'secondary', onClick: addCustomLink }, __('+ Add Link', 'core_wp'))
			)

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __('Label', 'core_wp'), initialOpen: true },
						el(TextControl, {
							label: __('Share label text', 'core_wp'),
							value: attributes.shareLabel,
							onChange: function (val) {
								setAttributes({ shareLabel: val })
							},
						})
					),
					el(
						PanelBody,
						{ title: __('LinkedIn', 'core_wp'), initialOpen: true },
						el(ToggleControl, {
							label: __('Enable LinkedIn', 'core_wp'),
							checked: attributes.linkedinEnabled,
							onChange: function (val) {
								setAttributes({ linkedinEnabled: val })
							},
						}),
						attributes.linkedinEnabled
							? el(TextControl, {
									label: __('Link text', 'core_wp'),
									value: attributes.linkedinText,
									onChange: function (val) {
										setAttributes({ linkedinText: val })
									},
								})
							: null
					),
					el(
						PanelBody,
						{ title: __('X (Twitter)', 'core_wp'), initialOpen: true },
						el(ToggleControl, {
							label: __('Enable X (Twitter)', 'core_wp'),
							checked: attributes.twitterEnabled,
							onChange: function (val) {
								setAttributes({ twitterEnabled: val })
							},
						}),
						attributes.twitterEnabled
							? el(TextControl, {
									label: __('Link text', 'core_wp'),
									value: attributes.twitterText,
									onChange: function (val) {
										setAttributes({ twitterText: val })
									},
								})
							: null
					),
					el(
						PanelBody,
						{ title: __('Facebook', 'core_wp'), initialOpen: true },
						el(ToggleControl, {
							label: __('Enable Facebook', 'core_wp'),
							checked: attributes.facebookEnabled,
							onChange: function (val) {
								setAttributes({ facebookEnabled: val })
							},
						}),
						attributes.facebookEnabled
							? el(TextControl, {
									label: __('Link text', 'core_wp'),
									value: attributes.facebookText,
									onChange: function (val) {
										setAttributes({ facebookText: val })
									},
								})
							: null
					),
					el(PanelBody, { title: __('Custom Links', 'core_wp'), initialOpen: false }, customLinkRows)
				),
				el('div', blockProps, el('div', { style: previewWrapStyle }, previewChildren))
			)
		},
	})
})(wp.blocks, wp.element, wp.blockEditor, wp.components, wp.i18n)
