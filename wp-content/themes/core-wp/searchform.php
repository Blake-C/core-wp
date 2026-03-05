<?php
/**
 * Template for displaying search forms
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package core_wp
 */

?>

<?php core_wp_dev_helper( pathinfo( __FILE__, PATHINFO_FILENAME ) ); ?>

<h2 class="show-for-sr">
	<?php
	echo esc_html_x(
		'Search Bar',
		'Heading for search area, for screen readers',
		'core_wp'
	);
	?>
</h2>

<form
	role="search"
	method="get"
	id="searchform"
	class="searchform"
	action="<?php echo esc_url( home_url( '/' ) ); ?>"
>
	<div class="row collapse">
		<div class="small-8 columns">
			<?php
			$core_wp_search_label = _x(
				'Search',
				'Search field label text',
				'core_wp'
			);

			$core_wp_search_placeholder = esc_attr_x(
				'Search...',
				'Search field placeholder attribute',
				'core_wp'
			);
			?>

			<label>
				<span class="show-for-sr">
					<?php echo esc_html( $core_wp_search_label ); ?>
				</span>

				<input
					type="text"
					placeholder="<?php echo esc_attr( $core_wp_search_placeholder ); ?>"
					value="<?php echo get_search_query(); ?>"
					name="s"
				/>
			</label>

		</div>

		<div class="small-4 columns">
			<?php
			$core_wp_search_value = esc_attr_x(
				'Search',
				'Search button value attribute',
				'core_wp'
			);
			?>

			<button type="submit" class="button expanded" id="search_submit">
				<?php echo esc_html( $core_wp_search_value ); ?>
			</button>
		</div>
	</div>
</form>
