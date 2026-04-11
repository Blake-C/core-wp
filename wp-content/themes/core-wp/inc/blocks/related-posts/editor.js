/* global wp */
;(function (blocks, element, blockEditor, i18n) {
	var el = element.createElement
	var __ = i18n.__
	var useBlockProps = blockEditor.useBlockProps

	blocks.registerBlockType('core-wp/related-posts', {
		icon: el(
			'svg',
			{ xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 24 24', 'aria-hidden': true, focusable: false },
			el('path', { d: 'M3 5h5v14H3zM4 6h3v3H4zM4 11h3v1H4zM4 13h2v1H4zM10 5h5v14h-5zM11 6h3v3h-3zM11 11h3v1h-3zM11 13h2v1h-2zM17 5h5v14h-5zM18 6h3v3h-3zM18 11h3v1h-3zM18 13h2v1h-2z' })
		),
		edit: function () {
			var blockProps = useBlockProps({
				style: {
					border: '1px dashed #ccc',
					borderRadius: '4px',
					padding: '16px',
				},
			})

			return el(
				'div',
				blockProps,
				el('strong', null, __('Related Posts', 'core_wp')),
				el(
					'p',
					{ style: { margin: '4px 0 0', color: '#757575' } },
					__(
						'Displays the 3 most recent posts in the same categories as this post on the front end.',
						'core_wp'
					)
				)
			)
		},
	})
})(wp.blocks, wp.element, wp.blockEditor, wp.i18n)
