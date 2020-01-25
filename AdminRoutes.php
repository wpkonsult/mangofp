<?php

function __generateUuid() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
    
            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),
    
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,
    
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,
    
            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }

class AdminRoutes {
    private $routes;
    public function __construct() {
        $this->routes = [
            ['endpoint' => '/labels', 'method' => 'GET', 'callback' => 'getLabels'],
            ['endpoint' => '/messages', 'method' => 'GET', 'callback' => 'getMessages'],
            ['endpoint' => '/messages', 'method' => 'POST', 'callback' => 'postMessage'],
            ['endpoint' => '/messages/(?P<uuid>[a-zA-Z0-9-]+)', 'method' => 'PUT', 'callback' => 'putMessage'],
        ];
        $version='1';
    }

    public function registerRestRoutes() {
        foreach ($this->routes as $key => $route) {
            register_rest_route( 
                'peaches', 
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
        $labels =  [
                [ 'id' => '001', 'name' => 'Praktiline arvuti baaskoolitus' ],
                [ 'id' => '002', 'name' => 'MS Office komplekskoolitus' ],
                [ 'id' => '003', 'name' => 'Exceli baaskursus' ],
                [ 'id' => '004', 'name' => 'Funktsioonid ja valemid Excelis' ],
                [ 'id' => '005', 'name' => 'Fotograafia ABC' ],
                [ 'id' => '006', 'name' => "PhotoShop'i algkoolitus" ],
                [ 'id' => '007', 'name' => 'Tootefoto pildistamine ja töötlus' ],
                [ 'id' => '008', 'name' => 'Esmaabi' ],
                [ 'id' => '009', 'name' => 'Canva' ],
                [ 'id' => '010', 'name' => 'Sketchup' ],
                [ 'id' => '011', 'name' => 'Sketchup edasijõudnutele' ],
        ];
        return new WP_REST_Response( ['labels' => $labels], 200 );
    }

    public function getMessages() {
        $messages = [
                [
                    'id' => __generateUuid(),
                    'form' => 1,
                    'labelId' => '009',
                    'code' => 'NEW',
                    'state' => 'Uus',
                    'email' => 'kati.kaalikas@test.com',
                    'name' => 'Kati',
                    'content' => [
                        'name' => 'Kati Kaalikas',
                        'message' => 'Tahan teha ilusaid asju'
                    ],
                ],
                [
                    'id' => __generateUuid(),
                    'form' => 1,
                    'labelId' => '009',
                    'code' => 'NEW',
                    'state' => 'Uus',
                    'email' => 'mati.kaalikas@test.com',
                    'name' => 'Mati',
                    'content' => [
                        'name' => 'Mati Kaalikas',
                        'message' => 'Mul on vaja aiakuur joonestada'
                    ],
                ],
                [
                    'id' => __generateUuid(),
                    'form' => 2,
                    'labelId' => '010',
                    'code' => 'WAIT4ACCEPT',
                    'state' => 'Aeg pakutud',
                    'email' => 'uudo.uugamets@test.com',
                    'name' => 'Uudo',
                    'content' => [
                        'name' => 'Uudo Uugamets',
                        'message' => 'Tahan tulla Sketchupi kursusele, aga aeg ei sobi'
                    ]
                ],
                [
                    'id' => __generateUuid(),
                    'form' => 2,
                    'labelId' => '009',
                    'code' => 'NEW',
                    'state' => 'Uus',
                    'email' => 'mati.kaalikas@test.com',
                    'name' => 'Mati',
                    'content' => [
                        'name' => 'Mati Kaalikas',
                        'message' => 'Ja sketchupiga tahaks kah koerakuuti joonistada'
                    ],
                ],
        ];

        return new WP_REST_Response( ['messages' => $messages], 200 );
    }

    public function postMessage($request) {
        error_log('Data submitted for update: ' . json_encode($request->get_params(), true));
    }
    
    public function putMessage($request) {
        error_log('Data submitted for put: ' . json_encode($request->get_params(), true));
    }
}