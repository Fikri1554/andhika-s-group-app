<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Request extends CI_Controller {

	function __construct()
	{
		parent::__construct();		
		$this->load->model('mpurchasing');
		$this->load->helper(array('form', 'url'));
	}

	function index()
	{
		$this->getRequest();
	}

	function getRequest($searchNya = "",$pageNya = "")
	{
		$dataOut = array();
		$trNya = "";		
		$btnDetail = "";
		$no = 1;
		$usrVessel = $this->session->userdata('usrVessel');
		$usrType = $this->session->userdata('userTypePurchase');
		$usrJbtn = $this->session->userdata('userPosition');
		$limitNya = "";
		$dataOut["listPage"] = "";
		$display = "10";

		$whereNya = " WHERE sts_delete = '0' AND type_request = 'vessel' ";

		if($usrType != "administrator")
		{
			$whereNya .= " AND vessel = '".$usrVessel."' ";
		}

		if($searchNya == "search")
		{
			$startDate = $_POST['startDate'];
			$endDate = $_POST['endDate'];

			$whereNya .= " AND date_request BETWEEN '".$startDate."' AND '".$endDate."' ";
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

		$sqlReq = "SELECT * FROM request ".$whereNya." ORDER BY id DESC,master_check ASC ".$limitNya;
		$rslReq = $this->mpurchasing->getDataQuery($sqlReq);
		foreach ($rslReq as $key => $value)
		{
			$stMaster = "";
			$btnAct = "";
			$stRequired = "";

			if($value->chief_check == '1' AND $value->master_check == '1')
			{ 
				$stMaster = "On Progress";
				$btnAct = "";
			}else{
				$btnAct = "<button onclick=\"editData('".$value->id."');\" class=\"btn btn-info btn-xs\" id=\"btnEdit\" type=\"button\"><i class=\"fa fa-edit\"></i> Edit</button>
							<button onclick=\"delData('".$value->id."')\" class=\"btn btn-danger btn-xs\" id=\"btnDel\" type=\"button\"><i class=\"fa fa-times-circle\"></i> Del</button>";
			}
			$cekDetail = $this->cekDetailReq($value->id);
			if($cekDetail == "ada")
			{
				$btnDetail = "<button onclick=\"editDataDetail('".$value->id."');\" title=\"Edit Detail\" class=\"btn btn-warning btn-xs\" id=\"btnViewDetail\" type=\"button\" style=\"margin:5px;\"><i class=\"glyphicon glyphicon-edit\"></i></button>";
				$btnDetail .= "<button onclick=\"modalUploadFile('".$value->id."');\" title=\"Upload File\" class=\"btn btn-success btn-xs\" id=\"btnViewDetail\" type=\"button\" style=\"margin:5px;\"><i class=\"glyphicon glyphicon-open\"></i></button>";
			}
			else{
				$btnDetail = "<button onclick=\"addDetail('".$value->id."');\" title=\"Add Detail\" class=\"btn btn-primary btn-xs\" id=\"btnAdd\" type=\"button\"><i class=\"glyphicon glyphicon-plus\" ></i></button>";
				
			}-
			if($value->chief_check != "0" AND $value->master_check != '0' AND strtolower($usrType) == strtolower("administrator"))
			{
				$btnDetail = "";
				if(strtolower($usrType) == strtolower("administrator"))
				{
					$btnDetail .= "<button onclick=\"editDataDetail('".$value->id."');\" title=\"Edit Detail\" class=\"btn btn-warning btn-xs\" id=\"btnViewDetail\" type=\"button\" style=\"margin:5px;\"><i class=\"glyphicon glyphicon-edit\"></i></button>";
				}
				$btnAct = " <button onclick=\"showModal('".$value->id."','viewData');\" class=\"btn btn-primary btn-xs\" id=\"btnView\" type=\"button\"><i class=\"fa fa-search\"></i> View</button>";
				$btnAct .= "<a href=\"".base_url('request/exportDataReq')."/".$value->id."\" class=\"btn btn-success btn-xs\" target=\"_blank\" style=\"margin-left:10px;\"><i class=\"fa fa-download\"></i> Export</a>";
			}
			else {
				if ($cekDetail == "ada") {
					if ($value->chief_check == "0" AND (strtolower($usrJbtn) == strtolower("C/E") || strtolower($usrJbtn) == strtolower("C/O") || strtolower($usrType) == strtolower("administrator"))) {
						if ($value->department == "DECK" AND (strtolower($usrJbtn) == strtolower("C/O") || strtolower($usrType) == strtolower("administrator"))) {
							$btnAct .= " <button onclick=\"showModal('".$value->id."','','chief');\" title=\"Approve Chief\" class=\"btn btn-primary btn-xs\" id=\"btnApproveCo\" type=\"button\"><i class=\"fa fa-hand-o-right\"></i> C/O</button>";
						}
						if ($value->department == "ENGINE" AND (strtolower($usrJbtn) == strtolower("C/E") || strtolower($usrType) == strtolower("administrator"))) {
							$btnAct .= " <button onclick=\"showModal('".$value->id."','','chief');\" title=\"Approve Chief\" class=\"btn btn-primary btn-xs\" id=\"btnApproveCe\" type=\"button\"><i class=\"fa fa-hand-o-right\"></i> C/E</button>";
						}
					}
					if (($value->master_check == "0" AND (strtolower($usrJbtn) == strtolower("MASTER") AND $value->chief_check == "1") || strtolower($usrType) == strtolower("administrator"))) {
						$btnAct .= " <button onclick=\"showModal('".$value->id."','','master');\" title=\"Approve Master\" class=\"btn btn-primary btn-xs\" id=\"btnApproveMaster\" type=\"button\"><i class=\"fa fa-hand-o-right\"></i> Master</button>";
					}
				}
			}
			

			if($value->st_data == '1')
			{
				$stMaster = "Complete";
			}
			if($value->st_data == '2')
			{
				$stMaster = "Cancel";
				$btnAct .= "<br>".$value->remark_cancel;
			}

			$stRequired = $this->cekPosisiData($value->id);

			if($value->revise_remark_check != "")
			{
				$stMaster = "Revise";
				$stRequired = "Superintendent";
				$btnAct .= "<br>".$value->revise_remark_check;
			}

			$trNya .= "<tr>";
				$trNya .= "<td align=\"center\">".$no."</td>";
				$trNya .= "<td align=\"center\">".$btnDetail."</td>";
				$trNya .= "<td align=\"center\">".$this->convertReturnName($value->date_request)."</td>";
				$trNya .= "<td align=\"center\">".$value->app_no."</td>";
				$trNya .= "<td align=\"center\">".$value->vessel."</td>";
				$trNya .= "<td align=\"center\">".$value->department."</td>";
				$trNya .= "<td align=\"center\">".$stMaster."</td>";
				$trNya .= "<td align=\"center\">".$stRequired."</td>";
				$trNya .= "<td align=\"center\">".$btnAct."</td>";
			$trNya .= "</tr>";
			$no++;
		}		
		$dataOut['optVsl'] = $this->getVessel();
		$dataOut['trNya'] = $trNya;

		if($searchNya == "search")
		{
			print json_encode($dataOut);
		}else{
			$this->load->view("purchasing/request",$dataOut);
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
		$linkLast = base_url('request/getRequest/-/'.$ttlList);

		$listPage = "Total : ".number_format($count,0)." Data";
		if($page != "")
		{
			$sLimit = ($display * ($page -1));
			$eLimit = $display;
			$bfrPage = $page - 1;
			$aftPage = $page + 1;

			$linkBfr = base_url('request/getRequest/-/'.$bfrPage);
			$linkAft = base_url('request/getRequest/-/'.$aftPage);			

			$listPage .= "<nav>";
            	$listPage .= "<ul class=\"pagination pagination-sm\">";
            		$listPage .= "<li class=\"page-item\"><a class=\"page-link\" href=\"".base_url('request/getRequest')."\">First</a></li>";
	         	if($page == 2)
	         	{
	         		$listPage .= "<li class=\"page-item\"><a class=\"page-link\" href=\"".base_url('request/getRequest')."\">".$bfrPage."</a></li>";
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
						$listPage .= "<li class=\"page-item\"><a class=\"page-link\" href=\"".base_url('request/getRequest/-/'.$lan)."\">".$lan."</a></li>";
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

	function cekDetailReq($idDetail = "")
	{
		$stCek = "";

		$sql = "SELECT * FROM request_detail WHERE id_request = '".$idDetail."' ";
		$rsl = $this->mpurchasing->getDataQuery($sql);

		if(count($rsl) > 0)
		{
			$stCek = "ada";
		}else{
			$stCek = "tidak";
		}
		return $stCek;
	}

	function addRequest()
	{
		$data = $_POST;
		$valData = array();
		$stData = "";
		$userId = $this->session->userdata('idUserPurchase');

		$valData['type_request'] = "vessel";
		$valData['date_request'] = $data['dateReq'];
		$valData['app_no'] = $data['appNo'];
		$valData['vessel'] = $data['vessel'];
		$valData['department'] = $data['dept'];
		$valData['date_add'] = date("Y-m-d");
		$valData['add_id'] = $userId;

		if($data['id'] == "")//save data
		{			
			try {
			$this->mpurchasing->insData("request",$valData);
			$stData = "Insert Success..!!";
			} catch (Exception $e) {
				$stData = "Failed =>".$e;
			}
		}else{
			try {
			$whereNya = "id = '".$data['id']."'";
			$this->mpurchasing->updateData($whereNya,$valData,"request");
			$stData = "Update Success..!!";
			} catch (Exception $e) {
				$stData = "Failed =>".$e;
			}
		}
		print json_encode($stData);
	}

	function addRequestDetail()
	{
		$data = $_POST;
		$arrIdEdit = array();
		$valData = array();
		$arrCode = array();
		$arrArtikel = array();
		$arrUnit = array();
		$arrWork = array();
		$arrStock = array();
		$arrReq = array();
		$arrMark = array();
		$arrRemark = array();
		$valData = array();
		$stData = "";
		$dateNow = date("Y-m-d");

		$arrCode = explode("*",$data['codeNo']);
		$arrArtikel = explode("*",$data['nameArtikel']);
		$arrUnit = explode("*",$data['unit']);
		$arrWork = explode("*",$data['working']);
		$arrStock = explode("*",$data['stock']);
		$arrReq = explode("*",$data['reqNya']);
		$arrMark = explode("*",$data['mark']);
		$arrRemark = explode("*",$data['remark']);

		if($data['id'] == "")//save data
		{			
			for ($lan=0; $lan < count($arrCode); $lan++)
			{
				$valData['id_request'] = $data['idReq'];
				$valData['code_no'] = $arrCode[$lan];
				$valData['article_name'] = $arrArtikel[$lan];
				$valData['unit'] = $arrUnit[$lan];
				$valData['working_on_board'] = $arrWork[$lan];
				$valData['stocking_on_board'] = $arrStock[$lan];
				$valData['request'] = $arrReq[$lan];

				if($arrMark[$lan] == "-")
				{
					$valData['mark'] = "";
				}else{
					$valData['mark'] = $arrMark[$lan];
				}

				if($arrRemark[$lan] != "-")
				{
					$valData['request_remark'] = $arrRemark[$lan];
				}

				$valData['date_add'] = $dateNow;
				
				try {
					$this->mpurchasing->insData("request_detail",$valData);
					$stData = "Insert Success..!!";
				} catch (Exception $e) {
					$stData = "Failed =>".$e;
				}
				$valData = array();
			}
		}else{			
			$arrIdEdit = explode("*",$data['id']);
			for ($hal=0; $hal < count($arrIdEdit); $hal++)
			{
				$valData['code_no'] = $arrCode[$hal];
				$valData['article_name'] = $arrArtikel[$hal];
				$valData['unit'] = $arrUnit[$hal];
				$valData['working_on_board'] = $arrWork[$hal];
				$valData['stocking_on_board'] = $arrStock[$hal];
				$valData['request'] = $arrReq[$hal];

				if($arrMark[$hal] == "-")
				{
					$valData['mark'] = "";
				}else{
					$valData['mark'] = $arrMark[$hal];
				}

				if($arrRemark[$hal] != "-")
				{
					$valData['request_remark'] = $arrRemark[$hal];
				}
				
				if($arrIdEdit[$hal] == "")//insert jika data baru
				{
					$valData['id_request'] = $data['idReq'];
					$valData['date_add'] = $dateNow;
					try {
						$this->mpurchasing->insData("request_detail",$valData);
						$stData = "Update Success..!!";
					} catch (Exception $e) {
						$stData = "Failed =>".$e;
					}
				}else{
					try {
						$whereNya = "id = '".$arrIdEdit[$hal]."'";
						$this->mpurchasing->updateData($whereNya,$valData,"request_detail");
						$stData = "Update Success..!!";
					} catch (Exception $e) {
						$stData = "Failed =>".$e;
					}
				}
				$valData = array();
			}
		}
		print json_encode($stData);
	}

	function getModalDetailReq()
	{
		$dataOut = array();
		$trNya = "";
		$data = $_POST;
		$idReq = $data['idReq'];
		$no = 1;
		$lblModal = "View Data";

		if($data['typeApprove'] == 'master'){ $lblModal = "Master Approval"; }
		else if($data['typeApprove'] == 'chief'){ $lblModal = "Chief Approval"; }

		$sql = " SELECT B.name_full FROM request A LEFT JOIN user B ON A.idUser_check = B.id WHERE A.id = '".$idReq."' ";
		$valReq = $this->mpurchasing->getDataQuery($sql);

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
		$dataOut['lblModal'] = $lblModal;
		$dataOut['userApprove'] = $valReq[0]->name_full;
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
		$this->load->view("purchasing/exportRequest",$dataOut);
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

	function addField()
	{
		$dataOut = array();
		$data = $_POST;
		$idField = $data['idField'];
		$divNya = "";

		$divNya .= "<div class=\"row\" id=\"idRemove_".$idField."\">";
		$divNya .= "<input type=\"hidden\" name=\"txtIdEdit[]\" id=\"txtIdEdit\" value=\"\">";
		$divNya .= "<div class=\"col-md-12\">";
			$divNya .= "<div class=\"col-md-1	 col-xs-12\">";
				$divNya .= "<div class=\"form-group\">";
					$divNya .= "<label for=\"txtCodePartNo\"><u>Part No:</u></label>";
					$divNya .= "<input placeholder=\"Code / Part No\" type=\"text\" class=\"form-control input-sm\" id=\"txtCodePartNo\" name=\"txtCodePartNo[]\" autocomplete=\"off\">";
				$divNya .= "</div>";
			$divNya .= "</div>";
			$divNya .= "<div class=\"col-md-2 col-xs-12\">";
				$divNya .= "<div class=\"form-group\">";
					$divNya .= "<label for=\"txtNameArticle\"><u>Name of Article:</u></label>";
					$divNya .= "<input placeholder=\"Name of Article\" type=\"text\" class=\"form-control input-sm\" id=\"txtNameArticle\" name=\"txtNameArticle[]\" autocomplete=\"off\">";
				$divNya .= "</div>";
			$divNya .= "</div>";
			$divNya .= "<div class=\"col-md-1 col-xs-12\">";
				$divNya .= "<div class=\"form-group\">";
					$divNya .= "<label for=\"txtUnit\"><u>Unit:</u></label>";
					$divNya .= "<input value=\"\" type=\"text\" class=\"form-control input-sm\" id=\"txtUnit\" name=\"txtUnit[]\" autocomplete=\"off\">";
				$divNya .= "</div>";
			$divNya .= "</div>";
			$divNya .= "<div class=\"col-md-1 col-xs-12\">";
				$divNya .= "<div class=\"form-group\">";
					$divNya .= "<label for=\"txtWorkOnBoard\"><u>Working:</u></label>";
					$divNya .= "<input value=\"0\" type=\"text\" onkeypress=\"javascript:return isNumber(event)\" class=\"form-control input-sm\" id=\"txtWorkOnBoard\" name=\"txtWorkOnBoard[]\" autocomplete=\"off\">";
				$divNya .= "</div>";
			$divNya .= "</div>";
			$divNya .= "<div class=\"col-md-1 col-xs-12\">";
				$divNya .= "<div class=\"form-group\">";
					$divNya .= "<label for=\"txtStockOnBoard\"><u>Stock:</u></label>";
					$divNya .= "<input value=\"0\" type=\"text\" onkeypress=\"javascript:return isNumber(event)\" class=\"form-control input-sm\" id=\"txtStockOnBoard\" name=\"txtStockOnBoard[]\" autocomplete=\"off\">";
				$divNya .= "</div>";
			$divNya .= "</div>";
			$divNya .= "<div class=\"col-md-1 col-xs-12\">";
				$divNya .= "<div class=\"form-group\">";
					$divNya .= "<label for=\"txtTotalReq\"><u>Request:</u></label>";
					$divNya .= "<input value=\"0\" type=\"text\" onkeypress=\"javascript:return isNumber(event)\" class=\"form-control input-sm\" id=\"txtTotalReq\" name=\"txtTotalReq[]\" autocomplete=\"off\">";
				$divNya .= "</div>";
			$divNya .= "</div>";
			$divNya .= "<div class=\"col-md-2 col-xs-12\">";
				$divNya .= "<div class=\"form-group\">";
					$divNya .= "<label for=\"slcMarkDetail\"><u>Mark Reference:</u></label>";
					$divNya .= "<select name=\"slcMarkDetail[]\" id=\"slcMarkDetail\" class=\"form-control input-sm\">
									<option value=\"\">- Select -</option>
									<option value=\"A\">TO BE REPLACE URGENTLY</option>
									<option value=\"B\">BETTER TO BE REPLACE</option>
									<option value=\"C\">STOCK FOR NEXT O/HAUL</option>
									<option value=\"D\">STOCK FOR EMERGENCY</option>
								</select>";
				$divNya .= "</div>";
			$divNya .= "</div>";
			$divNya .= "<div class=\"col-md-2 col-xs-12\">";
					$divNya .= "<div class=\"form-group\">";
						$divNya .= "<label for=\"txtRemark\"><u>Remark:</u></label>";
						$divNya .= "<textarea name=\"txtRemark[]\" class=\"form-control input-sm\" id=\"txtRemark\"></textarea>";
					$divNya .= "</div>";
				$divNya .= "</div>";
			$divNya .= "<div class=\"col-md-1 col-xs-12\">";
				$divNya .= "<div class=\"form-group\">";
					$divNya .= "<label for=\"\" style=\"font-weight: bold;\">&nbsp</label>";
					$divNya .= "<button class=\"btn btn-primary btn-block btn-xs\" title=\"Add\" id=\"btnAddField\" onclick=\"addRowDetail();\" type=\"button\" disabled=\"disabled\"><i class=\"glyphicon glyphicon-plus\"></i></button>";
					$divNya .= "<button class=\"btn btn-danger btn-block btn-xs\" title=\"Remove\" id=\"btnRmvField\" onclick=\"removeDetail('".$idField."');\" type=\"button\" disabled=\"disabled\"><i class=\"glyphicon glyphicon-minus\"></i></button>";
				$divNya .= "</div>";
			$divNya .= "</div>";
		$divNya .= "</div>";
		$divNya .= "</div>";

		$dataOut['noField'] = $idField+1;
		$dataOut['divNya'] = $divNya;
	 	print json_encode($dataOut);
	}

	function getVessel()
	{
		$optNya = "";

		$sql = "SELECT * FROM mst_vessel ORDER BY name ASC";
		$rsl = $this->mpurchasing->getDataQueryDb2($sql);

		foreach ($rsl as $key => $value)
		{
			$optNya .= "<option value='".$value->name."'>".$value->name."</option>";
		}
		return $optNya;
	}

	function getCompany()
	{
		$optNya = "";

		$sql = "SELECT * FROM mst_company ORDER BY name_company ASC";
		$rsl = $this->mpurchasing->getDataQuery($sql);

		foreach ($rsl as $key => $value)
		{
			$optNya .= "<option value='".$value->init."'>".$value->name_company."</option>";
		}
		return $optNya;
	}

	function editData()
	{
		$data = $_POST;
		$dataOut = array();
		$id = $data['id'];
		$typeEdit = $data['typeEdit'];

		if($typeEdit == "editReq")
		{
			$dataOut = $this->mpurchasing->getData("*","request","id = '".$id."'");
		}
		else if ($typeEdit == "editReqDetail") {
			$valDetail = $this->mpurchasing->getData("*", "request_detail", "id_request = '".$id."'");
			$chiefCheckResult = $this->mpurchasing->getData("chief_check", "request", "id = '".$id."'");
			$masterCheckResult = $this->mpurchasing->getData("master_check", "request", "id = '".$id."'");
			
			// Extract the check values from the result arrays
			$chiefCheck = $chiefCheckResult[0]->chief_check;
			$masterCheck = $masterCheckResult[0]->master_check;
			
			$valField = $this->addFieldEditDetail($valDetail, $chiefCheck, $masterCheck);
			$dataOut['divNya'] = $valField['divNya'];
			$dataOut['idField'] = $valField['idField'];
			$dataOut['idReq'] = $id;
		}
		

		print json_encode($dataOut);
	}

	function addFieldEditDetail($valDetail, $chiefCheck, $masterCheck) {
		$dataOut = array();
		$idField = 1;
		$divNya = "";
		$idRemove = "";
	
		for ($lan = 0; $lan < count($valDetail); $lan++) {
			// Determine disabled attribute based on chief and master checks
			$disabledExceptUnitAndRequest = "";
			if ($chiefCheck == 1 && $masterCheck == 1) {
				$disabledExceptUnitAndRequest = "disabled";
			}
	
			// Build HTML for each detail row
			$divNya .= "<div class='row' id='idRemove_$idField'>";
			$divNya .= "<input type='hidden' name='txtIdEdit[]' value='".$valDetail[$lan]->id."'>";
			$divNya .= "<div class='col-md-12'>";
			$divNya .= "<legend><label id='lblFormDetail'> Edit Data Detail</label></legend>";
	
			// Part No:
			$divNya .= "<div class='col-md-1 col-xs-12'>";
			$divNya .= "<div class='form-group'>";
			$divNya .= "<label for='txtCodePartNo'><u>Part No:</u></label>";
			$divNya .= "<input placeholder='Code / Part No' type='text' class='form-control input-sm' id='txtCodePartNo' name='txtCodePartNo[]' value='".$valDetail[$lan]->code_no."' " . ($disabledExceptUnitAndRequest ? "disabled" : "") . ">";
			$divNya .= "</div>";
			$divNya .= "</div>";
	
			// Name of Article:
			$divNya .= "<div class='col-md-2 col-xs-12'>";
			$divNya .= "<div class='form-group'>";
			$divNya .= "<label for='txtNameArticle'><u>Name of Article:</u></label>";
			$divNya .= "<input placeholder='Name of Article' type='text' class='form-control input-sm' id='txtNameArticle' name='txtNameArticle[]' value='".$valDetail[$lan]->article_name."' " . ($disabledExceptUnitAndRequest ? "disabled" : "") . ">";
			$divNya .= "</div>";
			$divNya .= "</div>";

			 // Unit:
			 $divNya .= "<div class='col-md-1 col-xs-12'>";
			 $divNya .= "<div class='form-group'>";
			 $divNya .= "<label for='txtUnit'><u>Unit:</u></label>";
			 $divNya .= "<input value='".$valDetail[$lan]->unit."' type='text' onkeypress='javascript:return isNumber(event)' class='form-control input-sm' id='txtUnit' name='txtUnit[]'>";
			 $divNya .= "</div>";
			 $divNya .= "</div>";
	
			// Working:
			$divNya .= "<div class='col-md-1 col-xs-12'>";
			$divNya .= "<div class='form-group'>";
			$divNya .= "<label for='txtWorkOnBoard'><u>Working:</u></label>";
			$divNya .= "<input value='".$valDetail[$lan]->working_on_board."' type='text' onkeypress='javascript:return isNumber(event)' class='form-control input-sm' id='txtWorkOnBoard' name='txtWorkOnBoard[]' " . ($disabledExceptUnitAndRequest ? "disabled" : "") . ">";
			$divNya .= "</div>";
			$divNya .= "</div>";
	
			// Stock:
			$divNya .= "<div class='col-md-1 col-xs-12'>";
			$divNya .= "<div class='form-group'>";
			$divNya .= "<label for='txtStockOnBoard'><u>Stock:</u></label>";
			$divNya .= "<input value='".$valDetail[$lan]->stocking_on_board."' type='text' onkeypress='javascript:return isNumber(event)' class='form-control input-sm' id='txtStockOnBoard' name='txtStockOnBoard[]' " . ($disabledExceptUnitAndRequest ? "disabled" : "") . ">";
			$divNya .= "</div>";
			$divNya .= "</div>";
	
			// Request:
			$divNya .= "<div class='col-md-1 col-xs-12'>";
			$divNya .= "<div class='form-group'>";
			$divNya .= "<label for='txtTotalReq'><u>Request:</u></label>";
			$divNya .= "<input value='".$valDetail[$lan]->request."' type='text' onkeypress='javascript:return isNumber(event)' class='form-control input-sm' id='txtTotalReq' name='txtTotalReq[]'>";
			$divNya .= "</div>";
			$divNya .= "</div>";
	
			// Mark Reference:
			$slcA = ($valDetail[$lan]->mark == "A") ? "selected='selected'" : "";
			$slcB = ($valDetail[$lan]->mark == "B") ? "selected='selected'" : "";
			$slcC = ($valDetail[$lan]->mark == "C") ? "selected='selected'" : "";
			$slcD = ($valDetail[$lan]->mark == "D") ? "selected='selected'" : "";
			$divNya .= "<div class='col-md-2 col-xs-12'>";
			$divNya .= "<div class='form-group'>";
			$divNya .= "<label for='slcMarkDetail'><u>Mark Reference:</u></label>";
			$divNya .= "<select name='slcMarkDetail[]' id='slcMarkDetail' class='form-control input-sm' " . ($disabledExceptUnitAndRequest ? "disabled" : "") . ">";
			$divNya .= "<option value=''>- Select -</option>";
			$divNya .= "<option value='A' $slcA>TO BE REPLACE URGENTLY</option>";
			$divNya .= "<option value='B' $slcB>BETTER TO BE REPLACE</option>";
			$divNya .= "<option value='C' $slcC>STOCK FOR NEXT O/HAUL</option>";
			$divNya .= "<option value='D' $slcD>STOCK FOR EMERGENCY</option>";
			$divNya .= "</select>";
			$divNya .= "</div>";
			$divNya .= "</div>";
	
			// Remark:
			$divNya .= "<div class='col-md-2 col-xs-12'>";
			$divNya .= "<div class='form-group'>";
			$divNya .= "<label for='txtRemark'><u>Remark:</u></label>";
			$divNya .= "<textarea name='txtRemark[]' class='form-control input-sm' id='txtRemark' " . ($disabledExceptUnitAndRequest ? "disabled" : "") . ">".$valDetail[$lan]->request_remark."</textarea>";
			$divNya .= "</div>";
			$divNya .= "</div>";
	
			// Action button
			$btnActDet = ($lan == 0) ? "<button class='btn btn-primary btn-block btn-sm' title='Add' id='btnAddField' onclick='addRowDetail();' type='button'><i class='glyphicon glyphicon-plus'></i></button>" :
									   "<button class='btn btn-danger btn-block btn-sm' title='Remove' id='btnRmvField' onclick='removeDetail(\"$idField\", \"".$valDetail[$lan]->id."\");' type='button'><i class='glyphicon glyphicon-minus'></i></button>";
			$divNya .= "<div class='col-md-1 col-xs-12'>";
			$divNya .= "<div class='form-group'>";
			$divNya .= "<label style='font-weight: bold;'>&nbsp;</label>";
			$divNya .= $btnActDet;
			$divNya .= "</div>";
			$divNya .= "</div>";
	
			$divNya .= "</div>"; // Close col-md-12
			$divNya .= "</div>"; // Close row
	
			$idField++;
		}
	
		$dataOut['divNya'] = $divNya;
		$dataOut['idField'] = $idField;
		return $dataOut;
	}
	
	

	function approveModalReq()
	{
		$data = $_POST;
		$valData = array();
		$stData = "";
		$userId = $this->session->userdata('idUserPurchase');
		$dateNow = date("Y-m-d H:i:s");

		if($data['typeApprove'] == 'chief')
		{
			$valData['chief_check'] = "1";
			$valData['id_chief'] = $userId;
			$valData['date_chiefCheck'] = $dateNow;
		}
		else if($data['typeApprove'] == 'master')
		{
			$valData['master_check'] = "1";
			$valData['id_master'] = $userId;
			$valData['date_masterCheck'] = $dateNow;

			$valData['revise_date_check'] = "0000-00-00";
			$valData['revise_userId_check'] = "0";
			$valData['revise_remark_check'] = "";
		}
		
		try {
			$whereNya = "id = '".$data['id']."'";
			$this->mpurchasing->updateData($whereNya,$valData,"request");
			$stData = "Approve Success..!!";

			$this->addDataMyAppLetter($data['id'],$data['typeApprove']);

			if($data['typeApprove'] == 'master')
			{
				//$this->sendRemaindByEmail($data['id']);
			}
			
		} catch (Exception $e) {
			$stData = "Failed =>".$e;
		}
		print json_encode($stData);
	}

	function addDataMyAppLetter($idReq = "",$typeApprove = "")
	{
		$dateNow = date("Y-m-d");
		$yearNow = date("Y");
		$monthNow = date("m");
		$noSurat = "1";
		$initDivisi = "DPK";
		$initCmp = "ADY";
		$idUsrLogin = $this->session->userdata('idUserPurchase');
		$usrAddLogin = $idUsrLogin."#".date("H:i")."#".date("d/m/Y");
		$insSqlSrv = array();
		$dept = "";
		$vsl = "";
		$chiefName = "";

		try {

			$sql = " SELECT * FROM request WHERE id = '".$idReq."' AND sts_delete = '0' ";
			$rsl = $this->mpurchasing->getDataQuery($sql);

			if(count($rsl) > 0)
			{
				$dept = $rsl[0]->department;
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

			if($typeApprove == "chief")
			{
				if($dept == "DECK")
				{
					$chiefName = "CHIEF OFFICER";
				}else{
					$chiefName = "CHIEF ENGGINER";
				}
			}else{
				$chiefName = "MASTER";
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
			$insSqlSrv["ket"] = $dept." / ".$vsl." / ".$chiefName;
			$insSqlSrv["copydoc"] = "0";
			$insSqlSrv["canceldoc"] = "0";
			$insSqlSrv["createdby"] = "Purch. System";
			$insSqlSrv["addusrdt"] = $usrAddLogin;

			$this->mpurchasing->insDataMyApps($insSqlSrv,"tblEmpNoSurat");
			//$this->mpurchasing->insDataMyAppsDahlia($insSqlSrv,"tblEmpNoSurat");

			$imgName = $this->createQRCode($batchno);

			if($typeApprove != "")
			{
				$dataUpd = array();

				if($typeApprove == "chief")
				{
					$dataUpd['qrcode_chief'] = $imgName;
				}else{
					$dataUpd['qrcode_master'] = $imgName;
				}			

				$whereNya = "id = '".$idReq."'";
				$this->mpurchasing->updateData($whereNya,$dataUpd,"request");
			}

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
		$sql = " SELECT (batchno + 1) AS batchNo FROM tblempnosurat ORDER BY batchno DESC Limit 0,1";
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

		$sql = "SELECT A.id,A.vessel,A.chief_check,A.master_check,B.email
				FROM request A
				LEFT JOIN send_mail B ON A.vessel = B.vessel AND B.sts_delete = '0'
				WHERE A.sts_delete = '0' AND A.id = '".$idReq."'";
		$rsl = $this->mpurchasing->getDataQuery($sql);

		foreach ($rsl as $key => $val)
		{
			if($val->chief_check == "1" AND $val->master_check == "1")
			{
				if($mailNya == "")
				{
					$mailNya = $val->email;
				}else{
					$mailNya .= ",".$val->email;
				}
			}
		}

		if($mailNya != "")
		{
			$mailNya = "ahmad.maulana@andhika.com";
			$subjectNya = "Request Purchasing From ".$rsl[0]->vessel;
			$isiEmailNya = $this->getContentSendMail($idReq,$rsl[0]->vessel);

			$valData = array();

			$valData['type_data'] = "Request Purchasing";
			$valData['subject'] = $subjectNya;
			$valData['email'] = $mailNya;
			$valData['isi_body'] = $isiEmailNya;

			$this->mpurchasing->insData("mst_notification",$valData);
			// mail($mailNya, $subjectNya, $isiEmailNya, $this->headers());
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
		$headers .= "CC: it@andhika.com,eproc@andhika.com\n";
		
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

		$isiMessage .= "<b>&nbsp;***** ".$vessel." Send Purchase Request. It requires your Check to process it. *****</b>";

		$isiMessage.= "<table width=\"800px\" border=\"1\" cellpadding=\"0\" cellspacing=\"0\" style=\"margin-top:30px;\">";
			$isiMessage.= $data["trNya"];
		$isiMessage.= "</table>";

		$isiMessage.= "<p style=\"margin-top:20px;\"><b><i>:::</i> Detail Request <i>:::</i></b></p>";

		$isiMessage.= "<table width=\"800px\" border=\"1\" cellpadding=\"0\" cellspacing=\"0\">";
			$isiMessage.= "<tr>";
				$isiMessage.= "<td align=\"center\">Name of Article</td>";
				$isiMessage.= "<td align=\"center\">Code / Part No</td>";
				$isiMessage.= "<td align=\"center\">Unit</td>";
				$isiMessage.= "<td align=\"center\">Working</td>";
				$isiMessage.= "<td align=\"center\">Stock</td>";
				$isiMessage.= "<td align=\"center\">Request</td>";
				$isiMessage.= "<td align=\"center\">Mark Ref.</td>";
			$isiMessage.= "</tr>";
			$isiMessage.= $data["trDet"];
		$isiMessage.= "</table>";
		$isiMessage.= "<p style=\"font-size:11px;\">NOTE : 	<br>
						- A = TO BE REPLACE URGENTLY<br>
						- B = BETTER TO BE REPLACE<br>
						- C = STOCK FOR NEXT O/HAUL<br>
						- D = STOCK FOR EMERGENCY </p>";

		$isiMessage .= "<p>To respon this Request, please check <a href=\"http://apps.andhika.com/purchasing\" target=\"_blank\">www.apps.andhika.com -> Purchasing System</a></p>";

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
			$trDet .= "<tr>";
				$trDet .= "<td style=\"vertical-align:top;width:35%;color:#000080;\">".$value->article_name."</td>";
				$trDet .= "<td align=\"center\" style=\"vertical-align:top;width:15%;color:#000080;\">".$value->code_no."</td>";				
				$trDet .= "<td align=\"center\" style=\"vertical-align:top;width:10%;color:#000080;\">".$value->unit."</td>";
				$trDet .= "<td align=\"center\" style=\"vertical-align:top;width:10%;color:#000080;\">".$value->working_on_board."</td>";
				$trDet .= "<td align=\"center\" style=\"vertical-align:top;width:10%;color:#000080;\">".$value->stocking_on_board."</td>";
				$trDet .= "<td align=\"center\" style=\"vertical-align:top;width:10%;color:#000080;\">".$value->request."</td>";
				$trDet .= "<td align=\"center\" style=\"vertical-align:top;width:10%;color:#000080;\">".$value->mark."</td>";
			$trDet .= "</tr>";
		}

		$dataOut["trNya"] = $trNya;
		$dataOut["trDet"] = $trDet;

		return $dataOut;
	}

	function getDataFile()
	{
		$idReq = $_POST['id'];
		$dataOut = array();
		$opt = "";
		$trNya = "";
		$no = 1;

		$opt = "<option value=\"\">- Select Item -</option>";

		$sql = "SELECT * FROM request_detail WHERE sts_delete = '0' AND id_request = '".$idReq."' ";
		$rsl = $this->mpurchasing->getDataQuery($sql);

		foreach ($rsl as $key => $val)
		{
			if($val->request_file == "")
			{
				$opt .= "<option value=\"".$val->id."\">".$val->code_no." || ".$val->article_name."</option>";
			}
			$linkFile = "";

			if($val->request_file != "")
			{
				$linkFile = "<a href=\"".base_url('uploadFile')."/".$val->request_file."\" target=\"_blank\" class=\"btn btn-info btn-xs btn-block\">View</a>";
				$linkFile .= "<button onclick=\"delFile('".$idReq."','".$val->id."','".$val->request_file."');\" class=\"btn btn-danger btn-xs btn-block\" title=\"Delete\">Delete</button>";
			}

			$trNya .= "<tr>";
				$trNya .= "<td align=\"center\">".$no."</td>";
				$trNya .= "<td align=\"center\">".$val->code_no."</td>";
				$trNya .= "<td>".$val->article_name."</td>";
				$trNya .= "<td>".$linkFile."</td>";
			$trNya .= "</tr>";

			$no++;
		}

		$dataOut['opt'] = $opt;
		$dataOut['trNya'] = $trNya;

		print json_encode($dataOut);
	}

	function saveFile()
	{
		$status = "";
		$dir = "./uploadFile";
		$data = $_POST;
		$idDetReq = $data['idDetReq'];
		$submitData = array();

		if($data['cekFileUpload'] != "")
		{
			$fileUploadNya = "";
			$fileName = $_FILES["fileUpload"]["name"];
			$newFileName = "fileReq_".$idDetReq;
			$fileUploadNya = $this->uploadFile($_FILES["fileUpload"]['tmp_name'],$dir,$fileName,$newFileName);
			$submitData['request_file'] = $fileUploadNya;

			if($fileUploadNya != "")
			{
				$whereNya = "id = '".$idDetReq."'";
				$this->mpurchasing->updateData($whereNya,$submitData,"request_detail");
			}

			$status = "Success..!!";
		}

		print $status;
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
				if($rsl[0]->check_approve4 == '1' AND $rsl[0]->check_approve5 == '0')
				{
					$statusCek = "Approval Finance";
				}
				if($rsl[0]->check_approve4 == '1' AND $rsl[0]->check_approve5 == '1')
				{
					$statusCek = "Create PO";
				}
			}
			if($rsl[0]->st_data == '1')
			{
				$statusCek = "-";
			}
		}

		return $statusCek;
	}

	function uploadFile($tmpFile = "",$dir = "",$fileName = "",$newFileName = "")
	{
		$stUpload = "";

		$dt = explode(".", $fileName);
		$newFileName = str_replace(array(' ','/','.',',','-'), '', $newFileName).".".trim($dt[count($dt)-1]);
		// move_uploaded_file($tmpFile, $dir."/".$fileName);
		// rename($dir."/".$fileName, $dir."/".$newFileName);

		if(move_uploaded_file($tmpFile, $dir."/".$fileName))
		{
			rename($dir."/".$fileName, $dir."/".$newFileName);
			$stUpload = $newFileName;
		}
		return $stUpload;
	}

	function delFile()
	{
		$idReqDet = $_POST['idDet'];
		$nmFile = $_POST['nmFile'];
		$valData = array();
		$dir = "./uploadFile";
		$stData = "";

		try {
				unlink($dir."/".$nmFile);

				$valData['request_file'] = "";
				$whereNya = "id = '".$idReqDet."'";
				$this->mpurchasing->updateData($whereNya,$valData,'request_detail');
				$stData = "Delete Success..!!";
			} catch (Exception $e) {
				$stData = "Failed =>".$e;
			}
		print json_encode($stData);
	}

	function delData()
	{
		$data = $_POST;
		$valData = array();
		$id = $data['id'];
		$typeDel = $data['typeDel'];
		$tbl = "";
		$stData = "";

		if($typeDel == "delReq")
		{
			$tbl = "request";
			$whereNya = "id = '".$id."'";
			$valData['sts_delete'] = "1";
			try {
				$this->mpurchasing->updateData($whereNya,$valData,$tbl);
				$stData = "Delete Success..!!";
			} catch (Exception $e) {
				$stData = "Failed =>".$e;
			}
		}
		else if($typeDel == "delReqDetail")
		{
			$idDel = $data['idDel'];
			try {
				$this->db->where('id',$idDel);
  				$this->db->delete('request_detail');
  				$stData = "Delete Success..!!";
  			} catch (Exception $e) {
  				$stData = "Failed =>".$e;
  			}
		}		
		print json_encode($stData);
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
		else if($bln == "08" || $bln == "8"){ $bln = "Ags"; }
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
		else if($bln == "08" || $bln == "8"){ $bln = "Ags"; }
		else if($bln == "09" || $bln == "9"){ $bln = "Sep"; }
		else if($bln == "10"){ $bln = "Okt"; }
		else if($bln == "11"){ $bln = "Nov"; }
		else if($bln == "12"){ $bln = "Des"; }

		return $tgl." ".$bln." ".$thn." ".$dataNya[1];
	}

}





?>