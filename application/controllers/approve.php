<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Approve extends CI_Controller {

	function __construct()
	{
		parent::__construct();		
		$this->load->model('mpurchasing');
		$this->load->helper(array('form', 'url'));
	}
	function index()
	{
	}

	function getApprovePr($searchNya = "")
	{
		$dataOut = array();
		$trNya = "";
		$no = 1;		
		$userId = $this->session->userdata('idUserPurchase');
		$usrJbtn = $this->session->userdata('userPosition');
		$usrType = $this->session->userdata('userTypePurchase');

		$whereNya = " WHERE sts_delete = '0' AND submit_check = '1' AND req_check_approve = '0' AND submit_offered = '0' ";

		if($searchNya == "search")
		{
			$valSearch = $_POST['valSearch'];

			$whereNya .= " AND vessel LIKE '%".$valSearch."%' ";
		}

		$sql = "SELECT * FROM request ".$whereNya." ORDER BY date_request DESC,req_check_approve ASC,create_offered DESC ";
		$data = $this->mpurchasing->getDataQuery($sql);

		foreach ($data as $key => $val)
		{
			$btnAct = "";

			$stApp = "Waitting";
			if($val->department == "DECK" AND (strtolower($usrJbtn) == strtolower("coo") || strtolower($usrType) == strtolower("administrator")))
			{
				$btnAct = " <button onclick=\"showModalCheckReq('".$val->id."');\" class=\"btn btn-danger btn-xs btn-block\" id=\"btnApvSI\" type=\"button\"><i class=\"fa fa-hand-o-right\"></i> Approve PR</button> ";
			}
			if($val->department == "ENGINE" AND (strtolower($usrJbtn) == strtolower("kadiv shipMgmt") || strtolower($usrType) == strtolower("administrator")))
			{
				$btnAct = " <button onclick=\"showModalCheckReq('".$val->id."');\" class=\"btn btn-danger btn-xs btn-block\" id=\"btnApvSI\" type=\"button\"><i class=\"fa fa-hand-o-right\"></i> Approve PR</button> ";
			}
			if($val->department == "PURCHASING" AND (strtolower($usrJbtn) == strtolower("kadept purch") || strtolower($usrType) == strtolower("administrator")))
			{
				$btnAct = " <button onclick=\"showModalApproveOffice('".$val->id."');\" class=\"btn btn-info btn-xs btn-block\" id=\"btnApvSI\" type=\"button\" title=\"Office\"><i class=\"fa fa-hand-o-right\"></i> Approve Office</button> ";
			}

			$stRequired = $this->cekPosisiData($val->id);

			$trNya .= "<tr>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$no."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$this->convertReturnName($val->date_request)."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$val->app_no."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$val->vessel."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$val->department."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$stApp."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$stRequired."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:10px;\">".$btnAct."</td>";
			$trNya .= "</tr>";
			$no++;
		}
		$dataOut['trNya'] = $trNya;

		if($searchNya == "search")
		{
			print json_encode($dataOut);
		}else{
			$this->load->view("purchasing/approvePr",$dataOut);
		}
	}

	function getApproveDraftPo($searchNya = "",$pageNya = "")
	{
		$dataOut = array();
		$trNya = "";
		$no = 1;		
		$userId = $this->session->userdata('idUserPurchase');
		$usrJbtn = $this->session->userdata('userPosition');
		$usrType = $this->session->userdata('userTypePurchase');
		$limitNya = "";
		$dataOut["listPage"] = "";
		$display = "20";

		$whereNya = " WHERE sts_delete = '0' AND ((submit_check = '1' AND req_check_approve = '1' AND submit_offered = '1') OR st_data = '1') ";

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
			$dataPage = $this->getPagingDraftPo(count($dataCount),$pageNya,$display);
			$limitNya = $dataPage['limit'];
			$dataOut["listPage"] = $dataPage['listPage'];
			if($pageNya != "")
			{
				$no = ($pageNya-1) * $display + 1;
			}
		}

		$sql = "SELECT * FROM request ".$whereNya." ORDER BY date_request DESC,req_check_approve ASC,create_offered DESC ".$limitNya;
		$data = $this->mpurchasing->getDataQuery($sql);

		foreach ($data as $key => $val)
		{
			$btnActKadeptPurc = "";
			$btnActKadivPurc = "";
			$btnActKadivShipMgmt = "";
			$btnActCoo = "";
			$btnActFinance = "";
			$stApp = "On Progress";
			$dateAprvKadepPurc = "";
			$dateAprvKadipPurch = "";
			$dateChkKadipShipMngt = "";
			$dateChkCoo = "";
			$dateChkFinance = "";

			$stApp = " <button onclick=\"showModal('".$val->id."','','viewData');\" class=\"btn btn-success btn-xs btn-block\" id=\"btnEdit\" type=\"button\">On Progress</button>";
							
			if($val->check_approve1 == '0')
			{
				$btnActKadeptPurc = "<i class=\"fa fa-question-circle\" style=\"font-size:24px;color:#05C823;cursor:pointer;\" title=\"Waitting Approve\"></i>";
				if(strtolower($usrJbtn) == strtolower("kadept purch") || strtolower($usrType) == strtolower("administrator"))
				{
					$btnActKadeptPurc = " <button onclick=\"showModal('".$val->id."','kadept purch');\" class=\"btn btn-primary btn-xs btn-block\" id=\"btnAppvKadept\" type=\"button\" title=\"Approve\"><i class=\"glyphicon glyphicon-thumbs-up\"></i> Approve</button> ";
				}
			}else{
				$btnActKadeptPurc = "<i class=\"glyphicon glyphicon-ok\"></i>";				
			}

			if($val->st_check_kadiv == '1')
			{
				if($val->check_approve1 == '1' AND $val->check_approve2 == '0')
				{
					$btnActKadivPurc = "<i class=\"fa fa-question-circle\" style=\"font-size:24px;color:#05C823;cursor:pointer;\" title=\"Waitting Approve\"></i>";
					if(strtolower($usrJbtn) == strtolower("kadiv purch") || strtolower($usrType) == strtolower("administrator"))
					{
						$btnActKadivPurc = " <button onclick=\"showModal('".$val->id."','kadiv purch');\" class=\"btn btn-primary btn-xs btn-block\" id=\"btnAppvKadept\" type=\"button\" title=\"Approve\"><i class=\"glyphicon glyphicon-thumbs-up\"></i> Approve</button> ";
					}
				}
				if($val->check_approve2 == '1' AND $val->check_approve3 == '0')
				{
					$btnActKadivPurc = "<i class=\"glyphicon glyphicon-ok\"></i>";
					$btnActKadivShipMgmt = "<i class=\"fa fa-question-circle\" style=\"font-size:24px;color:#05C823;cursor:pointer;\" title=\"Waitting for Approval\"></i>";
					if(strtolower($usrJbtn) == strtolower("kadiv shipMgmt") || strtolower($usrType) == strtolower("administrator"))
					{
						$btnActKadivShipMgmt = " <button onclick=\"showModal('".$val->id."','kadiv shipMgmt');\" class=\"btn btn-primary btn-xs btn-block\" id=\"btnAppvKadept\" type=\"button\" title=\"Approve\"><i class=\"glyphicon glyphicon-thumbs-up\"></i> Check</button> ";
					}
				}

				if($val->check_approve3 == '1' AND $val->check_approve4 == '0')
				{
					$btnActKadivPurc = "<i class=\"glyphicon glyphicon-ok\"></i>";
					$btnActKadivShipMgmt = "<i class=\"glyphicon glyphicon-ok\"></i>";
					$btnActCoo = "<i class=\"fa fa-question-circle\" style=\"font-size:24px;color:#05C823;cursor:pointer;\" title=\"Waitting Approve\"></i>";
					if(strtolower($usrJbtn) == strtolower("coo") || strtolower($usrType) == strtolower("administrator"))
					{
						$btnActCoo = " <button onclick=\"showModal('".$val->id."','coo');\" class=\"btn btn-primary btn-xs btn-block\" id=\"btnAppvKadept\" type=\"button\" title=\"Approve\"><i class=\"glyphicon glyphicon-thumbs-up\"></i> Check</button> ";
					}
				}

				if($val->check_approve4 == '1' AND $val->check_approve5 == '0')
				{
					$btnActKadivPurc = "<i class=\"glyphicon glyphicon-ok\"></i>";
					$btnActKadivShipMgmt = "<i class=\"glyphicon glyphicon-ok\"></i>";
					$btnActCoo = "<i class=\"glyphicon glyphicon-ok\"></i>";
					$btnActFinance = "<i class=\"fa fa-question-circle\" style=\"font-size:24px;color:#05C823;cursor:pointer;\" title=\"Waitting Approve\"></i>";
					if(strtolower($usrJbtn) == strtolower("finance") || strtolower($usrType) == strtolower("administrator"))
					{
						$btnActFinance = " <button onclick=\"showModal('".$val->id."','finance');\" class=\"btn btn-primary btn-xs btn-block\" id=\"btnAppvKadept\" type=\"button\" title=\"Approve\"><i class=\"glyphicon glyphicon-thumbs-up\"></i> Check</button> ";

						if($val->st_pending == "1")
						{
							$btnActFinance = " <button onclick=\"showModal('".$val->id."','finance');\" class=\"btn btn-danger btn-xs btn-block\" id=\"btnAppvKadept\" type=\"button\" title=\"Un Pending\">Un Pending</button> ";
						}
					}
				}
			}else{
				if($val->check_approve1 == '1')
				{
					$btnActKadivPurc = "<i class=\"fa fa-minus\" style=\"font-size:24px;color:#05C823;cursor:pointer;\"></i>";
				}

				if($val->check_approve1 == '1' AND $val->check_approve3 == '0')
				{
					$btnActKadivShipMgmt = "<i class=\"fa fa-question-circle\" style=\"font-size:24px;color:#05C823;cursor:pointer;\" title=\"Waitting for Approval\"></i>";
					if(strtolower($usrJbtn) == strtolower("kadiv shipMgmt") || strtolower($usrType) == strtolower("administrator"))
					{
						$btnActKadivShipMgmt = " <button onclick=\"showModal('".$val->id."','kadiv shipMgmt');\" class=\"btn btn-primary btn-xs btn-block\" id=\"btnAppvKadept\" type=\"button\" title=\"Approve\"><i class=\"glyphicon glyphicon-thumbs-up\"></i> Check</button> ";
					}
				}

				if($val->check_approve3 == '1' AND $val->check_approve4 == '0')
				{
					//$btnActKadivPurc = "<i class=\"glyphicon glyphicon-ok\"></i>";
					$btnActKadivShipMgmt = "<i class=\"glyphicon glyphicon-ok\"></i>";
					$btnActCoo = "<i class=\"fa fa-question-circle\" style=\"font-size:24px;color:#05C823;cursor:pointer;\" title=\"Waitting Approve\"></i>";
					if(strtolower($usrJbtn) == strtolower("coo") || strtolower($usrType) == strtolower("administrator"))
					{
						$btnActCoo = " <button onclick=\"showModal('".$val->id."','coo');\" class=\"btn btn-primary btn-xs btn-block\" id=\"btnAppvKadept\" type=\"button\" title=\"Approve\"><i class=\"glyphicon glyphicon-thumbs-up\"></i> Check</button> ";
					}
				}

				if($val->check_approve4 == '1' AND $val->check_approve5 == '0')
				{
					//$btnActKadivPurc = "<i class=\"glyphicon glyphicon-ok\"></i>";
					$btnActKadivShipMgmt = "<i class=\"glyphicon glyphicon-ok\"></i>";
					$btnActCoo = "<i class=\"glyphicon glyphicon-ok\"></i>";
					$btnActFinance = "<i class=\"fa fa-question-circle\" style=\"font-size:24px;color:#05C823;cursor:pointer;\" title=\"Waitting Approve\"></i>";
					if($val->st_check_finance == '1')
					{
						if(strtolower($usrJbtn) == strtolower("finance") || strtolower($usrType) == strtolower("administrator"))
						{
							$btnActFinance = " <button onclick=\"showModal('".$val->id."','finance');\" class=\"btn btn-primary btn-xs btn-block\" id=\"btnAppvKadept\" type=\"button\" title=\"Approve\"><i class=\"glyphicon glyphicon-thumbs-up\"></i> Check</button> ";
						}
					}else{
						$btnActFinance = "<i class=\"fa fa-minus\" style=\"font-size:24px;color:#05C823;cursor:pointer;\"></i>";
					}
				}
			}

			//if($val->st_data == "1" OR $val->check_approve5 == '1')
			if($val->st_data == "1" OR ($val->check_approve5 == '1' OR ($val->check_approve4 == '1' AND $val->st_check_finance == '0' AND $val->check_approve5 == '0')))
			{
				$btnActKadeptPurc = "<i class=\"glyphicon glyphicon-ok\"></i>";				
				$btnActKadivShipMgmt = "<i class=\"glyphicon glyphicon-ok\"></i>";
				$btnActCoo = "<i class=\"glyphicon glyphicon-ok\"></i>";
				$btnActFinance = "<i class=\"glyphicon glyphicon-ok\"></i>";

				$stApp = " <button onclick=\"showModal('".$val->id."','','viewData');\" class=\"btn btn-primary btn-xs btn-block\" id=\"btnEdit\" type=\"button\"><i class=\"fa fa-eye\"></i> Complete</button>";

				if($val->st_data == "0" AND ($val->check_approve5 == '1' OR ($val->check_approve4 == '1' AND $val->st_check_finance == '0' AND $val->check_approve5 == '0')))
				{
					$stApp = " <button onclick=\"showModal('".$val->id."','','viewData');\" class=\"btn btn-success btn-xs btn-block\" id=\"btnEdit\" type=\"button\">On Progress</button>";
				}

				if($val->st_check_kadiv == '1')
				{
					$btnActKadivPurc = "<i class=\"glyphicon glyphicon-ok\"></i>";
				}

				if($val->st_check_finance == '0' AND $val->check_approve5 == '0')
				{
					$btnActFinance = "<i class=\"fa fa-minus\" style=\"font-size:24px;color:#05C823;cursor:pointer;\"></i>";
				}
			}

			if($val->st_pending == "1")
			{
				$stApp = " <button onclick=\"showModal('".$val->id."','','viewData');\" class=\"btn btn-danger btn-xs btn-block\" id=\"btnEdit\" type=\"button\">Pending</button>";
				$btnActFinance = "<i class=\"fa fa-pause\" style=\"font-size:20px;color:red;cursor:pointer;\" title=\"Pending\"></i>";

				if(strtolower($usrJbtn) == strtolower("finance") || strtolower($usrType) == strtolower("administrator"))
				{
					$btnActFinance = " <button onclick=\"showModal('".$val->id."','finance');\" class=\"btn btn-primary btn-xs btn-block\" id=\"btnAppvKadept\" type=\"button\" title=\"Approve\"><i class=\"glyphicon glyphicon-thumbs-up\"></i> Check</button> ";
				}
			}

			if($val->date_approve1 != "0000-00-00 00:00:00")
			{
				$dateAprvKadepPurc = $this->convertReturnNameWithTime($val->date_approve1);
			}

			if($val->date_approve2 != "0000-00-00 00:00:00")
			{
				$dateAprvKadipPurch = $this->convertReturnNameWithTime($val->date_approve2);
			}

			if($val->date_approve3 != "0000-00-00 00:00:00")
			{
				$dateChkKadipShipMngt = $this->convertReturnNameWithTime($val->date_approve3);
			}

			if($val->date_approve4 != "0000-00-00 00:00:00")
			{
				$dateChkCoo = $this->convertReturnNameWithTime($val->date_approve4);
			}

			if($val->date_approve5 != "0000-00-00 00:00:00")
			{
				$dateChkFinance = $this->convertReturnNameWithTime($val->date_approve5);
			}

			$trNya .= "<tr>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$no."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$this->convertReturnName($val->date_request)."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$val->app_no."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$val->vessel."<br><i>( ".$val->department." )</i></td>";				
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$btnActKadeptPurc."<br>".$dateAprvKadepPurc."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$btnActKadivPurc."<br>".$dateAprvKadipPurch."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$btnActKadivShipMgmt."<br>".$dateChkKadipShipMngt."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$btnActCoo."<br>".$dateChkCoo."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$btnActFinance."<br>".$dateChkFinance."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$stApp."</td>";
			$trNya .= "</tr>";
			$no++;
		}
		$dataOut['trNya'] = $trNya;

		if($searchNya == "search")
		{
			print json_encode($dataOut);
		}else{
			$this->load->view("purchasing/draftPo",$dataOut);
		}
	}

	function getApprove($searchNya = "",$pageNya = "")
	{
		$dataOut = array();
		$trNya = "";
		$no = 1;		
		$userId = $this->session->userdata('idUserPurchase');
		$usrJbtn = $this->session->userdata('userPosition');
		$usrType = $this->session->userdata('userTypePurchase');
		$limitNya = "";
		$dataOut["listPage"] = "";
		$display = "20";

		$whereNya = " WHERE sts_delete = '0' AND submit_check = '1'";

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

		$sql = "SELECT * FROM request ".$whereNya." ORDER BY date_request DESC,req_check_approve ASC,create_offered DESC ".$limitNya;
		$data = $this->mpurchasing->getDataQuery($sql);

		foreach ($data as $key => $val)
		{
			$btnAct = "";
			$stApp = "On Progress";
			if($val->req_check_approve == '1' AND $val->submit_offered == '1')
			{
				$stApp = "Waitting Approve 1";
				
				if($val->check_approve1 == '0' AND (strtolower($usrJbtn) == strtolower("approve 1") || strtolower($usrType) == strtolower("administrator")))
				{
					$stApp = "Waitting";

					$btnAct = " <button onclick=\"showModal('".$val->id."','approve1');\" class=\"btn btn-primary btn-xs btn-block\" id=\"btnEdit\" type=\"button\"><i class=\"fa fa-hand-o-right\"></i> Approve 1</button> ";
				}
				if($val->check_approve1 == '1' AND (strtolower($usrJbtn) == strtolower("approve 1") || strtolower($usrType) == strtolower("administrator")))
				{
					$stApp = "Waitting Approve 2";
				}
				if($val->check_approve1 == '1' AND $val->check_approve2 == '0' AND (strtolower($usrJbtn) == strtolower("approve 2") || strtolower($usrType) == strtolower("administrator")))
				{
					$stApp = "Waitting";

					$btnAct = " <button onclick=\"showModal('".$val->id."','approve2');\" class=\"btn btn-primary btn-xs btn-block\" id=\"btnEdit\" type=\"button\"><i class=\"fa fa-hand-o-right\"></i> Approve 2</button> ";
				}				
				if($val->check_approve1 == '1' AND $val->check_approve2 == '1')
				{
					$stApp = "Approve";
					$btnAct = " <button onclick=\"showModal('".$val->id."','','viewData');\" class=\"btn btn-success btn-xs btn-block\" id=\"btnEdit\" type=\"button\"><i class=\"fa fa-search\"></i> View</button>";
				}
				// if($val->department == "PURCHASING" AND (strtolower($usrJbtn) == strtolower("approve 2") || strtolower($usrType) == strtolower("administrator")))//approve 2 = capt edy sukmono
				// {
				// 	$btnAct = " <button onclick=\"showModal('".$val->id."','approve1');\" class=\"btn btn-primary btn-xs btn-block\" id=\"btnEdit\" type=\"button\"><i class=\"fa fa-hand-o-right\"></i> Approve</button> ";
				// }+ 
			}
			if($val->req_check_approve == '0' AND $val->submit_offered == '0')
			{
				$stApp = "Waitting";
				if($val->department == "DECK" AND (strtolower($usrJbtn) == strtolower("approve 2") || strtolower($usrType) == strtolower("administrator")))
				{
					$btnAct = " <button onclick=\"showModalCheckReq('".$val->id."');\" class=\"btn btn-danger btn-xs btn-block\" id=\"btnApvSI\" type=\"button\"><i class=\"fa fa-hand-o-right\"></i> Approve PR</button> ";
				}
				if($val->department == "ENGINE" AND (strtolower($usrJbtn) == strtolower("approve 1") || strtolower($usrType) == strtolower("administrator")))
				{
					$btnAct = " <button onclick=\"showModalCheckReq('".$val->id."');\" class=\"btn btn-danger btn-xs btn-block\" id=\"btnApvSI\" type=\"button\"><i class=\"fa fa-hand-o-right\"></i> Approve PR</button> ";
				}
				if($val->department == "PURCHASING" AND (strtolower($usrJbtn) == strtolower("approve 2") || strtolower($usrType) == strtolower("administrator")))//approve 2 = capt edy sukmono
				{
					$btnAct = " <button onclick=\"showModalApproveOffice('".$val->id."');\" class=\"btn btn-info btn-xs btn-block\" id=\"btnApvSI\" type=\"button\" title=\"Office\"><i class=\"fa fa-hand-o-right\"></i> Approve Office</button> ";
				}
			}

			if($val->revise_offered == "1")
			{
				$stApp = "Revise";
				$btnAct = "";
			}

			if($val->st_data == "1")
			{
				$stApp = "Complete";
			}

			$stRequired = $this->cekPosisiData($val->id);

			$trNya .= "<tr>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$no."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$this->convertReturnName($val->date_request)."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$val->app_no."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$val->vessel."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$val->department."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$stApp."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$stRequired."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:10px;\">".$btnAct."</td>";
			$trNya .= "</tr>";
			$no++;
		}
		$dataOut['trNya'] = $trNya;

		if($searchNya == "search")
		{
			print json_encode($dataOut);
		}else{
			$this->load->view("purchasing/approve",$dataOut);
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
		$linkLast = base_url('approve/getApprove/-/'.$ttlList);

		$listPage = "Total : ".number_format($count,0)." Data";
		if($page != "")
		{
			$sLimit = ($display * ($page -1));
			$eLimit = $display;
			$bfrPage = $page - 1;
			$aftPage = $page + 1;

			$linkBfr = base_url('approve/getApprove/-/'.$bfrPage);
			$linkAft = base_url('approve/getApprove/-/'.$aftPage);			

			$listPage .= "<nav>";
            	$listPage .= "<ul class=\"pagination pagination-sm\">";
            		$listPage .= "<li class=\"page-item\"><a class=\"page-link\" href=\"".base_url('approve/getApprove')."\">First</a></li>";
	         	if($page == 2)
	         	{
	         		$listPage .= "<li class=\"page-item\"><a class=\"page-link\" href=\"".base_url('approve/getApprove')."\">".$bfrPage."</a></li>";
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
						$listPage .= "<li class=\"page-item\"><a class=\"page-link\" href=\"".base_url('approve/getApprove/-/'.$lan)."\">".$lan."</a></li>";
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

	function getPagingDraftPo($countData = "",$pageNya = "",$display = "")
	{
		$limitNya = array();
		$listPage = "";
		$count = $countData;
		$page = $pageNya;
		$sLimit = "0";
		$eLimit = $display;
		$ttlList = ceil($count/$display);
		$linkLast = base_url('approve/getApproveDraftPo/-/'.$ttlList);

		$listPage = "Total : ".number_format($count,0)." Data";
		if($page != "")
		{
			$sLimit = ($display * ($page -1));
			$eLimit = $display;
			$bfrPage = $page - 1;
			$aftPage = $page + 1;

			$linkBfr = base_url('approve/getApproveDraftPo/-/'.$bfrPage);
			$linkAft = base_url('approve/getApproveDraftPo/-/'.$aftPage);			

			$listPage .= "<nav>";
            	$listPage .= "<ul class=\"pagination pagination-sm\">";
            		$listPage .= "<li class=\"page-item\"><a class=\"page-link\" href=\"".base_url('approve/getApproveDraftPo')."\">First</a></li>";
	         	if($page == 2)
	         	{
	         		$listPage .= "<li class=\"page-item\"><a class=\"page-link\" href=\"".base_url('approve/getApproveDraftPo')."\">".$bfrPage."</a></li>";
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
						$listPage .= "<li class=\"page-item\"><a class=\"page-link\" href=\"".base_url('approve/getApproveDraftPo/-/'.$lan)."\">".$lan."</a></li>";
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

	function getModalDetailReq()
	{
		$dataOut = array();
		$trNya = "";
		$data = $_POST;
		$idReq = $data['idReq'];
		$typeApprove = $data['typeApprove'];
		$no = 1;
		$grandTotal1 = 0;
		$grandTotal2 = 0;
		$grandTotal3 = 0;
		$fileVendor1 = "Vendor 1";
		$fileVendor2 = "Vendor 2";
		$fileVendor3 = "Vendor 3";
		$lblVndName1 = "";
		$lblVndName2 = "";
		$lblVndName3 = "";
		$draftRevisi = "";
		$remarkModal = "";
		$bgTheadColor1 = "";
		$bgTheadColor2 = "";
		$bgTheadColor3 = "";
		$bgTheadColor4 = "";
		$curr1Nya = "";
		$curr2Nya = "";
		$curr3Nya = "";
		$disc1 = 0;
		$disc2 = 0;
		$disc3 = 0;
		$ppn1 = 0;
		$ppn2 = 0;
		$ppn3 = 0;
		$ongkir1 = 0;
		$ongkir2 = 0;
		$ongkir3 = 0;
		$kurs1 = 0;
		$kurs2 = 0;
		$kurs3 = 0;

		$dataOut['headReq'] = $this->mpurchasing->getData("*","request","id = '".$idReq."'");
		
		if($dataOut['headReq'][0]->type_check1 == "quot1")
		{
			$bgTheadColor2 = "#C2C2C2;color:#FFFFFF;";
			$bgTheadColor3 = "#C2C2C2;color:#FFFFFF;";
			$bgTheadColor4 = "#C2C2C2;color:#FFFFFF;";
		}

		if($dataOut['headReq'][0]->type_check1 == "quot2")
		{
			$bgTheadColor1 = "#C2C2C2;color:#FFFFFF;";
			$bgTheadColor3 = "#C2C2C2;color:#FFFFFF;";
			$bgTheadColor4 = "#C2C2C2;color:#FFFFFF;";
		}

		if($dataOut['headReq'][0]->type_check1 == "quot3")
		{
			$bgTheadColor1 = "#C2C2C2;color:#FFFFFF;";
			$bgTheadColor2 = "#C2C2C2;color:#FFFFFF;";
			$bgTheadColor4 = "#C2C2C2;color:#FFFFFF;";
		}

		if($dataOut['headReq'][0]->type_check1 == "custom")
		{
			$bgTheadColor1 = "#C2C2C2;color:#FFFFFF;";
			$bgTheadColor2 = "#C2C2C2;color:#FFFFFF;";
			$bgTheadColor3 = "#C2C2C2;color:#FFFFFF;";
		}

		$valQuot = $this->mpurchasing->getData("*","quotation","id_request = '".$idReq."'","id ASC");

		$valDetail = $this->mpurchasing->getData("*","request_detail","id_request = '".$idReq."'");
		foreach ($valDetail as $key => $val)
		{
			$artName = "<b><i style=\"font-size:11px;\">".$val->code_no."</i></b><br>";
			if($val->request_file != "")
			{
				$artName = "<a href=\"".base_url("uploadFile"."/".$val->request_file)."\" target=\"_blank\">".$artName."</a>";
			}

			$artName .= $val->article_name;
			$stSlc1 = "";
			$stSlc2 = "";
			$stSlc3 = "";
			$stSlcOther = "";
			$disableOpt = "";
			$otherVnd = "";

			if($typeApprove == "kadiv purch" OR $typeApprove == "" OR $typeApprove == "kadiv shipMgmt" OR $typeApprove == "coo" OR $typeApprove == "finance")
			{
				if($dataOut['headReq'][0]->type_check1 == "custom")
				{
					if($val->quot_custom1 == "quot1")
					{
						$stSlc1 = "selected=\"selected\"";
					}
					if($val->quot_custom1 == "quot2")
					{
						$stSlc2 = "selected=\"selected\"";
					}
					if($val->quot_custom1 == "quot3")
					{
						$stSlc3 = "selected=\"selected\"";
					}
					if($val->quot_custom1 == "other")
					{
						$stSlcOther = "selected=\"selected\"";
						if($val->quot_other1 != "")
						{
							if($otherVnd == "")
							{
								$otherVnd = "Vendor 1 (Qty : ".$val->quot_other1_qty.")";
							}else{
								$otherVnd .= "<br>Vendor 1 (Qty : ".$val->quot_other1_qty.")";
							}
						}
						if($val->quot_other2 != "")
						{
							if($otherVnd == "")
							{
								$otherVnd = "Vendor 2 (Qty : ".$val->quot_other2_qty.")";
							}else{
								$otherVnd .= "<br>Vendor 2 (Qty : ".$val->quot_other2_qty.")";
							}
						}
						if($val->quot_other3 != "")
						{
							if($otherVnd == "")
							{
								$otherVnd = "Vendor 3 (Qty : ".$val->quot_other3_qty.")";
							}else{
								$otherVnd .= "<br>Vendor 3 (Qty : ".$val->quot_other3_qty.")";
							}
						}
					}
				}
				$disableOpt = "disabled=\"disabled\"";
			}
			
			if($val->quot_qty1 > 0 AND $val->quot_price1 > 0)
			{
				$disc1 = $valQuot[0]->discount;
				$ppn1 = $valQuot[0]->ppn;
				$ongkir1 = $valQuot[0]->delivery_cost;
				$kurs1 = $valQuot[0]->kurs;

				$lblVndName1 = $valQuot[0]->vendor_company;
				if($valQuot[0]->file_name != "")
				{
					if($valQuot[0]->file_date_upload == "0000-00-00")
					{
						$fileVendor1 = "<a href=\"".base_url("uploadFile"."/".$valQuot[0]->file_name)."\" target=\"_blank\" style=\"color:#FFF;\"><u>Vendor 1</u></a>";
					}else{
						$fileVendor1 = "<a href=\"".base_url("uploadFile"."/".$valQuot[0]->file_name)."\" target=\"_blank\" style=\"color:#FFF;\"><u>Vendor 1</u></a> / ".$this->convertReturnName($valQuot[0]->file_date_upload);
					}
				}
			}
			if($val->quot_qty2 > 0 AND $val->quot_price2 > 0)
			{
				$disc2 = $valQuot[1]->discount;
				$ppn2 = $valQuot[1]->ppn;
				$ongkir2 = $valQuot[1]->delivery_cost;
				$kurs2 = $valQuot[1]->kurs;

				$lblVndName2 = $valQuot[1]->vendor_company;
				if($valQuot[1]->file_name != "")
				{
					if($valQuot[1]->file_date_upload == "0000-00-00")
					{
						$fileVendor2 = "<a href=\"".base_url("uploadFile"."/".$valQuot[1]->file_name)."\" target=\"_blank\" style=\"color:#FFF;\"><u>Vendor 2</u></a>";
					}else{
						$fileVendor2 = "<a href=\"".base_url("uploadFile"."/".$valQuot[1]->file_name)."\" target=\"_blank\" style=\"color:#FFF;\"><u>Vendor 2</u></a> / ".$this->convertReturnName($valQuot[1]->file_date_upload);
					}
				}
			}
			if($val->quot_qty3 > 0 AND $val->quot_price3 > 0)
			{
				$disc3 = $valQuot[2]->discount;
				$ppn3 = $valQuot[2]->ppn;
				$ongkir3 = $valQuot[2]->delivery_cost;
				$kurs3 = $valQuot[2]->kurs;

				$lblVndName3 = $valQuot[2]->vendor_company;
				if($valQuot[2]->file_name != "")
				{
					if($valQuot[2]->file_date_upload == "0000-00-00")
					{
						$fileVendor3 = "<a href=\"".base_url("uploadFile"."/".$valQuot[2]->file_name)."\" target=\"_blank\" style=\"color:#FFF;\"><u>Vendor 3</u></a>";
					}else{
						$fileVendor3 = "<a href=\"".base_url("uploadFile"."/".$valQuot[2]->file_name)."\" target=\"_blank\" style=\"color:#FFF;\"><u>Vendor 3</u></a> / ".$this->convertReturnName($valQuot[2]->file_date_upload);
					}
				}
			}
			$curr1Nya = $val->quot_curr1;
			$curr2Nya = $val->quot_curr2;
			$curr3Nya = $val->quot_curr3;
			$trNya .= "<tr>";
				$trNya .= "<td align=\"center\">".$no."</td>";
				$trNya .= "<td>".$artName."</td>";
				$trNya .= "<td align=\"center\">".$val->unit."<br>".$stSlc2."</td>";
				$trNya .= "<td align=\"center\">".$val->approved_order."</td>";
				$trNya .= "<td align=\"center\" style=\"background-color:".$bgTheadColor1.";\">".$val->quot_qty1."</td>";
				$trNya .= "<td align=\"right\" style=\"background-color:".$bgTheadColor1.";\">".number_format($val->quot_price1,2)."</td>";
				$trNya .= "<td align=\"right\" style=\"background-color:".$bgTheadColor1.";\">".number_format($val->quot_amount1,2)."</td>";
				$trNya .= "<td align=\"center\" style=\"background-color:".$bgTheadColor2.";\">".$val->quot_qty2."</td>";
				$trNya .= "<td align=\"right\" style=\"background-color:".$bgTheadColor2.";\">".number_format($val->quot_price2,2)."</td>";
				$trNya .= "<td align=\"right\" style=\"background-color:".$bgTheadColor2.";\">".number_format($val->quot_amount2,2)."</td>";
				$trNya .= "<td align=\"center\" style=\"background-color:".$bgTheadColor3.";\">".$val->quot_qty3."</td>";
				$trNya .= "<td align=\"right\" style=\"background-color:".$bgTheadColor3.";\">".number_format($val->quot_price3,2)."</td>";
				$trNya .= "<td align=\"right\" style=\"background-color:".$bgTheadColor3.";\">".number_format($val->quot_amount3,2)."</td>";
				$trNya .= "<td align=\"center\" style=\"background-color:".$bgTheadColor4.";\">
							<input type=\"hidden\" name=\"txtIdDetail[]\" id=\"txtIdDetail\" value=\"".$val->id."\">
							<select name=\"slcVendor[]\" id=\"slcVendor_".$val->id."\" class=\"form-control input-sm\" onchange=\"slcCustomVendor('".$val->id."')\" ".$disableOpt.">
								<option value=\"quot1\" ".$stSlc1.">Vendor 1</option>
								<option value=\"quot2\" ".$stSlc2.">Vendor 2</option>
								<option value=\"quot3\" ".$stSlc3.">Vendor 3</option>
								<option value=\"other\" ".$stSlcOther.">Others</option>
							</select>
							<div style=\"text-align:left;\" id=\"idDivCustom_".$val->id."\">".$otherVnd."</div>
							</td>";
			$trNya .= "</tr>";
			$grandTotal1 = $grandTotal1 + $val->quot_amount1;
			$grandTotal2 = $grandTotal2 + $val->quot_amount2;
			$grandTotal3 = $grandTotal3 + $val->quot_amount3;
			$no++;
		}

		if($dataOut['headReq'][0]->remark_check1 != "")
		{
			$remarkModal .= "<label style=\"background-color:#D1E2FF;padding:5px;border-radius:5px;width:100%;\"><u>Approved By : Kadept Purchase</u><i style=\"font-size:11px;color:red;\"> ( ".$this->convertReturnNameWithTime($dataOut['headReq'][0]->date_approve1)." )</i><br>".$dataOut['headReq'][0]->remark_check1."</label>";
		}

		if($dataOut['headReq'][0]->remark_check2 != "")
		{
			$remarkModal .= "<br><label style=\"background-color:#D1E2FF;padding:5px;border-radius:5px;width:100%;\"><u>Approved By : Kadiv Purchase</u><i style=\"font-size:11px;color:red;\"> ( ".$this->convertReturnNameWithTime($dataOut['headReq'][0]->date_approve2)." )</i><br>".$dataOut['headReq'][0]->remark_check2."</label>";
		}

		if($dataOut['headReq'][0]->remark_check3 != "")
		{
			$remarkModal .= "<label style=\"background-color:#D1E2FF;padding:5px;border-radius:5px;width:100%;\"><u>Checked By : Kadiv Ship Management</u><i style=\"font-size:11px;color:red;\"> ( ".$this->convertReturnNameWithTime($dataOut['headReq'][0]->date_approve3)." )</i><br>".$dataOut['headReq'][0]->remark_check3."</label>";
		}

		if($dataOut['headReq'][0]->remark_check4 != "")
		{
			$remarkModal .= "<label style=\"background-color:#D1E2FF;padding:5px;border-radius:5px;width:100%;\"><u>Checked By : COO</u><i style=\"font-size:11px;color:red;\"> ( ".$this->convertReturnNameWithTime($dataOut['headReq'][0]->date_approve4)." )</i><br>".$dataOut['headReq'][0]->remark_check4."</label>";
		}

		if($dataOut['headReq'][0]->st_pending == "1")
		{
			$sqlPend = $this->mpurchasing->getDataQuery("SELECT * FROM tbl_revisi WHERE type_revisi = 'pending' AND id_request = '".$idReq."' ORDER BY id DESC LIMIT 0,1");
			if(count($sqlPend) > 0)
			{
				$remarkModal .= "<label style=\"background-color:#D1E2FF;padding:5px;border-radius:5px;width:100%;\"><u>Pending By : Finance</u>
								<i style=\"font-size:11px;color:red;\"> ( ".$this->convertReturnNameWithTime($sqlPend[0]->date_revisi)." )</i><br>".$sqlPend[0]->reason."</label>";
			}
		}

		if($dataOut['headReq'][0]->st_pending == "0")
		{
			if($dataOut['headReq'][0]->remark_check5 != "")
			{
				$remarkModal .= "<label style=\"background-color:#D1E2FF;padding:5px;border-radius:5px;width:100%;\"><u>Checked By : Finance</u>
				<i style=\"font-size:11px;color:red;\"> ( ".$this->convertReturnNameWithTime($dataOut['headReq'][0]->date_approve5)." )</i><br>".$dataOut['headReq'][0]->remark_check5."</label>";
			}
		}

		if($dataOut['headReq'][0]->draft_po_revisi == "1")
		{
			$valDraftRev = $this->mpurchasing->getDataQuery("SELECT * FROM tbl_revisi WHERE type_revisi != 'pending' AND id_request = '".$idReq."' ORDER BY id DESC LIMIT 0,1");
			$draftRevisi = "<u>Revisi from : ".$valDraftRev[0]->type_revisi."</u> <i style=\"color:red;font-size:10px;\">".$this->convertReturnNameWithTime($valDraftRev[0]->date_revisi)."</i><br>".$valDraftRev[0]->reason;
		}

		$total1 = $grandTotal1;
		$total2 = $grandTotal2;
		$total3 = $grandTotal3;
		$afterDisc1 = $total1 - $disc1;
		$afterDisc2 = $total2 - $disc2;
		$afterDisc3 = $total3 - $disc3;
		$gtotal1 = $afterDisc1 + $ppn1 + $ongkir1;
		$gtotal2 = $afterDisc2 + $ppn2 + $ongkir2;
		$gtotal3 = $afterDisc3 + $ppn3 + $ongkir3;

		$totalAfterKurs1 = $gtotal1;
		if($kurs1 > 0) { $totalAfterKurs1 = $gtotal1 * $kurs1; }

		$totalAfterKurs2 = $gtotal2;
		if($kurs2 > 0) { $totalAfterKurs2 = $gtotal2 * $kurs2; }

		$totalAfterKurs3 = $gtotal3;
		if($kurs3 > 0) { $totalAfterKurs3 = $gtotal3 * $kurs3; }

		$trNya .= "<tr>";
			$trNya .= "	<td colspan=\"2\"></td>";
			$trNya .= "<td align=\"right\" colspan=\"2\">";
				$trNya .= "Total :<br>Discount :<br>After Discount :<br>PPN :<br>Delivery Cost :<br>Grand Total :<br>Kurs :<br>Total After Kurs (Rp) :";
			$trNya .= "</td>";
			$trNya .= "<td align=\"right\" colspan=\"3\" style=\"background-color:".$bgTheadColor1.";\">";
				$trNya .= "<span style=\"float:left;padding-left:50px;\">".strtoupper($curr1Nya)."</span>".number_format($total1,2);
				$trNya .= "<br><span style=\"float:left;padding-left:50px;\">".strtoupper($curr1Nya)."</span>".number_format($disc1,2);
				$trNya .= "<br><span style=\"float:left;padding-left:50px;\">".strtoupper($curr1Nya)."</span>".number_format($afterDisc1,2);
				$trNya .= "<br><span style=\"float:left;padding-left:50px;\">".strtoupper($curr1Nya)."</span>".number_format($ppn1,2);
				$trNya .= "<br><span style=\"float:left;padding-left:50px;\">".strtoupper($curr1Nya)."</span>".number_format($ongkir1,2);
				$trNya .= "<br><span style=\"float:left;padding-left:50px;\">".strtoupper($curr1Nya)."</span>".number_format($gtotal1,2);
				$trNya .= "<br><span style=\"float:left;padding-left:50px;\">".strtoupper($curr1Nya)."</span>".number_format($kurs1,2);
				$trNya .= "<br><span style=\"float:left;padding-left:50px;\">".strtoupper("idr")."</span>".number_format($totalAfterKurs1,2);
			$trNya .= "</td>";
			$trNya .= "<td align=\"right\" colspan=\"3\" style=\"background-color:".$bgTheadColor2.";\">";
				$trNya .= "<span style=\"float:left;padding-left:50px;\">".strtoupper($curr2Nya)."</span>".number_format($total2,2);
				$trNya .= "<br><span style=\"float:left;padding-left:50px;\">".strtoupper($curr2Nya)."</span>".number_format($disc2,2);
				$trNya .= "<br><span style=\"float:left;padding-left:50px;\">".strtoupper($curr2Nya)."</span>".number_format($afterDisc2,2);
				$trNya .= "<br><span style=\"float:left;padding-left:50px;\">".strtoupper($curr2Nya)."</span>".number_format($ppn2,2);
				$trNya .= "<br><span style=\"float:left;padding-left:50px;\">".strtoupper($curr2Nya)."</span>".number_format($ongkir2,2);
				$trNya .= "<br><span style=\"float:left;padding-left:50px;\">".strtoupper($curr2Nya)."</span>".number_format($gtotal2,2);
				$trNya .= "<br><span style=\"float:left;padding-left:50px;\">".strtoupper($curr2Nya)."</span>".number_format($kurs2,2);
				$trNya .= "<br><span style=\"float:left;padding-left:50px;\">".strtoupper("idr")."</span>".number_format($totalAfterKurs2,2);
			$trNya .= "</td>";
			$trNya .= "<td align=\"right\" colspan=\"3\" style=\"background-color:".$bgTheadColor3.";\">";
				$trNya .= "<span style=\"float:left;padding-left:50px;\">".strtoupper($curr3Nya)."</span>".number_format($total3,2);
				$trNya .= "<br><span style=\"float:left;padding-left:50px;\">".strtoupper($curr3Nya)."</span>".number_format($disc3,2);
				$trNya .= "<br><span style=\"float:left;padding-left:50px;\">".strtoupper($curr3Nya)."</span>".number_format($afterDisc3,2);
				$trNya .= "<br><span style=\"float:left;padding-left:50px;\">".strtoupper($curr3Nya)."</span>".number_format($ppn3,2);
				$trNya .= "<br><span style=\"float:left;padding-left:50px;\">".strtoupper($curr3Nya)."</span>".number_format($ongkir3,2);
				$trNya .= "<br><span style=\"float:left;padding-left:50px;\">".strtoupper($curr3Nya)."</span>".number_format($gtotal3,2);
				$trNya .= "<br><span style=\"float:left;padding-left:50px;\">".strtoupper($curr3Nya)."</span>".number_format($kurs3,2);
				$trNya .= "<br><span style=\"float:left;padding-left:50px;\">".strtoupper("idr")."</span>".number_format($totalAfterKurs3,2);
			$trNya .= "</td>";
			$trNya .= "<td style=\"background-color:".$bgTheadColor4.";\"></td>";
		$trNya .= "</tr>";
		$trNya .= "<tr>";
			$trNya .= "	<td colspan=\"2\">
							<textarea name=\"txtRemark\" class=\"form-control input-sm\" id=\"txtRemark\" placeholder=\"Remark\" ".$disableOpt."></textarea>
							<label id=\"idLblRemark\"></label>
						</td>";
			$trNya .= "<td align=\"left\" colspan=\"12\" style=\"font-size:12px;\" id=\"idNoteModalDetail\">".$draftRevisi."</td>";
		$trNya .= "</tr>";

		$dataOut['trNya'] = $trNya;
		$dataOut['idReq'] = $idReq;
		$dataOut['fileVendor1'] = $fileVendor1;
		$dataOut['fileVendor2'] = $fileVendor2;
		$dataOut['fileVendor3'] = $fileVendor3;
		$dataOut['lblVndName1'] = $lblVndName1;
		$dataOut['lblVndName2'] = $lblVndName2;
		$dataOut['lblVndName3'] = $lblVndName3;
		$dataOut['remarkModal'] = $remarkModal;
		$dataOut['bgTheadColor1'] = $bgTheadColor1;
		$dataOut['bgTheadColor2'] = $bgTheadColor2;
		$dataOut['bgTheadColor3'] = $bgTheadColor3;
		$dataOut['bgTheadColor4'] = $bgTheadColor4;
		print json_encode($dataOut);
	}

	function getModalDetailCheckRequest()
	{
		$dataOut = array();
		$trNya = "";
		$data = $_POST;
		$idReq = $data['idReq'];
		$no = 1;

		$rslHead = $this->mpurchasing->getData("*","request","id = '".$idReq."' AND sts_delete = '0'");

		$valDetail = $this->mpurchasing->getData("*","request_detail","id_request = '".$idReq."' AND sts_delete = '0'");
		foreach ($valDetail as $key => $val)
		{
			$article_name = $val->article_name;
			if($val->request_file)
			{
				$article_name = "<a href=\"" . base_url("/uploadFile") . "/" . $val->request_file . "\" target=\"_blank\">".$val->article_name."</a>";
			}
			$trNya .= "<tr>";
				$trNya .= "<td align=\"center\">".$no."</td>";
				$trNya .= "<td>".$article_name."</td>";
				$trNya .= "<td>".$val->code_no."</td>";
				$trNya .= "<td align=\"center\">".$val->unit."</td>";
				$trNya .= "<td align=\"center\">".$val->working_on_board."</td>";
				$trNya .= "<td align=\"center\">".$val->stocking_on_board."</td>";
				$trNya .= "<td align=\"center\">".$val->request."</td>";
				$trNya .= "<td align=\"center\">".$val->approved_order."</td>";
				$trNya .= "<td align=\"center\">".$val->mark."</td>";
				$trNya .= "<td align=\"center\">".$val->approve_remark."</td>";
			$trNya .= "</tr>";
			$no++;
		}

		$dataOut['dateNya'] = $this->convertReturnName($rslHead[0]->date_request);
		$dataOut['appNo'] = $rslHead[0]->app_no;
		$dataOut['Vessel'] = $rslHead[0]->vessel;
		$dataOut['Dept'] = $rslHead[0]->department;
		$dataOut['trNya'] = $trNya;

		print json_encode($dataOut);
	}

	function getModalDetailApproveOffice()
	{
		$dataOut = array();
		$trNya = "";
		$data = $_POST;
		$idReq = $data['idReq'];
		$no = 1;

		$rslHead = $this->mpurchasing->getData("*","request","id = '".$idReq."' AND sts_delete = '0'");

		$valDetail = $this->mpurchasing->getData("*","request_detail","id_request = '".$idReq."' AND sts_delete = '0'");
		foreach ($valDetail as $key => $val)
		{
			$trNya .= "<tr>";
				$trNya .= "<td align=\"center\">".$no."</td>";
				$trNya .= "<td>".$val->article_name."</td>";
				$trNya .= "<td>".$val->code_no."</td>";
				$trNya .= "<td align=\"center\">".$val->unit."</td>";
				$trNya .= "<td align=\"center\">".$val->working_on_board."</td>";
				$trNya .= "<td align=\"center\">".$val->stocking_on_board."</td>";
				$trNya .= "<td align=\"center\">".$val->request."</td>";
				$trNya .= "<td align=\"center\">".$val->mark."</td>";
				$trNya .= "<td align=\"center\">".$val->request_remark."</td>";
			$trNya .= "</tr>";
			$no++;
		}

		$dataOut['dateNya'] = $this->convertReturnName($rslHead[0]->date_request);
		$dataOut['appNo'] = $rslHead[0]->app_no;
		$dataOut['Vessel'] = $rslHead[0]->vessel;
		$dataOut['Dept'] = $rslHead[0]->department;
		$dataOut['trNya'] = $trNya;

		print json_encode($dataOut);
	}

	function approveModal()
	{
		$data = $_POST;
		$valData = array();
		$arrIdDet = array();
		$arrSlcVen = array();
		$arrTemp = array();
		$idReq = $data['idReq'];
		$typeApprove = $data['typeApprove'];
		$typeCheck = $data['typeCheck'];
		$userId = $this->session->userdata('idUserPurchase');

		try {
				if($typeApprove == "kadept purch")
				{
					$valData['check_approve1'] = '1';
					$valData['draft_po_revisi'] = '0';
					$valData['type_check1'] = $data['typeCheck'];
					$valData['remark_check1'] = $data['remark'];
					$valData['date_approve1'] = date("Y-m-d H:i:s");
					$valData['idUser_approve1'] = $userId;
					if($data['typeCheck'] == "custom")
					{					
						$arrIdDet = explode("*",$data['idDet']);
						$arrSlcVen = explode("*",$data['slcVen']);
						$arrSlcVenOther = explode("*",$data['slcVenOther']);
						$arrSlcVenOtherQty = explode("*",$data['slcVenOtherQty']);

						for ($lan=0; $lan < count($arrIdDet); $lan++)
						{
							if($arrSlcVen[$lan] == "other")
							{
								$slcVend = $arrSlcVen[$lan];
								$arrTempVnd = explode("#",$arrSlcVenOther[$lan]);
								$arrTempVndQty = explode("#",$arrSlcVenOtherQty[$lan]);

								$whereNya = "";

								for ($ast=0; $ast < count($arrTempVnd); $ast++)
								{
									if($ast == 0)
									{
										$whereNya = "id = '".$arrTempVnd[$ast]."'";
									}
									else{
										if($arrTempVnd[$ast] != "-")
										{
											$arrTemp["quot_other".$ast] = $arrTempVnd[$ast];
											$arrTemp["quot_other".$ast."_qty"] = $arrTempVndQty[$ast];
										}
									}
								}

								if($whereNya != "")
								{
									$arrTemp['quot_custom1'] = $arrSlcVen[$lan];
									$this->mpurchasing->updateData($whereNya,$arrTemp,"request_detail");
									$arrTemp = array();
								}

							}else{
								$arrTemp['quot_custom1'] = $arrSlcVen[$lan];
								$whereNya = "id = '".$arrIdDet[$lan]."'";
								$this->mpurchasing->updateData($whereNya,$arrTemp,"request_detail");
								$arrTemp = array();
							}
						}
					}

					$valData['qrcode_approve1'] = $this->addDataMyAppLetter($idReq);
					$this->cekGrandtotalPickUp($idReq,$data['typeCheck']);

					////$this->sendRemaindByEmail($idReq,'approve1');
				}
				else if($typeApprove == "kadiv purch")
				{
					$valData['check_approve2'] = '1';
					$valData['remark_check2'] = $data['remark'];
					$valData['date_approve2'] = date("Y-m-d H:i:s");
					$valData['idUser_approve2'] = $userId;

					$valData['qrcode_approve2'] = $this->addDataMyAppLetter($idReq);
				}
				else if($typeApprove == "kadiv shipMgmt")
				{
					$valData['check_approve3'] = '1';
					$valData['remark_check3'] = $data['remark'];
					$valData['date_approve3'] = date("Y-m-d H:i:s");
					$valData['idUser_approve3'] = $userId;
					$valData['qrcode_approve3'] = $this->addDataMyAppLetter($idReq);
				}
				else if($typeApprove == "coo")
				{
					$valData['check_approve4'] = '1';
					$valData['remark_check4'] = $data['remark'];
					$valData['date_approve4'] = date("Y-m-d H:i:s");
					$valData['idUser_approve4'] = $userId;
					$valData['qrcode_approve4'] = $this->addDataMyAppLetter($idReq);
				}
				else if($typeApprove == "finance")
				{
					$valData['st_pending'] = '0';
					$valData['check_approve5'] = '1';
					$valData['remark_check5'] = $data['remark'];
					$valData['date_approve5'] = date("Y-m-d H:i:s");
					$valData['idUser_approve5'] = $userId;
					$valData['qrcode_approve5'] = $this->addDataMyAppLetter($idReq);
				}

				$valData['last_send_mail'] = "0000-00-00";
			
				$whereNya = "id = '".$idReq."'";
				$this->mpurchasing->updateData($whereNya,$valData,"request");

			$stData = "Approve Success..!!";
		} catch (Exception $e) {
			$stData = "Failed =>".$e;
		}

		print json_encode($stData);
	}

	function approveModalCheckRequest()
	{
		$data = $_POST;
		$valData = array();
		$idReq = $data['idReq'];
		$userId = $this->session->userdata('idUserPurchase');
		$dateNow = date("Y-m-d H:i:s");

		try {

			$valData['req_check_approve'] = "1";
			$valData['req_check_id'] = $userId;
			$valData['req_check_date'] = $dateNow;
			$valData['last_send_mail'] = "0000-00-00";
			$valData['req_check_qrcode'] = $this->addDataMyAppLetter($idReq);

			$whereNya = "id = '".$idReq."'";
			$this->mpurchasing->updateData($whereNya,$valData,"request");

			$this->cekApproveNya($idReq);
			//$this->sendRemaindByEmailCheckRequest($idReq);

			$stData = "Approve Success..!!";
		} catch (Exception $e) {
			$stData = "Failed =>".$e;
		}

		print json_encode($stData);
	}

	function cekGrandtotalPickUp($idReq = "",$typeCheck = "")
	{
		$grandTotal = 0;
		$sql = "";
		$typeQuot = "";

		if($typeCheck == "quot1")
		{
			$sql = "SELECT SUM(quot_amount1) AS total FROM request_detail WHERE sts_delete = '0' AND id_request = '".$idReq."' ";
			$typeQuot = "1";
		}
		else if($typeCheck == "quot2")
		{
			$sql = "SELECT SUM(quot_amount2) AS total FROM request_detail WHERE sts_delete = '0' AND id_request = '".$idReq."' ";
			$typeQuot = "2";
		}
		else if($typeCheck == "quot3")
		{
			$sql = "SELECT SUM(quot_amount3) AS total FROM request_detail WHERE sts_delete = '0' AND id_request = '".$idReq."' ";
			$typeQuot = "3";
		}
		else if($typeCheck == "custom")
		{
			$sql = "SELECT SUM( CASE
					WHEN quot_custom1 =  'quot1' THEN quot_amount1
					WHEN quot_custom1 =  'quot2' THEN quot_amount2
					WHEN quot_custom1 =  'quot3' THEN quot_amount3
					WHEN quot_custom1 =  'other' THEN 
					CASE  WHEN quot_other1 =  'quot1' THEN quot_amount1
					WHEN quot_other2 =  'quot2' THEN quot_amount2
					WHEN quot_other3 =  'quot3' THEN quot_amount3
					END
					END
					) AS total
					FROM request_detail WHERE sts_delete = '0' AND id_request = '".$idReq."' ";	
		}

		if($sql != "")
		{
			$rsl = $this->mpurchasing->getDataQuery($sql);
			if(count($rsl) > 0)
			{
				$grandTotal = $rsl[0]->total;
			}
		}

		if($typeQuot != "")
		{
			$sqlq = " SELECT * FROM quotation WHERE sts_delete = '0' AND id_request = '".$idReq."' ORDER BY id ASC ";
			$rslq = $this->mpurchasing->getDataQuery($sqlq);

			if(count($rslq) > 0)
			{
				$disc = 0;
				$ppn = 0;
				$ongkir = 0;
				$kurs = 0;
				$typeQuot = $typeQuot -1;

				$disc = $rslq[$typeQuot]->discount;
				$ppn = $rslq[$typeQuot]->ppn;
				$ongkir = $rslq[$typeQuot]->delivery_cost;
				$kurs = $rslq[$typeQuot]->kurs;

				$grandTotal = ($grandTotal - $disc) + $ppn + $ongkir;

				if($kurs > 0)
				{
					$grandTotal = $grandTotal * $kurs;
				}
			}
		}

		if($grandTotal > 100000000) // diatas 100jt
		{
			$valData = array();
			$valData['st_check_kadiv'] = "1";
			$valData['st_check_finance'] = "1";

			$whereNya = "id = '".$idReq."'";
			$this->mpurchasing->updateData($whereNya,$valData,"request");
		}
		
		return $grandTotal;
	}

	function cekApproveNya($idReq = "")
	{
		$rslHead = $this->mpurchasing->getData("*","request","id = '".$idReq."' AND sts_delete = '0'");

		if(count($rslHead) > 0)
		{
			if($rslHead[0]->type_request == "office")
			{
				$valDetail = $this->mpurchasing->getData("*","request_detail","id_request = '".$idReq."' AND sts_delete = '0'");
				foreach ($valDetail as $key => $val)
				{
					$uptData = array();
					$uptData['approved_order'] = $val->request;

					$whereNya = "id = '".$val->id."'";
					$this->mpurchasing->updateData($whereNya,$uptData,"request_detail");
				}
			}
		}		
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
		$insSql = array();
		$vsl = "";
		$imgName = "";

		try {

			$sql = " SELECT * FROM request WHERE id = '".$idReq."' AND sts_delete = '0' ";
			$rsl = $this->mpurchasing->getDataQuery($sql);

			if(count($rsl) > 0)
			{
				$vsl = $rsl[0]->vessel;
				if($rsl[0]->type_request == "office")
				{
					$vsl = "Office";
				}
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
			$formatNoSrt = $this->createNo($noSurat)."/".$initCmp."/".$initDivisi."/".$monthNow.substr($yearNow, 2,2);

			$insSql["batchno"] = $batchno;
			$insSql["cmpcode"] = $initCmp;
			$insSql["nosurat"] = $formatNoSrt;
			$insSql["issueddiv"] = $initDivisi;
			$insSql["signedby"] = $initDivisi;
			$insSql["address"] = "Ship Management";
			$insSql["tglsurat"] = $dateNow;
			$insSql["ket"] =  "Approve / ".$vsl." / ".$fullNameLogin;
			$insSql["copydoc"] = "0";
			$insSql["canceldoc"] = "0";
			$insSql["createdby"] = "Purch. System";
			$insSql["addusrdt"] = $usrAddLogin;

			$this->mpurchasing->insDataMyApps($insSql,"tblEmpNoSurat");
			//$this->mpurchasing->insDataMyAppsDahlia($insSql,"tblEmpNoSurat");

			$imgName = $this->createQRCode($batchno);
		} catch (Exception $e) {
			$imgName = "Failed => ".$e->getMessage();
		}
		return $imgName;
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

	function addRevisiModal()
	{
		$data = $_POST;
		$valData = array();
		$userId = $this->session->userdata('idUserPurchase');
		$dateNow = date("Y-m-d H:i:s");
		$stData = "";

		try {
				if($data['typeApprove'] != "")
				{
					$tempRevisi = array();

					$tempRevisi['id_request'] = $data['idReq'];
					$tempRevisi['type_revisi'] = $data['typeApprove'];
					$tempRevisi['reason'] = $data['remark'];
					$tempRevisi['user_revisi'] = $userId;
					$tempRevisi['date_revisi'] = $dateNow;

					$this->mpurchasing->insData("tbl_revisi",$tempRevisi);

					if($data['typeApprove'] == "kadept purch")
					{
						$valData['submit_offered'] = '0';
						$valData['revise_offered'] = '1';
						$valData['revise_remark_offered'] = $data['remark'];
					}
				}

				$valData['draft_po_revisi'] = '1';
				$valData['st_pending'] = '0';
				$valData['check_approve1'] = '0';
				$valData['type_check1'] = "";
				$valData['remark_check1'] = "";
				$valData['date_approve1'] = "0000-00-00";
				$valData['idUser_approve1'] = "0";

				$valData['check_approve2'] = '0';
				$valData['remark_check2'] = "";
				$valData['date_approve2'] = "0000-00-00";
				$valData['idUser_approve2'] = '0';
				$valData['check_approve3'] = '0';
				$valData['remark_check3'] = "";
				$valData['date_approve3'] = "0000-00-00";
				$valData['idUser_approve3'] = '0';
				$valData['check_approve4'] = '0';
				$valData['remark_check4'] = "";
				$valData['date_approve4'] = "0000-00-00";
				$valData['idUser_approve4'] = '0';
				$valData['check_approve5'] = '0';
				$valData['remark_check5'] = "";
				$valData['date_approve5'] = "0000-00-00";
				$valData['idUser_approve5'] = '0';
				$valData['st_check_kadiv'] = '0';
				$valData['st_check_finance'] = '0';
				$valData['qrcode_approve1'] = '';
				$valData['qrcode_approve2'] = '';
				$valData['qrcode_approve3'] = '';
				$valData['qrcode_approve4'] = '';
				$valData['qrcode_approve5'] = '';

				$whereNya = "id = '".$data['idReq']."'";
				$this->mpurchasing->updateData($whereNya,$valData,"request");

				$arrTemp = array();
				$arrTemp['quot_custom1'] = "";
				$whereDetail = "id_request = '".$data['idReq']."'";
				$this->mpurchasing->updateData($whereDetail,$arrTemp,"request_detail");

				//$this->sendRevisiByEmail($data['idReq']);
				$stData = "Submit Success..!!";
			} catch (Exception $e) {
				$stData = "Failed =>".$e;
			}
		print json_encode($stData);
	}

	function addPendingModal()
	{
		$data = $_POST;
		$valData = array();
		$tempRevisi = array();
		$userId = $this->session->userdata('idUserPurchase');
		$dateNow = date("Y-m-d H:i:s");
		$stData = "";

		try {
				$tempRevisi['id_request'] = $data['idReq'];
				$tempRevisi['type_revisi'] = "pending";
				$tempRevisi['reason'] = $data['remark'];
				$tempRevisi['user_revisi'] = $userId;
				$tempRevisi['date_revisi'] = $dateNow;
				$this->mpurchasing->insData("tbl_revisi",$tempRevisi);
			
				$valData['st_pending'] = '1';
				$whereNya = "id = '".$data['idReq']."'";
				$this->mpurchasing->updateData($whereNya,$valData,"request");

				$stData = "Submit Success..!!";
			} catch (Exception $e) {
				$stData = "Failed =>".$e;
			}
		print json_encode($stData);
	}

	function addRevisiModalCheckRequest()
	{
		$data = $_POST;
		$valData = array();
		$userId = $this->session->userdata('idUserPurchase');
		$stData = "";

		try {
				$valData['check_order'] = '2';
				$valData['remark_revisi'] = $data['remark'];
				$valData['idUser_revisi'] = $userId;
				$valData['submit_check'] = '0';
				$valData['submit_check_date'] = '0000-00-00 00:00:00';

				$whereNya = "id = '".$data['idReq']."'";
				$this->mpurchasing->updateData($whereNya,$valData,"request");

				//$this->sendRemaindByEmailCheckRequest($data['idReq'],'revise');
				$stData = "Submit Success..!!";
			} catch (Exception $e) {
				$stData = "Failed =>".$e;
			}
		print json_encode($stData);
	}

	function addRevisiModalApproveOffice()
	{
		$data = $_POST;
		$valData = array();
		$userId = $this->session->userdata('idUserPurchase');
		$stData = "";

		try {
				$valData['check_order'] = "2";
				$valData['remark_revisi'] = $data['remark'];
				$valData['idUser_revisi'] = $userId;
				$valData['chief_check'] = "0";
				$valData['master_check'] = "0";
				$valData['id_chief'] = "0";
				$valData['date_chiefCheck'] = '0000-00-00 00:00:00';
				$valData['id_master'] = "0";
				$valData['date_masterCheck'] = '0000-00-00 00:00:00';
				$valData['submit_check'] = "0";
				$valData['submit_check_date'] = '0000-00-00 00:00:00';
				$valData['idUser_check'] = "0";

				$whereNya = "id = '".$data['idReq']."'";
				$this->mpurchasing->updateData($whereNya,$valData,"request");

				//$this->sendRemaindByEmailCheckRequest($data['idReq'],'revise');

				$stData = "Submit Success..!!";
			} catch (Exception $e) {
				$stData = "Failed =>".$e;
			}
		print json_encode($stData);
	}

	function sendRemaindByEmail($idReq = '',$typeAprv = '')
	{
		$mailNya = "";
		$subjectNya = "";
		$isiEmailNya = "";

		$sql = "SELECT id,vessel FROM request WHERE sts_delete = '0' AND id = '".$idReq."'";
		$rsl = $this->mpurchasing->getDataQuery($sql);

		$sqlMail = "SELECT * FROM send_mail WHERE sts_delete = '0' AND vessel = 'purchasing' ";
		$rslMail = $this->mpurchasing->getDataQuery($sqlMail);

		foreach ($rslMail as $key => $val)
		{
			if($mailNya == "")
			{
				if($typeAprv == 'approve1')
				{
					$mailNya = $val->email_approve2;
				}else{
					$mailNya = $val->email;
				}
			}else{
				if($typeAprv == 'approve1')
				{
					$mailNya .= ",".$val->email_approve2;
				}else{
					$mailNya .= ",".$val->email;
				}
			}
		}

		if($mailNya != "")
		{
			$mailNya = "ahmad.maulana@andhika.com";
			if($typeAprv == 'approve1')
			{
				if($rsl[0]->vessel == "vessel")
				{
					$subjectNya = "Purchasing. Approved 1 awaiting Approval 2 For ".$rsl[0]->vessel;
				}else{
					$subjectNya = "Purchasing. Approved 1 awaiting Approval 2";
				}
			}else{
				if($rsl[0]->vessel == "vessel")
				{
					$subjectNya = "Create PO For ".$rsl[0]->vessel;
				}else{
					$subjectNya = "Create PO ";
				}				
			}

			$isiEmailNya = $this->getContentSendMail($idReq,$rsl[0]->vessel,$typeAprv);
			
			//print_r($isiEmailNya);exit;
			mail($mailNya, $subjectNya, $isiEmailNya, $this->headers());
		}
	}

	function sendRevisiByEmail($idReq = '')
	{
		$mailNya = "";
		$subjectNya = "";
		$isiEmailNya = "";

		$sql = "SELECT id,vessel FROM request WHERE sts_delete = '0' AND id = '".$idReq."'";
		$rsl = $this->mpurchasing->getDataQuery($sql);

		$sqlMail = "SELECT * FROM send_mail WHERE sts_delete = '0' AND vessel = 'purchasing' ";
		$rslMail = $this->mpurchasing->getDataQuery($sqlMail);

		foreach ($rslMail as $key => $val)
		{
			if($mailNya == "")
			{
				$mailNya = $val->email;
			}else{
				$mailNya .= ",".$val->email;
			}
		}

		if($mailNya != "")
		{
			$mailNya = "ahmad.maulana@andhika.com";

			$subjectNya = "Revise For Vessel ".$rsl[0]->vessel;
			$isiEmailNya = $this->getContentSendMail($idReq,$rsl[0]->vessel,"");			
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
		//$headers .= "CC: it@andhika.com,eproc@andhika.com\n";
		
		return $headers;
	}

	function getContentSendMail($idReq = "",$vessel = "",$typeAprv = "")
	{
		$data = $this->getIsiContent($idReq);
		$isiMessage = "";

		$isiMessage .= "<p>";
			$isiMessage .= "*************************************************<br>";
			$isiMessage .= "PLEASE DO NOT REPLY THIS EMAIL..!!<br>";
			$isiMessage .= "*************************************************<br>";
		$isiMessage .= "</p>";

		if($typeAprv == 'approve1')
		{
			$isiMessage .= "<b>&nbsp;***** Approved 1 awaiting Approval 2 For Vessel ".$vessel." *****</b>";
		}
		else if($typeAprv == 'approve2')
		{
			$isiMessage .= "<b>&nbsp;***** Create PO For Vessel ".$vessel." *****</b>";
		}
		else if($typeAprv == "")
		{
			$isiMessage .= "<b>&nbsp;***** Revise, Make Changes to Offer For Vessel ".$vessel." *****</b>";
		}

		$isiMessage.= "<table width=\"800px\" border=\"1\" cellpadding=\"0\" cellspacing=\"0\" style=\"margin-top:30px;\">";
			$isiMessage.= $data["trNya"];
		$isiMessage.= "</table>";

		$isiMessage .= "<p>To respon this Request, please check <a href=\"apps.andhika.com/purchasing\" target=\"_blank\">www.apps.andhika.com</a></p>";

		$isiMessage .= "<p>";
			$isiMessage .= "*************************************************<br>";
			$isiMessage .= "END OF NOTIFICATION<br>";
			$isiMessage .= "*************************************************<br>";
		$isiMessage .= "</p>";

		return $isiMessage;
	}

	function getIsiContent($idReq = "",$typeSend = "")
	{
		$dataOut = array();
		$trNya = "";
		$remarknya = "";

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
			$remarknya = $val->remark_revisi;
		}

		if($typeSend != "")
		{
			$trNya .= "<tr>";
				$trNya .= "<td style=\"vertical-align:top;\">Remark Revise</td>";
				$trNya .= "<td style=\"vertical-align:top;color:#000080;\" colspan=\"3\">".$remarknya."</td>";
			$trNya .= "</tr>";
		}

		$dataOut["trNya"] = $trNya;

		return $dataOut;
	}

	function sendRemaindByEmailCheckRequest($idReq = '',$typeSend = '')
	{
		$mailNya = "";
		$subjectNya = "";
		$isiEmailNya = "";

		$sql = "SELECT id,vessel,department FROM request WHERE sts_delete = '0' AND id = '".$idReq."'";
		$rsl = $this->mpurchasing->getDataQuery($sql);

		if($typeSend == "")
		{
			$sqlMail = "SELECT * FROM send_mail WHERE sts_delete = '0' AND vessel = 'purchasing' ";
			$subjectNya = "Create Offer Request Purchasing For ".$rsl[0]->vessel;
			$isiEmailNya = $this->getContentSendMailCheckReq($idReq,$rsl[0]->vessel);
		}else{
			$sqlMail = "SELECT * FROM send_mail WHERE sts_delete = '0' AND vessel = '".$rsl[0]->vessel."' ";
			$subjectNya = "Revise Request Purchasing From ".$rsl[0]->vessel;
			$isiEmailNya = $this->getContentSendMailCheckReq($idReq,$rsl[0]->vessel,$typeSend);
		}
		$rslMail = $this->mpurchasing->getDataQuery($sqlMail);

		if(count($rslMail) > 0)
		{
			$mailNya = $rslMail[0]->email;
		}
		//print_r($isiEmailNya);exit;
		if($mailNya != "")
		{
			$mailNya = "ahmad.maulana@andhika.com";
			//print_r($isiEmailNya);exit;
			mail($mailNya, $subjectNya, $isiEmailNya, $this->headers());
		}
	}

	function getContentSendMailCheckReq($idReq = "",$vessel = "",$typeSend = "")
	{
		$data = $this->getIsiContent($idReq,$typeSend);
		$isiMessage = "";

		$isiMessage .= "<p>";
			$isiMessage .= "*************************************************<br>";
			$isiMessage .= "PLEASE DO NOT REPLY THIS EMAIL..!!<br>";
			$isiMessage .= "*************************************************<br>";
		$isiMessage .= "</p>";

		if($typeSend == "")
		{
			$isiMessage .= "<b>&nbsp;***** Approved Check Request For Vessel ".$vessel." *****</b>";
		}else{
			$isiMessage .= "<b>&nbsp;***** Revise Request For Vessel ".$vessel." *****</b>";
		}

		$isiMessage.= "<table width=\"800px\" border=\"1\" cellpadding=\"0\" cellspacing=\"0\" style=\"margin-top:30px;\">";
			$isiMessage.= $data["trNya"];
		$isiMessage.= "</table>";

		$isiMessage .= "<p>To respon this Request, please check <a href=\"http://apps.andhika.com/purchasing\" target=\"_blank\">www.apps.andhika.com</a></p>";

		$isiMessage .= "<p>";
			$isiMessage .= "*************************************************<br>";
			$isiMessage .= "END OF NOTIFICATION<br>";
			$isiMessage .= "*************************************************<br>";
		$isiMessage .= "</p>";

		return $isiMessage;
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
					$statusCek = "Purchaser";
				}
				if($rsl[0]->submit_offered == '1' AND $rsl[0]->check_approve1 == '0')
				{
					$statusCek = "Approval PIC 1";
				}
				if($rsl[0]->check_approve1 == '1' AND $rsl[0]->check_approve2 == '0')
				{
					$statusCek = "Approval PIC 2";
				}
				if($rsl[0]->check_approve1 == '1' AND $rsl[0]->check_approve2 == '1')
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