<?php
namespace API;

use Services\DB_PDO;
use Services\Response;
use Services\Validation;

class POST {
    
    public static function handle($url) {
        
        $postData = file_get_contents('php://input');
        $data = json_decode($postData, true);

        if($url == '/imports') {

            DB_PDO::connection();
            $import_id = DB_PDO::$pdo->query("SELECT import_id FROM `import`")->fetch()['import_id'];

            if(Validation::relatives_valid($data['citizens']) == false) {
                Response::response(400, 'Bad Requests');
                die();
            }

            foreach($data['citizens'] as $citizen) {

                $fields = self::create_fields($citizen, $import_id);

                $query_string = "INSERT INTO  citizen ". $fields['fields'] ." VALUES ". $fields['questions'] ."";
                DB_PDO::$pdo->prepare($query_string)->execute($citizen);
            }
            
            Response::response(201, 'import_id: '.$import_id);

            DB_PDO::$pdo->prepare("UPDATE `import` SET import_id=". ++$import_id ." WHERE id=1")->execute();
            DB_PDO::close();

        } else {
            Response::response(400, 'Bad Requests');
        }
    }

    private static function create_fields($citizen, $import_id) {

        $fields = '(';
        $questions = '(';

        foreach($citizen as $key => $val) {
            $fields .= $key.",";
            $questions .= ':'.$key.",";

            if($key == 'birth_date') {
                if(!Validation::date_valid($val)) {
                    Response::response(400, 'Bad Requests');
                }
            }
        }

        $fields .= "import_id)";
        $questions .= $import_id.")";

        return [
            'fields' => $fields,
            'questions' => $questions
        ];
    }

}