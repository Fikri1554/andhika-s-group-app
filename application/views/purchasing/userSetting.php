<?php $this->load->view("purchasing/menu"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<script type="text/javascript">
		$(document).ready(function(){

			$("#btnSetting").click(function(){
				$("#idDataTable").hide();
				$("#idForm").show(200);
			});
			$("#btnAddUser").click(function(){
				$("#idDataTable").hide();
				$("#idFormAddUser").show(200);
			});

			$("#slcUsrName").change(function(){
				var usr = $(this).val();

				$("#idLoading").show();
		    	$.post('<?php echo base_url("setting/getUserDetail"); ?>',
				{ usr : usr },
					function(data) 
					{							
						$("#txtFullName").val(data.full_name);
						$("#txtUsrName").val(data.username);
						$("#txtNameJbtn").val(data.nameJbtn);
						$("#idLoading").hide();
					},
				"json"
				);
			});

			$("#searchSlcUsrPurch").change(function(){
		    	var usrSrch = $(this).val();

			    $("#idLoading").show();
			    $.post('<?php echo base_url("setting/getDataUsr"); ?>',
				{ usrSrch : usrSrch },
					function(data) 
					{
						$("#idTbody").empty();
						$("#idTbody").append(data.trNya);					
						$("#idLoading").hide();
					},
				"json"
				);
		    });

		    $("#btnSave").click(function(){

		    	var myMenu = $("#slcMyMenu").val();
		    	var vessel = $("#slcVsl").val();		    	
		    	var userName = $("#txtUsrName").val();
		    	var userFull = $("#txtFullName").val();
		    	var jbtn = $("#txtNameJbtn").val();
		    	var userNamePurch = $("#slcUsrPurc").val();
		    	var userFullPurch = $("#slcUsrPurc :selected").text();

		    	$("#idLoading").show();
		    	$.post('<?php echo base_url("setting/addUsrSetting"); ?>',
				{ myMenu : myMenu,vessel : vessel,userName : userName,userFull : userFull,jbtn : jbtn,userNamePurch : userNamePurch,userFullPurch : userFullPurch },
					function(data) 
					{							
						alert(data);
						reloadPage();
					},
				"json"
				);
		    });
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
		    $("#searchSlcUsrName").change(function(){
		    	var usrSrch = $(this).val();

			    $("#idLoading").show();
			    $.post('<?php echo base_url("setting/getDataUsr"); ?>',
				{ usrSrch : usrSrch },
					function(data) 
					{
						$("#idTbody").empty();
						$("#idTbody").append(data.trNya);					
						$("#idLoading").hide();
					},
				"json"
				);
		    });
		});
		function slcVsl(search = "", typeData = "")
		{
			var searchNya = search;

		    $("#idLoading").show();
		    $.post('<?php echo base_url("setting/getUserObs"); ?>',
			{ searchNya : searchNya },
				function(data) 
				{
					if(typeData == "search")
					{
						$("#searchSlcUsrName").empty();
						$("#searchSlcUsrName").append(data);
					}else{
						$("#slcUsrName").empty();
						$("#slcUsrName").append(data);
					}					
					$("#idLoading").hide();
				},
			"json"
			);
		}
		function editData(id)
		{
			$("#idLoading").show();
			$("#lblForm").text("Edit Data");			
			
			$.post('<?php echo base_url("setting/editData"); ?>',
			{ id : id, typeEdit : "editReq" },
				function(data) 
				{
					$("#idDataTable").hide();
					$.each(data, function(i, item)
					{
						$("#txtIdEdit").val(item.id);
						$("#slcVessel").val(item.vessel);
						$("#txtDateReq").val(item.date_request);
						$("#txtAppNo").val(item.app_no);
						$("#txtDept").val(item.department);
					});
					$("#idLoading").hide();
					$("#idForm").show(200);
				},
			"json"
			);
		}
		function delData(id)
		{
			var cfm = confirm("Yakin Hapus..??");
			if(cfm)
			{
				$.post('<?php echo base_url("setting/delData"); ?>',
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
			window.location = "<?php echo base_url('setting/userSetting');?>";
		}
	</script>
</head>
<body>
	<section id="container">
		<section id="main-content">
			<section class="wrapper site-min-height" style="min-height:400px;">
				<h3>
					<i class="fa fa-angle-right"></i> User Setting<span style="display:none;padding-left:20px;" id="idLoading"><img src="<?php echo base_url('assets/img/loading.gif'); ?>" ></span>
				</h3>
				<div class="form-panel" id="idDataTable">
					<div class="row">
						<div class="col-md-2" id="btnNavAtas">
							<button type="button" id="btnSetting" class="btn btn-primary btn-sm btn-block" title="Setting"><i class="glyphicon glyphicon-wrench"></i> Setting</button>
						</div>
						<div class="col-md-2" id="btnNavAtasSlct">
							<select name="searchSlcVessel" id="searchSlcVessel" class="form-control input-sm" onchange="slcVsl($(this).val(),'search');">
								<option value="0">- Select Vessel -</option>
								<?php echo $optVessel; ?>
							</select>
						</div>
						<div class="col-md-2" id="btnNavAtasSlct">
							<select name="searchSlcUsrName" id="searchSlcUsrName" class="form-control input-sm">
								<option value="0">- Select Name -</option>
							</select>
						</div>
						<div class="col-md-2" id="btnNavAtasSlct">
							<select name="searchSlcUsrPurch" id="searchSlcUsrPurch" class="form-control input-sm">
								<option value="">- Select User -</option>
								<?php echo $optUserPurc; ?>
							</select>
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
											<th style="vertical-align: middle; width:20%;text-align:center;">Username</th>
											<th style="vertical-align: middle; width:20%;text-align:center;">Menu</th>
											<th style="vertical-align: middle; width:10%;text-align:center;">Action</th>
										</tr>
									</thead>
									<tbody id="idTbody">
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
				<div class="form-panel" id="idForm" style="display:none;">
					<div class="row">
						<div class="col-md-12">
							<legend><label id="lblForm"> Add Data</label></legend>
							<div class="col-md-3 col-xs-12">
								<div class="form-group">
								    <label for="slcVsl"><u>Vessel :</u></label>
								    <select name="slcVsl" id="slcVsl" class="form-control input-sm" onchange="slcVsl($(this).val(),'form');">
								    	<option value="">- Select -</option>
								    	<?php echo $optVessel; ?>
								    </select>
								</div>
							</div>
							<div class="col-md-3 col-xs-12">
								<div class="form-group">
								    <label for="slcUsrName"><u>Username :</u></label>
								    <select name="slcUsrName" id="slcUsrName" class="form-control input-sm">
								    	<option value="">- Select -</option>
								    </select>
								</div>
							</div>
							<div class="col-md-3 col-xs-12">
								<div class="form-group">
								    <label for="slcUsrPurc"><u>User Purchase :</u></label>
								    <select name="slcUsrPurc" id="slcUsrPurc" class="form-control input-sm">
								    	<option value=""></option>
								    	<?php echo $optUserPurc; ?>
								    </select>
								</div>
							</div>
							<div class="col-md-3 col-xs-12">
								<div class="form-group">
								    <label for="slcMyMenu"><u>My Menu :</u></label>
								    <select name="slcMyMenu" id="slcMyMenu" class="form-control input-sm">
								    	<option value="0">- Select -</option>
								    	<?php echo $optMenu; ?>
								    </select>
								</div>
							</div>
							<div class="form-group" align="center">
								<input type="hidden" name="" id="txtFullName" value="">
								<input type="hidden" name="" id="txtUsrName" value="">
								<input type="hidden" name="" id="txtNameJbtn" value="">
								<button id="btnSave" class="btn btn-primary btn-sm" name="btnSave" title="Save">
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
				</div>
			</section>
		</section>
	</section>
</body>
</html>

