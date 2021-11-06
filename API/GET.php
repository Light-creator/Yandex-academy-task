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
        if($url_arr[0] == 'imports' && is_numeric($url_arr[1]) == true) {
            $citizens = DB_PDO::$pdo->query("SELECT * FROM citizen WHERE import_id=".$url_arr[1])->fetchAll();

            if(count($url_arr) == 3 && $url_arr[2] == 'citizens') {
                Response::response(200, $citizens);
            } else if(count($url_arr) == 4 && $url_arr[2] == 'citizens' && $url_arr[3] == 'birthdays') {
                $list_month_presents = self::filter_birth($citizens);
                Response::response(200, $list_month_presents);
            } else if(count($url_arr) == 6 && $url_arr[2] == 'towns' && $url_arr[3] == 'stat' && $url_arr[4] == 'percentile' && $url_arr[5] == 'age') {
                $list_stat = self::stat_for_ages($citizens);
                print_r($list_stat);
                die();
            }
        }
        Response::response(400, 'Bad url');
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

    private static function get_ages_for_city($citizens) {
        $arr = [];

        foreach($citizens as $citizen) {
            $date = new DateTime($citizen['birth_date']);
            $now = new DateTime();
            $age = (int)$now->diff($date)->y;

            $arr[$citizen['town']][] = $age;
        }

        return $arr;
    }

    private static function stat_for_ages($citizens) {
        $stat = [
            'data' => []
        ];
        $ages = self::get_ages_for_city($citizens);
        foreach($ages as $key => $val) {
            sort($val);
            $p50 = round(count($val)) * 0.5 - 1;
            $p75 = round(count($val)) * 0.75 - 1;
            $p99 = round(count($val)) * 0.99 -1;

            $stat['data']['town'] = $key;
            $stat['data'][$key]['p50'] = $val[$p50];
            $stat['data'][$key]['p75'] = $val[$p75];
            $stat['data'][$key]['p99'] = $val[$p99];
        }
        return $stat;
    }

}