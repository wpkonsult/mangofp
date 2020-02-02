<?php
namespace MangoFp;

class CF7Connector {
    public function actionCF7Submit( $instance, $result ) {
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
        error_log('Title:' . wp_title( '&raquo;', true, '' ) );
    }
}