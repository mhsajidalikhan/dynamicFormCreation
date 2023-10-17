<?php
class DynamicForm {

    private $dbConn = '';


// Prevent from creating instance
public function __construct($conn){
$this->dbConn = $conn;
}

public function createForm($data){

}

 /***
     * @detail: Validate the field type and save the entry into database.
     * @params: $data: data recieved in request
     * @return: ARRAY status:Boolean, true or false,  message:STRING, correspondng message
     */
public function create($data)
    {   
      //  echo '<pre>', print_r($data), '</pre>'; exit;
        $isValidTypeResponse = $this->isFormhHaveValidFieldType($data['formFields']);
        if($isValidTypeResponse['status'] === false){
            echo json_encode($isValidTypeResponse);
            exit;
        }    
            $formFields = json_encode($data['formFields']);
            $queryStr = "INSERT INTO tbl_Forms (formName, formData,formRecipientEmail) VALUES (:formName,:formFields,:formRecipientEmail)";
            $stmt = $this->dbConn->prepare($queryStr);
            $stmt->bindParam('formName', $data['formName'],PDO::PARAM_STR);
            $stmt->bindParam('formFields', $formFields, PDO::PARAM_STR);
            $stmt->bindParam('formRecipientEmail', $data['formRecipientEmail'], PDO::PARAM_STR);
            if ($stmt->execute()) {
                return json_encode(array('status' => 'success', 'message' => 'Form has been created.'));
            } else {
                return json_encode(array('status' => 'fail', 'message' => 'Something went wrong.'));
            }      

    }
     /***
     * @detail: check the form fields type
     * @params: $formData: data recieved in request
     * @return: ARRAY status:Boolean, true or false,  message:STRING, correspondng message
     */
    private function isFormhHaveValidFieldType($formData){
     
        $allowedType = ['input', 'textarea', 'select', 'radio', 'checkbox'];
        $invalidFields = [];
        foreach($formData as $element){
            if(!in_array($element['fieldType'],$allowedType)){
                $invalidFields = $element['fieldType'];
            }

        }
       
        if(count($invalidFields) > 0){
            return ['status' => false, 'message' => implode(',', $invalidFields).' have invalid field types'];
        }else{
            return ['status' => true, 'message' => 'All fields have valid field type'];
        }
    }
      /***
     * @detail: Fetch all saved form from the database and returns  
     * @return: JSON, return all forms list
     */
   
    public function getAllForms()
    {
        $query = "SELECT formID, formName, formData, formRecipientEmail
        FROM tbl_Forms";
        $stmt = $this->dbConn->prepare($query);
        $stmt->execute();
       // $resultSet = $stmt->get_result();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);       
        if (count($result) > 0) {
            return json_encode(
                [
                    'status' => true,
                    'data' => $result
                ]
            );
        } else {
            return json_encode(
                [
                    'status' => false,
                    'data' => 'No data found'
                ]
            );
        }


    }
      /***
     * @detail: returna a single form corresponding to formId
     * @params: $formID: the ID of records
     * @return: JSON, status:Boolean, true or false, data: returns the corresponding record or error message
     * in case if the record not found
     */
    public function getForm($formID)
    {
        $query = "SELECT formID, formName, formData, formRecipientEmail
        FROM tbl_Forms WHERE formID = :formID";
        $stmt = $this->dbConn->prepare($query);
        $stmt->bindParam('formID', $formID, PDO::PARAM_INT);
        $stmt->execute();       
        $result = $stmt->fetch(PDO::FETCH_ASSOC);       
        if ($result) {
            return json_encode(
                [
                    'status' => true,
                    'data' => $result
                ]
            );
        } else {
            return json_encode(
                [
                    'status' => false,
                    'data' => 'No data found against this Id'
                ]
            );
        }


    }


}
