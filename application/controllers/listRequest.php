<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class ListRequest extends CI_Controller {

	function __construct()
	{
		parent::__construct();		
		$this->load->model('mpurchasing');
		$this->load->helper(array('form', 'url'));
	
	}
	function index()
	{
	}

	function getListRequest($searchNya = "",$pageNya = "")
	{
		$dataOut = array();
		$userId = $this->session->userdata('idUserPurchase');
		$userType = $this->session->userdata('userTypePurchase');
		$trNya = "";
		$no = 1;		
		$btnAct = "";
		$limitNya = "";
		$dataOut["listPage"] = "";
		$display = "10";

		$whereNya = "WHERE master_check = '1' AND chief_check = '1' AND sts_delete = '0' AND st_data != '2' AND type_request = 'vessel' ";

		if($userType == "user")
		{
			$sqlUsr = "SELECT * FROM user WHERE id = '".$userId."'";
			$rslUsr = $this->mpurchasing->getDataQuery($sqlUsr);

			if(count($rslUsr) > 0)
			{
				if($rslUsr[0]->vessel != "")
				{
					$tempVsl = explode(",",$rslUsr[0]->vessel);
					$vslNya = "";

					for ($lan=0; $lan < count($tempVsl); $lan++)
					{
						if($vslNya == "")
						{
							$vslNya = "'".$tempVsl[$lan]."'";
						}else{
							$vslNya .= ",'".$tempVsl[$lan]."'";
						}
					}
					if($vslNya != "")
					{
						$whereNya .= " AND vessel IN(".$vslNya.")";
					}
				}
			}
		}

		if($searchNya == "search")
		{
			$txtSearch = $_POST['valSearch'];
			$idSlcType = $_POST['idSlcType'];

			if($idSlcType == "appNo")
			{
				$whereNya .= " AND app_no LIKE '%".$txtSearch."%' ";
			}
			else if($idSlcType == "vessel")
			{
				$whereNya .= " AND vessel LIKE '%".$txtSearch."%' ";
			}
		}

		if($searchNya == "" || $searchNya == "-")
		{
			$sqlCount = "SELECT id FROM request ".$whereNya;
			$dataCount = $this->mpurchasing->getDataQuery($sqlCount);
			$dataPage = $this->getPaging(count($dataCount),$pageNya,$display);
			$limitNya = $dataPage['limit'];
			$dataOut["listPage"] = $dataPage['listPage'];
			if($pageNya != "")
			{
				$no = ($pageNya-1) * $display + 1;
			}
		}


		$sql = "SELECT * FROM request ".$whereNya." ORDER BY date_request DESC,check_order,submit_check,create_offered ASC ".$limitNya;
		$data = $this->mpurchasing->getDataQuery($sql);

		foreach ($data as $key => $val)
		{
			$stApp = "Ready";
			$btnAct = "";


			$btnExport = "<button onclick=\"exportData('".$val->id."');\" class=\"btn btn-success btn-xs\" id=\"btnEdit\" type=\"button\" title=\"Export\"><i class=\"fa fa-download\"></i></button>";

			if ($val->check_order == '0' AND $val->check_approve1 == '0' AND $val->check_approve2 == '0')
			{
				$btnAct = " <button onclick=\"checkReq('".$val->id."','viewData');\" class=\"btn btn-success btn-xs btn-block\" id=\"btnEdit\" type=\"button\"><i class=\"fa fa-check-square-o\"></i> Check</button> <button onclick=\"showModalCancel('".$val->id."');\" class=\"btn btn-danger btn-xs btn-block\" id=\"btnCancel\" type=\"button\"><i class=\"fa fa-times-circle\"></i> Cancel</button>";
				$btnAct .= "<button onclick=\"showModalRevise('".$val->id."');\" class=\"btn btn-danger btn-xs btn-block\" id=\"BtnReviseReq\" type=\"button\" title=\"Revise Data\"><i class=\"fa fa-mail-reply-all\"></i> Revise</button>";
			}

			if ($val->check_order == '1')
			{
				$stApp = "Waitting";

				$btnAct = " <button onclick=\"editData('".$val->id."');\" class=\"btn btn-info btn-xs btn-block\" id=\"btnEdit\" type=\"button\"><i class=\"fa fa-edit\"></i> Edit</button>
							<button onclick=\"showModalCancel('".$val->id."');\" class=\"btn btn-danger btn-xs btn-block\" id=\"btnCancel\" type=\"button\"><i class=\"fa fa-times-circle\"></i> Cancel</button>";

				if ($val->submit_check == '0')
				{
					$btnAct .= " <button onclick=\"submitData('".$val->id."');\" class=\"btn btn-primary btn-xs btn-block\" id=\"btnEdit\" type=\"button\"><i class=\"fa fa-hand-o-right\"></i> Submit</button>";
				}
				else
				{
					$stApp = "On Proses";
					$btnAct = " <button onclick=\"showModal('".$val->id."');\" class=\"btn btn-warning btn-xs btn-block\" id=\"btnEdit\" type=\"button\"><i class=\"fa fa-search\"></i> View</button>";
				}

				if ($val->check_approve1 == '1' AND $val->check_approve2 == '0')
				{
					$btnAct = " <button onclick=\"showModal('".$val->id."');\" class=\"btn btn-warning btn-xs btn-block\" id=\"btnEdit\" type=\"button\"><i class=\"fa fa-search\"></i> View</button>";
					$userId = $val->idUser_approve1;
					$stApp = "Completed";
				}
				if ($val->check_approve1 == '1' AND $val->check_approve2 == '1')
				{
					$btnAct = " <button onclick=\"showModal('".$val->id."');\" class=\"btn btn-warning btn-xs btn-block\" id=\"btnEdit\" type=\"button\"><i class=\"fa fa-search\"></i> View</button>";
					$userId1 = $val->idUser_approve1;
					$userId2 = $val->idUser_approve2;
					$stApp = "Completed";
				}
			}
			else if ($val->check_order == '2')
			{
				$userId = $val->idUser_revisi;
				$stApp = "Revisi <br>";
				$stApp .= "(<i>".$this->getUserid($userId)."</i>)";
				$btnAct = " <button onclick=\"viewRevisi('".$val->id."');\" class=\"btn btn-primary btn-xs btn-block\" id=\"btnEdit\" type=\"button\"><i class=\"fa fa-search\"></i> View Revisi</button> <button onclick=\"editData('".$val->id."');\" class=\"btn btn-info btn-xs btn-block\" id=\"btnEdit\" type=\"button\"><i class=\"fa fa-edit\"></i> Edit</button> <button onclick=\"showModalCancel('".$val->id."');\" class=\"btn btn-danger btn-xs btn-block\" id=\"btnCancel\" type=\"button\"><i class=\"fa fa-times-circle\"></i> Cancel</button>";
			}

			if ($val->create_offered == '1')
			{
				$stApp .= "<br><span style=\"color:blue;font-size:10px;\">Tgl Upl Quot : ".$this->convertReturnName($val->date_offered)."</span>";
			}
			if($val->st_data == '1')
			{
				$stApp = "Complete";
			}

			$stRequired = $this->cekPosisiData($val->id);

			if ($stRequired == "-")
			{
				$stRequired .= "<br> <a href=\"".base_url('purchasing/viewListPurchase')."/".$val->id."/superitenden\">View</a>";
			}
			$trNya .= "<tr id='row-".$val->id."'>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$no."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$btnExport."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$this->convertReturnName($val->date_request)."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$val->app_no."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$val->vessel."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$val->department."</td>";
				$trNya .= "<td align=\"center\" class=\"status\" style=\"font-size:11px;\">".$stApp."</td>";
				$trNya .= "<td align=\"center\" class=\"remark\" style=\"font-size:11px;\">".$val->remark_complete."<br>".$stRequired."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:10px;\">".$btnAct."</td>";
			$trNya .= "</tr>";

			$no++;
		}
		$dataOut['trNya'] = $trNya;

		if ($searchNya == "search")
		{
			print json_encode($dataOut);
		}
		else
		{
			$this->load->view("purchasing/listRequest", $dataOut);
		}

	}

	function getPaging($countData = "",$pageNya = "",$display = "")
	{
		$limitNya = array();
		$listPage = "";
		$count = $countData;
		$page = $pageNya;
		$sLimit = "0";
		$eLimit = $display;
		$ttlList = ceil($count/$display);
		$linkLast = base_url('listRequest/getListRequest/-/'.$ttlList);

		$listPage = "Total : ".number_format($count,0)." Data";
		if($page != "")
		{
			$sLimit = ($display * ($page -1));
			$eLimit = $display;
			$bfrPage = $page - 1;
			$aftPage = $page + 1;

			$linkBfr = base_url('listRequest/getListRequest/-/'.$bfrPage);
			$linkAft = base_url('listRequest/getListRequest/-/'.$aftPage);			

			$listPage .= "<nav>";
            	$listPage .= "<ul class=\"pagination pagination-sm\">";
            		$listPage .= "<li class=\"page-item\"><a class=\"page-link\" href=\"".base_url('listRequest/getListRequest')."\">First</a></li>";
	         	if($page == 2)
	         	{
	         		$listPage .= "<li class=\"page-item\"><a class=\"page-link\" href=\"".base_url('listRequest/getListRequest')."\">".$bfrPage."</a></li>";
	         		$listPage .= "<li class=\"page-item active\"><span class=\"page-link\">".$page."</span></li>";
	         	}else{	         		
	         		$listPage .= "<li class=\"page-item\"><a class=\"page-link\" href=\"".$linkBfr."\">".$bfrPage."</a></li>";
	               	$listPage .= "<li class=\"page-item active\"><span class=\"page-link\">".$page."</span></li>";
	         	}
	                
	        	if($page < $ttlList)
	        	{
	              	$listPage .= "<li class=\"page-item\"><a class=\"page-link\" href=\"".$linkAft."\">".$aftPage."</a></li>";
	              	if(($page + 1 ) < $ttlList)
	              	{
	              		$listPage .= "<li class=\"page-item\"><a class=\"page-link\" href=\"".$linkLast."\">Last</a></li>";
	              	}	             	
	             }
           		$listPage .= "</ul>";
      		$listPage .= "</nav>";
		}else{
			$listPage .= "<nav>";
				$listPage .= "<ul class=\"pagination pagination-sm\">";
					$listPage .= "<li class=\"page-item disabled\"><span class=\"page-link\">First</span></li>";
				if($ttlList >= 3)
				{
					$ttlList = 3;
				}
				for ($lan=1; $lan <= $ttlList; $lan++)
				{
					if($lan == 1)
					{
						$listPage .= "<li class=\"page-item active\"><span class=\"page-link\">".$lan."</span></li>";
					}else{
						$listPage .= "<li class=\"page-item\"><a class=\"page-link\" href=\"".base_url('listRequest/getListRequest/-/'.$lan)."\">".$lan."</a></li>";
					}
				}
				if($ttlList > 2)
				{
					$listPage .= "<li class=\"page-item\"><a class=\"page-link\" href=\"".$linkLast."\">Last</a></li>";
				}
				$listPage .= "</ul>";
			$listPage .= "</nav>";
		}		
		$limitNya['limit'] = "LIMIT ".$sLimit.",".$eLimit;
		$limitNya['listPage'] = $listPage;
		return $limitNya;
	}


	function addRequestDetail()
	{
		$data = $_POST;
		$arrIdDetail = array();
		$arrTtlApprove = array();
		$arrRemark = array();
		// print_r($data);
		// exit;
		$arrCekFile = array();
		$valData = array();
		$valDataReq = array();
		$userId = $this->session->userdata('idUserPurchase');
		$dir = "./uploadFile";

		$arrIdDetail = explode("*",$data['idDetail']);
		$arrTtlApprove = explode("*",$data['ttlApprove']);
		$arrRemark = explode("*",$data['remark']);
		$arrCekFile = explode("*",$data['cekFile']);

		try {
				for ($lan=0; $lan < count($arrIdDetail); $lan++)
				{
					$valData = array();

					$fileUploadNya = $arrCekFile[$lan];

					if($fileUploadNya == "-")
					{
						$valData['request_file'] = 'request_file_vessel';
						//$sql = "UPDATE request_detail SET request_file = request_file_vessel WHERE id = '72'"
						$fileUploadNya = "";
					}else{
						$fileUploadNya = "";
						$fileName = $_FILES["uploadFile_".$arrIdDetail[$lan]]["name"];
						$newFileName = "fileReq_".$arrIdDetail[$lan];
						$fileUploadNya = $this->uploadFile($_FILES["uploadFile_".$arrIdDetail[$lan]]['tmp_name'],$dir,$fileName,$newFileName);
						$valData['request_file'] = $fileUploadNya;
					}
					
					$valData['approved_order'] = $arrTtlApprove[$lan];

					if($arrRemark[$lan] == "-")
					{
						$valData['approve_remark'] = "";
					}else{
						$valData['approve_remark'] = $arrRemark[$lan];
					}

					$whereNya = "id = '".$arrIdDetail[$lan]."'";
					$this->mpurchasing->updateData($whereNya,$valData,"request_detail");
				}

				$valDataReq['check_order'] = '1';
				$valDataReq['date_check'] = date("Y-m-d");
				$valDataReq['idUser_check'] = $userId;
				$valDataReq['remark_revisi'] = "";
				$valDataReq['idUser_revisi'] = "0";

				$whereNya = "id = '".$data['idReq']."'";
				$this->mpurchasing->updateData($whereNya,$valDataReq,"request");
				$stData = "Save Success..!!";
		} catch (Exception $e) {
			$stData = "Failed =>".$e;
		}
		print $stData;
	}

	
	function addPurchasingDetail()
	{
		$data = $this->input->post(); 
		$idReq = $data['idReq'];
		$dateNow = date("Y-m-d");
		$dir = "./uploadFile";
		$stData = "";


		$arrIdEdit = explode('*', $data['id']);
		$arrCekFile = isset($_POST['cekFile']) ? explode("*", $_POST['cekFile']) : array();
		$arrCodeNo = explode('*', $data['codeNo']);
		$arrArtikel = explode('*', $data['nameArtikel']);
		$arrUnit = explode('*', $data['unit']);
		$arrWork = explode('*', $data['working']);
		$arrStock = explode('*', $data['stock']);
		$arrRequest = explode('*', $data['request']);
		$arrApprovedOrder = explode('*', $data['approved_order']);
		$arrMark = explode('*', $data['mark']);
		$arrRequestRemark = explode('*', $data['request_remark']);

				
		for ($i = 0; $i < count($arrIdEdit); $i++) {
			$valData = array();																																														
			$valData['code_no'] = $arrCodeNo[$i];
			$valData['article_name'] = $arrArtikel[$i];
			$valData['unit'] = $arrUnit[$i];
			$valData['working_on_board'] = $arrWork[$i];
			$valData['stocking_on_board'] = $arrStock[$i];
			$valData['request'] = $arrRequest[$i];
			$valData['approved_order'] = $arrApprovedOrder[$i];
			$valData['mark'] = $arrMark[$i];
			$valData['request_remark'] = $arrRequestRemark[$i];

			if ($arrIdEdit[$i] == "-") {
				$valData['id_request'] = $idReq;
				$valData['date_add'] = $dateNow;
				try {
					$this->mpurchasing->insData("request_detail", $valData);
					$newId = $this->db->insert_id();
					$stData = "Insert Success..!!";

					if (isset($_FILES["uploadFilePurchase0"])) {
						$fileName = $_FILES["uploadFilePurchase0"]["name"][$i];
						$fileTmpName = $_FILES["uploadFilePurchase0"]["tmp_name"][$i];
						$newFileName = "fileReq_" . $newId;
		
						$fileUploadNya = $this->uploadFile($fileTmpName, $dir, $fileName, $newFileName);
						$valData['request_file'] = $fileUploadNya;
	
						// Update record with the new file name
						$this->mpurchasing->updateData("id = '$newId'", array('request_file' => $fileUploadNya), "request_detail");
					}
				}catch(Exception $e) {
					$stData = "Failed =>" . $e->getMessage();
				}
			} 
			else {
				try {
					$whereNya = "id = '" . $arrIdEdit[$i] . "'";
					$this->mpurchasing->updateData($whereNya, $valData, "request_detail");
					$stData = "Update Success..!!";

					if ($arrCekFile[$i] == '1' && isset($_FILES['uploadFilePurchase0' . $arrIdEdit[$i]])) {
						$fileName = $_FILES['uploadFilePurchase0' . $arrIdEdit[$i]]['name'];
						$newFileName = "fileReq_" . $arrIdEdit[$i];
						$fileUploadNya = $this->uploadFile($_FILES['uploadFilePurchase0' . $arrIdEdit[$i]]['tmp_name'], $dir, $fileName, $newFileName);
						$valData['request_file'] = $fileUploadNya;

						// Update record with new file name
						$this->mpurchasing->updateData($whereNya, array('request_file' => $fileUploadNya), "request_detail");
					}

				} catch (Exception $e) {
					$stData = "Failed =>" . $e->getMessage();
				}
			}
		}

		echo json_encode($stData);
	}

	function delData() {
		$data = $_POST;
		$id = $data['id'];
		$typeDel = $data['typeDel'];
		$stData = "";
	
		if ($typeDel == "delPur") {
			$this->db->where('id', $id);
			$updateData = array('sts_delete' => 1);
			if ($this->db->update('request_detail', $updateData)) {
				$stData = "Delete Success..!!";
			} else {
				$stData = "Failed =>" . $this->db->error();
			}
		}
		echo json_encode($stData);
	}
	
	function editData()
	{
		$data = $_POST;
		$dataOut = array();
		$valData = array();
		$id = $data['id'];
		$typeEdit = $data['typeEdit'];
		$trNya = "";
		$no = 1;		

		if($typeEdit == "checkReq")
		{
			$dataOut['headNya'] = $this->mpurchasing->getData("*", "request", "id = '".$id."' AND sts_delete = '0'");
			$valDetail = $this->mpurchasing->getData("*", "request_detail", "id_request = '".$id."' AND sts_delete = '0'");
			$trNya = ""; // Initialize the variable
			$no = 1; // Initialize the row number
			foreach ($valDetail as $key => $val)
			{
				$linkFile = "";
				$artName = $val->article_name;
				if ($val->request_remark)
				{
					$artName .= "<br><br>Remark :<br><i style=\"font-size:10px;font-weight:bold;\">" . $val->request_remark . "</i>";
				}

				if ($val->request_file_vessel)
				{
					$linkFile = "<div id=\"idLinkFile_" . $val->id . "\" style=\"float:right;margin:10px;\">
									<a href=\"" . base_url("/uploadFile") . "/" . $val->request_file_vessel . "\" target=\"_blank\" class=\"btn btn-info btn-xs btn-block\">View</a>
									<button id=\"btnDel\" onclick=\"delFile('" . $val->id . "','" . $val->request_file_vessel . "');\" class=\"btn btn-danger btn-xs btn-block\" title=\"Delete\">Del</button>
								</div>";
				}

				$trNya .= "<tr id=\"row_" . $val->id . "\">";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">" . $no . "</td>";
				$trNya .= "<td align=\"left\" style=\"font-size:11px;\">" . $artName . "</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">" . $val->code_no . "</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">" . $val->unit . "</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">" . $val->working_on_board . "</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">" . $val->stocking_on_board . "</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">" . $val->request . "</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">" . $val->mark . "</td>";
				$trNya .= "<td style=\"font-size:11px;\">
								<input type=\"hidden\" name=\"txtIdReqDetail[]\" id=\"txtIdReqDetail\" value=\"" . $val->id . "\">
								<input type=\"text\" style=\"text-align:center;\" class=\"form-control input-sm\" name=\"txtTtlApprove[]\" id=\"txtTtlApprove\" value=\"" . $val->request . "\">
						</td>";
				$trNya .= "<td align=\"center\">
								<textarea name=\"txtRemark[]\" class=\"form-control input-sm\" id=\"txtRemark\">" . $val->approve_remark . "</textarea>
						</td>";
				$trNya .= "<td align=\"left\">
								<input type=\"file\" name=\"uploadFile[]\" id=\"uploadFile_" . $val->id . "\" class=\"form-control input-sm\">
								<button id=\"btnClear\" style=\"margin:10px;\" onclick=\"clearFile('uploadFile_" . $val->id . "');\" class=\"btn btn-primary btn-xs\">Clear</button>
								" . $linkFile . "
						</td>";
				$trNya .= "<td align=\"center\">
								<button onclick=\"delData('" . $val->id . "')\" class=\"btn btn-danger btn-xs\" id=\"btnDel\" type=\"button\"><i class=\"fa fa-times-circle\"></i> Del</button>
						</td>";
				$trNya .= "</tr>";
				$no++;
			}
			$dataOut['trNya'] = $trNya;
			$dataOut['idReq'] = $id;
			$dataOut['reqDate'] = $this->convertReturnName($dataOut['headNya'][0]->date_request);
		}
		else if($typeEdit == "editCheckReq")
		{
			$dataOut['headNya'] = $this->mpurchasing->getData("*", "request", "id = '".$id."' AND sts_delete = '0'");
			$valDetail = $this->mpurchasing->getData("*", "request_detail", "id_request = '".$id."' AND sts_delete = '0'");
			$trNya = ""; // Initialize the variable
			$no = 1; // Initialize the row number
			foreach ($valDetail as $key => $val)
			{
				$linkFile = "";
				$artName = $val->article_name;
				if ($val->request_remark)
				{
					$artName .= "<br><br>Remark :<br><i style=\"font-size:10px;font-weight:bold;\">" . $val->request_remark . "</i>";
				}

				if ($val->request_file_vessel)
				{
					$linkFile .= "<div id=\"idLinkFileReqVeesel_" . $val->id . "\" style=\"float:right;margin:10px;\">
									<a href=\"" . base_url("/uploadFile") . "/" . $val->request_file_vessel . "\" target=\"_blank\" class=\"btn btn-primary btn-xs btn-block\">File Vessel</a>
								</div>";
				}
				if ($val->request_file)
				{
					$linkFile .= "<div id=\"idLinkFile_" . $val->id . "\" style=\"float:right;margin:10px;\">
									<a href=\"" . base_url("/uploadFile") . "/" . $val->request_file . "\" target=\"_blank\" class=\"btn btn-info btn-xs btn-block\">View</a>
									<button id=\"btnDel\" onclick=\"delFile('" . $val->id . "','" . $val->request_file . "');\" class=\"btn btn-danger btn-xs btn-block\" title=\"Delete\">Del</button>
								</div>";
				}

				$trNya .= "<tr id=\"row_" . $val->id . "\">";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">" . $no . "</td>";
				$trNya .= "<td align=\"left\" style=\"font-size:11px;\">" . $artName . "</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">" . $val->code_no . "</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">" . $val->unit . "</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">" . $val->working_on_board . "</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">" . $val->stocking_on_board . "</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">" . $val->request . "</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">" . $val->mark . "</td>";
				$trNya .= "<td style=\"font-size:11px;\">
								<input type=\"hidden\" name=\"txtIdReqDetail[]\" id=\"txtIdReqDetail\" value=\"" . $val->id . "\">
								<input type=\"text\" style=\"text-align:center;\" class=\"form-control input-sm\" name=\"txtTtlApprove[]\" id=\"txtTtlApprove\" value=\"" . $val->approved_order . "\">
						</td>";
				$trNya .= "<td align=\"center\">
								<textarea name=\"txtRemark[]\" class=\"form-control input-sm\" id=\"txtRemark\">" . $val->approve_remark . "</textarea>
						</td>";
				$trNya .= "<td align=\"left\">
								<input type=\"file\" name=\"uploadFile[]\" id=\"uploadFile_" . $val->id . "\" class=\"form-control input-sm\">
								<button id=\"btnClear\" style=\"margin:10px;\" onclick=\"clearFile('uploadFile_" . $val->id . "');\" class=\"btn btn-primary btn-xs\">Clear</button>
								" . $linkFile . "
						</td>";
				$trNya .= "<td align=\"center\">
								<button onclick=\"delData('" . $val->id . "')\" class=\"btn btn-danger btn-xs\" id=\"btnDel\" type=\"button\"><i class=\"fa fa-times-circle\"></i> Del</button>
						</td>";
				$trNya .= "</tr>";
				$no++;
			}
			$dataOut['trNya'] = $trNya;
			$dataOut['idReq'] = $id;
			$dataOut['reqDate'] = $this->convertReturnName($dataOut['headNya'][0]->date_request);
		}

		else if($typeEdit == "delFile")
		{
			$stData = "";
			$dir = "./uploadFile";
			try {
				$nmFile = $data['nmFile'];
				unlink($dir."/".$nmFile);

				$valData['request_file'] = "";
				$whereNya = "id = '".$id."'";
				$this->mpurchasing->updateData($whereNya,$valData,"request_detail");
				$stData = "Delete Success..!!";
			} catch (Exception $ex) {
				$stData = "Failed =>".$ex;
			}
			$dataOut['stData'] = $stData;
		}

		print json_encode($dataOut);
	}

	function getModalDetailReq()
	{
		$dataOut = array();
		$trNya = "";
		$data = $_POST;
		$idReq = $data['idReq'];
		$no = 1;
		$stData = "Status : Not Approved";

		$dataReq = $this->mpurchasing->getData("*","request","id = '".$idReq."'");

		if($dataReq[0]->submit_check == "1" AND $dataReq[0]->req_check_approve == "1")
		{
			$stData = "Status : Approved";
		}


		$valDetail = $this->mpurchasing->getData("*","request_detail","id_request = '".$idReq."'");
		foreach ($valDetail as $key => $val)
		{
			$artName = $val->article_name;
			if($val->request_file != "")
			{
				$artName = "<a href=\"".base_url('uploadFile')."/".$val->request_file."\" target=\"_blank\">".$artName."</a>";
			}
			$trNya .= "<tr>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$no."</td>";
				$trNya .= "<td align=\"left\" style=\"font-size:11px;\">".$artName."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$val->code_no."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$val->unit."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$val->working_on_board."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$val->stocking_on_board."</td>";				
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$val->request."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$val->approved_order."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$val->mark."</td>";
				$trNya .= "<td align=\"left\" style=\"font-size:11px;\">".$val->request_remark."</td>";
			$trNya .= "</tr>";
			$no++;
		}
		$dataOut['trNya'] = $trNya;
		$dataOut['idReq'] = $idReq;
		$dataOut['stData'] = $stData;
		print json_encode($dataOut);
	}

	function submitCancel()
	{
		$data = $_POST;
		$valData = array();
		$idReq = $data['idReq'];
		$remark = $data['remark'];
		$userId = $this->session->userdata('idUserPurchase');

		try {
				$valData['st_data'] = '2';
				$valData['date_cancel'] = date("Y-m-d");
				$valData['idUser_cancel'] = $userId;
				$valData['remark_cancel'] = $remark;

				$whereNya = "id = '".$idReq."'";
				$this->mpurchasing->updateData($whereNya,$valData,"request");
				$stData = "Approve Success..!!";
		} catch (Exception $e) {
			$stData = "Failed =>".$e;
		}

		print json_encode($stData);
	}

	function submitRevise()
	{
		$data = $_POST;
		$valData = array();
		$idReq = $data['idReq'];
		$remark = $data['remark'];
		$userId = $this->session->userdata('idUserPurchase');

		try {
				$valData['chief_check'] = '0';
				$valData['id_chief'] = '0';
				$valData['date_chiefCheck'] = '0000-00-00';
				$valData['master_check'] = '0';
				$valData['id_master'] = '0';
				$valData['date_masterCheck'] = '0000-00-00';
				$valData['revise_date_check'] = date("Y-m-d");
				$valData['revise_userId_check'] = $userId;
				$valData['revise_remark_check'] = $remark;

				$whereNya = "id = '".$idReq."'";
				$this->mpurchasing->updateData($whereNya,$valData,"request");
				$stData = "Revise Success..!!";
		} catch (Exception $e) {
			$stData = "Failed =>".$e;
		}

		print json_encode($stData);
	}
	
	function viewRevisi()
	{
		$dataOut = array();
		$divNya = "";
		$data = $_POST;
		$idReq = $data['id'];
		$no = 1;

		$valDetail = $this->mpurchasing->getData("*","request","id = '".$idReq."'");
		if(count($valDetail) > 0)
		{
			$divNya .= "<div class=\"row\">";
				$divNya .= "<div class=\"col-md-1\">";
					$divNya .= "<label>Revisi :</label>";
				$divNya .= "</div>";
				$divNya .= "<div class=\"col-md-11\">";
					$divNya .= "<p>".$valDetail[0]->remark_revisi."</p>";
				$divNya .= "</div>";
			$divNya .= "</div>";
		}
		$dataOut['divNya'] = $divNya;
		print json_encode($dataOut);
	}

	function exportDataReq($idReq = '')
	{
		$dataOut = array();
		$trNya = "";
		$data = $_POST;
		$idReq = $idReq;
		$no = 1;

		$sql = "SELECT A.vessel,A.date_request,A.department,A.app_no,A.id_chief,A.date_chiefCheck,A.qrcode_chief,A.id_master,A.date_masterCheck,A.qrcode_master,A.date_check,A.qrcode_check,A.req_check_id,A.req_check_date,A.req_check_qrcode,A.submit_check_date,B.name_full 
				FROM request A 
				LEFT JOIN user B ON A.idUser_check = B.id 
				WHERE A.id = '".$idReq."' ";
		$valReq = $this->mpurchasing->getDataQuery($sql);

		$valDetail = $this->mpurchasing->getData("*","request_detail","id_request = '".$idReq."'");
		foreach ($valDetail as $key => $val)
		{
			$trNya .= "<tr>";
				$trNya .= "<td align=\"center\" style=\"width:20px;vertical-align:top;border:0px;font-size:11px;\">".$no."</td>";
				$trNya .= "<td align=\"left\" style=\"width:200px;vertical-align:top;border:0px;font-size:11px;\">".$val->article_name."</td>";
				$trNya .= "<td align=\"center\" style=\"width:40px;vertical-align:top;border:0px;font-size:11px;\">".$val->code_no."</td>";
				$trNya .= "<td align=\"center\" style=\"width:30px;vertical-align:top;border:0px;font-size:11px;\">".$val->unit."</td>";
				$trNya .= "<td align=\"center\" style=\"width:30px;vertical-align:top;border:0px;font-size:11px;\">".$val->approved_order."</td>";
				$trNya .= "<td align=\"center\" style=\"width:80px;vertical-align:top;border:0px;font-size:11px;\"></td>";				
				$trNya .= "<td align=\"center\" style=\"width:80px;vertical-align:top;border:0px;font-size:11px;\"></td>";
				$trNya .= "<td align=\"center\" style=\"width:150px;vertical-align:top;border:0px;font-size:11px;\"></td>";
			$trNya .= "</tr>";

			$no++;
		}

		$madeBy = "";
		$madeDate = "";
		$madeQrCode = "";
		$masterName = "";
		$masterDate = "";
		$masterQrCode = "";
		$superIntenden = "";
		$superIntendenDate = "";
		$superIntendenQrCode = "";
		$mngnAppvrName = "";
		$mngnAppvrDate = "";
		$mngnAppvrQrCode = "";

		if($valReq[0]->id_chief != '0')
		{
			$madeBy = $this->getFullNameByVessel($valReq[0]->id_chief);
			$madeDate = $this->convertReturnNameWithTime($valReq[0]->date_chiefCheck);
			if($valReq[0]->qrcode_chief != "")
			{
				$madeQrCode = "<img src=\"".base_url('imgQrCode/'.$valReq[0]->qrcode_chief)."\" style=\"width:7%;\">";
			}
		}

		if($valReq[0]->id_master != '0')
		{
			$masterName = $this->getFullNameByVessel($valReq[0]->id_master);
			$masterDate = $this->convertReturnNameWithTime($valReq[0]->date_masterCheck);
			if($valReq[0]->qrcode_master != "")
			{
				$masterQrCode = "<img src=\"".base_url('imgQrCode/'.$valReq[0]->qrcode_master)."\" style=\"width:7%;\">";
			}
		}

		if($valReq[0]->name_full != "")
		{
			$superIntenden = $valReq[0]->name_full;
			$superIntendenDate = $this->convertReturnNameWithTime($valReq[0]->submit_check_date);
			if($valReq[0]->qrcode_check != "")
			{
				$superIntendenQrCode = "<img src=\"".base_url('imgQrCode/'.$valReq[0]->qrcode_check)."\" style=\"width:7%;\">";
			}
		}

		if($valReq[0]->req_check_id != "0")
		{
			$mngnAppvrName = $this->getFullName($valReq[0]->req_check_id);
			$mngnAppvrDate = $this->convertReturnNameWithTime($valReq[0]->req_check_date);
			if($valReq[0]->req_check_qrcode != "")
			{
				$mngnAppvrQrCode = "<img src=\"".base_url('imgQrCode/'.$valReq[0]->req_check_qrcode)."\" style=\"width:7%;\">";
			}
		}

		$dataOut['madeBy'] = $madeBy;
		$dataOut['madeDate'] = $madeDate;
		$dataOut['madeQrCode'] = $madeQrCode;
		$dataOut['masterName'] = $masterName;
		$dataOut['masterDate'] = $masterDate;
		$dataOut['masterQrCode'] = $masterQrCode;
		$dataOut['superIntenden'] = $superIntenden;
		$dataOut['superIntendenDate'] = $superIntendenDate;
		$dataOut['superIntendenQrCode'] = $superIntendenQrCode;
		$dataOut['mngnAppvrName'] = $mngnAppvrName;
		$dataOut['mngnAppvrDate'] = $mngnAppvrDate;
		$dataOut['mngnAppvrQrCode'] = $mngnAppvrQrCode;
		$dataOut['appNo'] = $valReq[0]->app_no;
		$dataOut['vessel'] = $valReq[0]->vessel;
		$dataOut['trNya'] = $trNya;
		$dataOut['idReq'] = $idReq;
		//$dataOut['userApprove'] = $valReq[0]->name_full;
		$dataOut['tglReq'] = $this->convertReturnName($valReq[0]->date_request);
		$dataOut['department'] = $valReq[0]->department;
		//print_r($dataOut);exit;
		$this->load->view("purchasing/exportRequest",$dataOut);
	}

	function exportDataReqView($idReq = '')
	{
		$dataOut = array();
		$trNya = "";
		$data = $_POST;
		$idReq = $idReq;
		$no = 1;

		$sql = "SELECT A.vessel,A.date_request,A.department,A.app_no,A.id_chief,A.date_chiefCheck,A.qrcode_chief,A.id_master,A.date_masterCheck,A.qrcode_master,A.date_check,A.qrcode_check,A.req_check_id,A.req_check_date,A.req_check_qrcode,A.submit_check_date,B.name_full 
				FROM request A 
				LEFT JOIN user B ON A.idUser_check = B.id 
				WHERE A.id = '".$idReq."' ";
		$valReq = $this->mpurchasing->getDataQuery($sql);

		$valDetail = $this->mpurchasing->getData("*","request_detail","id_request = '".$idReq."'");
		foreach ($valDetail as $key => $val)
		{
			$trNya .= "<tr>";
				$trNya .= "<td align=\"center\" style=\"vertical-align:top;border:0px;font-size:11px;\">".$no."</td>";
				$trNya .= "<td align=\"left\" style=\"vertical-align:top;border:0px;font-size:11px;\">".$val->article_name."</td>";
				$trNya .= "<td align=\"center\" style=\"vertical-align:top;border:0px;font-size:11px;\">".$val->code_no."</td>";
				$trNya .= "<td align=\"center\" style=\"vertical-align:top;border:0px;font-size:11px;\">".$val->unit."</td>";
				$trNya .= "<td align=\"center\" style=\"vertical-align:top;border:0px;font-size:11px;\">".$val->working_on_board."</td>";
				$trNya .= "<td align=\"center\" style=\"vertical-align:top;border:0px;font-size:11px;\">".$val->stocking_on_board."</td>";				
				$trNya .= "<td align=\"center\" style=\"vertical-align:top;border:0px;font-size:11px;\">".$val->request."</td>";
				$trNya .= "<td align=\"center\" style=\"vertical-align:top;border:0px;font-size:11px;\">".$val->approved_order."</td>";
				$trNya .= "<td align=\"center\" style=\"vertical-align:top;border:0px;font-size:11px;\">".$val->mark."</td>";
				$trNya .= "<td align=\"left\" style=\"vertical-align:top;border:0px;font-size:11px;\">".$val->request_remark."</td>";
			$trNya .= "</tr>";

			$no++;
		}

		$madeBy = "";
		$madeDate = "";
		$madeQrCode = "";
		$masterName = "";
		$masterDate = "";
		$masterQrCode = "";
		$superIntenden = "";
		$superIntendenDate = "";
		$superIntendenQrCode = "";
		$mngnAppvrName = "";
		$mngnAppvrDate = "";
		$mngnAppvrQrCode = "";

		if($valReq[0]->id_chief != '0')
		{
			$madeBy = $this->getFullNameByVessel($valReq[0]->id_chief);
			$madeDate = $this->convertReturnNameWithTime($valReq[0]->date_chiefCheck);
			if($valReq[0]->qrcode_chief != "")
			{
				$madeQrCode = "<img src=\"".base_url('imgQrCode/'.$valReq[0]->qrcode_chief)."\" style=\"width:7%;\">";
			}
		}

		if($valReq[0]->id_master != '0')
		{
			$masterName = $this->getFullNameByVessel($valReq[0]->id_master);
			$masterDate = $this->convertReturnNameWithTime($valReq[0]->date_masterCheck);
			if($valReq[0]->qrcode_master != "")
			{
				$masterQrCode = "<img src=\"".base_url('imgQrCode/'.$valReq[0]->qrcode_master)."\" style=\"width:7%;\">";
			}
		}

		if($valReq[0]->name_full != "")
		{
			$superIntenden = $valReq[0]->name_full;
			$superIntendenDate = $this->convertReturnNameWithTime($valReq[0]->submit_check_date);
			if($valReq[0]->qrcode_check != "")
			{
				$superIntendenQrCode = "<img src=\"".base_url('imgQrCode/'.$valReq[0]->qrcode_check)."\" style=\"width:7%;\">";
			}
		}

		if($valReq[0]->req_check_id != "0")
		{
			$mngnAppvrName = $this->getFullName($valReq[0]->req_check_id);
			$mngnAppvrDate = $this->convertReturnNameWithTime($valReq[0]->req_check_date);
			if($valReq[0]->req_check_qrcode != "")
			{
				$mngnAppvrQrCode = "<img src=\"".base_url('imgQrCode/'.$valReq[0]->req_check_qrcode)."\" style=\"width:7%;\">";
			}
		}

		$dataOut['madeBy'] = $madeBy;
		$dataOut['madeDate'] = $madeDate;
		$dataOut['madeQrCode'] = $madeQrCode;
		$dataOut['masterName'] = $masterName;
		$dataOut['masterDate'] = $masterDate;
		$dataOut['masterQrCode'] = $masterQrCode;
		$dataOut['superIntenden'] = $superIntenden;
		$dataOut['superIntendenDate'] = $superIntendenDate;
		$dataOut['superIntendenQrCode'] = $superIntendenQrCode;
		$dataOut['mngnAppvrName'] = $mngnAppvrName;
		$dataOut['mngnAppvrDate'] = $mngnAppvrDate;
		$dataOut['mngnAppvrQrCode'] = $mngnAppvrQrCode;
		$dataOut['appNo'] = $valReq[0]->app_no;
		$dataOut['vessel'] = $valReq[0]->vessel;
		$dataOut['trNya'] = $trNya;
		$dataOut['idReq'] = $idReq;
		//$dataOut['userApprove'] = $valReq[0]->name_full;
		$dataOut['tglReq'] = $this->convertReturnName($valReq[0]->date_request);
		$dataOut['department'] = $valReq[0]->department;

		$this->load->view("purchasing/exportRequestView",$dataOut);
	}

	function exportDataReq02102023($idReq = '')
	{
		$dataOut = array();
		$trNya = "";
		$data = $_POST;
		$idReq = $idReq;
		$no = 1;

		$sql = "SELECT A.vessel,A.date_request,A.department,A.app_no,A.id_chief,A.date_chiefCheck,A.qrcode_chief,A.id_master,A.date_masterCheck,A.qrcode_master,A.date_check,A.qrcode_check,A.req_check_id,A.req_check_date,A.req_check_qrcode,A.submit_check_date,B.name_full 
				FROM request A 
				LEFT JOIN user B ON A.idUser_check = B.id 
				WHERE A.id = '".$idReq."' ";
		$valReq = $this->mpurchasing->getDataQuery($sql);

		$valDetail = $this->mpurchasing->getData("*","request_detail","id_request = '".$idReq."'");
		foreach ($valDetail as $key => $val)
		{
			$trNya .= "<tr>";
				$trNya .= "<td align=\"center\" style=\"vertical-align:top;border:0px;font-size:11px;\">".$no."</td>";
				$trNya .= "<td align=\"left\" style=\"vertical-align:top;border:0px;font-size:11px;\">".$val->article_name."</td>";
				$trNya .= "<td align=\"center\" style=\"vertical-align:top;border:0px;font-size:11px;\">".$val->code_no."</td>";
				$trNya .= "<td align=\"center\" style=\"vertical-align:top;border:0px;font-size:11px;\">".$val->unit."</td>";
				$trNya .= "<td align=\"center\" style=\"vertical-align:top;border:0px;font-size:11px;\">".$val->working_on_board."</td>";
				$trNya .= "<td align=\"center\" style=\"vertical-align:top;border:0px;font-size:11px;\">".$val->stocking_on_board."</td>";				
				$trNya .= "<td align=\"center\" style=\"vertical-align:top;border:0px;font-size:11px;\">".$val->request."</td>";
				$trNya .= "<td align=\"center\" style=\"vertical-align:top;border:0px;font-size:11px;\">".$val->approved_order."</td>";
				$trNya .= "<td align=\"center\" style=\"vertical-align:top;border:0px;font-size:11px;\">".$val->mark."</td>";
				$trNya .= "<td align=\"left\" style=\"vertical-align:top;border:0px;font-size:11px;\">".$val->request_remark."</td>";
			$trNya .= "</tr>";

			$no++;
		}

		$madeBy = "";
		$madeDate = "";
		$madeQrCode = "";
		$masterName = "";
		$masterDate = "";
		$masterQrCode = "";
		$superIntenden = "";
		$superIntendenDate = "";
		$superIntendenQrCode = "";
		$mngnAppvrName = "";
		$mngnAppvrDate = "";
		$mngnAppvrQrCode = "";

		if($valReq[0]->id_chief != '0')
		{
			$madeBy = $this->getFullNameByVessel($valReq[0]->id_chief);
			$madeDate = $this->convertReturnNameWithTime($valReq[0]->date_chiefCheck);
			if($valReq[0]->qrcode_chief != "")
			{
				$madeQrCode = "<img src=\"".base_url('imgQrCode/'.$valReq[0]->qrcode_chief)."\" style=\"width:7%;\">";
			}
		}

		if($valReq[0]->id_master != '0')
		{
			$masterName = $this->getFullNameByVessel($valReq[0]->id_master);
			$masterDate = $this->convertReturnNameWithTime($valReq[0]->date_masterCheck);
			if($valReq[0]->qrcode_master != "")
			{
				$masterQrCode = "<img src=\"".base_url('imgQrCode/'.$valReq[0]->qrcode_master)."\" style=\"width:7%;\">";
			}
		}

		if($valReq[0]->name_full != "")
		{
			$superIntenden = $valReq[0]->name_full;
			$superIntendenDate = $this->convertReturnNameWithTime($valReq[0]->submit_check_date);
			if($valReq[0]->qrcode_check != "")
			{
				$superIntendenQrCode = "<img src=\"".base_url('imgQrCode/'.$valReq[0]->qrcode_check)."\" style=\"width:7%;\">";
			}
		}

		if($valReq[0]->req_check_id != "0")
		{
			$mngnAppvrName = $this->getFullName($valReq[0]->req_check_id);
			$mngnAppvrDate = $this->convertReturnNameWithTime($valReq[0]->req_check_date);
			if($valReq[0]->req_check_qrcode != "")
			{
				$mngnAppvrQrCode = "<img src=\"".base_url('imgQrCode/'.$valReq[0]->req_check_qrcode)."\" style=\"width:7%;\">";
			}
		}

		$dataOut['madeBy'] = $madeBy;
		$dataOut['madeDate'] = $madeDate;
		$dataOut['madeQrCode'] = $madeQrCode;
		$dataOut['masterName'] = $masterName;
		$dataOut['masterDate'] = $masterDate;
		$dataOut['masterQrCode'] = $masterQrCode;
		$dataOut['superIntenden'] = $superIntenden;
		$dataOut['superIntendenDate'] = $superIntendenDate;
		$dataOut['superIntendenQrCode'] = $superIntendenQrCode;
		$dataOut['mngnAppvrName'] = $mngnAppvrName;
		$dataOut['mngnAppvrDate'] = $mngnAppvrDate;
		$dataOut['mngnAppvrQrCode'] = $mngnAppvrQrCode;
		$dataOut['appNo'] = $valReq[0]->app_no;
		$dataOut['vessel'] = $valReq[0]->vessel;
		$dataOut['trNya'] = $trNya;
		$dataOut['idReq'] = $idReq;
		//$dataOut['userApprove'] = $valReq[0]->name_full;
		$dataOut['tglReq'] = $this->convertReturnName($valReq[0]->date_request);
		$dataOut['department'] = $valReq[0]->department;
		//print_r($dataOut);exit;
		$this->load->view("purchasing/exportRequest_02102023",$dataOut);
	}

	function getFullNameByVessel($idUser = "")
	{
		$fullName = "";

		$sql = "SELECT id,full_name FROM login WHERE sts_delete = '0' AND id = '".$idUser."' ";
		$rsl = $this->mpurchasing->getDataQueryDb2($sql);

		if(count($rsl) > 0)
		{
			$fullName = ucwords(strtolower($rsl[0]->full_name));
		}

		return $fullName;
	}

	function getFullName($idUser = "")
	{
		$fullName = "";

		$sql = "SELECT id,name_full FROM user WHERE sts_delete = '0' AND id = '".$idUser."' ";
		$rsl = $this->mpurchasing->getDataQuery($sql);

		if(count($rsl) > 0)
		{
			$fullName = ucwords(strtolower($rsl[0]->name_full));
		}

		return $fullName;
	}

	function submitData()
	{
		$idReq = $_POST['id'];
		$updateData = array();
		$status = "";
		$dateNow = date("Y-m-d H:i:s");

		try {

			$updateData['submit_check'] = '1';
			$updateData['submit_check_date'] = $dateNow;
			$updateData['last_send_mail'] = "0000-00-00";
			$whereNya = "id = '".$idReq."'";

			$this->mpurchasing->updateData($whereNya,$updateData,"request");

			$this->addDataMyAppLetter($idReq);
			//$this->sendRemaindByEmail($idReq);

			$status = "Success..!!";
		} catch (Exception $ex) {
			$status = "Failed..!!";
		}
		print json_encode($status);
	}

	function addDataMyAppLetter($idReq = "")
	{
		$dateNow = date("Y-m-d");
		$yearNow = date("Y");
		$monthNow = date("m");
		$noSurat = "1";
		$initDivisi = "DPK";
		$initCmp = "ADY";
		$idUsrLogin = $this->session->userdata('idUserPurchase');
		$fullNameLogin = $this->session->userdata('fullName');
		$usrAddLogin = $idUsrLogin."#".date("H:i")."#".date("d/m/Y");
		$insSqlSrv = array();
		$vsl = "";

		try {

			$sql = " SELECT * FROM request WHERE id = '".$idReq."' AND sts_delete = '0' ";
			$rsl = $this->mpurchasing->getDataQuery($sql);

			if(count($rsl) > 0)
			{
				$vsl = $rsl[0]->vessel;
			}

			$sqlSrv = "	SELECT nosurat FROM tblEmpNoSurat
						WHERE cmpcode = '".$initCmp."' AND YEAR(tglsurat) = '".$yearNow."'
						ORDER BY nosurat DESC LIMIT 0,1 ";
			$rslSrv = $this->mpurchasing->getDataQueryMyApps($sqlSrv);

			if(count($rslSrv) > 0)
			{
				$ns = explode("/", $rslSrv[0]->nosurat);
				$noSurat = $ns[0]+1;
			}

			$batchno = $this->getBatchNo();
			$formatNoSrt = $this->createNo($noSurat)."/".$initCmp."/".$initDivisi."/".$monthNow.$yearNow;


			$insSqlSrv["batchno"] = $batchno;
			$insSqlSrv["cmpcode"] = $initCmp;
			$insSqlSrv["nosurat"] = $formatNoSrt;
			$insSqlSrv["issueddiv"] = $initDivisi;
			$insSqlSrv["signedby"] = $initDivisi;
			$insSqlSrv["address"] = "Ship Management";				
			$insSqlSrv["tglsurat"] = $dateNow;
			$insSqlSrv["ket"] = "Superintendent / ".$vsl." / ".$fullNameLogin;
			$insSqlSrv["copydoc"] = "0";
			$insSqlSrv["canceldoc"] = "0";
			$insSqlSrv["createdby"] = "Purch. System";
			$insSqlSrv["addusrdt"] = $usrAddLogin;

			$this->mpurchasing->insDataMyApps($insSqlSrv,"tblEmpNoSurat");
			$this->mpurchasing->insDataMyAppsDahlia($insSqlSrv,"tblEmpNoSurat");

			$imgName = $this->createQRCode($batchno);

			$dataUpd = array();

			$dataUpd['qrcode_check'] = $imgName;
			$whereNya = "id = '".$idReq."'";

			$this->mpurchasing->updateData($whereNya,$dataUpd,"request");
		} catch (Exception $e) {
			
		}
	}

	function createQRCode($batchNo = "")
	{
		$config = array();
		$this->load->library('ciqrcode');

		$config['cacheable']	= true;
		$config['cachedir']		= './imgQRCode/';
		$config['errorlog']		= './imgQRCode/';
		$config['imagedir']		= './imgQRCode/';
		$config['quality']		= true;
		$config['size']			= '1024';
		$config['black']		= array(224,255,255);
		$config['white']		= array(0,0,128);//untuk ubah warna di libralies/qrcode/qrimage.php white default 0,0,0
		$this->ciqrcode->initialize($config);

		$imgName = base64_encode($batchNo).'.png';

		$params['data'] = "http://apps.andhika.com/observasi/myLetter/viewLetter/".base64_encode($batchNo); //data yang akan di jadikan QR CODE
		$params['level'] = 'H'; //H=High
		$params['size'] = 5;
		$params['savename'] = FCPATH.$config['imagedir'].$imgName; //simpan image QR CODE ke folder assets/images/
		$params['logo'] = "./assets/img/andhika.png";

		$this->ciqrcode->generate($params); // fungsi untuk generate QR CODE

		return $imgName;
	}

	function createNo($noNya = "")
	{
		$dt = strlen($noNya);
		$outNo = "";
		if($dt == 1)
		{
			$outNo = "000".$noNya;
		}
		else if($dt == 2)
		{
			$outNo = "00".$noNya;
		}
		else if($dt == 3)
		{
			$outNo = "0".$noNya;
		}
		else{
			$outNo = $noNya;
		}
		
		return $outNo;
	}

	function getBatchNo()
	{
		$batchNo = "1";
		$sql = " SELECT (batchno + 1) AS batchNo FROM tblempnosurat ORDER BY batchno DESC LIMIT 0,1 ";
		$data = $this->mpurchasing->getDataQueryMyApps($sql);

		if(count($data) > 0)
		{
			$batchNo = $data[0]->batchNo;
		}

		return $batchNo;
	}

	function sendRemaindByEmail($idReq = '')
	{
		$mailNya = "";
		$subjectNya = "";
		$isiEmailNya = "";

		$sql = "SELECT id,vessel,department FROM request WHERE sts_delete = '0' AND id = '".$idReq."'";
		$rsl = $this->mpurchasing->getDataQuery($sql);

		$sqlMail = "SELECT id,email,email_approve1,email_approve2 FROM send_mail WHERE sts_delete = '0' AND vessel = '".$rsl[0]->vessel."'";
		$rslMail = $this->mpurchasing->getDataQuery($sqlMail);

		foreach ($rslMail as $key => $val)
		{
			if($rsl[0]->department == "DECK")
			{
				$mailNya = $val->email_approve2;
			}
			if($rsl[0]->department == "ENGINE")
			{
				$mailNya = $val->email_approve1;
			}
		}

		if($mailNya != "")
		{
			$mailNya = "ahmad.maulana@andhika.com";
			$subjectNya = "Waitting Approve Request From ".$rsl[0]->vessel;
			$isiEmailNya = $this->getContentSendMail($idReq,$rsl[0]->vessel);
			
			//print_r($isiEmailNya);exit;
			mail($mailNya, $subjectNya, $isiEmailNya, $this->headers());
		}
	}

	function headers()
	{
		$headers = "";
		$headers .= "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\n";
		$headers .= "X-Priority: 3\n";
		$headers .= "X-MSMail-Priority: Normal\n";
		$headers .= "X-Mailer: php\n";
		$headers .= "From: noreply@andhika.com\n";
		//$headers .= "CC: it@andhika.com\n";
		
		return $headers;
	}

	function getContentSendMail($idReq = "",$vessel = "")
	{
		$data = $this->getIsiContent($idReq);
		$isiMessage = "";

		$isiMessage .= "<p>";
			$isiMessage .= "*************************************************<br>";
			$isiMessage .= "PLEASE DO NOT REPLY THIS EMAIL..!!<br>";
			$isiMessage .= "*************************************************<br>";
		$isiMessage .= "</p>";

		$isiMessage .= "<b>&nbsp;***** ".$vessel." Send Request Purchasing.Please Approve For it. *****</b>";

		$isiMessage.= "<table width=\"800px\" border=\"1\" cellpadding=\"0\" cellspacing=\"0\" style=\"margin-top:30px;\">";
			$isiMessage.= $data["trNya"];
		$isiMessage.= "</table>";

		$isiMessage.= "<p style=\"margin-top:20px;\"><b><i>:::</i> Detail Offered <i>:::</i></b></p>";

		$isiMessage.= "<table width=\"800px\" border=\"1\" cellpadding=\"0\" cellspacing=\"0\">";
			$isiMessage.= "<tr>";
				$isiMessage.= "<td align=\"center\">Code / Part No</td>";
				$isiMessage.= "<td align=\"center\">Name of Article</td>";
				$isiMessage.= "<td align=\"center\">Unit</td>";
				$isiMessage.= "<td align=\"center\">Request</td>";
				$isiMessage.= "<td align=\"center\">Approve</td>";
			$isiMessage.= "</tr>";
			$isiMessage.= $data["trDet"];
		$isiMessage.= "</table>";

		$isiMessage .= "<p>To respon this Request, please check <a href=\"http://apps.andhika.com/purchasing\" target=\"_blank\">www.apps.andhika.com</a></p>";

		$isiMessage .= "<p>";
			$isiMessage .= "*************************************************<br>";
			$isiMessage .= "END OF NOTIFICATION<br>";
			$isiMessage .= "*************************************************<br>";
		$isiMessage .= "</p>";

		return $isiMessage;
	}

	function getIsiContent($idReq = "")
	{
		$dataOut = array();
		$trNya = "";
		$trDet = "";

		$sql = "SELECT * FROM request WHERE id = '".$idReq."' AND sts_delete = '0' ";
		$rsl = $this->mpurchasing->getDataQuery($sql);

		foreach ($rsl as $key => $val)
		{
			$trNya .= "<tr>";
				$trNya .= "<td style=\"vertical-align:top;width:15%;\">Vessel</td>";
				$trNya .= "<td style=\"vertical-align:top;width:35%;color:#000080;\"> ".$val->vessel."</td>";
				$trNya .= "<td style=\"vertical-align:top;width:15%;\">Tanggal</td>";
				$trNya .= "<td style=\"vertical-align:top;width:35%;color:#000080;\"> ".$this->convertReturnName($val->date_request)."</td>";
			$trNya .= "</tr>";
			$trNya .= "<tr>";
				$trNya .= "<td style=\"vertical-align: top;width:15%;\">App No</td>";
				$trNya .= "<td style=\"vertical-align: top;width:35%;color:#000080;\"> ".$val->app_no."</td>";
				$trNya .= "<td style=\"vertical-align: top;width:15%;\">Department</td>";
				$trNya .= "<td style=\"vertical-align: top;width:35%;color:#000080;\"> ".$val->department."</td>";
			$trNya .= "</tr>";
		}

		$sqlDet = "SELECT * FROM request_detail WHERE id_request = '".$idReq."' AND sts_delete = '0' ";
		$rslDet = $this->mpurchasing->getDataQuery($sqlDet);

		foreach ($rslDet as $key => $value)
		{
			$price1Nya = "";
			$price2Nya = "";
			$price3Nya = "";

			if($value->quot_price1 > 0)
			{
				$price1Nya = $value->quot_curr1." ".number_format($value->quot_price1,2);
			}
			if($value->quot_price2 > 0)
			{
				$price2Nya = $value->quot_curr2." ".number_format($value->quot_price2,2);
			}
			if($value->quot_price3 > 0)
			{
				$price3Nya = $value->quot_curr3." ".number_format($value->quot_price3,2);
			}

			$trDet .= "<tr>";
				$trDet .= "<td align=\"center\" style=\"vertical-align:top;width:15%;color:#000080;font-size:12px;\">".$value->code_no."</td>";
				$trDet .= "<td style=\"vertical-align:top;width:30%;color:#000080;font-size:12px;\">".$value->article_name."</td>";
				$trDet .= "<td align=\"center\" style=\"vertical-align:top;width:9%;color:#000080;font-size:12px;\">".$value->unit."</td>";
				$trDet .= "<td align=\"center\" style=\"vertical-align:top;width:10%;color:#000080;font-size:12px;\">".$value->request."</td>";
				$trDet .= "<td align=\"center\" style=\"vertical-align:top;width:12%;color:#000080;font-size:12px;\">".$value->approved_order."</td>";
			$trDet .= "</tr>";
		}

		$dataOut["trNya"] = $trNya;
		$dataOut["trDet"] = $trDet;

		return $dataOut;
	}

	function uploadFile($tmpFile = "",$dir = "",$fileName = "",$newFileName = "")
	{
		$dt = explode(".", $fileName);
		$newFileName = str_replace(array(' ','/','.',',','-'), '', $newFileName).".".trim($dt[count($dt)-1]);
		move_uploaded_file($tmpFile, $dir."/".$fileName);
		rename($dir."/".$fileName, $dir."/".$newFileName);
		return $newFileName;
	}

	function delFile($fileNya,$dir)
	{
		$dataDel = array();
		$dataOut = array();
		$de = explode(",",$fileNya);

		if(count($de) > 0)
		{
			for ($lan=0; $lan < count($de); $lan++)
			{
				unlink($dir."/".$de[$lan]);
				$dataDel[] = $de[$lan];
			}
		}
		if(count($dataDel) > 0)
		{
			for ($hal=0; $hal < count($dataDel) ; $hal++)
			{
				$do = explode("_", $dataDel[$hal]);
				$dl = explode(".", $do[1]);
				$dataOut[$dl[0]] = $dl[0];
			}
		}
		return $dataOut;
	}

	function cekPosisiData($idReq = "")
	{
		$statusCek = "";

		$sql = "SELECT * FROM request WHERE sts_delete = '0' AND id = '".$idReq."' ";
		$rsl = $this->mpurchasing->getDataQuery($sql);

		if(count($rsl) > 0)
		{
			if($rsl[0]->chief_check == '0' AND $rsl[0]->master_check == '0' AND $rsl[0]->department == 'DECK')
			{
				$statusCek = "Chief Officer";
			}
			if($rsl[0]->chief_check == '0' AND $rsl[0]->master_check == '0' AND $rsl[0]->department == 'ENGINE')
			{
				$statusCek = "Chief Engineer";
			}
			if($rsl[0]->chief_check == '1' AND $rsl[0]->master_check == '0')
			{
				$statusCek = "Master";
			}
			if($rsl[0]->master_check == '1' AND $rsl[0]->chief_check == '1')
			{
				$statusCek = "Superintendent";
				if($rsl[0]->submit_check == '1' AND $rsl[0]->department == 'DECK' AND $rsl[0]->create_offered == '0')
				{
					$statusCek = "Deck PIC";
				}
				if($rsl[0]->submit_check == '1' AND $rsl[0]->department == 'ENGINE' AND $rsl[0]->create_offered == '0')
				{
					$statusCek = "Engine PIC";
				}
				if($rsl[0]->req_check_approve == '1' AND $rsl[0]->submit_offered == '0')
				{
					$statusCek = "Quotation";
				}
				if($rsl[0]->submit_offered == '1' AND $rsl[0]->check_approve1 == '0')
				{
					$statusCek = "Approval Kadept Purch.";
				}
				if($rsl[0]->st_check_kadiv == '1')
				{
					if($rsl[0]->check_approve1 == '1' AND $rsl[0]->check_approve2 == '0')
					{
						$statusCek = "Approval Kadiv Purch.";
					}
					if($rsl[0]->check_approve2 == '1' AND $rsl[0]->check_approve3 == '0')
					{
						$statusCek = "Approval Kadiv ShipMgmt";
					}
				}else{
					if($rsl[0]->check_approve1 == '1' AND $rsl[0]->check_approve3 == '0')
					{
						$statusCek = "Approval Kadiv ShipMgmt";
					}
				}				
				if($rsl[0]->check_approve3 == '1' AND $rsl[0]->check_approve4 == '0')
				{
					$statusCek = "Approval COO";
				}
				if($rsl[0]->check_approve4 == '1' AND $rsl[0]->check_approve5 == '0' AND $rsl[0]->st_check_finance == '1')
				{
					$statusCek = "Approval Finance";
				}
				if($rsl[0]->check_approve4 == '1' AND $rsl[0]->check_approve5 == '0' AND $rsl[0]->st_check_finance == '0')
				{
					$statusCek = "Create PO";
				}
				if($rsl[0]->check_approve4 == '1' AND $rsl[0]->check_approve5 == '1')
				{
					$statusCek = "Create PO";
				}
				if($rsl[0]->submit_purchasing == '1')
				{
					$statusCek = "-";
				}
			}
			if($rsl[0]->st_data == '1')
			{
				$statusCek = "-";
			}
		}

		return $statusCek;
	}

	function getUserid($userId1 = "",$userId2 = "")
	{
		$dtUsr = "";
		$whereNya = "sts_delete = '0' ";

		if($userId1 != "" AND $userId2 == "")
		{
			$whereNya .= "AND id = '".$userId1."' ";
		}
		if($userId1 != "" AND $userId2 != "")
		{
			$whereNya .= "AND id IN('".$userId1."','".$userId2."')";
		}

		$dataUsr = $this->mpurchasing->getData("*","user",$whereNya);
		if(count($dataUsr) > 0)
		{
			if($userId1 != "" AND $userId2 == "" OR ($userId1 == $userId2))
			{
				$dtUsr = $dataUsr[0]->name_full;
			}
			if($userId1 != "" AND $userId2 != "" AND ($userId1 != $userId2) )
			{
				$dtUsr = $dataUsr[0]->name_full.",".$dataUsr[1]->name_full;
			}
		}

		return $dtUsr;
	}

	function convertReturnName($dateNya = "")
	{
		$dt = explode("-", $dateNya);
		$tgl = $dt[2];
		$bln = $dt[1];
		$thn = $dt[0];
		if($bln == "01" || $bln == "1"){ $bln = "Jan"; }
		else if($bln == "02" || $bln == "2"){ $bln = "Feb"; }
		else if($bln == "03" || $bln == "3"){ $bln = "Mar"; }
		else if($bln == "04" || $bln == "4"){ $bln = "Apr"; }
		else if($bln == "05" || $bln == "5"){ $bln = "Mei"; }
		else if($bln == "06" || $bln == "6"){ $bln = "Jun"; }
		else if($bln == "07" || $bln == "7"){ $bln = "Jul"; }
		else if($bln == "08" || $bln == "8"){ $bln = "Agus"; }
		else if($bln == "09" || $bln == "9"){ $bln = "Sep"; }
		else if($bln == "10"){ $bln = "Okt"; }
		else if($bln == "11"){ $bln = "Nov"; }
		else if($bln == "12"){ $bln = "Des"; }

		return $tgl." ".$bln." ".$thn;
	}

	function convertReturnNameWithTime($dateNya = "")
	{
		$dataNya = explode(" ", $dateNya);
		$dt = explode("-", $dataNya[0]);
		$tgl = $dt[2];
		$bln = $dt[1];
		$thn = $dt[0];
		if($bln == "01" || $bln == "1"){ $bln = "Jan"; }
		else if($bln == "02" || $bln == "2"){ $bln = "Feb"; }
		else if($bln == "03" || $bln == "3"){ $bln = "Mar"; }
		else if($bln == "04" || $bln == "4"){ $bln = "Apr"; }
		else if($bln == "05" || $bln == "5"){ $bln = "Mei"; }
		else if($bln == "06" || $bln == "6"){ $bln = "Jun"; }
		else if($bln == "07" || $bln == "7"){ $bln = "Jul"; }
		else if($bln == "08" || $bln == "8"){ $bln = "Agus"; }
		else if($bln == "09" || $bln == "9"){ $bln = "Sep"; }
		else if($bln == "10"){ $bln = "Okt"; }
		else if($bln == "11"){ $bln = "Nov"; }
		else if($bln == "12"){ $bln = "Des"; }

		return $tgl." ".$bln." ".$thn." ".$dataNya[1];
	}
	
































}