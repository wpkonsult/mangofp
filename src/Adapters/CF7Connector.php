<?php
namespace MangoFp;

class CF7Connector {
    public static function actionCF7Submit( $instance, $result ) {
        global $wpdb;
		
        $cases = ['spam', 'mail_sent', 'mail_failed'];

        if ( 
            empty( $result['status'] ) ||
            !in_array( $result['status'], $cases ) 
        ) {
            error_log('Validation failed, results not stored. Status: ' . $result['status'] ?? 'empty');
            return;
        }

        $submission = \WPCF7_Submission::get_instance();
        if ( 
            !$submission ||
            !$posted_data = $submission->get_posted_data() 
        ) {
            error_log('No posted data');
            return;
        }

        //TODO: Here the $posted_data can be serialised
        error_log('Posted data:' . print_r($posted_data, 1));
        error_log('Title:' . \wp_title( '&raquo;', true, '' ) );

        $table_name = $wpdb->prefix . 'mangofp_labels';
		$request = $wpdb->prepare( 
            "SELECT id, create_time, modify_time, delete_time, label_name
            FROM $table_name
            WHERE label_name LIKE '%s';
            ",
            ['test_label_mida_ei_ole']
        );
        $labelRow = $wpdb->get_row($request);
        error_log('Fetched label: ' . print_r($labelRow, true));
    }
}