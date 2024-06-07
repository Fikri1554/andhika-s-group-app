<?php $this->load->view("purchasing/menu"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<script type="text/javascript">
		$(document).ready(function(){
		});
		function saveData()
		{
			var newPass = $("#txtNewPass").val();
			var cfmPass = $("#txtConfirmPass").val();

			if(newPass == "")
			{
				alert("New Password Empty..!!");
				return false;
			}

			if(newPass != cfmPass)
			{
				alert("New Password dan confirm Password Tidak sama..!!!");
				return false;
			}

			$.post('<?php echo base_url("purchasing/changePass"); ?>',
			{ newPass:newPass, cfmPass:cfmPass },
				function(data)
				{
					alert(data);
					window.location = "<?php echo base_url('purchasing/logout');?>";
				},
			"json"
			);
		}
		function showPass()
        {
            var x = document.getElementById("txtNewPass");
            if (x.type === "password"){ x.type = "text"; } else { x.type = "password"; }
            var y = document.getElementById("txtConfirmPass");
            if (y.type === "password"){ y.type = "text"; } else { y.type = "password"; }
        }
		function reloadPage()
		{
			window.location = "<?php echo base_url('purchasing/getChangePass');?>";
		}
	</script>
</head>
<body>
	<section id="container">
		<section id="main-content">
			<section class="wrapper site-min-height" style="min-height:400px;">
				<div class="form-panel" id="idDataTable">
					<div class="row mt" id="idData1">
						<div class="col-md-12 col-xs-12" style="text-align:right;">
							<legend>
								<img src="<?php echo base_url('assets/img/loading.gif'); ?>" style="display:none;margin-right:5px;" >
								<b><i>:: Change Password ::</i></b>
							</legend>
						</div>
						<div class="col-md-3 col-xs-12">
							<label for="txtNewPass">New Password :</label>
							<input type="password" class="form-control input-sm" id="txtNewPass" value="" placeholder="New Password">
						</div>
						<div class="col-md-3 col-xs-12">
							<label for="txtConfirmPass">Confirm Password :</label>
							<input type="password" class="form-control input-sm" id="txtConfirmPass" value="" placeholder="Confirm Password">
						</div>
						<div class="col-md-2 col-xs-12">
							<input type="checkbox" id="idShowPass" name="idShowPass" class="form-check-input" onclick="showPass();" > 
							<label for="idShowPass">&nbsp Show Password</label>
						</div>
					</div>
					<div class="row" style="margin-top:10px;">
						<div class="col-md-6 col-xs-12">
							<button type="button" class="btn btn-primary btn-xs btn-block" title="Change" onclick="saveData();">
								<i class="fa fa-check"></i> Change
							</button>
						</div>
						<div class="col-md-6 col-xs-12">
							<button type="button" class="btn btn-danger btn-xs btn-block" title="Cancel" onclick="reloadPage();">
								<i class="fa fa-times-circle"></i> Cancel
							</button>
						</div>
					</div>
				</div>
			</section>
		</section>
	</section>
</body>
</html>

