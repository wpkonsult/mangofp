<?php

namespace MangoFp;

use MangoFp\UseCases\iOutput;

class AdminRoutes implements iOutput {
    private $routes;

    public function __construct() {
        $this->routes = [
            ['endpoint' => '/labels', 'method' => 'GET', 'callback' => 'getLabels'],
            ['endpoint' => '/templates', 'method' => 'GET', 'callback' => 'getTemplates'],
            ['endpoint' => '/states', 'method' => 'GET', 'callback' => 'getStates'],
            ['endpoint' => '/messages', 'method' => 'GET', 'callback' => 'getMessages'],
            ['endpoint' => '/messages', 'method' => 'POST', 'callback' => 'postMessage'],
            ['endpoint' => '/messages/(?P<uuid>[a-zA-Z0-9-]+)/emails', 'method' => 'POST', 'callback' => 'sendEmail'],
            ['endpoint' => '/messages/(?P<uuid>[a-zA-Z0-9-]+)', 'method' => 'POST', 'callback' => 'changeMessage'],
            ['endpoint' => '/messages/(?P<uuid>[a-zA-Z0-9-]+)', 'method' => 'GET', 'callback' => 'getMessageDetails'],
            ['endpoint' => '/attachments', 'method' => 'POST', 'callback' => 'addAttachments'],
            ['endpoint' => '/steps', 'method' => 'GET', 'callback' => 'getSteps'],
            ['endpoint' => '/steps/(?P<code>[a-zA-Z0-9-]+)', 'method' => 'POST', 'callback' => 'updateOrInsertSteps'],
            ['endpoint' => '/steps/(?P<code>[a-zA-Z0-9-]+)/(?P<operation>[a-z]+)', 'method' => 'POST', 'callback' => 'doWithStep'],
            ['endpoint' => '/steps', 'method' => 'POST', 'callback' => 'updateOrInsertSteps'],
            ['endpoint' => '/option', 'method' => 'POST', 'callback' => 'storeOption'],
            ['endpoint' => '/option/(?P<option>[a-z]+)', 'method' => 'GET', 'callback' => 'getOption'],
        ];
        $version = '1';
    }

    public function registerRestRoutes() {
        foreach ($this->routes as $key => $route) {
            register_rest_route(
                'mangofp',
                $route['endpoint'],
                [
                    'methods' => $route['method'],
                    'callback' => [$this, $route['callback']],
                    //'permission_callback' => function () {
                    //    return current_user_can( 'edit_others_posts' );
                    //}
                ]
            );
        }
    }

    public function getLabels() {
        $useCase = new UseCases\SettingsUseCase(
            $this,
            new MessagesDB()
        );

        return $useCase->fetchAllLabelsToOutput();
    }

    public function getTemplates() {
        $useCase = new UseCases\SettingsUseCase(
            $this,
            new MessagesDB()
        );

        return $useCase->fetchAllTemplatesToOutput();
    }

    public function getStates() {
        $useCase = new UseCases\SettingsUseCase(
            $this,
            new MessagesDB()
        );

        return $useCase->fetchAllStatesToOutput();
    }

    public function getSteps() {
        $useCase = new UseCases\SettingsUseCase(
            $this,
            new MessagesDB()
        );

        return $useCase->fetchAllStepsToOutput();
    }

    public function updateOrInsertSteps($request) {
        $params = json_decode(json_encode($request->get_params()), true);

        $useCase = new UseCases\SettingsUseCase(
            $this,
            new MessagesDB()
        );

        return $useCase->updateOrInsertStepAndReturnAllSteps($params);
    }

    public function doWithStep($request) {
        $params = json_decode(json_encode($request->get_params()), true);

        $useCase = new UseCases\SettingsUseCase(
            $this,
            new MessagesDB()
        );

        return $useCase->doWithStep($params['operation'], $params['code']);
    }

