<?php 
require_once 'config/constant.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Dynamic form creation</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
  <script src="assets/jquery-captcha.min.js"></script>
  <style>
    /* Remove the navbar's default margin-bottom and rounded borders */ 
    .navbar {
      margin-bottom: 0;
      border-radius: 0;
    }
    
    /* Add a gray background color and some padding to the footer */
    footer {
      background-color: #f2f2f2;
      padding: 25px;
    }
    
  .carousel-inner img {
      width: 100%; /* Set width to 100% */
      margin: auto;
      min-height:200px;
  }

 
  </style>
</head>
<script>
var siteURL = '<?=SITE_URL?>';
</script>
<body>
<!---------------------------- Navigation Menu ------------------->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
<div class="container text-center"> 
  <a class="navbar-brand" href="#">Dynamic Form creation</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
    <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
      <li class="nav-item active">
        <a class="nav-link" href="<?=SITE_URL?>">Home</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?=SITE_URL?>createFromRequestExample.php">Add a test reocrd</a>
      </li>
     
    </ul>

  </div>
</div>
</nav>


<!---------------------------- End of Navigation Menu ------------------->


<!---------------------------- Form listings table ------------------->
<div class="text-center loader mh-100" style="min-height:500px">
  <div class="spinner-border" role="status">
    <!-- <span class="sr-only">Loading...</span> -->
  </div>
</div>
  
<div class="container  mt-5"  id="secForms" style="display:none;"> 

  <div class="text-left"><h3>Dynamic Form List</h3></div>    
<table class="table table-bordered">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Recipient Email </th>
      <th scope="col">Form Name</th>
      <th scope="col">Form Data</th>
      <th scope="col">Action</th>
    </tr>
  </thead>
  <tbody id="tblBody">
 
  </tbody>
</table>
</div><br>
<!---------------------------- End Of Form listings table ------------------->



<!----------------------------  dynamic form ------------------->
<div class="container d-flex align-items-center justify-content-center mt-5 mb-5" >  
    <div class="row border border-1 border-secondary p-4 rounded-2" id="form" style="display:none;">

        <div class="col">
              <div class="alert alert-success"  style="display: none;">
            
              </div>
              <div class="alert alert-danger" style="display: none;">
                
              </div>

            <!---------------------------- Display dynamic form ------------------->
              <form id="dynamicFormContainer" action="<?=SITE_URL?>/solution/api/index.php/submitForm" >
            
              </form>
            <!---------------------------- End Of Display dynamic form ------------------->
          </div>
      </div>
</div>
<script src="script.js"></script>
<footer class="container-fluid text-center">
  <p>Dynamic form creation @ <?=date('Y')?></p>
</footer>

</body>

</html>
