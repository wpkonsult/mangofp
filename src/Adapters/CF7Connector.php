<?php
namespace MangoFp;

class CF7Connector {
    public static function actionCF7Submit( $result ) {
        $submission = \WPCF7_Submission::get_instance();
        if (
            !$submission ||
            !$posted_data = $submission->get_posted_data()
        ) {
            error_log('No posted data');
            return;
        }

        error_log('Posted data:' . print_r($posted_data, 1));
        $useCase = new UseCases\MessageUseCase(
            new AdminRoutes(),
            new MessagesDB()
        );
        $useCase->parseContentAndInsertToDatabase($posted_data);
    }
}