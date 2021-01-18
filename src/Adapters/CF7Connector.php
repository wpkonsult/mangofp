<?php
namespace MangoFp;

class CF7Connector {
    public static function actionCF7Submit( $result ) {
        $submission = \WPCF7_Submission::get_instance();
        $pageTitle = \wp_title('');
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
        if ($pageTitle) {
            $useCase->setTitle($pageTitle);
        }
        $useCase->parseContentAndInsertToDatabase($posted_data);
    }
}