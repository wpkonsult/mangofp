<?php

//namespace MangoFp;
/**
 *  Plugin name: Mango Form Processing
 *  Description: Manage Contact Form 7 contacts by process you define
 *  @link               http://fpmango.com
 *  @since              0.0.1
 *  @package            MangoFp
 *  Author:             Andres Järviste
 *  Version:            0.0.3
 *  Author URI:         https://mangofp.net
 *  Domain Path:        /languages
 */

function isDebug() {
    return ( defined('MANGO_FP_DEBUG') && MANGO_FP_DEBUG );
}

//Register Scripts to use
function registerVueScripts($page) {
    $page = '/' . $page;
	$chunk_vendors_js = plugin_dir_url( __FILE__ ) . 'assets' . $page . '/js/chunk-vendors.js';
    $app_js = plugin_dir_url( __FILE__ ) . 'assets' . $page . '/js/app.js';
    if (isDebug()) {
        $chunk_vendors_js = 'http://localhost:8080/js/chunk-vendors.js';
        $app_js = 'http://localhost:8080/js/app.js';
        if ($page == '/settings') {
            $chunk_vendors_js = 'http://localhost:3000/js/chunk-vendors.js';
            $app_js = 'http://localhost:3000/js/app.js';
        }
    }
    wp_register_script(
		'mangofp_vue_vendors',
		$chunk_vendors_js,
		false, //no dependencies
		'0.0.1',
		true //in the footer otherwise Vue triggers it too early
	);
	wp_register_script(
		'mangofp_vuejs',
		$app_js,
		['mangofp_vue_vendors'],
		'0.0.1',
		true //in the footer otherwise Vue triggers it too early
	);

	wp_enqueue_script('mangofp_vue_vendors');
	wp_enqueue_script('mangofp_vuejs');

    if (!isDebug()) {
        wp_register_style( 'vue-vendor-styles',  plugin_dir_url( __FILE__ ) . 'assets/css/chunk-vendors.css' );
        wp_enqueue_style( 'vue-vendor-styles' );
        wp_register_style( 'vue-app-styles',  plugin_dir_url( __FILE__ ) . 'assets/css/app.css' );
        wp_enqueue_style( 'vue-app-styles' );
    }

    wp_enqueue_style('vuetify_styles_font', 'https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900');
    wp_enqueue_style('vuetify_styles', 'https://cdn.jsdelivr.net/npm/@mdi/font@latest/css/materialdesignicons.min.css');
}

function makeMangoFpAdminMenuPage() {
	$pageHookSuffix = add_menu_page(
		__('MangoFP Contacts'),
		'MangoFp',
		'manage_options',
		'mangofp-admin',
		'renderAdmin'
	);
    add_submenu_page(
        'mangofp-admin',
        __('MangoFp Contacts'),
        __('Contacts'),
        'manage_options',
        'mangofp-admin',
        'renderAdmin'
    );
    $contactMenuSuffix = add_submenu_page(
        'mangofp-admin',
        __('MangoFp Settings'),
        __('Settings'),
        'manage_options',
        'mangofp-contact',
        'renderAdmin'
    );
	add_action( "load-{$pageHookSuffix}", 'loadContactsJs' );
	add_action( "load-{$contactMenuSuffix}", 'loadSettingsJs' );
}

function renderAdmin(){
	$CF7_PLUGIN_NAME = "contact-form-7/wp-contact-form-7.php";

	if (is_plugin_active($CF7_PLUGIN_NAME)) {
		echo '<div id="app"></div>';
	} else {
		echo '<strong>' . __('Mango Form Processor works only when Contact Form 7 is installed and activated.') . '</strong>';
	}
}

function loadContactsJs() {
    add_action('admin_enqueue_scripts', 'initContactsPage');
}

function loadSettingsJs() {
    add_action('admin_enqueue_scripts', 'initSettingsPage');
}

function getResources() {
	$resources = [
			'adminUrl' => get_rest_url( null, '/mangofp', 'rest'),
			'version' => ['main' => 'v.0.0.5'],
            'strings' => MangoFp\Localization::getSettingsStrings()
	];

	return apply_filters('mangofp_resources', $resources);
}

function initContactsPage() {
    registerVueScripts('');
    error_log('MangoFP starts ...');
    wp_localize_script(
        'mangofp_vuejs',
        'MANGOFP_RESOURCES',
        getResources(),
    );
}
function initSettingsPage() {
    registerVueScripts('settings');
    error_log('MangoFP Settings starts ...');
    wp_localize_script(
        'mangofp_vuejs',
        'MANGOFP_RESOURCES',
		getResources(),
    );
}

function actionCF7Submit( $result ) {
    return MangoFp\CF7Connector::actionCF7Submit($result);
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

function loadTranslations() {
    load_plugin_textdomain( 'mangofp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

require_once(ABSPATH . 'wp-admin/includes/image.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once plugin_dir_path(__FILE__) . 'autoload.php';

add_action('admin_menu', 'makeMangoFpAdminMenuPage');
add_action('wpcf7_before_send_mail','actionCF7Submit');
add_action('rest_api_init', 'registerRestRoutes' );
//add_action( 'plugins_loaded', 'checkForDatabaseUpdates' );
add_action( 'init', 'loadTranslations');

register_activation_hook( __FILE__, 'activateMFP' );
register_deactivation_hook( __FILE__, 'deactivateMFP' );
