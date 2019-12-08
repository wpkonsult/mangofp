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
	$CF7_PLUGIN_NAME = "contact-form-7/wp-contact-form-7.php";

	if (is_plugin_active($CF7_PLUGIN_NAME)) {
		echo '<div id="peachesMain"></div>';
	} else {
		echo '<strong>' . __('Peaches works only when Contact Form 7 is installed and activated.') . '</strong>';
	}
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

function actionCF7Submit( $instance, $result ) {
	$cases = ['spam', 'mail_sent', 'mail_failed'];

	if ( 
		empty( $result['status'] ) ||
		!in_array( $result['status'], $cases ) 
	) {
		error_log('Validation failed, results not stored. Status: ' . $result['status'] ?? 'empty');
		return;
	}

	$submission = WPCF7_Submission::get_instance();
	if ( 
		!$submission ||
		!$posted_data = $submission->get_posted_data() 
	) {
		error_log('No posted data');
		return;
	}

	//TODO: Here the $posted_data can be serialised
	error_log('Posted data:' . print_r($posted_data, 1));
}

add_action('admin_menu', 'makePeachesAdminMenuPage');
add_action( 'wpcf7_submit', 'actionCF7Submit', 10, 2 );

/**
*  Comment in for usage in frontend through shortcode
*
*  function addPeaches() {
*  	wp_enqueue_script('vuejs');
*  	return '<div id="peachesMain"></div>';
*  }
* add_action('wp_enqueue_scripts', 'registerVueScripts');
* add_shortcode("peaches4cf7", 'addPeaches');
*/