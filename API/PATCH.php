<?php
namespace API;

use Services\DB_PDO;
use Services\Response;
use Services\Validation;

class PATCH {
    
    public static function handle($url) {
        $url_arr = array_values(array_filter(explode('/', $url)));
        if(count($url_arr) >= 4) {
            DB_PDO::connection();

            $postData = file_get_contents('php://input');
            $citizen = json_decode($postData, true);

            $citizen['relatives'] = json_encode($citizen['relatives']);

            $citizen_old = DB_PDO::$pdo->query("SELECT * FROM citizen WHERE import_id=".$url_arr[1]." AND citizen_id=".$url_arr[3])->fetch();

            $fields = self::create_fields($citizen);

            $query_string = "UPDATE citizen SET ". $fields . " WHERE import_id=".$url_arr[1]." AND citizen_id=".$url_arr[3];
            if(DB_PDO::$pdo->prepare($query_string)->execute()) {
                $citizen_new = "SELECT * FROM citizen WHERE import_id=".$url_arr[1]." AND citizen_id=".$url_arr[3];

                if(Validation::relatives_update_valid($citizen_old, $citizen_new)) {
                    self::update_relatives($citizen_old, $citizen_new);
                }

                Response::response(200, 'Ok');
            } else {
                Response::response(400, 'Bad request');
            }
            DB_PDO::close();
        } else {
            Response::response(400, 'Bad url');
        }
        
    }

    private static function create_fields($citizen) {
        $fields = "";
        foreach($citizen as $key => $val) {
            $fields .= ' '.$key.'="'.$val.'",';
        }
        $fields = substr($fields,0,-1);

        return $fields;
    }

    private function update_relatives($citizen_old, $citizen_new) {
        $old_relatives = json_decode($citizen_old['relatives']);
        $new_relatives = json_decode($citizen_new['relatives']);

        foreach($old_relatives as $relation) {
            if(!in_array($relation, $new_relatives)) {
                $citizen_relative = DB_PDO::$pdo->query("SELECT * FROM citizen WHERE import_id=".$citizen_old['import_id']." AND citizen_id=".$relation)->fetch();

                $citizen_relative_relatives = json_decode($citizen_relative['relatives']);
                
                $query_string = "UPDATE citizen SET ". $fields . " WHERE import_id=".$citizen_old['import_id']." AND citizen_id=".$relation;
            }
        }
    }

}