/* global wp */
;(function (blocks, element, blockEditor, i18n) {
	var el = element.createElement
	var __ = i18n.__
	var useBlockProps = blockEditor.useBlockProps

	blocks.registerBlockType('core-wp/related-posts', {
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
