#!/bin/bash

# The global variables and aliases are used in the below functions
# They are also available in the cli container globally
SERVER_DIR='/home/webdev/www/public_html'

alias theme="cd $SERVER_DIR/wp-content/themes/core-wp"
alias theme_components="cd $SERVER_DIR/wp-content/themes/core-wp/theme_components"

wp-theme-unit-data() {
	WORKING_DIR=$(pwd);

	cd $SERVER_DIR/wp-content
	curl https://raw.githubusercontent.com/WPTT/theme-unit-test/master/themeunittestdata.wordpress.xml >> theme-unit-test-data.xml
	wp plugin install wordpress-importer --activate
	wp import theme-unit-test-data.xml --authors=create
	rm theme-unit-test-data.xml
	wp plugin uninstall wordpress-importer --deactivate
	cd $WORKING_DIR
}

wp-db-export() {
	echo "\n================================================================="
	echo "Export WordPress Database"
	echo "================================================================="

	echo "\nLeave this blank if you do not want to change the site url"
	echo "If you're moving the site to http://google.com, just put google.com"
	vared -p "Production URL: " -c REPLACEURL
	REPLACEURLCLEAN=$(echo $REPLACEURL | sed -e "s/http:\/\///g")

	WORKING_DIR=$(pwd);
	cd $SERVER_DIR

	if [[ "$REPLACEURLCLEAN" ]]; then
		wp search-replace "localhost" "$REPLACEURLCLEAN" --allow-root
		wp option update siteurl "http://$REPLACEURLCLEAN" --allow-root
	fi

	wp db export ./wp-content/wp_foundation_six_$(date +"%Y%m%d%H%M%s")_database.sql --allow-root

	if [[ "$REPLACEURLCLEAN" != "" ]]; then
		wp search-replace "$REPLACEURLCLEAN" "localhost" --allow-root
		wp option update siteurl "http://localhost" --allow-root
	fi

	cd $WORKING_DIR

	echo "\n"
}

wp-init() {
	WORKING_DIR=$(pwd);

	echo "\n\n"

	local WPUSER
	local WP_ADMIN_MAIL
	local PASSWORD
	local SITENAME

	# Accept user input for the Username name
	read "WPUSER?Wordpress Username: "

	# Accept user input for the Email Address name
	read "WP_ADMIN_MAIL?Wordpress User Email Address: "

	# Accept user input for the User Password name
	read -s "PASSWORD?Wordpress User Password: "
	echo ""

	# Accept user input for the Site Name name
	read "SITENAME?Site Name: "

	echo "\n\n"

	echo "\n================================================================="
	echo "Running Composer to install WordPress Files"
	echo "=================================================================\n"
	cd $SERVER_DIR/wp-content
	composer install

	echo "\n================================================================="
	echo "Running PNPM"
	echo "================================================================="

	# cd into theme
	cd $SERVER_DIR/wp-content/themes/core-wp

	echo "\nRunning pnpm install"
	if [ -d "$SERVER_DIR/wp-content/themes/core-wp/node_modules" ]; then
		rm -rf node_modules assets
		pnpm install
	else
		pnpm install
	fi

	echo "\nPNPM Build"
	pnpm run build

	echo "\n================================================================="
	echo "Running WP-CLI for WP Defaults"
	echo "================================================================="

	cd $SERVER_DIR

	echo "\nRunning WP-CLI"

	wp core install --url="localhost" --title="$SITENAME" --admin_user="$WPUSER" --admin_password="$PASSWORD" --admin_email="$WP_ADMIN_MAIL"

	wp user update $WPUSER --admin_color=light --show_admin_bar_front=false

	# show only 6 posts on an archive page, remove default tagline
	wp option update posts_per_page 6
	wp option update posts_per_rss 6
	wp option update blogdescription ""
	wp option update timezone_string America/Chicago

	# Delete sample page, and create homepage
	wp post delete $(wp post list --post_type=page --posts_per_page=1 --post_status=publish --pagename="sample-page" --field=ID --format=ids)
	wp post create --post_type=page --post_title=Home --post_status=publish --post_author=$(wp user get $WPUSER --field=ID)

	# Set homepage as front page
	wp option update show_on_front "page"

	# Set homepage to be the new page
	wp option update page_on_front $(wp post list --post_type=page --post_status=publish --posts_per_page=1 --pagename=home --field=ID --format=ids)

	# Set pretty urls
	wp rewrite structure "/%postname%/"
	wp rewrite flush

	# Delete sample posts
	wp post delete $(wp post list --post_type='post' --format=ids)

	# Activate default theme
	wp theme activate core-wp

	#Setup main navigation
	wp menu create "Main Navigation"
	wp menu location assign main-navigation primary

	# add pages to navigation
	export IFS=" "
	for pageid in $(wp post list --order="ASC" --orderby="date" --post_type=page --post_status=publish --posts_per_page=-1 --field=ID --format=ids); do
		wp menu item add-post main-navigation $pageid
	done

	# Remove preinstalled themes
	wp theme uninstall twentytwentyfive twentytwentyfour twentytwentythree

	# Remove preinstalled plugins
	wp plugin uninstall hello akismet

	# Activate plugins
	wp plugin activate query-monitor

	echo "\n\nDon't forget to:"
	echo "Update your style.css file in the base theme"
	echo "Go to http://realfavicongenerator.net/, and update your favicons/app icons\n\n"

	cd $WORKING_DIR
}
