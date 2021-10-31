<?php
namespace API;

class PATCH {
    
    public static function handle($url) {
        $postData = file_get_contents('php://input');
        $data = json_decode($postData, true);
        print_r($data);
    }

}