    public function storeOption($request) {
        $params = json_decode(json_encode($request->get_params()), true);

        //return $this->outputResult($params);
        $useCase = new UseCases\SettingsUseCase(
            $this,
            new MessagesDB()
        );

        return $useCase->storeOption(
            [
                'key' => $params['key'],
                'value' => $params['value'],
            ]
        );
    }

    public function getOption($request) {
        $params = json_decode(json_encode($request->get_params()), true);
        $option = $params['option'] ?? false;

        $useCase = new UseCases\SettingsUseCase(
            $this,
            new MessagesDB()
        );

        return $useCase->getOption($option);
    }

    public function getMessages() {
        $useCase = new UseCases\MessageUseCase(
            $this,
            new MessagesDB()
        );

        return $useCase->fetchAllMessagesToOutput();
    }

    public function addAttachments($request) {
        $fileParams = $request->get_file_params();
        error_log('Got file params:');
        error_log(print_r($fileParams, true));
        if (
            !$fileParams ||
            !\is_array($fileParams) ||
            !isset($fileParams['files']) ||
            !\is_array($fileParams['files'])
        ) {
            error_log('No files here. Raise error!');

            return;
        }

        $files = $fileParams['files'];
        $attachments = [];
        foreach ($files['name'] as $key => $name) {
            $file = [
                'name' => $files['name'][$key],
                'type' => $files['type'][$key],
                'tmp_name' => $files['tmp_name'][$key],
                'error' => $files['error'][$key],
                'size' => $files['size'][$key],
            ];
            if (UPLOAD_ERR_OK !== $file['error']) {
                error_log('Upload error of file: '.print_r($file, 1));

                continue;
            }
            $_FILES = ['upload_file' => $file];
            $attachId = \media_handle_upload('upload_file', 0);
            if (\is_wp_error($attachId)) {
                error_log('Media handle error!');

                continue;
            }
            $url = \wp_get_attachment_url($attachId);
            $filePath = \get_attached_file($attachId);
            if (!$filePath) {
                error_log('Error storing '.basename($url).' to media library');

                return;
            }

            $newAttachment = [
                'id' => $attachId,
                'url' => $url,
                'file_name' => basename($url),
                'server_path' => $filePath,
            ];
            $newAttattachments[] = $newAttachment;
            error_log('--> Added attachment: '.print_r($newAttachment, 1));
        }

        return $this->outputResult($newAttattachments);
    }

    public function sendEmail($request) {
        $params = json_decode(json_encode($request->get_params()), true);
        $useCase = new UseCases\MessageUseCase(
            $this,
            new MessagesDB()
        );

        return $useCase->sendEmailAndReturnMessage($params['email'], $params['uuid']);
    }

    public function changeMessage($request) {
        $params = json_decode(json_encode($request->get_params()), true);

        $useCase = new UseCases\MessageUseCase(
            $this,
            new MessagesDB()
        );

        if (isset($params['email']) && $params['email']) {
            return $useCase->sendEmailAndUpdateMessageAndReturnChangedMessage($params['email'], $params);
        }

        return $useCase->updateMessageAndReturnChangedMessage($params);
    }

    public function getMessageDetails($request) {
        $params = json_decode(json_encode($request->get_params()), true);
        $useCase = new UseCases\MessageUseCase(
            $this,
            new MessagesDB()
        );

        return $useCase->getMessageDetailsAndReturn($params);
    }

    public function outputResult(array $data) {
        return new \WP_REST_Response(
            [
                'payload' => $data,
                'status' => iOutput::RESULT_SUCCESS,
            ],
            200
        );
    }

    public function outputError(string $message, string $errorCode) {
        $error = new \WP_REST_Response(
            [
                'status' => iOutput::RESULT_ERROR,
                'error' => [
                    'code' => $errorCode,
                    'message' => $message,
                ],
            ]
        );
        $error->set_status(404);

        return $error;
    }
}
