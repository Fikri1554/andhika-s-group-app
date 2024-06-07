<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="andhika group">
    <meta name="keyword" content="andhika line, andhika group, andhika">
    <link rel="icon" href="<?php echo base_url("assets/img/andhika.gif"); ?>">
    <title>LOGIN - PURCHASING</title>

    <link href="<?php echo base_url(); ?>assets/css/bootstrap.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href="<?php echo base_url(); ?>assets/css/style.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/style-responsive.css" rel="stylesheet">
    <script src="<?php echo base_url();?>assets/js/jquery.js"></script>
    <script src="<?php echo base_url();?>assets/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery.backstretch.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
			$("#txtPass").keyup(function(event) {
				if (event.keyCode === 13) {
					$("#btnLogin").click();
				}
			});
            $("#btnLogin").click(function(){
              var user = $("#txtUser").val();
              var pass = $("#txtPass").val();
              $("#lblAlertUser").text("");

              if (user == "")
              {
                $("#lblAlertUser").text("User ID don't Empty..!!");
                return false;
              }
              if(pass == "")
              {
                $("#lblAlertUser").text("Password don't Empty..!!");
                return false;
              }
              
              $.post('<?php echo base_url("purchasing/login"); ?>',
              {   
                user : user,pass : pass
              },
                function(data) 
                { 
                    if(data)
                    {
                      window.location = "<?php echo base_url('purchasing/home');?>";
                    }else{
                      $("#lblAlertUser").text("Your Account Not Active..!!");
                      return false;
                    }
                },
              "json"
              );

            });
        });
        function showPass()
        {
            var x = document.getElementById("txtPass");
            if (x.type === "password")
            {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }
    </script>

  </head>
  <body style="background-color:#9c9c9c;">
	  <div id="login-page">
	  	<div class="container">
		      <form class="form-login">
		        <h2 class="form-login-heading" style="background-color:#A70000;font-weight:bold;font-size:28px;">
               <i class="fa fa-user-circle-o"></i> Purchasing
            </h2>
		        <div class="login-wrap">
		          <input type="text" id="txtUser" name="txtUser" class="form-control" placeholder="User ID" autofocus>
		          <br>
		            <input type="password" id="txtPass" name="txtPass" class="form-control" placeholder="Password">
              <hr>
              <label class="form-check-label">
                <input type="checkbox" id="idShowPass" name="idShowPass" class="form-check-input" onclick="showPass();" > Show Password
              </label>
              <br>
                <small id="lblAlertUser" class="form-text text-muted" style="color: red;"></small>
						    <button class="btn btn-danger btn-block" id="btnLogin" type="button"><i class="fa fa-lock"></i> SIGN IN</button>
		          <hr style="margin: 10px 0px 10px 0px;">
              <center>
                <label style="font-size: 11px;">Copyright @ <?php echo date("Y"); ?> Andhika Group</label>
              </center>
		        </div>
		      </form>	  	
	  	</div>
	  </div>
  </body>
  <script>
    $.backstretch("<?php echo base_url(); ?>assets/img/bgMyApps.jpg", {speed: 1200});
  </script>
</html>
