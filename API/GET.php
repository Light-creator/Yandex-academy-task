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
            $citizens_with_birth = self::filter_birth($citizens);

        } else {
            Response::response(400, 'Bad url');
        }
        DB_PDO::close();
    }

    private static function filter_birth($citizens, $month) {
        foreach($citizens as $citizen) {
            $citizen_month = explode('.', $citizen[]);
        }
    }

}