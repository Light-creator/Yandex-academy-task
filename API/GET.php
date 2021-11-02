<?php
namespace API;

use Services\DB_PDO;
use Services\Response;
use Services\Validation;
use \DateTime;

class GET {
    
    public static function handle($url) {
        $url_arr = array_values(array_filter(explode('/', $url)));
        
        DB_PDO::connection();
        if(count($url_arr) == 3 && $url_arr[0] == 'imports' && is_numeric($url_arr[1]) == true && $url_arr[2] == 'citizens') {

            $citizens = DB_PDO::$pdo->query("SELECT * FROM citizen WHERE import_id=".$url_arr[1])->fetchAll();

            Response::response(200, $citizens);

        } else if(count($url_arr) == 4 && $url_arr[0] == 'imports' && is_numeric($url_arr[1]) == true && $url_arr[2] == 'citizens' && $url_arr[3] == 'birthdays') {
            
            $citizens = DB_PDO::$pdo->query("SELECT * FROM citizen WHERE import_id=".$url_arr[1])->fetchAll();
            $list_month_presents = self::filter_birth($citizens);

            print_r($list_month_presents);
        } else {
            Response::response(400, 'Bad url');
        }
        DB_PDO::close();
    }

    private static function filter_birth($citizens) {
        $birth = [
            'data' => []
        ];
        
        for ($i=1; $i <= 12; $i++) { 
            $birth['data'][$i] = [];
            $list_prensents = [];

            foreach($citizens as $citizen) {
                $citizen_month = (int) explode('.', $citizen['birth_date'])[1];
                if($i == $citizen_month) {
                    $list_prensents = array_merge($list_prensents, json_decode($citizen['relatives']));
                    unset($citizen);
                }
            }

            $uniques = array_unique($list_prensents);
            foreach($uniques as $citizen_id) {
                $presents = count(array_keys($list_prensents, $citizen_id));

                $birth['data'][$i][] = [
                    'citizen_id' => $citizen_id,
                    'presents' => $presents
                ];
            }

        }
        return $birth;
    }

}