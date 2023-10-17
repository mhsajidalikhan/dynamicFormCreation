<?php
/**
 * @details : the class is used to handle the data submitted by dynamic form
 * 
 */
class SubmitForm {

    private $dbConn = '';
    private $dynamicFormInstance = '';



 /***
     * @detail:The constructor takes the two classes instances
     * $conn : DB instance
     * $dynamicFormInstance: Instance of dynamic class
     * 
     */
public function __construct($conn, $dynamicFormInstance){
$this->dbConn = $conn;
$this->dynamicFormInstance = $dynamicFormInstance;
}

public function createForm($data){

}

 /***
     * @detail: loop request data and remove the empty values in case of radio or checkboxes
     * call the validation method for validating data, if the validation passed then save the entry in database
     * otherwiser retruns the corresponding error
     * 
     * @params: $rawdata: data recieved in request in raw form    
     * @return: ARRAY status:Boolean, true or false,  message:STRING, correspondng message
     */
public function saveEntry($rawdata)
    {   
        try{   
            $data = [];   
            // remove blank value for handling checkbox and radios multiple values  
                foreach($rawdata as $key => $value){
                    if(is_array($value)){
                        $data[$key] =  array_filter($value);
                    }else{
                        $data[$key] = $value;
                    }
                    
                }
               
                $formData = $this->dynamicFormInstance->getForm($data['formId']);
                $formData = json_decode($formData, true)['data'];
                $this->sentEmail($formData['formRecipientEmail'],$formData['formData'], $data);
                $isValid = $this->validateData($data, $formData['formData']);
                if($isValid['status'] === false){
                    echo json_encode($isValid);
                    exit;
                }
          
                    $queryStr = "INSERT INTO tbl_submitform (recipientEmail,submitFormData,dynamicFormId) VALUES (:recipientEmail,:submitFormData,:dynamicFormId)";
                
                    $stmt = $this->dbConn->prepare($queryStr);
                //  $stmt->bindParam('formName', $data['formName'],PDO::PARAM_STR);
                    $submittedData = json_encode($data);
                    $stmt->bindParam('recipientEmail', $formData['formRecipientEmail'],PDO::PARAM_STR);
                    $stmt->bindParam('submitFormData', $submittedData, PDO::PARAM_STR);                    
                    $stmt->bindParam('dynamicFormId', $data['formId'],PDO::PARAM_INT);
                    if ($stmt->execute()) {
                        $this->sentEmail($formData['formRecipientEmail'],$formData['formData'], $data);
                        return json_encode(array('status' => true, 'message' => 'The Form has been submitted.'));
                    } else {
                        return json_encode(array('status' => false, 'message' => 'Something went wrong.'));
                    }      
            }catch (Exception $e) {
                //display custom message
               
                return json_encode(array('status' => false, 'message' => $e->getMessage()));
              }

    }



  /***
     * @detail: loop request data and gets the corresponding validation from the database data and then send
     * both to validate method for validation.
     * @params: $recepient:STRING, recipient email address,
     * $formInstance:ARRAY  fields data:ARRAY, fetch from database, 
     * $submitFormData: data recieved in request
     * @return: Always returns true
     */
    public function sentEmail($recepient,$formInstance, $submitFormData)
    {   
        try{
            $to = $recepient;
            $subject = 'Information submassion';
            $body = $this->emailBody($formInstance, $submitFormData);
            @mail($to, $subject, $body);
            return true;
        }catch(Exception $e){
            return true;
        }
    }
   
    /***
     * @detail: Used to compose email body
     * @params: $formData:ARRAY  fields data:ARRAY, fetch from database, $submitFormData: data recieved in requrest
     * @return:STRING return email body compring of string.
     */
    public function emailBody($formData, $submitFormData){
       
        $formData = json_decode($formData, true);
       $body = "<div>Below information is submitted via form</div>";
       $body .='<table width="90%" border="0">
                <tr>
                <th>Name</th>
                <th> Value</th>
                </tr>
            ';
        foreach($formData as $item){
            if($item['allowedInEmail'] == true){
                $body.= "<tr><td>".$item['fieldName']."</td><td>".$submitFormData[$item['fieldName']]."</td></tr>";
            }
        }
        $body.= "</table>";        
        return $body;

    }
    /***
     * @detail: loop request data and gets the corresponding validation from the database data and then send
     * both to validate method for validation.
     * @params: $data:ARRAY, request data , $fieldData:JSON, fields data, fetch from database
     * @return: ARRAY status:Boolean, true or false,  message:STRING, correspondng error message
     */
    function validateData($data, $formData) {   
     //  echo '<pre>', print_r($data), '</pre>'; exit;
        $validationStatus=[];
        $formData =  json_decode($formData,true);    
       foreach($data as $key =>  $value){
      
        $validationRule = $this->getValidation($key, $formData);        
        if (empty($validationRule['validationRule'])) {
           
            $validationStatus = ['status'=> true, 'message' =>  '' ];
            continue;
        }
      
        $rules = explode('|', $validationRule['validationRule']);
        for ($j = 0; $j < count($rules); $j++) {
            $validationStatus = $this->validateValue($value, $rules[$j], $validationRule['label']);
            
            if ($validationStatus['status'] == false) {           
                break 2;
            }
        }     
        
       }
       
   
        return $validationStatus;
    }
      /***
     * @detail: extract validation from the database data    
     * @params: $fieldName:STRING, name of the field in requested data, 
     * $fieldData:ARRAY, fields data, fetched from database
     * @return: ARRAY label:STRING,label name,  validationRule:STRING, validation rules
     */
    public function getValidation($fieldName, $formData){      
        $values = [];
        for($i=0; $i < count($formData); $i++){
            if($formData[$i]['fieldName'] == $fieldName){
            $values =  ['label' => $formData[$i]['fieldLabel'], 'validationRule' => $formData[$i]['validationRule']??''];
                break;
            }
            
       }       
       return $values;
    }
    

    
      /***
     * @detail: validate the values against the validation rules.  
     * @params: $value:STRING, value to be validated, 
     * $validationRule:STRING, rules need to be validate
     * $fieldLabel:STRING, label of the field use to compose a error message  
     **@return: ARRAY status:STRING, (true or false),  message:STRING, n case of validation fail
     */
    function validateValue($value, $validationRule, $fieldLabel) {
        $rules = explode(':',$validationRule);
       
        $status = ['status' => true, 'message' => '' ];      
        switch ($rules[0]) {
            case 'required':
                if (empty($value)) {
                    return ['status' => false, 'message' => $fieldLabel . ' is required.' ];
                }
                break;
            case 'minLength':
                if (strlen($value) < $rules[1]) {
                    return ['status' => false, 'message' => $fieldLabel . ' should not be less than ' . $rules[1] . ' words.' ];
                }
                break;
            case 'maxLength':
                if (strlen($value) > $rules[1]) {
                    return ['status' => false, 'message' => $fieldLabel . ' should not be greater than ' . $rules[1] . ' words.' ];
                }
                break;
            case 'min':
                if ($value < $rules[1]) {
                    return [ 'status' => false, 'message' => $fieldLabel + 'The value should not be greater than ' . $rules[1] ];
                }
                break;
            case 'max':
                if ($value > $rules[1]) {
                    return ['status' => false, 'message' => $fieldLabel + 'should not be less than ' . $rules[1] . ' words.' ];
                }
                break;
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {                  
                    return [ 'status' => false, 'message' => $fieldLabel . ' does not have a valid email address.' ];
                  }
                
                break;
    
        }
        return $status;
    }


}
