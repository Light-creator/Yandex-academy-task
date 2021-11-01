<?php
namespace Services;

class Response {

    public static function response($code, $msg) {
        http_response_code($code);
        json_encode($msg);
    }

}