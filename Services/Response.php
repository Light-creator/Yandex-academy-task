<?php
namespace Services;

class Response {

    public static function response($code, $msg) {
        $res = [
            'message' => $msg
        ];

        http_response_code($code);
        print_r(json_encode($res));
    }

}