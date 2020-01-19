<?php

class AdminRoutes {
    private $routes;
    public function __construct() {
        $this->routes = [
            [ 'endpoint' => '/labels', 'method' => 'GET', 'callback' => 'getLabels'],
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
}