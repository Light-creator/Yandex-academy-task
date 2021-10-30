<?php
namespace API;

use Services\DB_PDO;

class POST {
    
    public static function handle($url) {
        
        $postData = file_get_contents('php://input');
        $data = json_decode($postData, true);

        if($url == '/imports') {

            DB_PDO::connection();
            $import_id = DB_PDO::$pdo->query("SELECT import_id FROM `import`")->fetch()['import_id'];

            foreach($data['citizens'] as $citizen) {

                $fields = '(';
                $questions = '(';

                foreach($citizen as $key => $val) {
                    $fields .= $key.",";
                    $questions .= ':'.$key.",";
                }

                $fields .= "import_id)";
                $questions .= $import_id.")";

                $query_string = "INSERT INTO  citizen ". $fields ." VALUES ". $questions ."";
                DB_PDO::$pdo->prepare($query_string)->execute($citizen);
            }
            
            $res = [
                'message' => $import_id
            ];

            http_response_code(201);
            echo json_encode($res);

            DB_PDO::$pdo->prepare("UPDATE import SET import_id=". $import_id++)->execute();
            DB_PDO::close();

        } else {
            $res = [
                'message' => 'Bad requests'
            ];

            http_response_code(201);
            echo json_encode($res);
        }
    }

}