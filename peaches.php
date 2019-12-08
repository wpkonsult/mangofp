<?php
/**
 *  Plugin name: Peaches
 *  Description: User defined states and their management for Contact Form 7 results
 */

//Register Scripts to use 
function registerVueScripts() {
	//TODO: Intorduce setting that build is loaded in dev environment and that for production its linked from the place were its created
	wp_register_script( 
		'vuejs', 
		'http://localhost:8080/dist/build.js',
		false, //no dependencies
		'0.0.1',
		true //in the footer otherwise it Vue triggered too early
	);
}

function makePeachesAdminMenuPage() {
	$page_hook_suffix = add_menu_page(
		'Peaches Plugin Page',
		'Peaches4CF7',
		'manage_options',
		'peaches-admin',
		'renderAdmin'
	);
	add_action( "load-{$page_hook_suffix}", 'loadAdminJs' );
}

function renderAdmin(){
	echo '<div id="peachesMain"></div>';
}

function loadAdminJs() {
	add_action('admin_enqueue_scripts', 'initAdminPage');
}

function initAdminPage() {
	registerVueScripts();
	wp_enqueue_script('vuejs');
	
	//Send localized data to the script
	//wp_localize_script( $this->wpsitemonitor, 'WPSITEMONITOR_ADMIN_TEXTS', array(
	//	'ajaxurl' => admin_url( 'admin-ajax.php' ),
	//	'id' => esc_html__('Id', 'wpsitemonitor'),
	//	'category' => esc_html__('Category', 'wpsitemonitor'),
	//	'name' => esc_html__('Name', 'wpsitemonitor'),
	//	'value' => esc_html__('Value', 'wpsitemonitor')
	//) );
}

add_action('admin_menu', 'makePeachesAdminMenuPage');

function addPeaches() {
	wp_enqueue_script('vuejs');
	return '<div id="peachesMain"></div>';
}

/**
 * Comment in for usage in frontend through shortcode
 * add_action('wp_enqueue_scripts', 'registerVueScripts');
 * add_shortcode("peaches4cf7", 'addPeaches');
 */