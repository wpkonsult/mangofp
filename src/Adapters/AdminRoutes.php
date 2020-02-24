<?php

namespace MangoFp;
use MangoFp\UseCases\iOutput;

class AdminRoutes implements iOutput {
    private $routes;
    public function __construct() {
        $this->routes = [
            ['endpoint' => '/labels', 'method' => 'GET', 'callback' => 'getLabels'],
            ['endpoint' => '/messages', 'method' => 'GET', 'callback' => 'getMessages'],
            ['endpoint' => '/messages', 'method' => 'POST', 'callback' => 'postMessage'],
            ['endpoint' => '/messages/(?P<uuid>[a-zA-Z0-9-]+)', 'method' => 'POST', 'callback' => 'changeMessage'],
        ];
        $version='1';
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
        $useCase = new UseCases\LabelsUseCase(
            $this, 
            new MessagesDB()
        );
        return $useCase->fetchAllLabelsToOutput();
    }

    public function getMessages() {
        $useCase = new UseCases\MessageUseCase(
            $this, 
            new MessagesDB()
        );
        return $useCase->fetchAllMessagesToOutput();
    }

    public function postMessages($request) {
        error_log('Data submitted for update: ' . json_encode($request->get_params(), true));
    }
    
    public function changeMessage($request) {

        //error_log('Data submitted for put: ' . json_encode($request->get_params(), true));
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
    public function outputResult(array $data) {
        return new \WP_REST_Response([
                'payload' => $data,
                'status'=> iOutput::RESULT_SUCCESS 
            ], 
            200 
        );
    }

    public function outputError(string $message, string $errorCode) {
        $error = new \WP_REST_Response( [
                'status' => iOutput::RESULT_ERROR,
                'error' => [
                    'code'=> $errorCode,
                    'message' => $message
                ]
            ] 
        ); 
        $error->set_status(404);
        return $error;
    }
}