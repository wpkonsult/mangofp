<?php

class AdminRoutes {
    private $routes;
    public function __construct() {
        $this->routes = [
            ['endpoint' => '/labels', 'method' => 'GET', 'callback' => 'getLabels'],
            ['endpoint' => '/messages', 'method' => 'GET', 'callback' => 'getMessages'],
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
                    'id' => '1',
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
                    'id' => '2',
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
                    'id' => '3',
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
                    'id' => '4',
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
}