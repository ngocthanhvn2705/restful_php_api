<?php 
    header("Access-Control-Allow-Origin:*");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Methods: PUT");
    header("Access-Control-Allow-Headers:Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");
    include_once("../../../model/category.php");
    include_once("../../../config/db_azure.php");

    $db = new db();
    $connect = $db->connect();

    $category = new category($connect);

    $data = json_decode(file_get_contents("php://input"));
    $category->setId($data-> id);
    $category->setName($data-> name);
    

    if($category->update()){
        echo json_encode(array('message', 'category updated'));
    }else{
        echo json_encode(array('message', 'category not updated'));
    }


?>