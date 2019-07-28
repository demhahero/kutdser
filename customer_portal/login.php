<?php
include_once "header.php";
 ?>
 <title>Customer Portal</title>
 <script>
     $(document).ready(function () {
         $("#login_form").submit(function (e) {
           e.preventDefault();
           var username = $("input[name=\"username\"]").val();
           var password = $("input[name=\"password\"]").val();

           $.post("<?= $api_url ?>authentication/authentication_api.php",
                   {
                     action:"login",
                     username: username,
                     password: password,
                     customer:"yes"
                   }
           , function (data_response, status) {
               data_response = $.parseJSON(data_response);
               if (data_response.login == true) {
                 window.location.href = 'orders/customer_orders.php';
               } else
               {
                   alert(data_response.message);

                 }
           });
         });
     });
 </script>

 <style>
 body {font-family: Arial, Helvetica, sans-serif;}
 form {border: 3px solid #f1f1f1;}

 input[type=text], input[type=password] {
   width: 100%;
   padding: 12px 20px;
   margin: 8px 0;
   display: inline-block;
   border: 1px solid #ccc;
   box-sizing: border-box;
 }

 button {
   color: white;
   padding: 14px 20px;
   margin: 8px 0;
   border: none;
   cursor: pointer;
   width: 100%;
 }

 button:hover {
   opacity: 0.8;
 }

 .cancelbtn {
   width: auto;
   padding: 10px 18px;
   background-color: #f44336;
 }




 .container {
   padding: 16px;
 }

 span.psw {
   float: right;
   padding-top: 16px;
 }

 /* Change styles for span and cancel button on extra small screens */
 @media screen and (max-width: 300px) {
   span.psw {
      display: block;
      float: none;
   }
   .cancelbtn {
      width: 100%;
   }
 }
 </style>
 </head>
 <body>

 <h2>Login Form</h2>

 <form id="login_form">


   <div class="container">
     <label for="username"><b>Username</b></label>
     <input type="text" placeholder="Enter Username" name="username" required>

     <label for="password"><b>Password</b></label>
     <input type="password" placeholder="Enter Password" name="password" required>

     <button class="btn btn-default" type="submit">Login</button>
     <!-- <label>
       <input type="checkbox" checked="checked" name="remember"> Remember me
     </label> -->
   </div>

   <!-- <div class="container" style="background-color:#f1f1f1">
     <button type="button" class="cancelbtn">Cancel</button>
     <span class="psw">Forgot <a href="#">password?</a></span>
   </div> -->
 </form>

 <?php
 include_once "footer.php";
 ?>
