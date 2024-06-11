<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Purchasing extends CI_Controller {

	function __construct()
	{
		parent::__construct();		
		$this->load->model('mpurchasing');
		$this->load->helper(array('form', 'url'));
		$this->load->library('../controllers/MasErpApi');
	}
	function index()
	{
		$this->load->view('purchasing/login');
	}

	function home()
	{
		$this->load->view('purchasing/home');
	}

	function getListPurchasing($searchNya = "",$pageNya = "")
	{
		$dataOut = array();
		$trNya = "";
		$no = 1;
		$limitNya = "";
		$dataOut["listPage"] = "";
		$display = "10";

		$whereNya = " WHERE (check_approve5 = '1' OR (check_approve4 = '1' AND st_check_finance = '0' AND check_approve5 = '0')) AND check_approve4 = '1' AND sts_delete = '0'";

		if($searchNya == "search")
		{
			$slcSearch = $_POST['slcSearch'];
			$valSearch = $_POST['valSearch'];

			if($slcSearch == "complete")
			{
				$whereNya .= " AND st_data = '1' ";
			}
			else if($slcSearch == "draft")
			{
				$whereNya .= " AND st_data = '0' AND create_purchasing = '1' ";
			}
			else if($slcSearch == "vessel")
			{
				$whereNya .= " AND vessel LIKE '%".$valSearch."%' ";
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

		$sql = "SELECT * FROM request ".$whereNya." ORDER BY id DESC,master_check ASC ".$limitNya;
		$rsl = $this->mpurchasing->getDataQuery($sql);
		
		foreach ($rsl as $key => $val)
		{
			$dataQuot = array();
			$stApp = "Ready";
			$btnAct = "";
			$vendor = "";
			$dataQuot = $this->getQuot($val->id,$val->type_check1);

			if($dataQuot['pic_vendor'] != "")
			{
				$vendor = $dataQuot['vendor_company'];
				$vendor .= "<br><i>(".$dataQuot['pic_vendor'].")</i>";
			}			

			if($val->create_purchasing == '0')
			{
				$btnAct = " <button onclick=\"checkPurchasing('".$val->id."','".$val->type_check1."');\" class=\"btn btn-primary btn-xs\" id=\"btnEdit\" type=\"button\"><i class=\"fa fa-check-square-o\"></i> Create Purchasing</button>";
			}
			else if($val->create_purchasing == '1' AND $val->submit_purchasing == '0')
			{
				$stApp = "Draft";
				$btnAct = " <button onclick=\"editPurchasing('".$val->id."','".$val->type_check1."');\" class=\"btn btn-success btn-xs\" id=\"btnEdit\" type=\"button\"><i class=\"fa fa-pencil-square-o\"></i> Edit Purchasing</button>";
				$btnAct .= " <button onclick=\"cekSubmitPurchasing('".$val->id."');\" class=\"btn btn-primary btn-xs\" id=\"btnEdit\" type=\"button\"><i class=\"fa fa-check-square-o\"></i> Submit</button>";
			}
			if($val->submit_purchasing == '1')
			{
				$stApp = "Draft";
				$btnAct = " <a href=\"".base_url('purchasing/viewListPurchase')."/".$val->id."\" class=\"btn btn-danger btn-xs\"><i class=\"fa fa-edit\"></i> Daftar Purchase</a>";

				if($val->st_data == '0')
				{
					$stApp = "PO Done";
				}
			}
			if($val->st_data == '1')
			{
				$stApp = "Complete";
				$btnAct = " <a href=\"".base_url('purchasing/viewListPurchase')."/".$val->id."\" class=\"btn btn-danger btn-xs\"><i class=\"fa fa-edit\"></i> Daftar Purchase</a>";
			}
			$trNya .= "<tr>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$no."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$this->convertReturnName($val->date_request)."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$val->app_no."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$val->vessel."</td>";
				$trNya .= "<td align=\"left\" style=\"font-size:11px;\">".$vendor."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$stApp."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:10px;\">".$btnAct."</td>";
			$trNya .= "</tr>";
			$no++;
		}
		$dataOut['trNya'] = $trNya;

		if($searchNya == "search")
		{
			print json_encode($dataOut);			
		}else{
			$dataOut['optCompany'] = $this->getCompany();
			$this->load->view("purchasing/listPurchasing",$dataOut);
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
		$linkLast = base_url('purchasing/getListPurchasing/-/'.$ttlList);

		$listPage = "Total : ".number_format($count,0)." Data";
		if($page != "")
		{
			$sLimit = ($display * ($page -1));
			$eLimit = $display;
			$bfrPage = $page - 1;
			$aftPage = $page + 1;

			$linkBfr = base_url('purchasing/getListPurchasing/-/'.$bfrPage);
			$linkAft = base_url('purchasing/getListPurchasing/-/'.$aftPage);			

			$listPage .= "<nav>";
            	$listPage .= "<ul class=\"pagination pagination-sm\">";
            		$listPage .= "<li class=\"page-item\"><a class=\"page-link\" href=\"".base_url('purchasing/getListPurchasing')."\">First</a></li>";
	         	if($page == 2)
	         	{
	         		$listPage .= "<li class=\"page-item\"><a class=\"page-link\" href=\"".base_url('purchasing/getListPurchasing')."\">".$bfrPage."</a></li>";
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
						$listPage .= "<li class=\"page-item\"><a class=\"page-link\" href=\"".base_url('purchasing/getListPurchasing/-/'.$lan)."\">".$lan."</a></li>";
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

	function addPurchasing()
	{
		$data = $_POST;
		$userId = $this->session->userdata('idUserPurchase');
		$idReq = $data['idReq'];
		$stData = "";

		$arrReqDate = array();
		$arrPoNo = array();
		$arrPoNoOri = array();
		$arrDatePo = array();
		$arrSubject = array();
		$arrPicVendor = array();
		$arrVendorCom = array();
		$arrVendorCode = array();
		$arrShip = array();
		$arrShipCom = array();
		$arrIdPurch = array();
		$arridDet = array();
		$arrQty = array();
		$arrCurr = array();
		$arrPrice = array();
		$arrAmount = array();
		$valData = array();
		$valDataReq = array();
		$valDataPurcs = array();
		$arrIdDetail = array();
		$updData = array();

		$arrReqDate = explode("*",$data['reqDate']);
		$arrPoNo = explode("*",$data['poNo']);
		$arrPoNoOri = explode("*",$data['poNoOri']);
		$arrDatePo = explode("*",$data['datePo']);
		$arrSubject = explode("*",$data['subject']);
		$arrPicVendor = explode("*",$data['picVendor']);
		$arrVendorCom = explode("*",$data['vendorCom']);
		$arrVendorCode = explode("*",$data['vendorCode']);
		$arrShip = explode("*",$data['ship']);
		$arrShipCom = explode("*",$data['shipCom']);
		$arrIdPurch = explode("*",$data['idPurch']);

		$idLP1 = 0;
		$idLP2 = 0;
		$idLP3 = 0;
		
		try {
			for ($lan=0; $lan < 3; $lan++)
			{
				$dataIns = array();
				$idNya = "idLP".($lan+1);

				if($arrPicVendor[$lan] != "-" AND $arrVendorCom[$lan] != "-")
				{
					$dataIns['id_request'] = $idReq;
					$dataIns['date_request'] = $arrReqDate[$lan];
					$dataIns['order_name'] = $arrPicVendor[$lan];
					$dataIns['order_company'] = $arrVendorCom[$lan];
					$dataIns['ship_name'] = $arrShip[$lan];
					$dataIns['ship_company'] = $arrShipCom[$lan];
					$dataIns['po_date'] = $arrDatePo[$lan];
					$dataIns['po_no'] = $arrPoNo[$lan];
					$dataIns['po_no_int'] = $arrPoNoOri[$lan];
					$dataIns['subject'] = $arrSubject[$lan];

					if($arrIdPurch[$lan] == "-")
					{
						${$idNya} = $this->mpurchasing->insData("list_purchase",$dataIns,"return");
					}else{
						${$idNya} = $arrIdPurch[$lan];
						$whereNya = "id = '".$arrIdPurch[$lan]."'";
						$this->mpurchasing->updateData($whereNya,$dataIns,"list_purchase");
					}
				}
			}

			for ($hal=1; $hal <= 3; $hal++)//urutan vendor
			{
				if($data['idDetail'.$hal] != "")
				{
					$arridDet = explode("*",$data['idDetail'.$hal]);
					$arrQty = explode("*",$data['qty'.$hal]);
					$arrCurr = explode("*",$data['curr'.$hal]);
					$arrPrice = explode("*",$data['price'.$hal]);
					$arrAmount = explode("*",$data['amount'.$hal]);
					
					for ($lanHal=0; $lanHal < count($arridDet); $lanHal++)
					{
						$qCustom = $this->cekCustomQuot($arridDet[$lanHal]);

						if($qCustom['customQuot'] == "other")
						{
							if($qCustom['quotOther'.$hal] == "quot1")
							{
								$updData['purchase_qty'] = $arrQty[$lanHal];
								$updData['purchase_curr'] = $arrCurr[$lanHal];
								$updData['purchase_price'] = $arrPrice[$lanHal];
								$updData['purchase_amount'] = $arrAmount[$lanHal];

								if(${"idLP".$hal} > 0)
								{
									$updData['purchase_id'] = ${"idLP".$hal};
								}
							}
							if($qCustom['quotOther'.$hal] == "quot2")
							{
								$updData['purchase2_qty'] = $arrQty[$lanHal];
								$updData['purchase2_curr'] = $arrCurr[$lanHal];
								$updData['purchase2_price'] = $arrPrice[$lanHal];
								$updData['purchase2_amount'] = $arrAmount[$lanHal];

								if(${"idLP".$hal} > 0)
								{
									$updData['purchase2_id'] = ${"idLP".$hal};
								}
							}
							if($qCustom['quotOther'.$hal] == "quot3")
							{
								$updData['purchase3_qty'] = $arrQty[$lanHal];
								$updData['purchase3_curr'] = $arrCurr[$lanHal];
								$updData['purchase3_price'] = $arrPrice[$lanHal];
								$updData['purchase3_amount'] = $arrAmount[$lanHal];

								if(${"idLP".$hal} > 0)
								{
									$updData['purchase3_id'] = ${"idLP".$hal};
								}
							}
						}else{
							$updData['purchase_qty'] = $arrQty[$lanHal];
							$updData['purchase_curr'] = $arrCurr[$lanHal];
							$updData['purchase_price'] = $arrPrice[$lanHal];
							$updData['purchase_amount'] = $arrAmount[$lanHal];

							if(${"idLP".$hal} > 0)
							{
								$updData['purchase_id'] = ${"idLP".$hal};
							}
						}

						$whereNya = "id = '".$arridDet[$lanHal]."'";
						$this->mpurchasing->updateData($whereNya,$updData,"request_detail");
						$updData = array();
					}
				}

				$uptlistPurc = array();
				$uptlistPurc['vendor_quot'] = "quot".$hal;
				$wherePurcNya = "id = '".${"idLP".$hal}."'";
				$this->mpurchasing->updateData($wherePurcNya,$uptlistPurc,"list_purchase");
			}

			$valDataReq['create_purchasing'] = '1';
			$valDataReq['date_purchasing'] = date("Y-m-d");
			$valDataReq['idUser_purchasing'] = $userId;

			$whereNya = "id = '".$idReq."'";
			$this->mpurchasing->updateData($whereNya,$valDataReq,"request");

			$stData = "Save Success..!!";
		} catch (Exception $e) {
			$stData = "Failed =>".$e;
		}

		print $stData;
	}

	function viewListPurchase($idReq = "",$typeView = "")
	{
		$usrType = $this->session->userdata('userTypePurchase');
		$dataOut = array();
		$trNya = "";
		$no = 1;

		$whereNya = "id_request = '".$idReq."' AND sts_delete = '0' ";
		$sql = "SELECT * FROM list_purchase WHERE ".$whereNya;
		$rsl = $this->mpurchasing->getDataQuery($sql);

		foreach ($rsl as $key => $value)
		{
			$vendor = $value->order_name."<br><i style=\"font-size:10px;\">(".$value->order_company.")</i>";
			$ship = $value->ship_name."<br><i style=\"font-size:10px;\">(".$value->ship_company.")</i>";

			$linkAction = "";

			if($value->st_data == "0")
			{
				if($typeView == "")
				{
					$linkAction = " <button onclick=\"createPO('".$value->id."');\" class=\"btn btn-primary btn-xs btn-block\" id=\"btnCreatePo\" type=\"button\"><i class=\"fa fa-check-square-o\"></i> Create</button>";
				}
			}else{
				if($typeView == "")
				{
					$laNya = base_url("purchasing/exportPurchasing"."/".$value->id);
					$linkAction = "<a href=\"".$laNya."\" class=\"btn btn-primary btn-xs btn-block\"><i class=\"fa fa-check-square-o\"></i> View</a>";
				}else{
					$laNya = base_url("purchasing/exportPurchasing"."/".$value->id."/".$typeView);
					$linkAction = "<a href=\"".$laNya."\" class=\"btn btn-primary btn-xs btn-block\" target=\"_blank\"><i class=\"fa fa-check-square-o\"></i> View</a>";
				}
				

				if($value->send_erp == "0")
				{
					if($typeView == "")
					{
						$linkAction .= "<button onclick=\"getDataSendErp('".$value->id."');\" class=\"btn btn-danger btn-xs btn-block\" id=\"btnSendToErp\" type=\"button\" title=\"Send To ERP\" style=\"margin-top:5px;\"><i class=\"fa fa-location-arrow\"></i> Send to Erp</button>";
					}
				}else{
					if($usrType == "administrator")
					{
						$linkAction .= "<button onclick=\"cancelDataToErp('".$value->id."');\" class=\"btn btn-danger btn-xs btn-block\" id=\"btnCancelErp\" type=\"button\" title=\"Cancel ERP\" style=\"margin-top:5px;\"><i class=\"fa fa-history\"></i> Cancel</button>";
					}
				}

				if($value->st_bargecharge == "0")
				{
					$linkAction .= "<button onclick=\"getModalBargeCharge('".$value->id."');\" class=\"btn btn-success btn-xs btn-block\" id=\"btnBargeCharge\" type=\"button\" title=\"Barge Charge\" style=\"margin-top:5px;\">Barge Charge</button>";
				}else{
					$laNya = base_url("purchasing/exportPurchasingBargeCharge"."/".$value->id);
					$linkAction .= "<a href=\"".$laNya."\" class=\"btn btn-primary btn-xs btn-block\" style=\"margin-top:5px;\" title=\"View Barge Charge\"><i class=\"fa fa-eye\"></i> View BC</a>";
					if($value->send_erp_bargecharge == '0')
					{
						$linkAction .= "<button onclick=\"getDataSendErpBargeCharge('".$value->id."');\" class=\"btn btn-info btn-xs btn-block\" id=\"btnBargeCharge\" type=\"button\" title=\"Send to Erp Barge Charge\" style=\"margin-top:5px;\">Send to Erp BC</button>";
					}else{
						$linkAction .= "<button onclick=\"alert('".$value->id."');\" class=\"btn btn-danger btn-xs btn-block\" id=\"btnCancelErp\" type=\"button\" title=\"Cancel ERP Barge Charge\" style=\"margin-top:5px;\"><i class=\"fa fa-history\"></i> Cancel Barge charge</button>";
					}					
				}
			}

			$trNya .= "<tr>";
				$trNya .= "<td align=\"center\">".$no."</td>";
				$trNya .= "<td align=\"center\">".$this->convertReturnName($value->date_request)."</td>";
				$trNya .= "<td align=\"center\">".$value->po_no."</td>";
				$trNya .= "<td align=\"center\">".$this->convertReturnName($value->po_date)."</td>";
				$trNya .= "<td align=\"left\">".$vendor."</td>";
				$trNya .= "<td align=\"left\">".$ship."</td>";
				$trNya .= "<td align=\"center\">".$linkAction."</td>";
			$trNya .= "</tr>";

			$no++;
		}

		$cssDisplay = "";
		if($typeView != "")
		{
			$cssDisplay = "display:none;";
		}

		$dataOut['cssDisplay'] = $cssDisplay;
		$dataOut['trNya'] = $trNya;

		$this->load->view("purchasing/daftarPurchase",$dataOut);
	}

	function getDataToErpBargeCharge()
	{
		$erpControl = new MasErpApi();
		$dataOut = array();
		$subTotal = 0;
		$grandTotal = 0;
		$id = $_POST['id'];
		$trNya = "";
		$no = 1;
		$qrCodeApprv1 = "";
		$qrCodeApprv2 = "";
		$trTTDnya = "";
		$trTTDnya2 = "";
		$trTTDnya3 = "";
		$companyNya = "";

		$dataOut['headNya'] = $this->mpurchasing->getData("*","list_purchase","id = '".$id."' AND sts_delete = '0' ");
		$dbErpByCmp = $erpControl->cekDbErpByCompany($dataOut['headNya'][0]->ship_company);
		$itemCodeOpt = $erpControl->getOptItemCodeErp("","",$dbErpByCmp,$dataOut['headNya'][0]->erp_itemcode);
		$itemCodeOpt2 = $erpControl->getOptItemCodeErp("","",$dbErpByCmp,$dataOut['headNya'][0]->erp_itemcode2);

		$optSatuan = $erpControl->getOptItemSatuanErp("return",$dataOut['headNya'][0]->erp_itemcode,$dbErpByCmp,$dataOut['headNya'][0]->satuan_code_bargecharge);
		$optSatuan2 = $erpControl->getOptItemSatuanErp("return",$dataOut['headNya'][0]->erp_itemcode2,$dbErpByCmp,$dataOut['headNya'][0]->satuan_code_bargecharge2);

		$slcOpt = "<select id=\"slcErp_kodeBrg_BC_".$id."\" name=\"slcErp_kodeBrgBC[]\" class=\"form-control\" onchange=\"slcSatuanByItemCode($(this).val(),'".$id."','".$dbErpByCmp."');\">";
			$slcOpt .= "<option value=\"\">- SELECT CODE -</option>";
			$slcOpt .= $itemCodeOpt;
		$slcOpt .= "</select>";

		$slcOpt2 = "<select id=\"slcErp_kodeBrg_BC_".$id."\" name=\"slcErp_kodeBrgBC[]\" class=\"form-control\" onchange=\"slcSatuanByItemCode($(this).val(),'".$id."','".$dbErpByCmp."');\">";
			$slcOpt2 .= "<option value=\"\">- SELECT CODE -</option>";
			$slcOpt2 .= $itemCodeOpt2;
		$slcOpt2 .= "</select>";

		$slcOptSatuan = "<select id=\"slcSatuanErp_BC_".$id."\" name=\"slcErp_satuan[]\" class=\"form-control\">";
			$slcOptSatuan .= "<option value=\"-\">- SELECT -</option>";
			$slcOptSatuan .= $optSatuan;
		$slcOptSatuan .= "</select>";

		$slcOptSatuan2 = "<select id=\"slcSatuanErp_BC_".$id."\" name=\"slcErp_satuan[]\" class=\"form-control\">";
			$slcOptSatuan2 .= "<option value=\"-\">- SELECT -</option>";
			$slcOptSatuan2 .= $optSatuan2;
		$slcOptSatuan2 .= "</select>";

		$purcAmount = $dataOut['headNya'][0]->qty_bargecharge * $dataOut['headNya'][0]->price_bargecharge;
		$purcAmount2 = $dataOut['headNya'][0]->qty_bargecharge2 * $dataOut['headNya'][0]->price_bargecharge2;

		$qtySatuan = $dataOut['headNya'][0]->qty_bargecharge." ".$dataOut['headNya'][0]->satuan_bargecharge;
		$qtySatuan2 = $dataOut['headNya'][0]->qty_bargecharge2." ".$dataOut['headNya'][0]->satuan_bargecharge2;

		$trNya .= "<tr>";
			$trNya .= "<td align=\"center\" style=\"border:0px;vertical-align:top;\">".$no."</td>";
			$trNya .= "<td align=\"center\" style=\"border:0px;vertical-align:top;\">".$slcOpt."</td>";
			$trNya .= "<td align=\"left\" style=\"border:0px;vertical-align:top;\">".$dataOut['headNya'][0]->detail_bargecharge."</td>";
			$trNya .= "<td align=\"center\" style=\"border:0px;vertical-align:top;\">".$qtySatuan."</td>";
			$trNya .= "<td align=\"center\" style=\"border:0px;vertical-align:top;\">".$slcOptSatuan."</td>";
			$trNya .= "<td align=\"right\" style=\"border:0px;vertical-align:top;\">".number_format($dataOut['headNya'][0]->price_bargecharge,2)."</td>";
			$trNya .= "<td align=\"right\" style=\"border:0px;vertical-align:top;\">".number_format($purcAmount,2)."</td>";
		$trNya .= "</tr>";

		if($dataOut['headNya'][0]->detail_bargecharge2 != "")
		{
			$no ++;
			$trNya .= "<tr>";
				$trNya .= "<td align=\"center\" style=\"border:0px;vertical-align:top;\">".$no."</td>";
				$trNya .= "<td align=\"center\" style=\"border:0px;vertical-align:top;\">".$slcOpt2."</td>";
				$trNya .= "<td align=\"left\" style=\"border:0px;vertical-align:top;\">".$dataOut['headNya'][0]->detail_bargecharge2."</td>";
				$trNya .= "<td align=\"center\" style=\"border:0px;vertical-align:top;\">".$qtySatuan2."</td>";
				$trNya .= "<td align=\"center\" style=\"border:0px;vertical-align:top;\">".$slcOptSatuan2."</td>";
				$trNya .= "<td align=\"right\" style=\"border:0px;vertical-align:top;\">".number_format($dataOut['headNya'][0]->price_bargecharge2,2)."</td>";
				$trNya .= "<td align=\"right\" style=\"border:0px;vertical-align:top;\">".number_format($purcAmount2,2)."</td>";
			$trNya .= "</tr>";
		}

		$subTotal = $purcAmount + $purcAmount2;
		$discountBC = $dataOut['headNya'][0]->discount_bargecharge;

		$ppn = $dataOut['headNya'][0]->ppn_bargecharge;

		$typePpnNya = "";

		if($dataOut['headNya'][0]->type_ppn == "I")
		{
			$afterDisc = $subTotal - $dataOut['headNya'][0]->discount_bargecharge;
			$afterDisc = $afterDisc - $ppn;
			$typePpnNya = " <i style=\"margin-right:10px;color:red;font-size:12px;\">( PPN Inklusif )</i>";
		}
		else if($dataOut['headNya'][0]->type_ppn == "E")
		{
			$afterDisc = $subTotal - $dataOut['headNya'][0]->discount_bargecharge;
			$typePpnNya = " <i style=\"margin-right:10px;color:red;font-size:12px;\">( PPN Eksklusif (+11%) )</i>";
		}
		else if($dataOut['headNya'][0]->type_ppn == "E")
		{
			$afterDisc = $subTotal - $dataOut['headNya'][0]->discount_bargecharge;
			$typePpnNya = " <i style=\"margin-right:10px;color:red;font-size:12px;\">( PPN Eksklusif (+11%) )</i>";
		}
		else{
			$afterDisc = $subTotal - $dataOut['headNya'][0]->discount_bargecharge;
		}

		$grandTotal = $afterDisc + $ppn;

		$trNya .= "<tr>";
			$trNya .= " <td rowspan=\"6\" style=\"border:0px;padding-top:10px;font-size:10px;vertical-align:top;\" align=\"right\"></td>";
			$trNya .= " <td rowspan=\"6\" colspan=\"3\"  style=\"border:0px;padding-top:10px;font-size:10px;vertical-align:top;\"></td>";
			$trNya .= " <td colspan=\"2\" style=\"font-weight:bold;padding-top:10px;border:0px;\" align=\"right\">Sub Total :</td>";
			$trNya .= " <td style=\"font-weight:bold;padding-top:10px;border:0px;\" align=\"right\" id=\"idTtlAmount\">".number_format($subTotal,2)."</td>";
		$trNya .= "</tr>";
		$trNya .= "<tr>";
			$trNya .= "	<td colspan=\"2\" style=\"border:0px;font-weight:bold;\" align=\"right\">Discount :</td>";
			$trNya .= "	<td style=\"border:0px;font-weight:bold;\" align=\"right\">".number_format($discountBC,2)."</td>";
		$trNya .= "</tr>";
		$trNya .= "<tr>";
			$trNya .= " <td colspan=\"2\" style=\"font-weight:bold;border:0px;\" align=\"right\">Total After Discount :</td>";
			$trNya .= " <td style=\"font-weight:bold;border:0px;\" align=\"right\" id=\"idTtlAfterDisc\">".number_format($afterDisc,2)."</td>";
		$trNya .= "</tr>";
		$trNya .= "<tr>";
			$trNya .= " <td colspan=\"2\" style=\"font-weight:bold;border:0px;\" align=\"right\">".$typePpnNya." PPN :</td>";
			$trNya .= " <td style=\"font-weight:bold;border:0px;\" align=\"right\" id=\"idLblPPN\">".number_format($ppn,2)."</td>";
		$trNya .= "</tr>";
		$trNya .= "<tr>";
			$trNya .= " <td colspan=\"2\" style=\"font-weight:bold;border:0px;\" align=\"right\">Grand Total :</td>";
			$trNya .= " <td style=\"font-weight:bold;border:0px;\" align=\"right\" id=\"idGrandTotal\">".number_format($grandTotal,2)."</td>";
		$trNya .= "</tr>";
			
		$dataOut['trNya'] = $trNya;
		$dataOut['idRequest'] = $dataOut['headNya'][0]->id_request;
		$dataOut['idPurch'] = $id;
		$dataOut['poDate'] = $this->convertReturnName($dataOut['headNya'][0]->po_date);
		$dataOut['optSupplier'] = $erpControl->getOptSupplierErp("",$erpControl->cekDbErpByCompany($dataOut['headNya'][0]->ship_company),$dataOut['headNya'][0]->supplier_code);
		$dataOut['tempDBNya'] = $erpControl->cekDbErpByCompany($dataOut['headNya'][0]->ship_company);
		$dataOut['itemCode'] = $dataOut['headNya'][0]->erp_itemcode;

		print json_encode($dataOut);
	}

	function saveBargeCharge()
	{
		$userId = $this->session->userdata('idUserPurchase');
		$dateNow = date("Y-m-d");
		$data = $_POST;
		$idPurch = $data['txtIdPurchaseBC'];
		$stData = "";
		$valData = array();
		$typePpn = "T";

		try {				
				if($data['optPpnBC'] == "tidak")
				{
					$typePpn = "T";
				}
				else if($data['optPpnBC'] == "tidakdipungutpajak")
				{
					$typePpn = "K";
				}
				else if($data['optPpnBC'] == "inklusif")
				{
					$typePpn = "I";
				}
				else if($data['optPpnBC'] == "eksklusif")
				{
					$typePpn = "E";
				}

				$valData['st_bargecharge'] = '1';
				$valData['po_no_bargecharge'] = $data['txtPOBargeCharge'];
				$valData['date_bargecharge'] = $data['txtDate_poBargeCharge'];

				$valData['erp_itemcode'] = $data['itemCode'];
				$valData['erp_itemname'] = $data['itemName'];
				$valData['detail_bargecharge'] = $data['txtDetailBC'];
				$valData['qty_bargecharge'] = $data['txtQtyBC'];
				$valData['satuan_code_bargecharge'] = $data['satuanCode'];
				$valData['satuan_bargecharge'] = $data['satuanName'];
				$valData['currency_bargecharge'] = $data['currBC'];
				$valData['price_bargecharge'] = $data['txtPriceBC'];

				$valData['erp_itemcode2'] = $data['itemCode2'];
				$valData['erp_itemname2'] = $data['itemName2'];
				$valData['detail_bargecharge2'] = $data['txtDetailBC2'];
				$valData['qty_bargecharge2'] = $data['txtQtyBC2'];
				$valData['satuan_code_bargecharge2'] = $data['satuanCode2'];
				$valData['satuan_bargecharge2'] = $data['satuanName2'];
				$valData['currency_bargecharge2'] = $data['currBC2'];
				$valData['price_bargecharge2'] = $data['txtPriceBC2'];

				$valData['discount_bargecharge'] = $data['discountBC'];
				$valData['type_ppn_bargecharge'] = $typePpn;
				$valData['ppn_bargecharge'] = $data['txtTotalPpnBC'];

				$valData['add_user_bargecharge'] = $userId;
				$valData['add_date_bargecharge'] = $dateNow;

				$whereNya = "id = '".$idPurch."'";
				$this->mpurchasing->updateData($whereNya,$valData,"list_purchase");

				$stData = "Submit Success..!!";
		} catch (Exception $e) {
			$stData = "Failed =>".$e;
		}

		print $stData;
	}

	function sendToERPBargeCharge()
	{
		$data = $_POST;
		$userId = $this->session->userdata('idUserPurchase');
		$dateNow = date('Y-m-d');
		$stData = "";
		$uptDataPurch = array();
		$uptDetail = array();		
		$dataOut = array();

		print_r($data);exit;

		$idRequest = $data['txtIdReqSendErp'];
		$idPurchase = $data['txtIdPurchSendErp'];
		$supplierName = $data['supplierName'];
		$sn = explode("<=>", $supplierName);
		$supplierName = $sn[1];
		$supplierCode = $data['supplierCode'];

		try {

			$uptDataPurch['send_erp_bargecharge'] = "1";
			$uptDataPurch['add_user_bargecharge'] = $userId;
			$uptDataPurch['add_date_bargecharge'] = $dateNow;

			$whereNya = "id = '".$idPurchase."'";
			$this->mpurchasing->updateData($whereNya,$uptDataPurch,"list_purchase");

			$stData = $this->insertToErpBargeCharge($idPurchase);
		} catch (Exception $ex) {
			$stData = "Failed => ".$ex->getMessage();
		}

		$dataOut['stData'] = $stData;

		print json_encode($dataOut);
	}

	function exportPurchasingBargeCharge($idPurch = "",$typeData = "")
	{
		$dataOut = array();
		$subTotal = 0;
		$grandTotal = 0;
		$id = $idPurch;
		$trNya = "";
		$no = 1;
		$qrCodeApprv1 = "";
		$qrCodeApprv2 = "";
		$trTTDnya = "";
		$trTTDnya2 = "";
		$trTTDnya3 = "";
		$currNya = "";

		$dataOut['headNya'] = $this->mpurchasing->getData("*","list_purchase","id = '".$id."' AND sts_delete = '0' ");
		$dataReq = $this->mpurchasing->getData("*","request","id = '".$dataOut['headNya'][0]->id_request."' AND sts_delete = '0' ");
		
		$purcAmount = $dataOut['headNya'][0]->qty_bargecharge * $dataOut['headNya'][0]->price_bargecharge;
		$purcAmount2 = $dataOut['headNya'][0]->qty_bargecharge2 * $dataOut['headNya'][0]->price_bargecharge2;

		$trNya .= "<tr>";
			$trNya .= "<td align=\"center\" style=\"border:0px;vertical-align:top;\">".$no."</td>";
			$trNya .= "<td align=\"center\" style=\"border:0px;vertical-align:top;\">".$dataOut['headNya'][0]->erp_itemname."</td>";
			$trNya .= "<td align=\"left\" style=\"border:0px;vertical-align:top;\">".$dataOut['headNya'][0]->detail_bargecharge."</td>";
			$trNya .= "<td align=\"center\" style=\"border:0px;vertical-align:top;\">".$dataOut['headNya'][0]->qty_bargecharge."</td>";
			$trNya .= "<td align=\"right\" style=\"border:0px;vertical-align:top;\">".number_format($dataOut['headNya'][0]->price_bargecharge,0)."</td>";
			$trNya .= "<td align=\"right\" style=\"border:0px;vertical-align:top;\">".number_format($purcAmount,0)."</td>";
		$trNya .= "</tr>";

		if($dataOut['headNya'][0]->detail_bargecharge2 != "")
		{
			$no ++;
			$trNya .= "<tr>";
				$trNya .= "<td align=\"center\" style=\"border:0px;vertical-align:top;\">".$no."</td>";
				$trNya .= "<td align=\"center\" style=\"border:0px;vertical-align:top;\">".$dataOut['headNya'][0]->erp_itemname2."</td>";
				$trNya .= "<td align=\"left\" style=\"border:0px;vertical-align:top;\">".$dataOut['headNya'][0]->detail_bargecharge2."</td>";
				$trNya .= "<td align=\"center\" style=\"border:0px;vertical-align:top;\">".$dataOut['headNya'][0]->qty_bargecharge2."</td>";
				$trNya .= "<td align=\"right\" style=\"border:0px;vertical-align:top;\">".number_format($dataOut['headNya'][0]->price_bargecharge2,0)."</td>";
				$trNya .= "<td align=\"right\" style=\"border:0px;vertical-align:top;\">".number_format($purcAmount2,0)."</td>";
			$trNya .= "</tr>";
		}

		if($purcAmount2 == "")
		{
			$purcAmount2 = 0;
		}

		$subTotal = $purcAmount + $purcAmount2;
		$ppn = $dataOut['headNya'][0]->ppn_bargecharge;
		$typePpnNya = "";

		if($dataOut['headNya'][0]->type_ppn == "I")
		{
			$afterDisc = $subTotal - $dataOut['headNya'][0]->discount_bargecharge;
			$afterDisc = $afterDisc - $ppn;
			$typePpnNya = " <i style=\"margin-right:10px;color:red;font-size:12px;\">( PPN Inklusif )</i>";
		}
		else if($dataOut['headNya'][0]->type_ppn == "E")
		{
			$afterDisc = $subTotal - $dataOut['headNya'][0]->discount_bargecharge;
			$typePpnNya = " <i style=\"margin-right:10px;color:red;font-size:12px;\">( PPN Eksklusif (+11%) )</i>";
		}
		else if($dataOut['headNya'][0]->type_ppn == "E")
		{
			$afterDisc = $subTotal - $dataOut['headNya'][0]->discount_bargecharge;
			$typePpnNya = " <i style=\"margin-right:10px;color:red;font-size:12px;\">( PPN Eksklusif (+11%) )</i>";
		}
		else{
			$afterDisc = $subTotal - $dataOut['headNya'][0]->discount_bargecharge;
		}

		$grandTotal = $afterDisc + $ppn;

		$trNya .= "<tr>";
			$trNya .= " <td colspan=\"5\" style=\"font-weight:bold;padding-top:10px;border:0px;\" align=\"right\">Sub Total :</td>";
			$trNya .= " <td style=\"font-weight:bold;padding-top:10px;border:0px;\" align=\"right\" id=\"idTtlAmount\">".number_format($subTotal,2)."</td>";
		$trNya .= "</tr>";
		$trNya .= "<tr>";
			$trNya .= "	<td colspan=\"5\" style=\"border:0px;font-weight:bold;\" align=\"right\">Discount :</td>";
			$trNya .= "	<td style=\"border:0px;font-weight:bold;\" align=\"right\">".number_format($dataOut['headNya'][0]->discount_bargecharge,2)."</td>";
		$trNya .= "</tr>";
		$trNya .= "<tr>";
			$trNya .= " <td colspan=\"5\" style=\"font-weight:bold;border:0px;\" align=\"right\">Total After Discount :</td>";
			$trNya .= " <td style=\"font-weight:bold;border:0px;\" align=\"right\" id=\"idTtlAfterDisc\">".number_format($afterDisc,2)."</td>";
		$trNya .= "</tr>";
		$trNya .= "<tr>";
			$trNya .= " <td colspan=\"5\" style=\"font-weight:bold;border:0px;\" align=\"right\">".$typePpnNya." PPN :</td>";
			$trNya .= " <td style=\"font-weight:bold;border:0px;\" align=\"right\" id=\"idLblPPN\">".number_format($ppn,2)."</td>";
		$trNya .= "</tr>";
		$trNya .= "<tr>";
			$trNya .= " <td colspan=\"5\" style=\"font-weight:bold;border:0px;\" align=\"right\">".$currNya."Grand Total :</td>";
			$trNya .= " <td style=\"font-weight:bold;border:0px;\" align=\"right\" id=\"idGrandTotal\">".number_format($grandTotal,2)."</td>";
		$trNya .= "</tr>";
			
		$dataOut['trNya'] = $trNya;
		$dataOut['btnBackPage'] = "backPage('".$dataOut['headNya'][0]->id_request."');";
		$dataOut['poDate'] = $this->convertReturnName($dataOut['headNya'][0]->po_date);

		if($typeData == "")
		{
			$this->load->view("purchasing/viewPurchasingBC",$dataOut);
		}else{
			$trTTDnya .= "<tr>";

			if($dataReq[0]->st_check_kadiv == "0")
			{
				if($dataReq[0]->qrcode_approve1 != "")
				{
					$trTTDnya .= "<td style=\"width:200px;\" align=\"center\">";
						$trTTDnya .= "<img src=\"".base_url('/imgQrCode')."/".$dataReq[0]->qrcode_approve1."\" style=\"width:20%;\">";	
					$trTTDnya .= "</td>";
					$trTTDnya2 .= "<td style=\"width:200px;border-bottom:1px solid black;\" align=\"center\"><b>Defandra Putra</b></td>";
					$trTTDnya3 .= "<td style=\"width:200px;\" align=\"center\">";
						$trTTDnya3 .= "<i style=\"font-size:10px;\">".$this->convertReturnNameWithTime($dataReq[0]->date_approve1)."</i>";
					$trTTDnya3 .= "</td>";
				}

				if($dataReq[0]->department == "DECK")
				{
					if($dataReq[0]->qrcode_approve4 != "")
					{
						$trTTDnya .= "<td style=\"width:200px;\" align=\"center\">";
							$trTTDnya .= "<img src=\"".base_url('/imgQrCode')."/".$dataReq[0]->qrcode_approve4."\" style=\"width:20%;\">";	
						$trTTDnya .= "</td>";
						$trTTDnya2 .= "<td style=\"width:200px;border-bottom:1px solid black;\" align=\"center\"><b>Eddy Sukmono</b></td>";
						$trTTDnya3 .= "<td style=\"width:200px;\" align=\"center\">";
							$trTTDnya3 .= "<i style=\"font-size:10px;\">".$this->convertReturnNameWithTime($dataReq[0]->date_approve4)."</i>";
						$trTTDnya3 .= "</td>";
					}
				}else{
					if($dataReq[0]->qrcode_approve3 != "")
					{
						$trTTDnya .= "<td style=\"width:200px;\" align=\"center\">";
							$trTTDnya .= "<img src=\"".base_url('/imgQrCode')."/".$dataReq[0]->qrcode_approve3."\" style=\"width:20%;\">";	
						$trTTDnya .= "</td>";
						$trTTDnya2 .= "<td style=\"width:200px;border-bottom:1px solid black;\" align=\"center\"><b>Hari Joko Purnomo</b></td>";
						$trTTDnya3 .= "<td style=\"width:200px;\" align=\"center\">";
							$trTTDnya3 .= "<i style=\"font-size:10px;\">".$this->convertReturnNameWithTime($dataReq[0]->date_approve3)."</i>";
						$trTTDnya3 .= "</td>";
					}
				}
			}else{
				// if($dataReq[0]->qrcode_approve1 != "")
				// {				
				// 	$trTTDnya .= "<td style=\"width:150px;\" align=\"center\">";
				// 		$trTTDnya .= "<img src=\"".base_url('/imgQrCode')."/".$dataReq[0]->qrcode_approve1."\" style=\"width:15%;\">";	
				// 	$trTTDnya .= "</td>";
				// 	$trTTDnya2 .= "<td style=\"width:150px;border-bottom:1px solid black;\" align=\"center\"><b>Defandra Putra</b></td>";
				// 	$trTTDnya3 .= "<td style=\"width:150px;\" align=\"center\">";
				// 		$trTTDnya3 .= "<i style=\"font-size:10px;\">".$this->convertReturnNameWithTime($dataReq[0]->date_approve1)."</i>";
				// 	$trTTDnya3 .= "</td>";
				// }

				if($dataReq[0]->department == "DECK")
				{
					if($dataReq[0]->qrcode_approve4 != "")
					{				
						$trTTDnya .= "<td style=\"width:150px;\" align=\"center\">";
							$trTTDnya .= "<img src=\"".base_url('/imgQrCode')."/".$dataReq[0]->qrcode_approve4."\" style=\"width:15%;\">";	
						$trTTDnya .= "</td>";
						$trTTDnya2 .= "<td style=\"width:150px;border-bottom:1px solid black;\" align=\"center\"><b>Eddy Sukmono</b></td>";
						$trTTDnya3 .= "<td style=\"width:150px;\" align=\"center\">";
							$trTTDnya3 .= "<i style=\"font-size:10px;\">".$this->convertReturnNameWithTime($dataReq[0]->date_approve4)."</i>";
						$trTTDnya3 .= "</td>";
					}
				}
				// else{
				// 	// if($dataReq[0]->qrcode_approve3 != "")
				// 	// {				
				// 	// 	$trTTDnya .= "<td style=\"width:150px;\" align=\"center\">";
				// 	// 		$trTTDnya .= "<img src=\"".base_url('/imgQrCode')."/".$dataReq[0]->qrcode_approve3."\" style=\"width:15%;\">";	
				// 	// 	$trTTDnya .= "</td>";
				// 	// 	$trTTDnya2 .= "<td style=\"width:150px;border-bottom:1px solid black;\" align=\"center\"><b>Hari Joko Purnomo</b></td>";
				// 	// 	$trTTDnya3 .= "<td style=\"width:150px;\" align=\"center\">";
				// 	// 		$trTTDnya3 .= "<i style=\"font-size:10px;\">".$this->convertReturnNameWithTime($dataReq[0]->date_approve3)."</i>";
				// 	// 	$trTTDnya3 .= "</td>";
				// 	// }
				// }
				if($dataReq[0]->qrcode_approve2 != "")
				{				
					$trTTDnya .= "<td style=\"width:150px;\" align=\"center\">";
						$trTTDnya .= "<img src=\"".base_url('/imgQrCode')."/".$dataReq[0]->qrcode_approve2."\" style=\"width:15%;\">";	
					$trTTDnya .= "</td>";
					$trTTDnya2 .= "<td style=\"width:150px;border-bottom:1px solid black;\" align=\"center\"><b>Pribadi Arijanto</b></td>";
					$trTTDnya3 .= "<td style=\"width:150px;\" align=\"center\">";
						$trTTDnya3 .= "<i style=\"font-size:10px;\">".$this->convertReturnNameWithTime($dataReq[0]->date_approve2)."</i>";
					$trTTDnya3 .= "</td>";
				}

				if($dataReq[0]->department == "ENGINE")
				{
					if($dataReq[0]->qrcode_approve4 != "")
					{				
						$trTTDnya .= "<td style=\"width:150px;\" align=\"center\">";
							$trTTDnya .= "<img src=\"".base_url('/imgQrCode')."/".$dataReq[0]->qrcode_approve4."\" style=\"width:15%;\">";	
						$trTTDnya .= "</td>";
						$trTTDnya2 .= "<td style=\"width:150px;border-bottom:1px solid black;\" align=\"center\"><b>Eddy Sukmono</b></td>";
						$trTTDnya3 .= "<td style=\"width:150px;\" align=\"center\">";
							$trTTDnya3 .= "<i style=\"font-size:10px;\">".$this->convertReturnNameWithTime($dataReq[0]->date_approve4)."</i>";
						$trTTDnya3 .= "</td>";
					}
				}

				if($dataReq[0]->qrcode_approve5 != "")
				{
					$trTTDnya .= "<td style=\"width:150px;\" align=\"center\">";
						$trTTDnya .= "<img src=\"".base_url('/imgQrCode')."/".$dataReq[0]->qrcode_approve5."\" style=\"width:15%;\">";	
					$trTTDnya .= "</td>";
					$trTTDnya2 .= "<td style=\"width:150px;border-bottom:1px solid black;\" align=\"center\"><b>Marita</b></td>";
					$trTTDnya3 .= "<td style=\"width:150px;\" align=\"center\">";
						$trTTDnya3 .= "<i style=\"font-size:10px;\">".$this->convertReturnNameWithTime($dataReq[0]->date_approve5)."</i>";
					$trTTDnya3 .= "</td>";
				}
			}

			$trTTDnya .= "</tr>";
			$trTTDnya .= "<tr>";
				$trTTDnya .= $trTTDnya2;
			$trTTDnya .= "</tr>";
			$trTTDnya .= "<tr>";
				$trTTDnya .= $trTTDnya3;
			$trTTDnya .= "</tr>";

			$dataOut['trTTDnya'] = $trTTDnya;
			
			$this->load->view("purchasing/exportDataBC",$dataOut);
		}		
	}

	function insertToErpBargeCharge($idPurchase)
	{
		$erpControl = new MasErpApi();
		$dataInsErp = array();
		$tempDataDet = array();
		$dataReturn = "";
		$erpDept = "";
		$erpGdng = "";
		$initCmp = "";
		$poNo = "";
		$poDate = "";
		$extraDisc = 0;
		$note = "";
		$rackNo = "";
		$ongkir = 0;
		$typePpn = "";
		$ppnPercen = 0;
		$kdPpn = "";
		$vndQuot = "";

		$idRequest = $data['txtIdReqSendErp'];
		$idPurchase = $data['txtIdPurchSendErp'];
		$supplierName = $data['supplierName'];
		$supplierCode = $data['supplierCode'];
		$txtVesselSendErp = $data['txtVesselSendErp'];
		$txtVslCompanySendErp = $data['txtVslCompanySendErp'];

		try {

			$sql="SELECT B.init,A.* FROM list_purchase A LEFT JOIN mst_company B ON B.name_company = A.ship_company WHERE A.id = '".$idPurchase."'";
			$rsl= $this->mpurchasing->getDataQuery($sql);

			if(count($rsl) > 0)
			{
				$poNo = $rsl[0]->po_no;
				$poDate = $rsl[0]->po_date;
				$note = $rsl[0]->subject;
				$extraDisc = $rsl[0]->discount;
				$initCmp = $rsl[0]->init;
				$ongkir = $rsl[0]->delivery_cost;
				$typePpn = $rsl[0]->type_ppn;
				$vndQuot = $rsl[0]->vendor_quot;
			}

			$sqlDet = "SELECT * FROM request_detail WHERE id_request = '".$idRequest."' AND ((purchase_id = '".$idPurchase."' AND purchase_price > 0) OR (purchase2_id = '".$idPurchase."' AND purchase2_price > 0) OR (purchase3_id = '".$idPurchase."' AND purchase3_price > 0)) AND sts_delete = '0' ";
			$rslDet = $this->mpurchasing->getDataQuery($sqlDet);

			$dbNya = $erpControl->cekDbErpByCompany($txtVslCompanySendErp);
			$dbThn = $erpControl->cekDbThn($dbNya);

			//$dataDept = $this->mpurchasing->querySqlServerErp("SELECT * FROM Warehouses WHERE NamaGudang like '%".$txtVesselSendErp."%'",$dbNya);
			$sql = "SELECT * FROM ".$dbThn."..Warehouses WHERE NamaGudang like '%".$txtVesselSendErp."%'";
			// $rsl = $this->mpurchasing->querySqlServerErp($sql,$dbNya);
			$dataDept = $this->mpurchasing->querySqlServerErp($sql,"sqlSrvErp_and");

			if(count($dataDept) > 0)
			{
				$erpDept = $dataDept[0]->KodeDept;
				$erpGdng = $dataDept[0]->KodeGudang;
			}

			$rackNo = "R".strtoupper($erpGdng);

			if (count($rslDet) > 0)
			{
				for ($lan=0; $lan < count($rslDet) ; $lan++)
				{
					if($rslDet[$lan]->quot_custom1 == "other")
					{
						if($vndQuot == "" OR $vndQuot == "quot1")
						{
							$purcQty = $rslDet[$lan]->purchase_qty;
							$purcPrice = $rslDet[$lan]->purchase_price;
						}
						else if($vndQuot == "quot2")
						{
							$purcQty = $rslDet[$lan]->purchase2_qty;
							$purcPrice = $rslDet[$lan]->purchase2_price;
						}
						else if($vndQuot == "quot3")
						{
							$purcQty = $rslDet[$lan]->purchase3_qty;
							$purcPrice = $rslDet[$lan]->purchase3_price;
						}
					}else{
						$purcQty = $rslDet[$lan]->purchase_qty;
						$purcPrice = $rslDet[$lan]->purchase_price;
					}

					$tempDataDet[$lan]['usePph'] = true;
					$tempDataDet[$lan]['warehouseCode'] = $erpGdng;
					$tempDataDet[$lan]['rack'] = $rackNo;
					$tempDataDet[$lan]['itemCode'] = $rslDet[$lan]->code_item_erp;
					$tempDataDet[$lan]['itemName'] = $rslDet[$lan]->article_name;
					$tempDataDet[$lan]['qty'] = $purcQty;
					$tempDataDet[$lan]['unitType'] = $rslDet[$lan]->satuan_erp;
					$tempDataDet[$lan]['purchasePrice'] = $purcPrice;
					$tempDataDet[$lan]['discount1'] = 0;
					$tempDataDet[$lan]['discountPercent1'] = 0;
					$tempDataDet[$lan]['discount2'] = 0;
					$tempDataDet[$lan]['discountPercent2'] = 0;
					$tempDataDet[$lan]['discount3'] = 0;
					$tempDataDet[$lan]['discountPercent3'] = 0;
					$tempDataDet[$lan]['note'] = "";
				}
			}

			if($typePpn == "")
			{
				$typePpn = "T";
				$kdPpn = "";
				$ppnPercen = 0;
			}else{
				$kdPpn = "PPN11";
				$ppnPercen = 11;
			}

			if(strtoupper($initCmp) == "ADY")
			{
				$initCmp = "adn";
			}

			$rateNya = 1;
			//$dataVendor = $this->mpurchasing->querySqlServerErp("SELECT KodeCrc FROM Vendors WHERE KodeLgn = '".$supplierCode."'",$dbNya);
			$sql = "SELECT KodeCrc FROM ".$dbThn."..Vendors WHERE KodeLgn = '".$supplierCode."'";
			// $rsl = $this->mpurchasing->querySqlServerErp($sql,$dbNya);
			$dataVendor = $this->mpurchasing->querySqlServerErp($sql,"sqlSrvErp_and");

			if(count($dataVendor) > 0)
			{
				if(strtoupper($dataVendor[0]->KodeCrc) != "IDR")
				{
					$sqlReq = "SELECT type_check1 FROM request WHERE id = '".$idRequest."' AND sts_delete = '0' ";
					$rslReq = $this->mpurchasing->getDataQuery($sqlReq);

					if(count($rslReq) > 0)
					{
						$sqlKurs = "SELECT kurs FROM quotation WHERE id_request = '".$idRequest."' AND sts_delete = '0' ORDER BY id ASC ";
						$rslKurs = $this->mpurchasing->getDataQuery($sqlKurs);

						if(count($rslKurs) > 0)
						{
							if($rslReq[0]->type_check1 == "quot1")
							{
								$rateNya = $rslKurs[0]->kurs;
							}
							if($rslReq[0]->type_check1 == "quot2")
							{
								$rateNya = $rslKurs[1]->kurs;
							}
							if($rslReq[0]->type_check1 == "quot3")
							{
								$rateNya = $rslKurs[2]->kurs;
							}
							if($rslReq[0]->type_check1 == "custom")
							{
								$sqlVnd = "SELECT vendor_quot FROM list_purchase WHERE id_request = '".$idRequest."' AND supplier_code = '".$supplierCode."' AND sts_delete = '0' ";
								$rslVnd = $this->mpurchasing->getDataQuery($sqlVnd);

								if(count($rslVnd) > 0)
								{
									if($rslVnd[0]->vendor_quot == "quot1")
									{
										$rateNya = $rslKurs[0]->kurs;
									}
									if($rslVnd[0]->vendor_quot == "quot2")
									{
										$rateNya = $rslKurs[1]->kurs;
									}
									if($rslVnd[0]->vendor_quot == "quot3")
									{
										$rateNya = $rslKurs[2]->kurs;
									}
								}
							}
						}
					}
				}
			}

			$cekKurs = substr($supplierCode, -3);
			if(strstr(strtolower($cekKurs), "usd"))
			{
				if($rateNya <= 1)
				{
					$rateNya = "15000";
				}
			}

			$dataInsErp = array(
								"departmentCode"=>$erpDept,
								"projectCode"=>"",
								"counterCode"=>"",
								"transactionNumber"=>$poNo,
								"transactionDate"=>$poDate,
								"supplierCode"=>$supplierCode,
								"warehouseCode"=>$erpGdng,
								"paymentTermCode"=>"D30",
								"shippingAddress"=>$txtVslCompanySendErp." - ".$txtVesselSendErp,
								"journalCode"=>"1",
								"rate"=>$rateNya,
								"extraDiscount1"=>$extraDisc,
								"extraDiscountPercent1"=>0,
								"extraDiscount2"=>0,
								"extraDiscountPercent2"=>0,
								"ppnType"=>$typePpn,
								"kodePpn"=>$kdPpn,
								"ppnPersen"=> $ppnPercen,
								"taxNumber"=> "",
								"taxAdditionalNote"=> "",
								"ppnDate"=> "",
								"pphCode"=>"",
								"note"=>$note,
								"freightCost"=>$ongkir,
								"purchaseOrderItemDtos"=>$tempDataDet
								);

			$dataReturn = $erpControl->actionERP($initCmp,"save",$dataInsErp);
		} catch (Exception $ex){
			$dataReturn = "Failed => ".$ex->getMessage();
		}

		return $dataReturn;
	}

	function getOptionItemCode()
	{
		$erpControl = new MasErpApi();
		$idPurch = $_POST['txtIdPurc'];
		$dataOut = array();

		$headNya = $this->mpurchasing->getData("*","list_purchase","id = '".$idPurch."' AND sts_delete = '0' ");
		$dbErpByCmp = $erpControl->cekDbErpByCompany($headNya[0]->ship_company);

		$dataOut['optItemCode'] = $erpControl->getOptItemCodeErp("","",$dbErpByCmp);
		$dataOut['dbErpByCmp'] = $dbErpByCmp;
		$dataOut['poHeader'] = $headNya[0]->po_no;

		print json_encode($dataOut);
	}

	function savePO()
	{
		$data = $_POST;
		$idPurch = $data['idPurch'];
		$stData = "";
		$valData = array();
		$typePpn = "T";

		try {
				//$this->addDataMyAppLetter($idPurch);
				$valData['st_data'] = '1';
				$valData['discount'] = $data['disc'];
				$valData['ppn'] = $data['ppn'];
				$valData['note'] = $data['note'];
				$valData['delivery_cost'] = $data['deliveryCost'];

				if($data['optPpn'] == "tidak")
				{
					$typePpn = "T";
				}
				else if($data['optPpn'] == "tidakdipungutpajak")
				{
					$typePpn = "K";
				}
				else if($data['optPpn'] == "inklusif")
				{
					$typePpn = "I";
				}
				else if($data['optPpn'] == "eksklusif")
				{
					$typePpn = "E";
				}

				$valData['type_ppn'] = $typePpn;

				$whereNya = "id = '".$idPurch."'";
				$this->mpurchasing->updateData($whereNya,$valData,"list_purchase");

				//$this->sendRemaindByEmailToFinance($idPurch);

				$stData = "Submit Success..!!";
		} catch (Exception $e) {
			$stData = "Failed =>".$e;
		}
		print json_encode($stData);
	}

	function sendRemaindByEmailToFinance($idListPurch = '')
	{
		$mailNya = "";
		$subjectNya = "";
		$isiEmailNya = "";

		$sql = "SELECT id,ship_name FROM list_purchase WHERE sts_delete = '0' AND id = '".$idListPurch."'";
		$rsl = $this->mpurchasing->getDataQuery($sql);

		$sqlMail = "SELECT * FROM send_mail WHERE sts_delete = '0' AND vessel = 'purchasing'";
		$rslMail = $this->mpurchasing->getDataQuery($sqlMail);

		foreach ($rslMail as $key => $val)
		{
			$mailNya = $val->email_finance;
		}

		if($mailNya != "")
		{
			// $mailNya = "ahmad.maulana@andhika.com";
			$subjectNya = "Create PO Done For Vessel ".$rsl[0]->ship_name;
			$isiEmailNya = $this->getContentSendMail($rsl[0]->id,$rsl[0]->ship_name);			
			// print_r($isiEmailNya);exit;
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

	function getContentSendMail($idListPurch = "",$vessel = "")
	{
		$data = $this->getIsiContent($idListPurch);
		$isiMessage = "";

		$isiMessage .= "<p>";
			$isiMessage .= "*************************************************<br>";
			$isiMessage .= "PLEASE DO NOT REPLY THIS EMAIL..!!<br>";
			$isiMessage .= "*************************************************<br>";
		$isiMessage .= "</p>";

		$isiMessage .= "<b>&nbsp;***** Create PO Done For Vessel ".$vessel." *****</b>";

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

	function getIsiContent($idListPurch = "")
	{
		$dataOut = array();
		$trNya = "";
		$remarknya = "";

		$sql = "SELECT * FROM list_purchase WHERE id = '".$idListPurch."' AND sts_delete = '0' ";
		$rsl = $this->mpurchasing->getDataQuery($sql);

		foreach ($rsl as $key => $val)
		{
			$trNya .= "<tr>";
				$trNya .= "<td style=\"vertical-align:top;width:15%;\">Order To</td>";
				$trNya .= "<td style=\"vertical-align:top;width:35%;color:#000080;\"> ".$val->order_company."<br> <i>( ".$val->order_name." )</i>"."</td>";
				$trNya .= "<td style=\"vertical-align:top;width:15%;\">Ship To</td>";
				$trNya .= "<td style=\"vertical-align:top;width:35%;color:#000080;\"> ".$val->ship_name."<br> <i>( ".$val->ship_company." )</i>"."</td>";
			$trNya .= "</tr>";
			$trNya .= "<tr>";
				$trNya .= "<td style=\"vertical-align: top;width:15%;\">PO Date</td>";
				$trNya .= "<td style=\"vertical-align: top;width:35%;color:#000080;\"> ".$this->convertReturnName($val->po_date)."</td>";
				$trNya .= "<td style=\"vertical-align: top;width:15%;\">PO No</td>";
				$trNya .= "<td style=\"vertical-align: top;width:35%;color:#000080;\"> ".$val->po_no."</td>";
			$trNya .= "</tr>";
			$trNya .= "<tr>";
				$trNya .= "<td style=\"vertical-align:top;\">Subject</td>";
				$trNya .= "<td style=\"vertical-align:top;color:#000080;\" colspan=\"3\"> ".$val->subject."</td>";
			$trNya .= "</tr>";
		}

		$dataOut["trNya"] = $trNya;

		return $dataOut;
	}

	function addDataMyAppLetter($idPurch = '')
	{
		$dateNow = date("Y-m-d");
		$yearNow = date("Y");
		$monthNow = date("m");
		$noSurat = "1";
		$initDivisi = "-";
		$idUsrLogin = $this->session->userdata('idUserPurchase');
		$usrAddLogin = $idUsrLogin."#".date("H:i")."#".date("d/m/Y");

		try {

			$sql = "SELECT A.*,B.init,C.idUser_approve1, C.idUser_approve2
					FROM list_purchase A
					LEFT JOIN mst_company B ON A.ship_company = B.name_company
					LEFT JOIN request C ON C.id = A.id_request
					WHERE A.id = '".$idPurch."' ";
			$rsl = $this->mpurchasing->getDataQuery($sql);

			$cmp = $rsl['0']->ship_company;
			$poDate = $rsl['0']->po_date;
			$poNo = $rsl['0']->po_no;
			$initCmp = $rsl['0']->init;
			
			for ($lan=1; $lan <= 2; $lan++)
			{
				$nmApprv = "-";

				if($lan == '1')
				{
					$sql = "SELECT id,name_full FROM user WHERE sts_delete = '0' AND id = '".$rsl['0']->idUser_approve1."' ";
				}else{
					$sql = "SELECT id,name_full FROM user WHERE sts_delete = '0' AND id = '".$rsl['0']->idUser_approve2."' ";
				}
				$rslNya = $this->mpurchasing->getDataQuery($sql);

				if(count($rslNya) > 0)
				{
					$nmApprv = $rslNya[0]->name_full;
				
					$sqlSrv = "	SELECT nosurat FROM tblEmpNoSurat
								WHERE cmpcode = '".$initCmp."' AND YEAR(tglsurat) = '".$yearNow."'
								ORDER BY nosurat DESC LIMIT 0,1 ";
					$rslSrv = $this->mpurchasing->getDataQueryMyApps($sqlSrv);

					if(count($rslSrv) > 0)
					{
						$ns = explode("/", $rslSrv[0]->nosurat);
						$noSurat = $ns[0]+1;
					}

					$sqlSrv2 = " SELECT A.empno,A.nama,
									CASE 
										WHEN B.kddiv IS NULL THEN '-'
										WHEN B.kddiv = '080' THEN '030'
										WHEN B.kddiv = '' AND A.nama = 'EDDY SUKMONO' THEN '030'
										ELSE B.kddiv
									END AS kddivnya
								FROM tblmstemp A
								LEFT JOIN tblempgen B ON B.empno = A.empno 
								WHERE A.nama = '".$nmApprv."' AND A.stsresign = '0' AND A.deletests = '0'
								AND B.eftivedt = (SELECT MAX(eftivedt) FROM tblempgen WHERE empno= A.empno AND deletests=0)";

					$rslSrv2 = $this->mpurchasing->querySqlServer($sqlSrv2);

					if(count($rslSrv2) > 0)
					{
						$sql = "SELECT divcode FROM tblmstdivnsrt WHERE deletests = '0' AND kddiv = '".$rslSrv2[0]->kddivnya."' ";
						$rslNya = $this->mpurchasing->getDataQuery($sql);

						if(count($rslNya) > 0)
						{
							$initDivisi = $rslNya[0]->divcode;
						}
					}
				}

				$formatNoSrt = $this->createNo($noSurat)."/".$initCmp."/".$initDivisi."/".$monthNow.substr($yearNow, 2,2);
				$batchno = $this->getBatchNo();

				$insSqlSrv = array();

				$insSqlSrv["batchno"] = $batchno;
				$insSqlSrv["cmpcode"] = $initCmp;
				$insSqlSrv["nosurat"] = $formatNoSrt;
				$insSqlSrv["issueddiv"] = $initDivisi;
				$insSqlSrv["signedby"] = $initDivisi;
				$insSqlSrv["address"] = "Ship Management";				
				$insSqlSrv["tglsurat"] = $dateNow;
				$insSqlSrv["ket"] = $poNo." / ".$nmApprv;
				$insSqlSrv["copydoc"] = "0";
				$insSqlSrv["canceldoc"] = "0";
				$insSqlSrv["createdby"] = "Purch. System";
				$insSqlSrv["addusrdt"] = $usrAddLogin;

				$this->mpurchasing->insDataMyApps($insSqlSrv,"tblEmpNoSurat");
				//$this->mpurchasing->insDataMyAppsDahlia($insSqlSrv,"tblEmpNoSurat");

				$imgName = $this->createQRCode($batchno);

				$dataUpd = array();

				if($lan == '1')
				{
					$dataUpd['qrcode_approve1'] = $imgName;
				}else{
					$dataUpd['qrcode_approve2'] = $imgName;
				}

				$whereNya = "id = '".$idPurch."'";
				$this->mpurchasing->updateData($whereNya,$dataUpd,"list_purchase");
			}

		} catch (Exception $e) {
			
		}
	}

	function createQRCodeOri($batchNo = "")
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
		$config['white']		= array(70,130,180);
		$this->ciqrcode->initialize($config);

		$imgName = base64_encode($batchNo).'.png';

		$params['data'] = "http://apps.andhika.com/observasi/myLetter/viewLetter/".base64_encode($batchNo); //data yang akan di jadikan QR CODE
		$params['level'] = 'H'; //H=High
		$params['size'] = 5;
		$params['savename'] = FCPATH.$config['imagedir'].$imgName; //simpan image QR CODE ke folder assets/images/

		$this->ciqrcode->generate($params); // fungsi untuk generate QR CODE

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

	function submitPurchasing()
	{
		$data = $_POST;
		$idReq = $data['idReq'];
		$stData = "";
		$valData = array();

		try {
				$valData['submit_purchasing'] = '1';

				$whereNya = "id = '".$idReq."'";
				$this->mpurchasing->updateData($whereNya,$valData,"request");
				$stData = "Submit Success..!!";
		} catch (Exception $e) {
			$stData = "Failed =>".$e;
		}
		print json_encode($stData);
	}

	function viewPurchasing()
	{
		$data = $_POST;
		$grandTotal = 0;
		$id = $data['id'];
		$trNya = "";
		$no = 1;

		$dataOut['headNya'] = $this->mpurchasing->getData("*","list_purchase","id = '".$id."' AND sts_delete = '0' ");
		$valDetail = $this->mpurchasing->getData("*","request_detail","(purchase_id = '".$id."' OR purchase2_id = '".$id."' OR purchase3_id = '".$id."') AND sts_delete = '0' ");
		
		foreach ($valDetail as $key => $val)
		{
			// $slcIdr = "";
			// $linkPO = "";

			// if($val->purchase_curr == "idr"){ $slcIdr = "Rp."; }

			if($val->quot_custom1 == "other")
			{
				if($dataOut['headNya'][0]->vendor_quot == "" OR $dataOut['headNya'][0]->vendor_quot == "quot1")
				{
					$purcQty = $val->purchase_qty." ".$val->unit;
					$purcPrice = number_format($val->purchase_price,2);
					$purcAmount = number_format($val->purchase_amount,2);

					$grandTotal = $grandTotal + $val->purchase_amount;
				}
				else if($dataOut['headNya'][0]->vendor_quot == "quot2")
				{
					$purcQty = $val->purchase2_qty." ".$val->unit;
					$purcPrice = number_format($val->purchase2_price,2);
					$purcAmount = number_format($val->purchase2_amount,2);

					$grandTotal = $grandTotal + $val->purchase2_amount;
				}
				else if($dataOut['headNya'][0]->vendor_quot == "quot3")
				{
					$purcQty = $val->purchase3_qty." ".$val->unit;
					$purcPrice = number_format($val->purchase3_price,2);
					$purcAmount = number_format($val->purchase3_amount,2);

					$grandTotal = $grandTotal + $val->purchase3_amount;
				}
			}else{
				$purcQty = $val->purchase_qty." ".$val->unit;
				$purcPrice = number_format($val->purchase_price,2);
				$purcAmount = number_format($val->purchase_amount,2);

				$grandTotal = $grandTotal + $val->purchase_amount;
			}
				
			$trNya .= "<tr>";
				$trNya .= "<td align=\"center\">".$no."</td>";
				$trNya .= "<td align=\"center\">".$val->code_no."</td>";
				$trNya .= "<td align=\"left\">".$val->article_name."</td>";				
				$trNya .= "<td align=\"center\">".$val->request." ".$val->unit."</td>";
				$trNya .= "<td align=\"center\">".$val->approved_order." ".$val->unit."</td>";
				$trNya .= "<td align=\"center\">".$purcQty."</td>";
				$trNya .= "<td align=\"right\">".$purcPrice."</td>";
				$trNya .= "<td align=\"right\">".$purcAmount."</td>";
			$trNya .= "</tr>";
			
			$no++;
		}
			$trNya .= "<tr>";
				$trNya .= "<td colspan=\"7\" align=\"right\">Sub Total :</td>";
				$trNya .= "<td align=\"right\" id=\"idTtlAmount\">
							".number_format($grandTotal,2)."
							<input type=\"hidden\" id=\"txtSubTotal\" value=\"".$grandTotal."\">
							</td>";
			$trNya .= "</tr>";
			$trNya .= "<tr>";
				$trNya .= "	<td colspan=\"6\" align=\"right\">Discount :</td>";
				$trNya .= "	<td>
								<select class=\"form-control input-sm\" id=\"slcTypeDisc\" onchange=\"slcTypeDisc();\">
									<option value=\"angka\">Angka</option>
									<option value=\"persen\">%</option>
								</select>
								<input style=\"text-align:right;display:none;\" oninput=\"hitungDisc();\" type=\"text\" class=\"form-control input-sm\" id=\"txtInputDisc\" value=\"\" placeholder=\"%\">
							</td>";
				$trNya .= "	<td>
							<input style=\"text-align:right;\" oninput=\"hitungPPN();\" type=\"text\" class=\"form-control input-sm\" id=\"txtDiscount\" value=\"0\">
							</td>";
			$trNya .= "</tr>";
			$trNya .= "<tr>";
				$trNya .= "<td colspan=\"7\" align=\"right\">Total After Discount :</td>";
				$trNya .= "<td align=\"right\" id=\"idTtlAfterDisc\">".number_format($grandTotal,2)."</td>";
			$trNya .= "</tr>";
			
			$trNya .= "<tr>";
				$trNya .= "<td colspan=\"7\" align=\"right\" id=\"listPpn\">";
    				$trNya .= "<label class=\"radio-inline\">";
    					$trNya .= "<input type=\"radio\" name=\"optPpn[]\" onchange=\"hitungPPN();\" value=\"tidak\" checked>Tidak Ada PPN";
    				$trNya .= "</label>";
    				$trNya .= "<label class=\"radio-inline\">";
    					$trNya .= "<input type=\"radio\" name=\"optPpn[]\" onchange=\"hitungPPN();\" value=\"tidakdipungutpajak\">PPN Tidak di pungut Pajak";
    				$trNya .= "</label>";
    				$trNya .= "<label class=\"radio-inline\">";
    					$trNya .= "<input type=\"radio\" name=\"optPpn[]\" onchange=\"hitungPPN();\" value=\"inklusif\">PPN Inklusif";
    				$trNya .= "</label>";
    				$trNya .= "<label class=\"radio-inline\" style=\"margin-right:15px;\">";
    					$trNya .= "<input type=\"radio\" name=\"optPpn[]\" onchange=\"hitungPPN();\" value=\"eksklusif\">PPN Eksklusif <i style=\"font-size:11px;color:red;\">(+11%)</i>";
    				$trNya .= "</label>";
				$trNya .= "</td>";
				$trNya .= "<td align=\"right\">
								<input style=\"text-align:right;\" oninput=\"grandTotalPurchasing();\" type=\"text\" class=\"form-control input-sm\" id=\"idLblPPN\" value=\"0\">
							</td>";
			$trNya .= "</tr>";
			$trNya .= "<tr>";
				$trNya .= "	<td colspan=\"7\" align=\"right\">Delivery Cost :</td>";
				$trNya .= "	<td>
							<input style=\"text-align:right;\" oninput=\"grandTotalPurchasing();\" type=\"text\" class=\"form-control input-sm\" id=\"txtDelivery\" value=\"0\">
							</td>";
			$trNya .= "</tr>";
			$trNya .= "<tr>";
				$trNya .= "<td colspan=\"7\" align=\"right\">Grand Total :</td>";
				$trNya .= "<td align=\"right\" id=\"idGrandTotal\">".number_format($grandTotal,2)."</td>";
			$trNya .= "</tr>";
			$trNya .= "<tr>";
				$trNya .= "<td align=\"right\">Note :</td>";
				$trNya .= "<td colspan=\"7\" align=\"right\">
								<textarea class=\"form-control input-sm\" id=\"txtNote\"></textarea>
							</td>";
			$trNya .= "</tr>";
		$dataOut['trNya'] = $trNya;
		$dataOut['poDate'] = $this->convertReturnName($dataOut['headNya'][0]->po_date);

		print json_encode($dataOut);
	}

	function exportPurchasing($idPurch = "",$typeData = "")
	{
		$dataOut = array();
		$subTotal = 0;
		$grandTotal = 0;
		$id = $idPurch;
		$trNya = "";
		$no = 1;
		$qrCodeApprv1 = "";
		$qrCodeApprv2 = "";
		$trTTDnya = "";
		$trTTDnya2 = "";
		$trTTDnya3 = "";
		$currNya = "";

		$dataOut['headNya'] = $this->mpurchasing->getData("*","list_purchase","id = '".$id."' AND sts_delete = '0' ");
		$dataReq = $this->mpurchasing->getData("*","request","id = '".$dataOut['headNya'][0]->id_request."' AND sts_delete = '0' ");
		$valDetail = $this->mpurchasing->getData("*","request_detail","(purchase_id = '".$id."' OR purchase2_id = '".$id."' OR purchase3_id = '".$id."') AND sts_delete = '0' ","purchase_id ASC");

		foreach ($valDetail as $key => $val)
		{
			$slcIdr = "";
			$linkPO = "";

			if($val->purchase_curr == "idr")
			{
				$slcIdr = "Rp.";
			}else{
				$currNya = "(".strtoupper($val->purchase_curr).") ";
			}

			if($val->quot_custom1 == "other")
			{
				if($dataOut['headNya'][0]->vendor_quot == "" OR $dataOut['headNya'][0]->vendor_quot == "quot1")
				{
					$purcQty = $val->purchase_qty." ".$val->unit;
					$purcPrice = number_format($val->purchase_price,2);
					$purcAmount = number_format($val->purchase_amount,2);

					$subTotal = $subTotal + $val->purchase_amount;
				}
				else if($dataOut['headNya'][0]->vendor_quot == "quot2")
				{
					$purcQty = $val->purchase2_qty." ".$val->unit;
					$purcPrice = number_format($val->purchase2_price,2);
					$purcAmount = number_format($val->purchase2_amount,2);

					$subTotal = $subTotal + $val->purchase2_amount;
				}
				else if($dataOut['headNya'][0]->vendor_quot == "quot3")
				{
					$purcQty = $val->purchase3_qty." ".$val->unit;
					$purcPrice = number_format($val->purchase3_price,2);
					$purcAmount = number_format($val->purchase3_amount,2);

					$subTotal = $subTotal + $val->purchase3_amount;
				}
			}else{
				$purcQty = $val->purchase_qty." ".$val->unit;
				$purcPrice = number_format($val->purchase_price,2);
				$purcAmount = number_format($val->purchase_amount,2);

				$subTotal = $subTotal + $val->purchase_amount;
			}
			
			$trNya .= "<tr>";
				$trNya .= "<td align=\"center\" style=\"border:0px;vertical-align:top;\">".$no."</td>";
				$trNya .= "<td align=\"center\" style=\"border:0px;vertical-align:top;\">".$val->code_no."</td>";
				$trNya .= "<td align=\"left\" style=\"border:0px;vertical-align:top;\">".$val->article_name."</td>";
				$trNya .= "<td align=\"center\" style=\"border:0px;vertical-align:top;\">".$purcQty."</td>";
				$trNya .= "<td align=\"right\" style=\"border:0px;vertical-align:top;\">".$purcPrice."</td>";
				$trNya .= "<td align=\"right\" style=\"border:0px;vertical-align:top;\">".$purcAmount."</td>";
			$trNya .= "</tr>";
			//$subTotal = $subTotal + $val->purchase_amount;
			$no++;
		}

		$ppn = $dataOut['headNya'][0]->ppn;
		$typePpnNya = "";

		if($dataOut['headNya'][0]->type_ppn == "I")
		{
			$afterDisc = $subTotal - $dataOut['headNya'][0]->discount;
			$afterDisc = $afterDisc - $ppn;
			$typePpnNya = " <i style=\"margin-right:10px;color:red;font-size:12px;\">( PPN Inklusif )</i>";
		}
		else if($dataOut['headNya'][0]->type_ppn == "E")
		{
			$afterDisc = $subTotal - $dataOut['headNya'][0]->discount;
			$typePpnNya = " <i style=\"margin-right:10px;color:red;font-size:12px;\">( PPN Eksklusif (+11%) )</i>";
		}
		else if($dataOut['headNya'][0]->type_ppn == "E")
		{
			$afterDisc = $subTotal - $dataOut['headNya'][0]->discount;
			$typePpnNya = " <i style=\"margin-right:10px;color:red;font-size:12px;\">( PPN Eksklusif (+11%) )</i>";
		}
		else{
			$afterDisc = $subTotal - $dataOut['headNya'][0]->discount;
		}

		$grandTotal = $afterDisc + $ppn + $dataOut['headNya'][0]->delivery_cost;

			$trNya .= "<tr>";
				$trNya .= " <td rowspan=\"6\" style=\"border:0px;padding-top:10px;font-size:10px;vertical-align:top;\" align=\"right\">Note :</td>";
				$trNya .= " <td rowspan=\"6\" colspan=\"2\"  style=\"border:0px;padding-top:10px;font-size:10px;vertical-align:top;\"><i>".$dataOut['headNya'][0]->note."</i></td>";

				$trNya .= " <td colspan=\"2\" style=\"font-weight:bold;padding-top:10px;border:0px;\" align=\"right\">Sub Total :</td>";
				$trNya .= " <td style=\"font-weight:bold;padding-top:10px;border:0px;\" align=\"right\" id=\"idTtlAmount\">".number_format($subTotal,2)."</td>";
			$trNya .= "</tr>";
			$trNya .= "<tr>";
				$trNya .= "	<td colspan=\"2\" style=\"border:0px;font-weight:bold;\" align=\"right\">Discount :</td>";
				$trNya .= "	<td style=\"border:0px;font-weight:bold;\" align=\"right\">".number_format($dataOut['headNya'][0]->discount,2)."</td>";
			$trNya .= "</tr>";
			$trNya .= "<tr>";
				$trNya .= " <td colspan=\"2\" style=\"font-weight:bold;border:0px;\" align=\"right\">Total After Discount :</td>";
				$trNya .= " <td style=\"font-weight:bold;border:0px;\" align=\"right\" id=\"idTtlAfterDisc\">".number_format($afterDisc,2)."</td>";
			$trNya .= "</tr>";
			$trNya .= "<tr>";
				$trNya .= " <td colspan=\"2\" style=\"font-weight:bold;border:0px;\" align=\"right\">".$typePpnNya." PPN :</td>";
				$trNya .= " <td style=\"font-weight:bold;border:0px;\" align=\"right\" id=\"idLblPPN\">".number_format($ppn,2)."</td>";
			$trNya .= "</tr>";
			$trNya .= "<tr>";
				$trNya .= "	<td colspan=\"2\" style=\"font-weight:bold;border:0px;\" align=\"right\">Delivery Cost :</td>";
				$trNya .= "	<td style=\"font-weight:bold;border:0px;\" align=\"right\">".number_format($dataOut['headNya'][0]->delivery_cost,2)."</td>";
			$trNya .= "</tr>";
			$trNya .= "<tr>";
				$trNya .= " <td colspan=\"2\" style=\"font-weight:bold;border:0px;\" align=\"right\">".$currNya."Grand Total :</td>";
				$trNya .= " <td style=\"font-weight:bold;border:0px;\" align=\"right\" id=\"idGrandTotal\">".number_format($grandTotal,2)."</td>";
			$trNya .= "</tr>";
			
		$dataOut['trNya'] = $trNya;
		$dataOut['btnBackPage'] = "backPage('".$dataOut['headNya'][0]->id_request."');";
		$dataOut['poDate'] = $this->convertReturnName($dataOut['headNya'][0]->po_date);

		if($typeData == "")
		{
			$this->load->view("purchasing/viewPurchasing",$dataOut);
		}else{
			$trTTDnya .= "<tr>";

			if($dataReq[0]->st_check_kadiv == "0")
			{
				if($dataReq[0]->qrcode_approve1 != "")
				{
					$trTTDnya .= "<td style=\"width:200px;\" align=\"center\">";
						$trTTDnya .= "<img src=\"".base_url('/imgQrCode')."/".$dataReq[0]->qrcode_approve1."\" style=\"width:20%;\">";	
					$trTTDnya .= "</td>";
					$trTTDnya2 .= "<td style=\"width:200px;border-bottom:1px solid black;\" align=\"center\"><b>Defandra Putra</b></td>";
					$trTTDnya3 .= "<td style=\"width:200px;\" align=\"center\">";
						$trTTDnya3 .= "<i style=\"font-size:10px;\">".$this->convertReturnNameWithTime($dataReq[0]->date_approve1)."</i>";
					$trTTDnya3 .= "</td>";
				}

				if($dataReq[0]->department == "DECK")
				{
					if($dataReq[0]->qrcode_approve4 != "")
					{
						$trTTDnya .= "<td style=\"width:200px;\" align=\"center\">";
							$trTTDnya .= "<img src=\"".base_url('/imgQrCode')."/".$dataReq[0]->qrcode_approve4."\" style=\"width:20%;\">";	
						$trTTDnya .= "</td>";
						$trTTDnya2 .= "<td style=\"width:200px;border-bottom:1px solid black;\" align=\"center\"><b>Eddy Sukmono</b></td>";
						$trTTDnya3 .= "<td style=\"width:200px;\" align=\"center\">";
							$trTTDnya3 .= "<i style=\"font-size:10px;\">".$this->convertReturnNameWithTime($dataReq[0]->date_approve4)."</i>";
						$trTTDnya3 .= "</td>";
					}
				}else{
					if($dataReq[0]->qrcode_approve3 != "")
					{
						$trTTDnya .= "<td style=\"width:200px;\" align=\"center\">";
							$trTTDnya .= "<img src=\"".base_url('/imgQrCode')."/".$dataReq[0]->qrcode_approve3."\" style=\"width:20%;\">";	
						$trTTDnya .= "</td>";
						$trTTDnya2 .= "<td style=\"width:200px;border-bottom:1px solid black;\" align=\"center\"><b>Hari Joko Purnomo</b></td>";
						$trTTDnya3 .= "<td style=\"width:200px;\" align=\"center\">";
							$trTTDnya3 .= "<i style=\"font-size:10px;\">".$this->convertReturnNameWithTime($dataReq[0]->date_approve3)."</i>";
						$trTTDnya3 .= "</td>";
					}
				}
			}else{
				// if($dataReq[0]->qrcode_approve1 != "")
				// {				
				// 	$trTTDnya .= "<td style=\"width:150px;\" align=\"center\">";
				// 		$trTTDnya .= "<img src=\"".base_url('/imgQrCode')."/".$dataReq[0]->qrcode_approve1."\" style=\"width:15%;\">";	
				// 	$trTTDnya .= "</td>";
				// 	$trTTDnya2 .= "<td style=\"width:150px;border-bottom:1px solid black;\" align=\"center\"><b>Defandra Putra</b></td>";
				// 	$trTTDnya3 .= "<td style=\"width:150px;\" align=\"center\">";
				// 		$trTTDnya3 .= "<i style=\"font-size:10px;\">".$this->convertReturnNameWithTime($dataReq[0]->date_approve1)."</i>";
				// 	$trTTDnya3 .= "</td>";
				// }

				if($dataReq[0]->department == "DECK")
				{
					if($dataReq[0]->qrcode_approve4 != "")
					{				
						$trTTDnya .= "<td style=\"width:150px;\" align=\"center\">";
							$trTTDnya .= "<img src=\"".base_url('/imgQrCode')."/".$dataReq[0]->qrcode_approve4."\" style=\"width:15%;\">";	
						$trTTDnya .= "</td>";
						$trTTDnya2 .= "<td style=\"width:150px;border-bottom:1px solid black;\" align=\"center\"><b>Eddy Sukmono</b></td>";
						$trTTDnya3 .= "<td style=\"width:150px;\" align=\"center\">";
							$trTTDnya3 .= "<i style=\"font-size:10px;\">".$this->convertReturnNameWithTime($dataReq[0]->date_approve4)."</i>";
						$trTTDnya3 .= "</td>";
					}
				}
				// else
				// {
				// 	if($dataReq[0]->qrcode_approve3 != "")
				// 	{				
				// 		$trTTDnya .= "<td style=\"width:150px;\" align=\"center\">";
				// 			$trTTDnya .= "<img src=\"".base_url('/imgQrCode')."/".$dataReq[0]->qrcode_approve3."\" style=\"width:15%;\">";	
				// 		$trTTDnya .= "</td>";
				// 		$trTTDnya2 .= "<td style=\"width:150px;border-bottom:1px solid black;\" align=\"center\"><b>Hari Joko Purnomo</b></td>";
				// 		$trTTDnya3 .= "<td style=\"width:150px;\" align=\"center\">";
				// 			$trTTDnya3 .= "<i style=\"font-size:10px;\">".$this->convertReturnNameWithTime($dataReq[0]->date_approve3)."</i>";
				// 		$trTTDnya3 .= "</td>";
				// 	}
				// }
				if($dataReq[0]->qrcode_approve2 != "")
				{				
					$trTTDnya .= "<td style=\"width:150px;\" align=\"center\">";
						$trTTDnya .= "<img src=\"".base_url('/imgQrCode')."/".$dataReq[0]->qrcode_approve2."\" style=\"width:15%;\">";	
					$trTTDnya .= "</td>";
					$trTTDnya2 .= "<td style=\"width:150px;border-bottom:1px solid black;\" align=\"center\"><b>Pribadi Arijanto</b></td>";
					$trTTDnya3 .= "<td style=\"width:150px;\" align=\"center\">";
						$trTTDnya3 .= "<i style=\"font-size:10px;\">".$this->convertReturnNameWithTime($dataReq[0]->date_approve2)."</i>";
					$trTTDnya3 .= "</td>";
				}

				if($dataReq[0]->department == "ENGINE")
				{
					if($dataReq[0]->qrcode_approve4 != "")
					{				
						$trTTDnya .= "<td style=\"width:150px;\" align=\"center\">";
							$trTTDnya .= "<img src=\"".base_url('/imgQrCode')."/".$dataReq[0]->qrcode_approve4."\" style=\"width:15%;\">";	
						$trTTDnya .= "</td>";
						$trTTDnya2 .= "<td style=\"width:150px;border-bottom:1px solid black;\" align=\"center\"><b>Eddy Sukmono</b></td>";
						$trTTDnya3 .= "<td style=\"width:150px;\" align=\"center\">";
							$trTTDnya3 .= "<i style=\"font-size:10px;\">".$this->convertReturnNameWithTime($dataReq[0]->date_approve4)."</i>";
						$trTTDnya3 .= "</td>";
					}
				}

				if($dataReq[0]->qrcode_approve5 != "")
				{
					$trTTDnya .= "<td style=\"width:150px;\" align=\"center\">";
						$trTTDnya .= "<img src=\"".base_url('/imgQrCode')."/".$dataReq[0]->qrcode_approve5."\" style=\"width:15%;\">";	
					$trTTDnya .= "</td>";
					$trTTDnya2 .= "<td style=\"width:150px;border-bottom:1px solid black;\" align=\"center\"><b>Marita</b></td>";
					$trTTDnya3 .= "<td style=\"width:150px;\" align=\"center\">";
						$trTTDnya3 .= "<i style=\"font-size:10px;\">".$this->convertReturnNameWithTime($dataReq[0]->date_approve5)."</i>";
					$trTTDnya3 .= "</td>";
				}
			}
			$trTTDnya .= "</tr>";
			$trTTDnya .= "<tr>";
				$trTTDnya .= $trTTDnya2;
			$trTTDnya .= "</tr>";
			$trTTDnya .= "<tr>";
				$trTTDnya .= $trTTDnya3;
			$trTTDnya .= "</tr>";

			$dataOut['trTTDnya'] = $trTTDnya;
			
			$this->load->view("purchasing/exportData",$dataOut);
		}		
	}

	function getDataToErp()
	{
		$erpControl = new MasErpApi();
		$dataOut = array();
		$subTotal = 0;
		$grandTotal = 0;
		$id = $_POST['id'];
		$trNya = "";
		$no = 1;
		$qrCodeApprv1 = "";
		$qrCodeApprv2 = "";
		$trTTDnya = "";
		$trTTDnya2 = "";
		$trTTDnya3 = "";
		$companyNya = "";

		$dataOut['headNya'] = $this->mpurchasing->getData("*","list_purchase","id = '".$id."' AND sts_delete = '0' ");
		$dbErpByCmp = $erpControl->cekDbErpByCompany($dataOut['headNya'][0]->ship_company);
		$valDetail = $this->mpurchasing->getData("*","request_detail","(purchase_id = '".$id."' OR purchase2_id = '".$id."' OR purchase3_id = '".$id."') AND sts_delete = '0' ","purchase_id ASC");
		$itemCodeOpt = $erpControl->getOptItemCodeErp("","",$dbErpByCmp);

		foreach ($valDetail as $key => $val)
		{
			$slcOpt = "<select id=\"slcErp_kodeBrg_".$val->id."\" name=\"slcErp_kodeBrg[]\" class=\"form-control\" onchange=\"slcSatuanByItemCode($(this).val(),'".$val->id."','".$dbErpByCmp."');\">";
				$slcOpt .= "<option value=\"\">- SELECT CODE -</option>";
				$slcOpt .= $itemCodeOpt;
			$slcOpt .= "</select>";

			$slcOptSatuan = "<select id=\"slcSatuanErp_".$val->id."\" name=\"slcErp_satuan[]\" class=\"form-control\">";
				$slcOptSatuan .= "<option value=\"-\">- SELECT -</option>";
			$slcOptSatuan .= "</select>";

			$inputReqDetId = "<input type=\"hidden\" id=\"txtIdReqDetail_".$val->id."\" name=\"txtIdReqDetail[]\" value=\"".$val->id."\">";

			if($val->quot_custom1 == "other")
			{
				if($dataOut['headNya'][0]->vendor_quot == "" OR $dataOut['headNya'][0]->vendor_quot == "quot1")
				{
					$purcQty = $val->purchase_qty." ".strtoupper($val->unit);
					$purcPrice = number_format($val->purchase_price,2);
					$purcAmount = number_format($val->purchase_amount,2);

					$subTotal = $subTotal + $val->purchase_amount;
				}
				else if($dataOut['headNya'][0]->vendor_quot == "quot2")
				{
					$purcQty = $val->purchase2_qty." ".strtoupper($val->unit);
					$purcPrice = number_format($val->purchase2_price,2);
					$purcAmount = number_format($val->purchase2_amount,2);

					$subTotal = $subTotal + $val->purchase2_amount;
				}
				else if($dataOut['headNya'][0]->vendor_quot == "quot3")
				{
					$purcQty = $val->purchase3_qty." ".strtoupper($val->unit);
					$purcPrice = number_format($val->purchase3_price,2);
					$purcAmount = number_format($val->purchase3_amount,2);

					$subTotal = $subTotal + $val->purchase3_amount;
				}
			}else{
				$purcQty = $val->purchase_qty." ".strtoupper($val->unit);
				$purcPrice = number_format($val->purchase_price,2);
				$purcAmount = number_format($val->purchase_amount,2);

				$subTotal = $subTotal + $val->purchase_amount;
			}

			$trNya .= "<tr>";
				$trNya .= "<td align=\"center\" style=\"border:0px;vertical-align:top;\">".$no."</td>";
				$trNya .= "<td align=\"center\" style=\"border:0px;vertical-align:top;\">".$inputReqDetId.$slcOpt."</td>";
				$trNya .= "<td align=\"left\" style=\"border:0px;vertical-align:top;\">".$val->article_name."</td>";
				$trNya .= "<td align=\"center\" style=\"border:0px;vertical-align:top;\">".$purcQty."</td>";
				$trNya .= "<td align=\"center\" style=\"border:0px;vertical-align:top;\">".$slcOptSatuan."</td>";
				$trNya .= "<td align=\"right\" style=\"border:0px;vertical-align:top;\">".$purcPrice."</td>";
				$trNya .= "<td align=\"right\" style=\"border:0px;vertical-align:top;\">".$purcAmount."</td>";
			$trNya .= "</tr>";
			// $subTotal = $subTotal + $val->purchase_amount;
			$no++;
		}

		$afterDisc = $subTotal - $dataOut['headNya'][0]->discount;
		$ppn = $dataOut['headNya'][0]->ppn;
		$grandTotal = $afterDisc + $ppn + $dataOut['headNya'][0]->delivery_cost;

			$trNya .= "<tr>";
				$trNya .= " <td rowspan=\"6\" style=\"border:0px;padding-top:10px;font-size:10px;vertical-align:top;\" align=\"right\">Note :</td>";
				$trNya .= " <td rowspan=\"6\" colspan=\"3\"  style=\"border:0px;padding-top:10px;font-size:10px;vertical-align:top;\"><i>".$dataOut['headNya'][0]->note."</i></td>";

				$trNya .= " <td colspan=\"2\" style=\"font-weight:bold;padding-top:10px;border:0px;\" align=\"right\">Sub Total :</td>";
				$trNya .= " <td style=\"font-weight:bold;padding-top:10px;border:0px;\" align=\"right\" id=\"idTtlAmount\">".number_format($subTotal,0)."</td>";
			$trNya .= "</tr>";
			$trNya .= "<tr>";
				$trNya .= "	<td colspan=\"2\" style=\"border:0px;font-weight:bold;\" align=\"right\">Discount :</td>";
				$trNya .= "	<td style=\"border:0px;font-weight:bold;\" align=\"right\">".number_format($dataOut['headNya'][0]->discount,0)."</td>";
			$trNya .= "</tr>";
			$trNya .= "<tr>";
				$trNya .= " <td colspan=\"2\" style=\"font-weight:bold;border:0px;\" align=\"right\">Total After Discount :</td>";
				$trNya .= " <td style=\"font-weight:bold;border:0px;\" align=\"right\" id=\"idTtlAfterDisc\">".number_format($afterDisc,0)."</td>";
			$trNya .= "</tr>";
			$trNya .= "<tr>";
				$trNya .= " <td colspan=\"2\" style=\"font-weight:bold;border:0px;\" align=\"right\">PPN :</td>";
				$trNya .= " <td style=\"font-weight:bold;border:0px;\" align=\"right\" id=\"idLblPPN\">".number_format($ppn,0)."</td>";
			$trNya .= "</tr>";
			$trNya .= "<tr>";
				$trNya .= "	<td colspan=\"2\" style=\"font-weight:bold;border:0px;\" align=\"right\">Delivery Cost :</td>";
				$trNya .= "	<td style=\"font-weight:bold;border:0px;\" align=\"right\">".number_format($dataOut['headNya'][0]->delivery_cost,0)."</td>";
			$trNya .= "</tr>";
			$trNya .= "<tr>";
				$trNya .= " <td colspan=\"2\" style=\"font-weight:bold;border:0px;\" align=\"right\">Grand Total :</td>";
				$trNya .= " <td style=\"font-weight:bold;border:0px;\" align=\"right\" id=\"idGrandTotal\">".number_format($grandTotal,0)."</td>";
			$trNya .= "</tr>";
			
		$dataOut['trNya'] = $trNya;
		$dataOut['idRequest'] = $dataOut['headNya'][0]->id_request;
		$dataOut['idPurch'] = $id;
		$dataOut['poDate'] = $this->convertReturnName($dataOut['headNya'][0]->po_date);
		$dataOut['optSupplier'] = $erpControl->getOptSupplierErp("",$erpControl->cekDbErpByCompany($dataOut['headNya'][0]->ship_company),$dataOut['headNya'][0]->supplier_code);
		$dataOut['tempDBNya'] = $erpControl->cekDbErpByCompany($dataOut['headNya'][0]->ship_company);

		print json_encode($dataOut);
	}

	function sendToERP()
	{
		$userId = $this->session->userdata('idUserPurchase');
		$dateNow = date('Y-m-d');
		$stData = "";
		$arrTempIdDetail = array();
		$arrTempItemCode = array();
		$arrTempSatuan = array();
		$arrTempSatuanName = array();
		$uptDataPurch = array();
		$uptDetail = array();		
		$data = $_POST;
		$dataOut = array();

		$idRequest = $data['txtIdReqSendErp'];
		$idPurchase = $data['txtIdPurchSendErp'];
		$supplierName = $data['supplierName'];
		$supplierName = explode("<=>", $supplierName);
		$supplierCode = $data['supplierCode'];

		$arrTempIdDetail = explode("*",$data['idDetail']);
		$arrTempItemCode = explode("*",$data['itemCode']);
		$arrTempSatuan = explode("*",$data['satuan']);
		$arrTempSatuanName = explode("*",$data['stName']);

		try {

			$uptDataPurch['supplier_name'] = $supplierName[1];
			$uptDataPurch['supplier_code'] = $supplierCode;
			$uptDataPurch['send_erp'] = "1";
			$uptDataPurch['user_send_erp'] = $userId;
			$uptDataPurch['date_send_erp'] = $dateNow;

			$whereNya = "id = '".$idPurchase."'";
			$this->mpurchasing->updateData($whereNya,$uptDataPurch,"list_purchase");

			for ($lan=0; $lan < count($arrTempIdDetail); $lan++)
			{
				$uptDetail['code_item_erp'] = $arrTempItemCode[$lan];
				$uptDetail['satuan_erp'] = $arrTempSatuan[$lan];
				$uptDetail['satuanname_erp'] = $arrTempSatuanName[$lan];

				$whereDetail = "id = '".$arrTempIdDetail[$lan]."'";
				$this->mpurchasing->updateData($whereDetail,$uptDetail,"request_detail");
			}

			$cekCompleteSendErp = $this->cekCompleteData($idRequest);
			if($cekCompleteSendErp == "yes")
			{
				$uptRequest = array();

				$uptRequest['st_data'] = '1';

				$whereReq = "id = '".$idRequest."'";
				$this->mpurchasing->updateData($whereReq,$uptRequest,"request");
			}

			$stData = $this->insertToErp($data);
		} catch (Exception $ex) {
			$stData = "Failed => ".$ex->getMessage();
		}

		$dataOut['stData'] = $stData;

		print json_encode($dataOut);
	}

	function insertToErp($data)
	{
		$erpControl = new MasErpApi();
		$dataInsErp = array();
		$tempDataDet = array();
		$dataReturn = "";
		$erpDept = "";
		$erpGdng = "";
		$initCmp = "";
		$poNo = "";
		$poDate = "";
		$extraDisc = 0;
		$note = "";
		$rackNo = "";
		$ongkir = 0;
		$typePpn = "";
		$ppnPercen = 0;
		$kdPpn = "";
		$vndQuot = "";

		$idRequest = $data['txtIdReqSendErp'];
		$idPurchase = $data['txtIdPurchSendErp'];
		$supplierName = $data['supplierName'];
		$supplierCode = $data['supplierCode'];
		$txtVesselSendErp = $data['txtVesselSendErp'];
		$txtVslCompanySendErp = $data['txtVslCompanySendErp'];

		try {

			$sql="SELECT B.init,A.* FROM list_purchase A LEFT JOIN mst_company B ON B.name_company = A.ship_company WHERE A.id = '".$idPurchase."'";
			$rsl= $this->mpurchasing->getDataQuery($sql);

			if(count($rsl) > 0)
			{
				$poNo = $rsl[0]->po_no;
				$poDate = $rsl[0]->po_date;
				$note = $rsl[0]->subject;
				$extraDisc = $rsl[0]->discount;
				$initCmp = $rsl[0]->init;
				$ongkir = $rsl[0]->delivery_cost;
				$typePpn = $rsl[0]->type_ppn;
				$vndQuot = $rsl[0]->vendor_quot;
			}

			$sqlDet = "SELECT * FROM request_detail WHERE id_request = '".$idRequest."' AND ((purchase_id = '".$idPurchase."' AND purchase_price > 0) OR (purchase2_id = '".$idPurchase."' AND purchase2_price > 0) OR (purchase3_id = '".$idPurchase."' AND purchase3_price > 0)) AND sts_delete = '0' ";
			$rslDet = $this->mpurchasing->getDataQuery($sqlDet);

			$dbNya = $erpControl->cekDbErpByCompany($txtVslCompanySendErp);
			$dbThn = $erpControl->cekDbThn($dbNya);

			//$dataDept = $this->mpurchasing->querySqlServerErp("SELECT * FROM Warehouses WHERE NamaGudang like '%".$txtVesselSendErp."%'",$dbNya);
			$sql = "SELECT * FROM ".$dbThn."..Warehouses WHERE NamaGudang like '%".$txtVesselSendErp."%'";
			// $rsl = $this->mpurchasing->querySqlServerErp($sql,$dbNya);
			$dataDept = $this->mpurchasing->querySqlServerErp($sql,"sqlSrvErp_and");

			if(count($dataDept) > 0)
			{
				$erpDept = $dataDept[0]->KodeDept;
				$erpGdng = $dataDept[0]->KodeGudang;
			}

			$rackNo = "R".strtoupper($erpGdng);

			if (count($rslDet) > 0)
			{
				for ($lan=0; $lan < count($rslDet) ; $lan++)
				{
					if($rslDet[$lan]->quot_custom1 == "other")
					{
						if($vndQuot == "" OR $vndQuot == "quot1")
						{
							$purcQty = $rslDet[$lan]->purchase_qty;
							$purcPrice = $rslDet[$lan]->purchase_price;
						}
						else if($vndQuot == "quot2")
						{
							$purcQty = $rslDet[$lan]->purchase2_qty;
							$purcPrice = $rslDet[$lan]->purchase2_price;
						}
						else if($vndQuot == "quot3")
						{
							$purcQty = $rslDet[$lan]->purchase3_qty;
							$purcPrice = $rslDet[$lan]->purchase3_price;
						}
					}else{
						$purcQty = $rslDet[$lan]->purchase_qty;
						$purcPrice = $rslDet[$lan]->purchase_price;
					}

					$tempDataDet[$lan]['usePph'] = true;
					$tempDataDet[$lan]['warehouseCode'] = $erpGdng;
					$tempDataDet[$lan]['rack'] = $rackNo;
					$tempDataDet[$lan]['itemCode'] = $rslDet[$lan]->code_item_erp;
					$tempDataDet[$lan]['itemName'] = $rslDet[$lan]->article_name;
					$tempDataDet[$lan]['qty'] = $purcQty;
					$tempDataDet[$lan]['unitType'] = $rslDet[$lan]->satuan_erp;
					$tempDataDet[$lan]['purchasePrice'] = $purcPrice;
					$tempDataDet[$lan]['discount1'] = 0;
					$tempDataDet[$lan]['discountPercent1'] = 0;
					$tempDataDet[$lan]['discount2'] = 0;
					$tempDataDet[$lan]['discountPercent2'] = 0;
					$tempDataDet[$lan]['discount3'] = 0;
					$tempDataDet[$lan]['discountPercent3'] = 0;
					$tempDataDet[$lan]['note'] = "";
				}
			}

			if($typePpn == "")
			{
				$typePpn = "T";
				$kdPpn = "";
				$ppnPercen = 0;
			}else{
				$kdPpn = "PPN11";
				$ppnPercen = 11;
			}

			if(strtoupper($initCmp) == "ADY")
			{
				$initCmp = "adn";
			}

			$rateNya = 1;
			//$dataVendor = $this->mpurchasing->querySqlServerErp("SELECT KodeCrc FROM Vendors WHERE KodeLgn = '".$supplierCode."'",$dbNya);
			$sql = "SELECT KodeCrc FROM ".$dbThn."..Vendors WHERE KodeLgn = '".$supplierCode."'";
			// $rsl = $this->mpurchasing->querySqlServerErp($sql,$dbNya);
			$dataVendor = $this->mpurchasing->querySqlServerErp($sql,"sqlSrvErp_and");

			if(count($dataVendor) > 0)
			{
				if(strtoupper($dataVendor[0]->KodeCrc) != "IDR")
				{
					$sqlReq = "SELECT type_check1 FROM request WHERE id = '".$idRequest."' AND sts_delete = '0' ";
					$rslReq = $this->mpurchasing->getDataQuery($sqlReq);

					if(count($rslReq) > 0)
					{
						$sqlKurs = "SELECT kurs FROM quotation WHERE id_request = '".$idRequest."' AND sts_delete = '0' ORDER BY id ASC ";
						$rslKurs = $this->mpurchasing->getDataQuery($sqlKurs);

						if(count($rslKurs) > 0)
						{
							if($rslReq[0]->type_check1 == "quot1")
							{
								$rateNya = $rslKurs[0]->kurs;
							}
							if($rslReq[0]->type_check1 == "quot2")
							{
								$rateNya = $rslKurs[1]->kurs;
							}
							if($rslReq[0]->type_check1 == "quot3")
							{
								$rateNya = $rslKurs[2]->kurs;
							}
							if($rslReq[0]->type_check1 == "custom")
							{
								$sqlVnd = "SELECT vendor_quot FROM list_purchase WHERE id_request = '".$idRequest."' AND supplier_code = '".$supplierCode."' AND sts_delete = '0' ";
								$rslVnd = $this->mpurchasing->getDataQuery($sqlVnd);

								if(count($rslVnd) > 0)
								{
									if($rslVnd[0]->vendor_quot == "quot1")
									{
										$rateNya = $rslKurs[0]->kurs;
									}
									if($rslVnd[0]->vendor_quot == "quot2")
									{
										$rateNya = $rslKurs[1]->kurs;
									}
									if($rslVnd[0]->vendor_quot == "quot3")
									{
										$rateNya = $rslKurs[2]->kurs;
									}
								}
							}
						}
					}
				}
			}

			$cekKurs = substr($supplierCode, -3);
			if(strstr(strtolower($cekKurs), "usd"))
			{
				if($rateNya <= 1)
				{
					$rateNya = "15000";
				}
			}

			$dataInsErp = array(
								"departmentCode"=>$erpDept,
								"projectCode"=>"",
								"counterCode"=>"",
								"transactionNumber"=>$poNo,
								"transactionDate"=>$poDate,
								"supplierCode"=>$supplierCode,
								"warehouseCode"=>$erpGdng,
								"paymentTermCode"=>"D30",
								"shippingAddress"=>$txtVslCompanySendErp." - ".$txtVesselSendErp,
								"journalCode"=>"1",
								"rate"=>$rateNya,
								"extraDiscount1"=>$extraDisc,
								"extraDiscountPercent1"=>0,
								"extraDiscount2"=>0,
								"extraDiscountPercent2"=>0,
								"ppnType"=>$typePpn,
								"kodePpn"=>$kdPpn,
								"ppnPersen"=> $ppnPercen,
								"taxNumber"=> "",
								"taxAdditionalNote"=> "",
								"ppnDate"=> "",
								"pphCode"=>"",
								"note"=>$note,
								"freightCost"=>$ongkir,
								"purchaseOrderItemDtos"=>$tempDataDet
								);

			$dataReturn = $erpControl->actionERP($initCmp,"save",$dataInsErp);
		} catch (Exception $ex){
			$dataReturn = "Failed => ".$ex->getMessage();
		}

		return $dataReturn;
	}

	function cancelDataToErp($idPurch = "")
	{
		$data = $_POST;		
		$stData = "";
		$valData = array();

		if($idPurch == "")
		{
			$idPurc = $data['idPurc'];
		}else{
			$idPurc = $idPurch;
		}

		try {
				$valData['send_erp'] = '0';

				$whereNya = "id = '".$idPurc."'";
				$this->mpurchasing->updateData($whereNya,$valData,"list_purchase");

				$stData = "Cancel Success..!!";
		} catch (Exception $e) {
			$stData = "Failed =>".$e;
		}

		if($idPurch == "")
		{
			print json_encode($stData);
		}else{
			return $stData;
		}		
	}

	function editData()
	{
		$data = $_POST;
		$dataOut = array();
		$id = $data['id'];
		$typeEdit = $data['typeEdit'];
		$typeCheck = $data['typeCheck'];
		$trNya1 = "";
		$trNya2 = "";
		$trNya3 = "";
		$no = 1;

		if($typeEdit == "checkPurchasing")
		{
			$grandTotal = 0;
			$dataOut['headNya'] = $this->mpurchasing->getData("*","request","id = '".$id."' AND sts_delete = '0'");
			$dataQuot = $this->mpurchasing->getData("*","quotation","id_request = '".$id."' AND sts_delete = '0'","id ASC");

			if($dataOut['headNya'][0]->type_check1 == "custom")
			{
				$tempdata = array();
				$sqlCek = "SELECT * FROM request_detail WHERE id_request = '".$id."' ";
				$rslCek = $this->mpurchasing->getDataQuery($sqlCek);
				foreach ($rslCek as $key => $val)
				{
					if($val->quot_custom1 == "quot1")
					{
						$tempdata[$val->quot_custom1][$key]['id'] = $val->id;
						$tempdata[$val->quot_custom1][$key]['article_name'] = $val->article_name;
						$tempdata[$val->quot_custom1][$key]['code_no'] = $val->code_no;
						$tempdata[$val->quot_custom1][$key]['unit'] = $val->unit;
						$tempdata[$val->quot_custom1][$key]['approved_order'] = $val->approved_order;
						$tempdata[$val->quot_custom1][$key]['qtyBeli'] = $val->approved_order;
						$tempdata[$val->quot_custom1][$key]['curr'] = $val->quot_curr1;
						$tempdata[$val->quot_custom1][$key]['price'] = $val->quot_price1;
						$tempdata[$val->quot_custom1][$key]['amount'] = $val->quot_amount1;

						$dataOut['pic_vendor1'] = $dataQuot[0]->pic_vendor;
						$dataOut['vendor_company1'] = $dataQuot[0]->vendor_company;
						$dataOut['vendor_code1'] = $dataQuot[0]->vendor_code;
						$dataOut['file_name1'] = $dataQuot[0]->file_name;

						$tempdata[$val->quot_custom1][$key]['code_no'] = $val->code_no;
					}
					else if($val->quot_custom1 == "quot2")
					{
						$tempdata[$val->quot_custom1][$key]['id'] = $val->id;
						$tempdata[$val->quot_custom1][$key]['article_name'] = $val->article_name;
						$tempdata[$val->quot_custom1][$key]['code_no'] = $val->code_no;
						$tempdata[$val->quot_custom1][$key]['unit'] = $val->unit;
						$tempdata[$val->quot_custom1][$key]['approved_order'] = $val->approved_order;
						$tempdata[$val->quot_custom1][$key]['qtyBeli'] = $val->approved_order;
						$tempdata[$val->quot_custom1][$key]['curr'] = $val->quot_curr2;
						$tempdata[$val->quot_custom1][$key]['price'] = $val->quot_price2;
						$tempdata[$val->quot_custom1][$key]['amount'] = $val->quot_amount2;

						$dataOut['pic_vendor2'] = $dataQuot[1]->pic_vendor;
						$dataOut['vendor_company2'] = $dataQuot[1]->vendor_company;
						$dataOut['vendor_code2'] = $dataQuot[0]->vendor_code;
						$dataOut['file_name2'] = $dataQuot[1]->file_name;

						$tempdata[$val->quot_custom1][$key]['code_no'] = $val->code_no;
					}
					else if($val->quot_custom1 == "quot3")
					{
						$tempdata[$val->quot_custom1][$key]['id'] = $val->id;
						$tempdata[$val->quot_custom1][$key]['article_name'] = $val->article_name;
						$tempdata[$val->quot_custom1][$key]['code_no'] = $val->code_no;
						$tempdata[$val->quot_custom1][$key]['unit'] = $val->unit;
						$tempdata[$val->quot_custom1][$key]['approved_order'] = $val->approved_order;
						$tempdata[$val->quot_custom1][$key]['qtyBeli'] = $val->approved_order;
						$tempdata[$val->quot_custom1][$key]['curr'] = $val->quot_curr3;
						$tempdata[$val->quot_custom1][$key]['price'] = $val->quot_price3;
						$tempdata[$val->quot_custom1][$key]['amount'] = $val->quot_amount3;

						$dataOut['pic_vendor3'] = $dataQuot[2]->pic_vendor;
						$dataOut['vendor_company3'] = $dataQuot[2]->vendor_company;
						$dataOut['vendor_code3'] = $dataQuot[0]->vendor_code;
						$dataOut['file_name3'] = $dataQuot[2]->file_name;

						$tempdata[$val->quot_custom1][$key]['code_no'] = $val->code_no;
					}
					else if($val->quot_custom1 == "other")
					{						
						for ($lan=1; $lan <= 3; $lan++)
						{
							$qtOth = "quot_other".$lan;
							for ($hal=1; $hal <= 3; $hal++)
							{
								$quotNya = "quot".$hal;
								$qc = "quot_curr".$hal;
								$qp = "quot_price".$hal;
								$qa = "quot_amount".$hal;
								$qtyNya = "quot_other".$hal."_qty";

								$amounNya = $val->$qtyNya * $val->$qp;

								if($val->$qtOth == $quotNya)
								{
									$tempdata[$val->$qtOth][$key]['id'] = $val->id;
									$tempdata[$val->$qtOth][$key]['article_name'] = $val->article_name;
									$tempdata[$val->$qtOth][$key]['code_no'] = $val->code_no;
									$tempdata[$val->$qtOth][$key]['unit'] = $val->unit;
									$tempdata[$val->$qtOth][$key]['approved_order'] = $val->approved_order;
									$tempdata[$val->$qtOth][$key]['qtyBeli'] = $val->$qtyNya;
									$tempdata[$val->$qtOth][$key]['code_no'] = $val->code_no;
									$tempdata[$val->$qtOth][$key]['curr'] = $val->$qc;
									$tempdata[$val->$qtOth][$key]['price'] = $val->$qp;
									$tempdata[$val->$qtOth][$key]['amount'] = $amounNya;

									$dataOut['pic_vendor'.$hal] = $dataQuot[($hal-1)]->pic_vendor;
									$dataOut['vendor_company'.$hal] = $dataQuot[($hal-1)]->vendor_company;
									$dataOut['vendor_code'.$hal] = $dataQuot[($hal-1)]->vendor_code;
									$dataOut['file_name'.$hal] = $dataQuot[($hal-1)]->file_name;
								}
							}
						}
					}

				}
				
				foreach ($tempdata as $keys => $valNya)
				{
					$grandTotal = 0;
					$trNya = "";
					$quotNo = 0;
					$no = 1;

					if($keys == "quot1")
					{
						$trNya = "trNya1";
						$quotNo = 1;
					}
					if($keys == "quot2")
					{
						$trNya = "trNya2";
						$quotNo = 2;
					}
					if($keys == "quot3")
					{
						$trNya = "trNya3";
						$quotNo = 3;
					}

					foreach ($valNya as $key => $valQuot)
					{
						$nmArt = "<i style=\"font-size:10px;\">(".$valQuot['code_no'].")</i><br>";
						$nmArt .= $valQuot['article_name'];

						$curr = "";
						$slcIdr = "";
						$slcUsd = "";
						$slcSgd = "";
						$price = "0";
						$amount = "0";

						$curr = $valQuot['curr'];
						if($curr == "idr")
						{
							$slcIdr = "selected=\"selected\"";
						}
						if($curr == "usd")
						{
							$slcUsd = "selected=\"selected\"";
						}
						if($curr == "sgd")
						{
							$slcSgd = "selected=\"selected\"";
						}

						$price = $valQuot['price'];
						$amount = $valQuot['amount'];

						${$trNya} .= "<tr>";
							${$trNya} .= "<td align=\"center\">".$no."</td>";
							${$trNya} .= "<td align=\"left\">".$nmArt."</td>";
							${$trNya} .= "<td align=\"center\">".$valQuot['unit']."</td>";
							${$trNya} .= "<td align=\"center\" id=\"idLblReq_".$valQuot['id']."\">".$valQuot['approved_order']."</td>";
							${$trNya} .= "<td align=\"center\">
											<input type=\"hidden\" name=\"txtIdReqDetail".$quotNo."[]\" id=\"txtIdReqDetail_".$valQuot['id']."\" value=\"".$valQuot['id']."\">
											<input type=\"text\" style=\"text-align:center;\" oninput=\"sumAmount('".$valQuot['id']."');\" class=\"form-control input-sm\" name=\"txtTtlApprove".$quotNo."[]\" id=\"txtTtlApprove_".$valQuot['id']."\" value=\"".$valQuot['qtyBeli']."\">
										</td>";
							${$trNya} .= "<td align=\"center\">";
								${$trNya}	.="<select name=\"slcCurrency".$quotNo."[]\" id=\"slcCurrency_".$valQuot['id']."\" class=\"form-control input-sm\">";
									${$trNya} .= "<option value=\"idr\" ".$slcIdr.">IDR (Rp)</option>";
									${$trNya} .= "<option value=\"usd\" ".$slcUsd.">USD ($)</option>";
									${$trNya} .= "<option value=\"sgd\" ".$slcSgd.">SGD (S$)</option>";
								${$trNya} .= "</select>";
							${$trNya} .="</td>";
							${$trNya} .= "<td align=\"center\">
											<input type=\"text\" style=\"text-align:right\" oninput=\"sumAmount('".$valQuot['id']."');\" class=\"form-control input-sm\" name=\"txtPrice".$quotNo."[]\" id=\"txtPrice_".$valQuot['id']."\" value=\"".$price."\">
										</td>";
							${$trNya} .= "<td align=\"center\">
											<input type=\"text\" style=\"text-align:right\" class=\"form-control input-sm\" name=\"txtAmount".$quotNo."[]\" id=\"txtAmount_".$valQuot['id']."\" value=\"".$amount."\">
										</td>";
						${$trNya} .= "</tr>";
						$grandTotal = $grandTotal + $amount;
						$no++;
					}
					
					${$trNya} .= "<tr>";
						${$trNya} .= "<td colspan=\"6\" align=\"right\">Total Amount :</td>";
						${$trNya} .= "<td colspan=\"2\" align=\"center\" id=\"idTtlAmount\">".number_format($grandTotal,0)."</td>";
					${$trNya} .= "</tr>";
				}
				$dataOut['trNya1'] = $trNya1;
				$dataOut['trNya2'] = $trNya2;
				$dataOut['trNya3'] = $trNya3;
				$dataOut['linkFile1'] = "";
				$dataOut['linkFile2'] = "";
				$dataOut['linkFile3'] = "";

				$linkFile = "";
				if(isset($dataOut['file_name1']))
				{
					$linkFile = "<a href=\"".base_url("uploadFile/")."/".$dataOut['file_name1']."\" target=\"_blank\"> View File</a>";
					$dataOut['linkFile1'] = $linkFile;
				}
				if(isset($dataOut['file_name2']))
				{
					$linkFile = "<a href=\"".base_url("uploadFile/")."/".$dataOut['file_name2']."\" target=\"_blank\"> View File</a>";
					$dataOut['linkFile2'] = $linkFile;
				}
				if(isset($dataOut['file_name3']))
				{
					$linkFile = "<a href=\"".base_url("uploadFile/")."/".$dataOut['file_name3']."\" target=\"_blank\"> View File</a>";
					$dataOut['linkFile3'] = $linkFile;
				}
				
				$dataOut['idReq'] = $id;
				$dataOut['lblReqDate'] = $this->convertReturnName($dataOut['headNya'][0]->date_request);
				$dataOut['reqDate'] = $dataOut['headNya'][0]->date_request;
			}else{
				$linkFile = "";
				$valDetail = $this->mpurchasing->getData("*","request_detail","id_request = '".$id."' AND sts_delete = '0' ");
				foreach ($valDetail as $key => $val)
				{
					$nmArt = "<i style=\"font-size:10px;\">(".$val->code_no.")</i><br>";
					$nmArt .= $val->article_name;

					$curr = "";
					$slcIdr = "";
					$slcUsd = "";
					$slcSgd = "";
					$price = "0";
					$amount = "0";

					if($dataOut['headNya'][0]->type_check1 == "quot1")
					{
						$curr = $val->quot_curr1;
						if($curr == "idr")
						{
							$slcIdr = "selected=\"selected\"";
						}
						if($curr == "usd")
						{
							$slcUsd = "selected=\"selected\"";
						}
						if($curr == "sgd")
						{
							$slcSgd = "selected=\"selected\"";
						}

						$price = $val->quot_price1;
						$amount = $val->quot_amount1;
						$linkFile = $dataQuot[0]->file_name;

						$dataOut['pic_vendor1'] = $dataQuot[0]->pic_vendor;
						$dataOut['vendor_company1'] = $dataQuot[0]->vendor_company;
						$dataOut['vendor_code1'] = $dataQuot[0]->vendor_code;
					}
					else if($dataOut['headNya'][0]->type_check1 == "quot2")
					{
						$curr = $val->quot_curr2;
						if($curr == "idr")
						{
							$slcIdr = "selected=\"selected\"";
						}
						if($curr == "usd")
						{
							$slcUsd = "selected=\"selected\"";
						}
						if($curr == "sgd")
						{
							$slcSgd = "selected=\"selected\"";
						}

						$price = $val->quot_price2;
						$amount = $val->quot_amount2;
						$linkFile = $dataQuot[1]->file_name;

						$dataOut['pic_vendor1'] = $dataQuot[1]->pic_vendor;
						$dataOut['vendor_company1'] = $dataQuot[1]->vendor_company;
						$dataOut['vendor_code1'] = $dataQuot[0]->vendor_code;
					}
					else if($dataOut['headNya'][0]->type_check1 == "quot3")
					{
						$curr = $val->quot_curr3;
						if($curr == "idr")
						{
							$slcIdr = "selected=\"selected\"";
						}
						if($curr == "usd")
						{
							$slcUsd = "selected=\"selected\"";
						}
						if($curr == "sgd")
						{
							$slcSgd = "selected=\"selected\"";
						}

						$price = $val->quot_price3;
						$amount = $val->quot_amount3;
						$linkFile = $dataQuot[2]->file_name;

						$dataOut['pic_vendor1'] = $dataQuot[2]->pic_vendor;
						$dataOut['vendor_company1'] = $dataQuot[2]->vendor_company;
						$dataOut['vendor_code1'] = $dataQuot[0]->vendor_code;
					}

					$trNya1 .= "<tr>";
						$trNya1 .= "<td align=\"center\">".$no."</td>";
						$trNya1 .= "<td align=\"left\">".$nmArt."</td>";
						$trNya1 .= "<td align=\"center\">".$val->unit."</td>";
						$trNya1 .= "<td align=\"center\" id=\"idLblReq_".$val->id."\">".$val->approved_order."</td>";
						$trNya1 .= "<td align=\"center\">
										<input type=\"hidden\" name=\"txtIdReqDetail1[]\" id=\"txtIdReqDetail\" value=\"".$val->id."\">
										<input type=\"text\" style=\"text-align:center;\" oninput=\"sumAmount('".$val->id."');\" class=\"form-control input-sm\" name=\"txtTtlApprove1[]\" id=\"txtTtlApprove_".$val->id."\" value=\"".$val->approved_order."\">
									</td>";
						$trNya1 .= "<td align=\"center\">";
							$trNya1	.="<select name=\"slcCurrency1[]\" id=\"slcCurrency_".$val->id."\" class=\"form-control input-sm\">";
								$trNya1 .= "<option value=\"idr\" ".$slcIdr.">IDR (Rp)</option>";
								$trNya1 .= "<option value=\"usd\" ".$slcUsd.">USD ($)</option>";
								$trNya1 .= "<option value=\"sgd\" ".$slcSgd.">SGD (S$)</option>";
							$trNya1 .= "</select>";
						$trNya1 .="</td>";
						$trNya1 .= "<td align=\"center\">
										<input type=\"text\" style=\"text-align:right\" oninput=\"sumAmount('".$val->id."');\" class=\"form-control input-sm\" name=\"txtPrice1[]\" id=\"txtPrice_".$val->id."\" value=\"".$price."\">
									</td>";
						$trNya1 .= "<td align=\"center\">
										<input type=\"text\" style=\"text-align:right\" class=\"form-control input-sm\" name=\"txtAmount1[]\" id=\"txtAmount_".$val->id."\" value=\"".$amount."\">
									</td>";
					$trNya1 .= "</tr>";
					$grandTotal = $grandTotal + $amount;
					$no++;
				}
					$trNya1 .= "<tr>";
						$trNya1 .= "<td colspan=\"6\" align=\"right\">Total Amount :</td>";
						$trNya1 .= "<td colspan=\"2\" align=\"center\" id=\"idTtlAmount\">".number_format($grandTotal,0)."</td>";
					$trNya1 .= "</tr>";
				$dataOut['trNya1'] = $trNya1;
				$dataOut['idReq'] = $id;

				if($linkFile != "")
				{
					$linkFile = "<a href=\"".base_url("uploadFile/")."/".$linkFile."\" target=\"_blank\"> View File</a>";
				}
				$dataOut['linkFile'] = $linkFile;
				$dataOut['lblReqDate'] = $this->convertReturnName($dataOut['headNya'][0]->date_request);
				$dataOut['reqDate'] = $dataOut['headNya'][0]->date_request;
			}
			$dataOut['poNo'] = $this->getNoPO();
		}
		else if($typeEdit == "editPurchasing")
		{
			$linkFile1 = "";
			$linkFile2 = "";
			$linkFile3 = "";

			$dataOut['dataPurc'] = $this->mpurchasing->getData("*","list_purchase","id_request = '".$id."' AND sts_delete = '0' ");
			$dataReq = $this->mpurchasing->getData("*","request","id = '".$id."' AND sts_delete = '0'","id ASC");
			$dataQuot = $this->mpurchasing->getData("*","quotation","id_request = '".$id."' AND sts_delete = '0'","id ASC");

			for ($lan=0; $lan < count($dataOut['dataPurc']) ; $lan++)
			{
				$grandTotal = 0;
				$no = 1;
				// $valDetail = $this->mpurchasing->getData("*","request_detail","id_request = '".$id."' AND purchase_id = '".$dataOut['dataPurc'][$lan]->id."' AND sts_delete = '0' ");
				$valDetail = $this->mpurchasing->getData("*","request_detail","id_request = '".$id."' AND (purchase_id = '".$dataOut['dataPurc'][$lan]->id."' OR purchase2_id = '".$dataOut['dataPurc'][$lan]->id."' OR purchase3_id = '".$dataOut['dataPurc'][$lan]->id."') AND sts_delete = '0' ");
				$trNya = "trNya".($lan+1);
				$linkFile = "linkFile".($lan+1);
				foreach ($valDetail as $key => $val)
				{
					$nmArt = "<i style=\"font-size:10px;\">(".$val->code_no.")</i><br>";
					$nmArt .= $val->article_name;

					$curr = "";
					$slcIdr = "";
					$slcUsd = "";
					$slcSgd = "";
					$price = "0";
					$amount = "0";

					$curr = $val->purchase_curr;
					if($curr == "idr")
					{
						$slcIdr = "selected=\"selected\"";
					}
					if($curr == "usd")
					{
						$slcUsd = "selected=\"selected\"";
					}
					if($curr == "sgd")
					{
						$slcSgd = "selected=\"selected\"";
					}

					$price = $val->purchase_price;
					$amount = $val->purchase_amount;

					${$trNya} .= "<tr>";
						${$trNya} .= "<td align=\"center\">".$no."</td>";
						${$trNya} .= "<td align=\"left\">".$nmArt."</td>";
						${$trNya} .= "<td align=\"center\">".$val->unit."</td>";
						${$trNya} .= "<td align=\"center\" id=\"idLblReq_".$val->id."\">".$val->approved_order."</td>";
						${$trNya} .= "<td align=\"center\">
										<input type=\"hidden\" name=\"txtIdReqDetail".($lan+1)."[]\" id=\"txtIdReqDetail\" value=\"".$val->id."\">
										<input type=\"text\" style=\"text-align:center;\" oninput=\"sumAmount('".$val->id."','".($lan+1)."');\" class=\"form-control input-sm\" name=\"txtTtlApprove".($lan+1)."[]\" id=\"txtTtlApprove_".$val->id."\" value=\"".$val->purchase_qty."\">
									</td>";
						${$trNya} .= "<td align=\"center\">";
							${$trNya}	.="<select name=\"slcCurrency".($lan+1)."[]\" id=\"slcCurrency_".$val->id."\" class=\"form-control input-sm\">";
								${$trNya} .= "<option value=\"idr\" ".$slcIdr.">IDR (Rp)</option>";
								${$trNya} .= "<option value=\"usd\" ".$slcUsd.">USD ($)</option>";
								${$trNya} .= "<option value=\"sgd\" ".$slcSgd.">SGD (S$)</option>";
							${$trNya} .= "</select>";
						${$trNya} .="</td>";
						${$trNya} .= "<td align=\"center\">
										<input type=\"text\" style=\"text-align:right\" oninput=\"sumAmount('".$val->id."','".($lan+1)."');\" class=\"form-control input-sm\" name=\"txtPrice".($lan+1)."[]\" id=\"txtPrice_".$val->id."\" value=\"".$price."\">
									</td>";
						${$trNya} .= "<td align=\"center\">
										<input type=\"text\" style=\"text-align:right\" class=\"form-control input-sm\" name=\"txtAmount".($lan+1)."[]\" id=\"txtAmount".($lan+1)."_".$val->id."\" value=\"".$amount."\">
									</td>";
					${$trNya} .= "</tr>";
					$grandTotal = $grandTotal + $amount;
					$no++;
				}
				${$trNya} .= "<tr>";
					${$trNya} .= "<td colspan=\"6\" align=\"right\">Total Amount :</td>";
					${$trNya} .= "<td colspan=\"2\" align=\"center\" id=\"idTtlAmount".($lan+1)."\">".number_format($grandTotal,0)."</td>";
				${$trNya} .= "</tr>";
				// if()
				// {

				// }
				//${$linkFile} = "<a href=\"".base_url("uploadFile/")."/".$dataOut['dataQuot']['file_name']."\" target=\"_blank\"> View File</a>";
			}

			$dataOut['trNya1'] = $trNya1;
			$dataOut['trNya2'] = $trNya2;
			$dataOut['trNya3'] = $trNya3;
			$dataOut['idReq'] = $id;
			$dataOut['linkFile1'] = $linkFile1;
			$dataOut['linkFile2'] = $linkFile2;
			$dataOut['linkFile3'] = $linkFile3;
			$dataOut['reqDateLbl'] = $this->convertReturnName($dataOut['dataPurc'][0]->date_request);
		}		

		print json_encode($dataOut);
	}

	function cekCustomQuot($idDet = "")
	{
		$dataOut = array();
		$customQuot = "";
		$quotOther1 = "";
		$quotOther2 = "";
		$quotOther3 = "";


		$sql = "SELECT * FROM request_detail WHERE sts_delete = 0 AND id = '".$idDet."'";
		$rsl = $this->mpurchasing->getDataQuery($sql);

		if(count($rsl) > 0)
		{
			if($rsl[0]->quot_custom1 == "other")
			{
				$customQuot = $rsl[0]->quot_custom1;
				$quotOther1 = $rsl[0]->quot_other1;
				$quotOther2 = $rsl[0]->quot_other2;
				$quotOther3 = $rsl[0]->quot_other3;
			}
		}

		$dataOut['customQuot'] = $customQuot;
		$dataOut['quotOther1'] = $quotOther1;
		$dataOut['quotOther2'] = $quotOther2;
		$dataOut['quotOther3'] = $quotOther3;

		return $dataOut;
	}

	function getNoPO()
	{
		$dataOut = array();

		$sql = "SELECT MAX(po_no_int)+1 AS nextPo FROM list_purchase";
		$rsl = $this->mpurchasing->getDataQuery($sql);

		$poNo = $rsl[0]->nextPo;

		$dataOut['poNo1'] = $poNo;
		$dataOut['poNo2'] = $poNo+1;
		$dataOut['poNo3'] = $poNo+2;

		return $dataOut;
	}

	function createNoPO()
	{
		$poNo = "0";
		$initVsl = "SM";
		$initCmp = "-";
		$thn = "-";
		$bln = "-";
		$dataOut = array();

		$dateNya = $_POST['dateNya'];
		$vsl = $_POST['vsl'];
		$company = $_POST['company'];
		$poNo = $_POST['poNo'];

		if($dateNya != "")
		{
			$dt = explode("-", $dateNya);
			$thn = substr($dt[0], 2,4) ;
			$bln = $dt[1];
		}
		

		if($company != "")
		{
			$sqlCmp = "SELECT init FROM mst_company WHERE name_company = '".$company."' ";
			$rslCmp = $this->mpurchasing->getDataQuery($sqlCmp);

			if(count($rslCmp) > 0)
			{
				$initCmp = $rslCmp[0]->init;
				if($initCmp == "AND")
				{
					$initCmp = "ADH";
				}
			}			
		}

		$sqlVsl = "SELECT init FROM init_vessel_po WHERE sts_delete = '0' AND vessel = '".$vsl."' ";
		$rslVsl = $this->mpurchasing->getDataQuery($sqlVsl);

		if(count($rslVsl) > 0)
		{
			$initVsl = $rslVsl[0]->init;
		}

		$dataOut['formatNya'] = $this->createFormatNo($poNo)."/".$initVsl."/CO/".$initCmp."/".$bln.$thn;// CO = credit Order

		print json_encode($dataOut);
	}

	function createFormatNo($noNya = "")
	{
		$dt = strlen($noNya);
		$outNo = "";
		if($dt == 1)
		{
			$outNo = "00".$noNya;
		}
		else if($dt == 2)
		{
			$outNo = "0".$noNya;
		}
		else{
			$outNo = $noNya;
		}
		return $outNo;
	}

	function getQuot($idReq = "",$typeCheck = "")
	{
		$dataOut = array();
		$key = 0;

		$sql = "SELECT * FROM quotation WHERE id_request = '".$idReq."' ORDER BY id ASC";
		$rsl = $this->mpurchasing->getDataQuery($sql);

		if($typeCheck == "custom")
		{
			$dataOut['pic_vendor'] = "Custom";
			$dataOut['vendor_company'] = "Custom";
			$dataOut['file_name'] = "";
		}else{
			if($typeCheck == "quot1"){ $key = 0; }
			if($typeCheck == "quot2"){ $key = 1; }
			if($typeCheck == "quot3"){ $key = 2; }			

			if($key < count($rsl))
			{
				$dataOut['pic_vendor'] = $rsl[$key]->pic_vendor;
				$dataOut['vendor_company'] = $rsl[$key]->vendor_company;
				$dataOut['file_name'] = $rsl[$key]->file_name;
			}else{
				$dataOut['pic_vendor'] = "";
				$dataOut['vendor_company'] = "";
				$dataOut['file_name'] = "";
			}
		}		

		return $dataOut;
	}

	function getCompany()
	{
		$optNya = "";

		$sql = "SELECT * FROM mst_company ORDER BY name_company ASC";
		$rsl = $this->mpurchasing->getDataQuery($sql);

		foreach ($rsl as $key => $value)
		{
			$optNya .= "<option value='".$value->name_company."'>".$value->name_company."</option>";
		}
		return $optNya;
	}

	function getVessel()
	{
		$optNya = "";

		$sql = "SELECT * FROM mst_vessel ORDER BY name ASC";
		$rsl = $this->mpurchasing->getDataQuery($sql);

		foreach ($rsl as $key => $value)
		{
			$optNya .= "<option value='".$value->name."'>".$value->name."</option>";
		}
		return $optNya;
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

	function cekCompleteData($idReq = "")
	{
		$stCek = "";

		$rsl = $this->mpurchasing->getData("send_erp","list_purchase","sts_delete = '0' AND id_request = '".$idReq."' ");

		if(count($rsl) > 0)
		{
			$stCek = "yes";
			foreach ($rsl as $key => $value)
			{
				if($value->send_erp == '0')
				{
					$stCek = "no";
				}
			}
		}

		return $stCek;
	}

	function getChangePass()
	{
		$this->load->view('purchasing/changePassword');
	}

	function changePass()
	{
		$idUsrLogin = $this->session->userdata('idUserPurchase');
		$data = $_POST;
		$newPass = $data['newPass'];
		$stData = "";
		$valData = array();

		try {
				$valData['password'] = md5($newPass);

				$whereNya = "id = '".$idUsrLogin."'";
				$this->mpurchasing->updateData($whereNya,$valData,"user");
				$stData = "Change Password Success, Please Login againt..!!";
		} catch (Exception $e) {
			$stData = "Failed =>".$e;
		}
		print json_encode($stData);
	}

	function cekMenuLogin()
	{
		$tempData = array();
		$dataOut = array();
		$usrVessel = $this->session->userdata('usrVessel');

		$sql = "SELECT B.menu
				FROM user_setting A
				LEFT JOIN mst_menu B ON B.id = A.id_menu
				WHERE A.username = '".$_POST['user']."' ";
		$rsl = $this->mpurchasing->getDataQuery($sql);

		foreach ($rsl as $key => $value)
		{
			$tempData[]['menu'] = $value->menu;
		}

		if($usrVessel == "")
		{
			$tempData[]['menu'] = "change pass";
		}
		$dataOut = $tempData;
		print json_encode($dataOut);
	}

	function getOptSupplierErp123($returnType = "",$dbNya)
	{
		$opt = "";

		$sql = "SELECT KodeLgn,NamaLgn FROM Vendors WHERE NonAktif = 0 ORDER BY NamaLgn ASC ";
		$rsl = $this->mpurchasing->querySqlServerErp($sql,$dbNya);

		if(count($rsl) > 0)
		{
			foreach ($rsl as $key => $value)
			{
				$opt .= "<option value=\"".$value->KodeLgn."\">".$value->KodeLgn." - ".$value->NamaLgn."</option>";
			}
		}

		if($returnType == "")
		{
			return $opt;
		}else{
			print json_encode($opt);
		}
	}

	function getOptItemCodeErp123($inventoryId = "",$returnType = "",$dbNya = "")
	{
		$opt = "";
		$whereNya = " WHERE SUBSTRING(KodeItem,1,3) = '513'";

		if($inventoryId != "")
		{
			$whereNya .= " AND InventoryId = '".$inventoryId."' ";
		}

		$sql = "SELECT * FROM Inventories ".$whereNya;
		$rsl = $this->mpurchasing->querySqlServerErp($sql,$dbNya);

		if(count($rsl) > 0)
		{
			foreach ($rsl as $key => $value)
			{
				$opt .= "<option value=\"".$value->KodeItem."\">".$value->NamaBarang."</option>";
			}
		}

		if($returnType == "")
		{
			return $opt;
		}else{
			print json_encode($opt);
		}
	}

	function getOptItemSatuanErp123()
	{
		$opt = "";

		$itemCode = $_POST['itemCode'];
		$tempDBNya = $_POST['tempDBNya'];

		$sql = "SELECT * FROM Inventories WHERE KodeItem = '".$itemCode."'";
		$rsl = $this->mpurchasing->querySqlServerErp($sql,$tempDBNya);

		if(count($rsl) > 0)
		{
			$opt .= "<option value=\"1\">".$rsl[0]->KodeSatuan."</option>";

			if($rsl[0]->Konversi2 == "1")
			{
				$opt .= "<option value=\"2\">".$rsl[0]->Satuan2."</option>";
			}
			if($rsl[0]->Konversi3 == "1")
			{
				$opt .= "<option value=\"3\">".$rsl[0]->Satuan3."</option>";
			}
			if($rsl[0]->Konversi4 == "1")
			{
				$opt .= "<option value=\"4\">".$rsl[0]->Satuan4."</option>";
			}
		}

		print json_encode($opt);
	}

	function cekDbByCompany123($company)
	{
		$dbName = "";		

		if(strstr(strtolower($company),"andhika line"))
		{
			$dbName = "sqlSrvErp_and";
		}
		else if(strstr(strtolower($company),"adnyana"))
		{
			$dbName = "sqlSrvErp_ady";
		}
		else if(strstr(strtolower($company),"andhini eka karya sejahtera"))
		{
			$dbName = "sqlSrvErp_aes";
		}
		else if(strstr(strtolower($company),"andhika samudra internusa"))
		{
			$dbName = "sqlSrvErp_asi";
		}
		else if(strstr(strtolower($company),"indah bima prima"))
		{
			$dbName = "sqlSrvErp_ibp";
		}
		
		return $dbName;
	}

	function login()
	{
		$data = $_POST;
		$user = $data['user'];
		$pass = md5($data['pass']);
		$status = '';
		$whereNya = "username = '".$user."' AND password = '".$pass."' AND sts_delete = '0' ";
		
		$cekLogin = $this->mpurchasing->getData("*","user",$whereNya);

		if(count($cekLogin) > 0)
		{	
			$this->session->set_userdata('idUserPurchase',$cekLogin[0]->id);
			$this->session->set_userdata('fullName',$cekLogin[0]->name_full);
			$this->session->set_userdata('userName',$cekLogin[0]->username);
			$this->session->set_userdata('userTypePurchase',$cekLogin[0]->type);
			$this->session->set_userdata('userPosition',$cekLogin[0]->position);
			$this->session->set_userdata('usrVessel',"");
			$status = true;
		}
		else
		{
			$pass = base64_encode($data['pass']);
			$sql = "SELECT A.*,B.name as nameJbtn,C.name as nameVsl
					FROM login A
					LEFT JOIN mst_jabatan B ON B.id = A.id_jabatan
					LEFT JOIN mst_vessel C ON C.id = A.vessel
					WHERE A.username = '".$user."' AND A.password = '".$pass."' AND A.sts_delete = '0'
					";
			$cekObs = $this->mpurchasing->getDataQueryDb2($sql);

			if(count($cekObs) > 0)
			{
				$usrType = $cekObs[0]->user_type;
				if($usrType == "admin")
				{
					$usrType = "administrator";
				}

				$this->session->set_userdata('idUserPurchase',$cekObs[0]->id);
				$this->session->set_userdata('fullName',$cekObs[0]->full_name);
				$this->session->set_userdata('userName',$cekObs[0]->username);
				$this->session->set_userdata('userTypePurchase',$usrType);
				$this->session->set_userdata('userPosition',$cekObs[0]->nameJbtn);
				$this->session->set_userdata('usrVessel',$cekObs[0]->nameVsl);
				$status = true;
			}else{
				$status = false;
			}			
		}
		print json_encode($status);
	}

	function logout()
	{
		$this->session->unset_userdata('idUserPurchase');
		$this->session->unset_userdata('fullName');
		$this->session->unset_userdata('userTypePurchase');
		$this->session->unset_userdata('userPosition');
		$this->session->unset_userdata('userName');
		$this->session->unset_userdata('usrVessel');
		// $this->session->sess_destroy();
		redirect(base_url());
	}


















}