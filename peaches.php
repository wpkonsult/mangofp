<?php
/**
 *  Plugin name: Peaches
 *  Description: User defined states and their management for Contact Form 7 results
 */

//Register Scripts to use 
function registerVueScripts() {
	wp_register_script( 'vuejs', 'https://cdn.jsdelivr.net/npm/vue/dist/vue.js' );
	wp_register_script( 'peaches_main', plugin_dir_url( __FILE__ ).'main.js', 'vuejs', true );
}
add_action('wp_enqueue_scripts', 'registerVueScripts');

function addPeaches() {
	wp_enqueue_script('vuejs');
	wp_enqueue_script('peaches_main');
	return '<div id="peachesMain">{{ message }}</div>';
}

add_shortcode("peaches4cf7", 'addPeaches');