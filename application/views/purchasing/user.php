<?php $this->load->view("purchasing/menu"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#btnSaveUser").click(function(){
		    	$("#idLoading").show();
		    	$.post('<?php echo base_url("setting/addUser"); ?>',
				{ idEditUser : $("#idEditUser").val(),fullName : $("#txtFullName").val(),userName : $("#txtUserName").val(),passWord : $("#txtPass").val(),typeUser : $("#slcTypeUser").val(),position : $("#slcPosition").val() },
					function(data) 
					{							
						alert(data);
						reloadPage();
					},
				"json"
				);
		    });
			$("#btnAddUser").click(function(){
				$("#idDataTable").hide();
				$("#idFormAddUser").show(200);
			});
		});
		function editData(id)
		{
			$("#idLoading").show();
			$("#lblForm").text("Edit Data");			
			
			$.post('<?php echo base_url("setting/getDataEdit"); ?>',
			{ id : id },
				function(data) 
				{
					$("#idDataTable").hide();
					$.each(data, function(i, item)
					{
						$("#idEditUser").val(item.id);
						$("#txtFullName").val(item.name_full);
						$("#txtUserName").val(item.username);
						$("#slcTypeUser").val(item.type);
						$("#slcPosition").val(item.position);
					});
					$("#idLoading").hide();
					$("#idFormAddUser").show(200);
				},
			"json"
			);
		}
		function delData(id)
		{
			var cfm = confirm("Yakin Hapus..??");
			if(cfm)
			{
				$.post('<?php echo base_url("setting/delDataUser"); ?>',
				{ idDel : id },
				function(data) 
				{
					reloadPage();
				},
				"json"
				);
			}			
		}
		function reloadPage()
		{
			window.location = "<?php echo base_url('setting/getUserPurchase');?>";
		}
	</script>
</head>
<body>
	<section id="container">
		<section id="main-content">
			<section class="wrapper site-min-height" style="min-height:400px;">
				<h3>
					<i class="fa fa-angle-right"></i> User<span style="display:none;padding-left:20px;" id="idLoading"><img src="<?php echo base_url('assets/img/loading.gif'); ?>" ></span>
				</h3>
				<div class="form-panel" id="idDataTable">
					<div class="row">
						<div class="col-md-2" id="btnNavAtas">
							<button type="button" id="btnAddUser" class="btn btn-primary btn-sm btn-block" title="Setting"><i class="fa fa-plus-square"></i> Add User</button>
						</div>
						<div class="col-md-2" id="btnNavAtas">
							<button type="button" id="idBtnRefresh" onclick="reloadPage();" class="btn btn-success btn-sm btn-block" title="Refresh"><i class="glyphicon glyphicon-refresh"></i> Refresh</button>
						</div>
					</div>
					<div class="row mt" id="idData1">
						<div class="col-md-12">
							<div class="table-responsive">
								<table class="table table-border table-striped table-bordered table-condensed table-advance table-hover">
									<thead>
										<tr style="background-color: #A70000;color: #FFF;height:40px;">
											<th style="vertical-align: middle; width:5%;text-align:center;">No</th>
											<th style="vertical-align: middle; width:20%;text-align:center;">Full Name</th>
											<th style="vertical-align: middle; width:15%;text-align:center;">Username</th>
											<th style="vertical-align: middle; width:10%;text-align:center;">Type</th>
											<th style="vertical-align: middle; width:10%;text-align:center;">Position</th>
											<th style="vertical-align: middle; width:10%;text-align:center;">Action</th>
										</tr>
									</thead>
									<tbody id="idTbody">
										<?php echo $trNya; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="form-panel" id="idFormAddUser" style="display:none;">
					<div class="row">
						<div class="col-md-12">
							<legend><label id="lblForm"> Add User</label></legend>
							<div class="col-md-3 col-xs-12">
								<div class="form-group">
								    <label for="txtFullName"><u>Full Name :</u></label>
								    <input type="text" class="form-control input-sm" id="txtFullName" placeholder="Full Name" value="">
								</div>
							</div>
							<div class="col-md-3 col-xs-12">
								<div class="form-group">
								    <label for="txtUserName"><u>Username :</u></label>
								    <input type="text" class="form-control input-sm" id="txtUserName" placeholder="User Name" value="">
								</div>
							</div>
							<div class="col-md-2 col-xs-12">
								<div class="form-group">
								    <label for="txtPass"><u>Password :</u></label>
								    <input type="text" class="form-control input-sm" id="txtPass" placeholder="Password" value="">
								</div>
							</div>
							<div class="col-md-2 col-xs-12">
								<div class="form-group">
								    <label for="slcTypeUser"><u>Type User :</u></label>
								    <select name="slcTypeUser" id="slcTypeUser" class="form-control input-sm">
								    	<option value="">- Select -</option>
								    	<option value="administrator">Admin</option>
								    	<option value="user">User</option>
								    </select>
								</div>
							</div>
							<div class="col-md-2 col-xs-12">
								<div class="form-group">
								    <label for="slcPosition"><u>Position :</u></label>
								    <select name="slcPosition" id="slcPosition" class="form-control input-sm">
								    	<option value="">- Select -</option>
								    	<option value="coo">Coo</option>
								    	<option value="finance">Finance</option>
								    	<option value="kadept purch">Kadept Purchase</option>
								    	<option value="kadiv purch">kadiv Purchase</option>
								    	<option value="kadiv shipMgmt">kadiv Ship Management</option>
								    	<option value="si">Super Intendent</option>
								    </select>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group" align="center">
							<input type="hidden" name="" id="idEditUser" value="">
							<button id="btnSaveUser" class="btn btn-primary btn-sm" name="btnSaveUser" title="Save">
								<i class="fa fa-check-square-o"></i>
								Save
							</button>
							<button id="btnCancel" onclick="reloadPage();" class="btn btn-danger btn-sm" name="btnCancel" title="Cancel">
								<i class="fa fa-ban"></i>
								Cancel
							</button>
						</div>
					</div>
				</div>
			</section>
		</section>
	</section>
</body>
</html>

