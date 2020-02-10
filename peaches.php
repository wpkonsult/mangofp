<?php

//namespace MangoFp;
/**
 *  Plugin name: Mango Form Processing
 *  Description: User defined states and their management for Contact Form 7 results
 */

function isDebug() {
    return ( defined('MANGO_FP_DEBUG') && MANGO_FP_DEBUG );
}

//Register Scripts to use 
function registerVueScripts() {
	$chunk_vendors_js = plugin_dir_url( __FILE__ ) . 'assets/js/chunk-vendors.js';
    $app_js = plugin_dir_url( __FILE__ ) . 'assets/js/app.js';
    if (isDebug()) {
        $chunk_vendors_js = 'http://localhost:8080/js/chunk-vendors.js';
        $app_js = 'http://localhost:8080/js/app.js';
    }
    wp_register_script( 
		'vue_vendors', 
		$chunk_vendors_js,
		false, //no dependencies
		'0.0.1',
		true //in the footer otherwise Vue triggers it too early
	);
	wp_register_script( 
		'vuejs', 
		$app_js,
		['vue_vendors'],
		'0.0.1',
		true //in the footer otherwise Vue triggers it too early
	);

	wp_enqueue_script('vue_vendors');
	wp_enqueue_script('vuejs');

    if (!isDebug()) {
        wp_register_style( 'vue-vendor-styles',  plugin_dir_url( __FILE__ ) . 'assets/css/chunk-vendors.css' );
        wp_enqueue_style( 'vue-vendor-styles' );
        wp_register_style( 'vue-app-styles',  plugin_dir_url( __FILE__ ) . 'assets/css/app.css' );
        wp_enqueue_style( 'vue-app-styles' );
    }

    wp_enqueue_style('vuetify_styles_font', 'https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900');
    wp_enqueue_style('vuetify_styles', 'https://cdn.jsdelivr.net/npm/@mdi/font@latest/css/materialdesignicons.min.css');
}

function makePeachesAdminMenuPage() {
	$page_hook_suffix = add_menu_page(
		'MangoFP Plugin Page',
		'MangoFormProcessing',
		'manage_options',
		'peaches-admin',
		'renderAdmin'
	);
	add_action( "load-{$page_hook_suffix}", 'loadAdminJs' );
}

function renderAdmin(){
	$CF7_PLUGIN_NAME = "contact-form-7/wp-contact-form-7.php";

	if (is_plugin_active($CF7_PLUGIN_NAME)) {
		echo '<div id="app"></div>';
	} else {
		echo '<strong>' . __('Peaches works only when Contact Form 7 is installed and activated.') . '</strong>';
	}
}

function loadAdminJs() {
	add_action('admin_enqueue_scripts', 'initAdminPage');
}

function initAdminPage() {
    registerVueScripts();
    //Send localized data to the script
    error_log('MangoFP starts ...');
    wp_localize_script(
        'vuejs', 
        'RESOURCES',
        [
            //'adminUrl' => esc_url_raw( rest_url() . 'peaches'),
            'adminUrl' => get_rest_url( null, '/peaches', 'rest')
        ]
    );
}

function actionCF7Submit( $instance, $result ) {
    return MangoFp\CF7Connector::actionCF7Submit($instance, $result);
}

function registerRestRoutes() {
    $adminRoutes = new MangoFp\AdminRoutes();
    $adminRoutes->registerRestRoutes();   
}

function activateMFP() {
    MangoFp\MessagesDB::installDatabase();
}

function checkForDatabaseUpdates() {
    MangoFp\MessagesDB::installDatabase();
}

function deactivateMFP() {
    MangoFp\MessagesDB::removeDatabase();
}

require_once plugin_dir_path(__FILE__) . 'autoload.php';

add_action('admin_menu', 'makePeachesAdminMenuPage');
add_action( 'wpcf7_submit', 'actionCF7Submit', 10, 2 );
add_action( 'rest_api_init', 'registerRestRoutes' );
//add_action( 'plugins_loaded', 'checkForDatabaseUpdates' );

register_activation_hook( __FILE__, 'activateMFP' );
register_deactivation_hook( __FILE__, 'deactivateMFP' );
		
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