<?php
require_once '../config/db.php';
require_once 'DynamicForm.php';
require_once 'SubmitForm.php';
$conn = Db::getInstance();

$objDynamicForm = new DynamicForm($conn);

// if call is for interacting with dynamic form

if (strpos($_SERVER['PATH_INFO'], 'dynamicForm')!== false){
   
    //save form into database request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     
        $postData = json_decode(file_get_contents('php://input'), true);
       echo $objDynamicForm->create($postData);
       exit;
    
    }elseif($_SERVER['REQUEST_METHOD'] === 'GET'){
       // request for getting a single record
        if(!empty($_GET['id'])){
            echo $objDynamicForm->getForm($_GET['id']);
        }else{
            // else return all form list
            echo $objDynamicForm->getAllForms();
        }
    }
    
// when user submit the form
}elseif(strpos($_SERVER['PATH_INFO'], 'submitForm')!== false){
    $objsubmitForm = new SubmitForm($conn, $objDynamicForm);
    $postData = $_POST;  
    echo $objsubmitForm->saveEntry($postData);
}

?>