<?php
namespace Services;

use API\GET;
use API\POST;
use API\PATCH;

class App {

    public static function run() {
        $method = $_SERVER['REQUEST_METHOD'];
        $url = $_SERVER['REQUEST_URI'];

        if($method == "POST") {
            return POST::handle($url);
        } else if($method == "GET") {
            return GET::handle($url);
        } else if($method == "PATCH") {
            return PATCH::handle($url);
        } else {
            return json_encode(
                [
                    'code' => 400,
                    'message' => 'Bad requests'
                ]
            );
        }
    }

    private static function get_uri_data() {
        $method = $_SERVER['REQUEST_METHOD'];
        $url = $_SERVER['REQUEST_URI'];

        print_r($url);
    }

}