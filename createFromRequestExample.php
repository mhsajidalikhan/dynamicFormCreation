<?php

require_once 'config/constant.php';


$url = SITE_URL.'api/index.php/dynamicForm';
$postData = [
    'formName' => 'form3',
    'formRecipientEmail' => 'test@gmail.com',
    'formFields' => [
      [
        'fieldLabel' => 'First Name',
        'fieldName' => 'firstName',        
         'fieldType'=> 'input',          
         'allowedInEmail'=>true,
        'validationRule'=> 'required|minLength:5|maxLength:10',
      ],
      [
        'fieldLabel' => 'Middle Name',
        'fieldName' => 'middleName',      
         'fieldType'=> 'input', 
         'allowedInEmail'=>false,
        'validationRule'=> 'required|minLength:5',
      ],
      [
        'fieldLabel' => 'Last Name',
        'fieldName' => 'lastName',       
         'fieldType'=> 'input', 
         'allowedInEmail'=>true,
        'validationRule'=> 'required|minLength:5|maxLength:10',
      ],
      [
      
        'fieldName' => 'email',
        'fieldLabel' => 'Email',
         'fieldType'=> 'input', 
         'allowedInEmail'=>true,
        'validationRule'=> 'required|email',
      ],
      [
      
        'fieldName' => 'age',
        'fieldLabel' => 'Age',
         'fieldType'=> 'input', 
         'allowedInEmail'=>true,
        'validationRule'=> 'required|min:18|max:50',
      ],
      [
        'fieldLabel' => 'Gender',
        'fieldName' => 'gender',       
         'fieldType'=> 'select', 
         'allowedInEmail'=>false,
        'validationRule'=> 'required',
        'options' => [
          'M' => 'male',
          'f' => 'female'
        ]
      ],
      [  'fieldLabel' => 'Hobbies',
         'fieldName' => 'hobbies',     
         'fieldType'=> 'checkbox',          
         'allowedInEmail'=>false,
        'validationRule'=> 'required',
        
        'options' => [
          'study' => 'Study',
          'sport' => 'Sports',
          'movies' => 'watching Movies'
        ]
        
      ],
      [   
        'fieldLabel' => 'Skin Colour',
        'fieldName' => 'SkinColour',        
         'fieldType'=> 'radio',
         
         'allowedInEmail'=>false,
        'validationRule'=> 'required',
        'options' => [
          'black' => 'Black',
          'white' => 'White',
          'halfWhite' => 'Half White'
        ]
        
     ],
    

    ]

];
$postData = json_encode($postData);
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => $postData,
   CURLOPT_HTTPHEADER => array(
    'accept: application/json, text/plain, */*',
    'content-type: application/json',
      ),
));

$response = curl_exec($curl);
curl_close($curl);
echo $response;
