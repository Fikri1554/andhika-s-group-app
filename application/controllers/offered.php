<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Offered extends CI_Controller {

	function __construct()
	{
		parent::__construct();		
		$this->load->model('mpurchasing');
		$this->load->helper(array('form', 'url'));
		$this->load->library('../controllers/MasErpApi');
	}

	function index() { }

	function getListOffer($searchNya = "")
	{
		$userPosition = $this->session->userdata('userPosition');
		$userId = $this->session->userdata('idUserPurchase');
		$dataOut = array();
		$trNya = "";
		$no = 1;

		$whereNya = " sts_delete = '0' AND check_order = '1' AND submit_check = '1' AND (check_approve1 = '0' OR check_approve2 = '0') AND submit_offered = '0' AND req_check_approve = '1' ";

		if($userPosition == "si")
		{
			$vslHandle = $this->getHandleVessel($userId);

			if($vslHandle != "")
			{
				$whereNya .= " AND vessel IN(".$vslHandle.")";
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

		$sql = "SELECT * FROM request WHERE ".$whereNya." AND st_data != '1'";
		$rsl = $this->mpurchasing->getDataQuery($sql);
		
		foreach ($rsl as $key => $val) {
			$btnAct = "";
			$stOffer = "Ready";

			if($val->create_offered == "0")
			{
				$btnAct = " <button onclick=\"createQuotation('".$val->id."');\" class=\"btn btn-info btn-xs btn-block\" id=\"btnCreate\" type=\"button\"><i class=\"fa fa-check-square-o\"></i> Create</button>";
				$btnAct .= " <button onclick=\"completeTask('".$val->id."');\" class=\"btn btn-success btn-xs btn-block\" id=\"btnComplete\" type=\"button\"><i class=\"fa fa-check-square-o\"></i> Complete</button>";
			}
			else if($val->create_offered == "1" AND $val->submit_offered == "0")
			{
				if($userPosition == "si")
				{
					$btnAct = " <button onclick=\"completeTask('".$val->id."');\" class=\"btn btn-success btn-xs btn-block\" id=\"btnComplete\" type=\"button\"><i class=\"fa fa-check-square-o\"></i>Complete</button>";
					$btnAct .= " <button onclick=\"editData('".$val->id."');\" class=\"btn btn-warning btn-xs btn-block\" id=\"btnEdit\" type=\"button\"><i class=\"fa fa-edit\"></i> Edit</button>";
				}else
				{
					$btnAct = " <button onclick=\"submitData('".$val->id."');\" class=\"btn btn-primary btn-xs btn-block\" id=\"btnSubmit\" type=\"button\"><i class=\"fa fa-hand-o-right\"></i> Submit</button>";
					$btnAct .= " <button onclick=\"editData('".$val->id."');\" class=\"btn btn-warning btn-xs btn-block\" id=\"btnEdit\" type=\"button\"><i class=\"fa fa-edit\"></i> Edit</button>";
					
					if($userType == "administrator")
					{
						$btnAct .= " <button onclick=\"completeTask('".$val->id."');\" class=\"btn btn-success btn-xs btn-block\" id=\"btnComplete\" type=\"button\"><i class=\"fa fa-check-square-o\"></i>Complete</button>";
					}
				}		
			}
			else if($val->create_offered == "2")
			{
				$stOffer = "Approve";
			}
			if($val->check_approve1 == "1")
			{
				$btnAct = "";
				$stOffer = "On Progress";
			}
			if($val->revise_offered == "1")
			{
				$stOffer = "Revise";
				if($val->revise_remark_offered != "")
				{
					$stOffer .= "<br><b style=\"font-size:9px;\"><i>( ".$val->revise_remark_offered." )</i></b>";
				}				
			}

			$trNya .= "<tr>";
				$trNya .= "<td align=\"center\">".$no."</td>";
				$trNya .= "<td align=\"center\">".$this->convertReturnName($val->date_request)."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$val->app_no."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$val->vessel."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$val->department."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:11px;\">".$stOffer."</td>";
				$trNya .= "<td align=\"center\" style=\"font-size:10px;\">".$btnAct."</td>";
			$trNya .= "</tr>";

			$no++;
		}

		$dataOut["trNya"] = $trNya;
		$dataOut["vslNya"] = $this->getVessel();
		$dataOut["companyNya"] = $this->getCompany();

		if($searchNya == "search") {
			print json_encode($dataOut);
		} else {
			$this->load->view("purchasing/listOffer",$dataOut);
		}
	}

	function submitComplete() {
		$data = $_POST;
		$valData = array();
		$idReq = $data['idReq'];
		$remark = $data['remark'];
		$userName = $this->session->userdata('userName');
		$dateNow = date('dmY'); // Format tanggal: ddmmyyyy
		$userFormatted = $userName . '#' . $dateNow;
	
		$response = array(); // Initialize response array
	
		try {
			$valData['st_data'] = '1'; // Update status to Complete
			$valData['remark_complete'] = $remark;
			$valData['user_complete'] = $userFormatted;
			$whereNya = "id = '" . $idReq . "'";
	
			// Log the data being updated
			log_message('debug', 'Updating request with data: ' . json_encode($valData) . ' where: ' . $whereNya);
			
			$this->mpurchasing->updatedata($whereNya, $valData, "request");
	
			// Verify that the data was updated
			$updatedData = $this->mpurchasing->getDataQuery("SELECT st_data FROM request WHERE id = '" . $idReq . "'");
			log_message('debug', 'Updated data: ' . json_encode($updatedData));
	
			if ($updatedData[0]->st_data == '1') {
				$response['success'] = true;
				$response['message'] = "Submit Success..!!";
			} else {
				throw new Exception("Failed to update status in the database.");
			}
		} catch (Exception $e) {
			$response['success'] = false;
			$response['message'] = "Failed: " . $e->getMessage(); // Get the exception message
			
			// Log the error
			log_message('error', 'Failed to update request: ' . $e->getMessage());
		}
	
		header('Content-Type: application/json'); // Ensure the response is JSON
		echo json_encode($response); // Return the response as JSON
	}
	
	
	function addOffered()
	{
		$data = $_POST;
		$dir = "./uploadFile";
		$userId = $this->session->userdata('idUserPurchase');
		$dataUpd = array();
		$dataUpdReq = array();
		$dataIns1 = array();
		$dataIns2 = array();
		$dataIns3 = array();
		$idReq = $data['idReq'];
		$arrIdDetail = array();
		$arrQty1 = array();
		$arrQty2 = array();
		$arrQty3 = array();
		$arrCurr1 = array();
		$arrCurr2 = array();
		$arrCurr3 = array();
		$arrPrice1 = array();
		$arrPrice2 = array();
		$arrPrice3 = array();
		$arrAmount1 = array();
		$arrAmount2 = array();
		$arrAmount3 = array();
		$fileNamePO1 = "";
		$fileNamePO2 = "";
		$fileNamePO3 = "";
		$dateNow = date("Y-m-d");

		$arrIdDetail = explode("*",$data['idEditDetail1']);
		$arrQty1 = explode("*",$data['qty1']);
		$arrQty2 = explode("*",$data['qty2']);
		$arrQty3 = explode("*",$data['qty3']);
		$arrCurr1 = explode("*",$data['curr1']);
		$arrCurr2 = explode("*",$data['curr2']);
		$arrCurr3 = explode("*",$data['curr3']);
		$arrPrice1 = explode("*",$data['price1']);
		$arrPrice2 = explode("*",$data['price2']);
		$arrPrice3 = explode("*",$data['price3']);
		$arrAmount1 = explode("*",$data['amount1']);
		$arrAmount2 = explode("*",$data['amount2']);
		$arrAmount3 = explode("*",$data['amount3']);

		if($data['cekUploadPO1'] != "")
		{
			$newFileNamePo = "quotation1_".$idReq;
			$fileNamePo = $_FILES["fileUploadPO1"]["name"];
			$fileNamePO1 = $this->uploadFile($_FILES["fileUploadPO1"]['tmp_name'],$dir,$fileNamePo,$newFileNamePo);
			$dataIns1['file_name'] = $fileNamePO1;
			$dataIns1['file_date_upload'] = $dateNow;
		}

		$tempVendor = explode("<=>", $data['vendorCompany1']);

		$dataIns1['id_request'] = $idReq;
		$dataIns1['request_date'] = $data['reqDate1'];
		$dataIns1['app_no'] = $data['appNo1'];
		$dataIns1['pic_vendor'] = $data['picVendor1'];
		$dataIns1['vendor_company'] = trim($tempVendor[1]);
		$dataIns1['vendor_code'] = trim($tempVendor[0]);
		$dataIns1['ship_name'] = $data['vesselName1'];
		$dataIns1['ship_company'] = $data['vesselCompany1'];
		$dataIns1['discount'] = $data['txtDiscQuot1'];
		$dataIns1['ppn'] = $data['txtPPNQuot1'];
		$dataIns1['delivery_cost'] = $data['txtOngkirQuot1'];
		$dataIns1['kurs'] = $data['txtKursQuot1'];

		try {
				$dataUpdReq['create_offered'] = "1";				
				$dataUpdReq['remark_offered'] = $data['txtRemark'];

				if($data['txtIdQuot1'] == "")// pertama x save saja di catat
				{
					$dataUpdReq['date_offered'] = date("Y-m-d");
					$dataUpdReq['idUser_offered'] = $userId;
				}

				$whereNya = "id = '".$idReq."'";
				$this->mpurchasing->updateData($whereNya,$dataUpdReq,"request");

				if($data['txtIdQuot1'] == "")
				{
					$this->mpurchasing->insData("quotation",$dataIns1);
				}else{
					$whereNya = "id = '".$data['txtIdQuot1']."'";
					$this->mpurchasing->updateData($whereNya,$dataIns1,"quotation");
				}

				if($data['picVendor2'] != "" AND $data['vendorCompany2'] != "")
				{
					if($data['cekUploadPO2'] != "")
					{
						$newFileNamePo = "quotation2_".$idReq;
						$fileNamePo = $_FILES["fileUploadPO2"]["name"];
						$fileNamePO2 = $this->uploadFile($_FILES["fileUploadPO2"]['tmp_name'],$dir,$fileNamePo,$newFileNamePo);
						$dataIns2['file_name'] = $fileNamePO2;
						$dataIns2['file_date_upload'] = $dateNow;
					}

					$tempVendor2 = explode("<=>", $data['vendorCompany2']);

					if(count($tempVendor2) > 0)
					{
						$dataIns2['vendor_company'] = trim($tempVendor2[1]);
						$dataIns2['vendor_code'] = trim($tempVendor2[0]);
					}

					$dataIns2['id_request'] = $idReq;
					$dataIns2['request_date'] = $data['reqDate2'];
					$dataIns2['app_no'] = $data['appNo2'];
					$dataIns2['pic_vendor'] = $data['picVendor2'];
					
					$dataIns2['ship_name'] = $data['vesselName2'];
					$dataIns2['ship_company'] = $data['vesselCompany2'];
					$dataIns2['discount'] = $data['txtDiscQuot2'];
					$dataIns2['ppn'] = $data['txtPPNQuot2'];
					$dataIns2['delivery_cost'] = $data['txtOngkirQuot2'];
					$dataIns2['kurs'] = $data['txtKursQuot2'];

					if($data['txtIdQuot2'] == "")
					{
						$this->mpurchasing->insData("quotation",$dataIns2);
					}else{
						$whereNya = "id = '".$data['txtIdQuot2']."'";
						$this->mpurchasing->updateData($whereNya,$dataIns2,"quotation");
					}
				}

				if($data['picVendor3'] != "" AND $data['vendorCompany3'] != "")
				{
					if($data['cekUploadPO3'] != "")
					{
						$newFileNamePo = "quotation3_".$idReq;
						$fileNamePo = $_FILES["fileUploadPO3"]["name"];
						$fileNamePO3 = $this->uploadFile($_FILES["fileUploadPO3"]['tmp_name'],$dir,$fileNamePo,$newFileNamePo);
						$dataIns3['file_name'] = $fileNamePO3;
						$dataIns3['file_date_upload'] = $dateNow;
					}

					$tempVendor3 = explode("<=>", $data['vendorCompany3']);

					if(count($tempVendor2) > 0)
					{
						$dataIns3['vendor_company'] = trim($tempVendor3[1]);
						$dataIns3['vendor_code'] = trim($tempVendor3[0]);
					}

					$dataIns3['id_request'] = $idReq;
					$dataIns3['request_date'] = $data['reqDate3'];
					$dataIns3['app_no'] = $data['appNo3'];
					$dataIns3['pic_vendor'] = $data['picVendor3'];
					
					$dataIns3['ship_name'] = $data['vesselName3'];
					$dataIns3['ship_company'] = $data['vesselCompany3'];
					$dataIns3['discount'] = $data['txtDiscQuot3'];
					$dataIns3['ppn'] = $data['txtPPNQuot3'];
					$dataIns3['delivery_cost'] = $data['txtOngkirQuot3'];
					$dataIns3['kurs'] = $data['txtKursQuot3'];

					if($data['txtIdQuot3'] == "")
					{
						$this->mpurchasing->insData("quotation",$dataIns3);
					}else{
						$whereNya = "id = '".$data['txtIdQuot3']."'";
						$this->mpurchasing->updateData($whereNya,$dataIns3,"quotation");
					}
				}

				for ($lan=0; $lan < count($arrIdDetail) ; $lan++)
				{
					$dataUpd['quot_price1'] = $arrPrice1[$lan];
					$dataUpd['quot_qty1'] = $arrQty1[$lan];
					$dataUpd['quot_curr1'] = $arrCurr1[$lan];
					$dataUpd['quot_amount1'] = $arrAmount1[$lan];
					$dataUpd['quot_price2'] = $arrPrice2[$lan];
					$dataUpd['quot_qty2'] = $arrQty2[$lan];
					$dataUpd['quot_curr2'] = $arrCurr2[$lan];
					$dataUpd['quot_amount2'] = $arrAmount2[$lan];
					$dataUpd['quot_price3'] = $arrPrice3[$lan];
					$dataUpd['quot_qty3'] = $arrQty3[$lan];
					$dataUpd['quot_curr3'] = $arrCurr3[$lan];
					$dataUpd['quot_amount3'] = $arrAmount3[$lan];

					$whereNya = "id = '".$arrIdDetail[$lan]."'";
					$this->mpurchasing->updateData($whereNya,$dataUpd,"request_detail");
					$dataUpd = array();
				}

				$stData = "Save Success..!!";
		} catch (Exception $e) {
			$stData = "Failed =>".$e;
		}

		print_r($stData);
	}

	function getEdit()
	{
		$userPosition = $this->session->userdata('userPosition');
		$erpControl = new MasErpApi();
		$dataOut = array();
		$id = $_POST['id'];
		$typeEdit = $_POST['typeEdit'];
		$no = 1;

		if($typeEdit == "createQuotation")
		{
			$trNya1 = "";
			$trNya2 = "";
			$trNya3 = "";

			$sql = "SELECT * FROM request WHERE id = '".$id."' AND sts_delete = '0' ";
			$dataOut['dataNya'] = $this->mpurchasing->getDataQuery($sql);

			$sqlDetail = "SELECT * FROM request_detail WHERE id_request = '".$id."' AND sts_delete = '0' ORDER BY id ASC ";
			$rslDetail = $this->mpurchasing->getDataQuery($sqlDetail);

			foreach ($rslDetail as $key => $val)
			{
				$actName = $val->article_name;
				$stDisInput = "";

				if($val->request_file != "")
				{
					$actName = "<a href=\"".base_url("uploadFile"."/".$val->request_file)."\" target=\"_blank\">".$val->article_name."</a>";
				}

				if($userPosition == "si")
				{
					$stDisInput = "disabled=\"disabled\"";
				}

				$trNya1 .= "<tr>";
					$trNya1 .= "<td align=\"center\">".$no."</td>";
					$trNya1 .= "<td align=\"left\">".$actName."</td>";
					$trNya1 .= "<td align=\"center\">".$val->code_no."</td>";
					$trNya1 .= "<td align=\"center\">".$val->unit."</td>";
					$trNya1 .= "<td align=\"center\">".$val->request."</td>";
					$trNya1 .= "<td align=\"center\" id=\"idLblAppOrder1_".$no."\">".$val->approved_order."</td>";
					$trNya1 .= "<td><input style=\"text-align:right;\" type=\"text\" id=\"txtQty1_".$no."\" name=\"txtQty1[]\" value=\"0\" class=\"form-control input-sm\" oninput=\"sumData('".$no."','1');\" ".$stDisInput."></td>";
					$trNya1 .= "<td align=\"center\">
									<input type=\"hidden\" name=\"txtIdDetailReq1[]\" id=\"txtIdDetailReq1_".$no."\" value=\"".$val->id."\">
									<select name=\"slcCurr1[]\" id=\"slcCurr1_".$no."\" class=\"form-control input-sm\" ".$stDisInput.">
										<option value=\"idr\">IDR (Rp)</option>
										<option value=\"usd\">USD ($)</option>
										<option value=\"sgd\">SGD (S$)</option>
									</select>
								</td>";
					$trNya1 .= "<td><input style=\"text-align:right;\" type=\"text\" id=\"txtPrice1_".$no."\" name=\"txtPrice1[]\" value=\"0\" class=\"form-control input-sm\" oninput=\"sumData('".$no."','1');\" ".$stDisInput."></td>";
					$trNya1 .= "<td><input style=\"text-align:right;\" type=\"text\" id=\"txtTotal1_".$no."\" name=\"txtTotal1[]".$no."\" value=\"0\" class=\"form-control input-sm\" disabled=\"disabled\"></td>";
				$trNya1 .= "</tr>";

				$trNya2 .= "<tr>";
					$trNya2 .= "<td align=\"center\">".$no."</td>";
					$trNya2 .= "<td align=\"left\">".$actName."</td>";
					$trNya2 .= "<td align=\"center\">".$val->code_no."</td>";
					$trNya2 .= "<td align=\"center\">".$val->unit."</td>";
					$trNya2 .= "<td align=\"center\">".$val->request."</td>";
					$trNya2 .= "<td align=\"center\" id=\"idLblAppOrder2_".$no."\">".$val->approved_order."</td>";
					$trNya2 .= "<td><input style=\"text-align:right;\" type=\"text\" id=\"txtQty2_".$no."\" name=\"txtQty2[]\" value=\"0\" class=\"form-control input-sm\" oninput=\"sumData('".$no."','2');\" ".$stDisInput."></td>";
					$trNya2 .= "<td align=\"center\">
									<input type=\"hidden\" name=\"txtIdDetailReq2[]\" id=\"txtIdDetailReq2_".$no."\" value=\"".$val->id."\">
									<select name=\"slcCurr2[]\" id=\"slcCurr2_".$no."\" class=\"form-control input-sm\" ".$stDisInput.">
										<option value=\"idr\">IDR (Rp)</option>
										<option value=\"usd\">USD ($)</option>
										<option value=\"sgd\">SGD (S$)</option>
									</select>
								</td>";
					$trNya2 .= "<td><input style=\"text-align:right;\" type=\"text\" id=\"txtPrice2_".$no."\" name=\"txtPrice2[]\" value=\"0\" class=\"form-control input-sm\" oninput=\"sumData('".$no."','2');\" ".$stDisInput."></td>";
					$trNya2 .= "<td><input style=\"text-align:right;\" type=\"text\" id=\"txtTotal2_".$no."\" name=\"txtTotal2[]".$no."\" value=\"0\" class=\"form-control input-sm\" disabled=\"disabled\"></td>";
				$trNya2 .= "</tr>";

				$trNya3 .= "<tr>";
					$trNya3 .= "<td align=\"center\">".$no."</td>";
					$trNya3 .= "<td align=\"left\">".$actName."</td>";
					$trNya3 .= "<td align=\"center\">".$val->code_no."</td>";
					$trNya3 .= "<td align=\"center\">".$val->unit."</td>";
					$trNya3 .= "<td align=\"center\">".$val->request."</td>";
					$trNya3 .= "<td align=\"center\" id=\"idLblAppOrder3_".$no."\">".$val->approved_order."</td>";
					$trNya3 .= "<td><input style=\"text-align:right;\" type=\"text\" id=\"txtQty3_".$no."\" name=\"txtQty3[]\" value=\"0\" class=\"form-control input-sm\" oninput=\"sumData('".$no."','3');\" ".$stDisInput."></td>";
					$trNya3 .= "<td align=\"center\">
									<input type=\"hidden\" name=\"txtIdDetailReq3[]\" id=\"txtIdDetailReq3_".$no."\" value=\"".$val->id."\">
									<select name=\"slcCurr3[]\" id=\"slcCurr3_".$no."\" class=\"form-control input-sm\" ".$stDisInput.">
										<option value=\"idr\">IDR (Rp)</option>
										<option value=\"usd\">USD ($)</option>
										<option value=\"sgd\">SGD (S$)</option>
									</select>
								</td>";
					$trNya3 .= "<td><input style=\"text-align:right;\" type=\"text\" id=\"txtPrice3_".$no."\" name=\"txtPrice3[]\" value=\"0\" class=\"form-control input-sm\" oninput=\"sumData('".$no."','3');\" ".$stDisInput."></td>";
					$trNya3 .= "<td><input style=\"text-align:right;\" type=\"text\" id=\"txtTotal3_".$no."\" name=\"txtTotal3[]".$no."\" value=\"0\" class=\"form-control input-sm\" disabled=\"disabled\"></td>";
				$trNya3 .= "</tr>";

				$no++;
			}
			$trNya1 .= "<tr>";
				$trNya1 .= "<td align=\"right\" colspan=\"9\">Total :</td>";
				$trNya1 .= "<td align=\"right\" id=\"idTotalQuot1\"></td>";
			$trNya1 .= "<tr>";
			$trNya1 .= "<tr>";
				$trNya1 .= "<td align=\"right\" colspan=\"9\">Discount :</td>";
				$trNya1 .= "<td align=\"right\">";
					$trNya1 .= "<input style=\"text-align:right;\" type=\"text\" id=\"txtDiscountQuot1\" value=\"0\" class=\"form-control input-sm\" oninput=\"sumDataByDiscPPnOngkir('1');\">";
				$trNya1 .= "</td>";
			$trNya1 .= "<tr>";
			$trNya1 .= "<tr>";
				$trNya1 .= "<td align=\"right\" colspan=\"9\">After Discount :</td>";
				$trNya1 .= "<td align=\"right\" id=\"idAfterDiscQuot1\"></td>";
			$trNya1 .= "<tr>";
			$trNya1 .= "<tr>";
				$trNya1 .= "<td align=\"right\" colspan=\"9\">PPN :</td>";
				$trNya1 .= "<td align=\"right\">";
					$trNya1 .= "<input style=\"text-align:right;\" type=\"text\" id=\"txtPPNQuot1\" value=\"0\" class=\"form-control input-sm\" oninput=\"sumDataByDiscPPnOngkir('1');\">";
				$trNya1 .= "</td>";
			$trNya1 .= "<tr>";
			$trNya1 .= "<tr>";
				$trNya1 .= "<td align=\"right\" colspan=\"9\">Delivery Cost :</td>";
				$trNya1 .= "<td align=\"right\">";
					$trNya1 .= "<input style=\"text-align:right;\" type=\"text\" id=\"txtDeliveryQuot1\" value=\"0\" class=\"form-control input-sm\" oninput=\"sumDataByDiscPPnOngkir('1');\">";
				$trNya1 .= "</td>";
			$trNya1 .= "<tr>";
			$trNya1 .= "<tr>";
				$trNya1 .= "<td align=\"right\" colspan=\"9\">Grand Total :</td>";
				$trNya1 .= "<td align=\"right\" id=\"idGrandTotalQuot1\"></td>";
			$trNya1 .= "<tr>";
			$trNya1 .= "<tr>";
				$trNya1 .= "<td align=\"right\" colspan=\"9\">Kurs :</td>";
				$trNya1 .= "<td align=\"right\">";
					$trNya1 .= "<input style=\"text-align:right;\" type=\"text\" id=\"txtKursQuot1\" value=\"0\" class=\"form-control input-sm\" oninput=\"konversiIdr('1');\">";
				$trNya1 .= "</td>";
			$trNya1 .= "<tr>";
			$trNya1 .= "<tr>";
				$trNya1 .= "<td align=\"right\" colspan=\"9\">Total After Kurs :</td>";
				$trNya1 .= "<td align=\"right\" id=\"idTotalAfterKursQuot1\"></td>";
			$trNya1 .= "<tr>";
			$trNya2 .= "<tr>";
				$trNya2 .= "<td align=\"right\" colspan=\"9\">Total :</td>";
				$trNya2 .= "<td align=\"right\" id=\"idTotalQuot2\"></td>";
			$trNya2 .= "<tr>";
			$trNya2 .= "<tr>";
				$trNya2 .= "<td align=\"right\" colspan=\"9\">Discount :</td>";
				$trNya2 .= "<td align=\"right\">";
					$trNya2 .= "<input style=\"text-align:right;\" type=\"text\" id=\"txtDiscountQuot2\" value=\"0\" class=\"form-control input-sm\" oninput=\"sumDataByDiscPPnOngkir('2');\">";
				$trNya2 .= "</td>";
			$trNya2 .= "<tr>";
			$trNya2 .= "<tr>";
				$trNya2 .= "<td align=\"right\" colspan=\"9\">After Discount :</td>";
				$trNya2 .= "<td align=\"right\" id=\"idAfterDiscQuot2\"></td>";
			$trNya2 .= "<tr>";
			$trNya2 .= "<tr>";
				$trNya2 .= "<td align=\"right\" colspan=\"9\">PPN :</td>";
				$trNya2 .= "<td align=\"right\">";
					$trNya2 .= "<input style=\"text-align:right;\" type=\"text\" id=\"txtPPNQuot2\" value=\"0\" class=\"form-control input-sm\" oninput=\"sumDataByDiscPPnOngkir('2');\">";
				$trNya2 .= "</td>";
			$trNya2 .= "<tr>";
			$trNya2 .= "<tr>";
				$trNya2 .= "<td align=\"right\" colspan=\"9\">Delivery Cost :</td>";
				$trNya2 .= "<td align=\"right\">";
					$trNya2 .= "<input style=\"text-align:right;\" type=\"text\" id=\"txtDeliveryQuot2\" value=\"0\" class=\"form-control input-sm\" oninput=\"sumDataByDiscPPnOngkir('2');\">";
				$trNya2 .= "</td>";
			$trNya2 .= "<tr>";
			$trNya2 .= "<tr>";
				$trNya2 .= "<td align=\"right\" colspan=\"9\">Grand Total :</td>";
				$trNya2 .= "<td align=\"right\" id=\"idGrandTotalQuot2\"></td>";
			$trNya2 .= "<tr>";
			$trNya2 .= "<tr>";
				$trNya2 .= "<td align=\"right\" colspan=\"9\">Kurs :</td>";
				$trNya2 .= "<td align=\"right\">";
					$trNya2 .= "<input style=\"text-align:right;\" type=\"text\" id=\"txtKursQuot2\" value=\"0\" class=\"form-control input-sm\" oninput=\"konversiIdr('2');\">";
				$trNya2 .= "</td>";
			$trNya2 .= "<tr>";
			$trNya2 .= "<tr>";
				$trNya2 .= "<td align=\"right\" colspan=\"9\">Total After Kurs :</td>";
				$trNya2 .= "<td align=\"right\" id=\"idTotalAfterKursQuot2\"></td>";
			$trNya2 .= "<tr>";
			$trNya3 .= "<tr>";
				$trNya3 .= "<td align=\"right\" colspan=\"9\">Total :</td>";
				$trNya3 .= "<td align=\"right\" id=\"idTotalQuot3\"></td>";
			$trNya3 .= "<tr>";
			$trNya3 .= "<tr>";
				$trNya3 .= "<td align=\"right\" colspan=\"9\">Discount :</td>";
				$trNya3 .= "<td align=\"right\">";
					$trNya3 .= "<input style=\"text-align:right;\" type=\"text\" id=\"txtDiscountQuot3\" value=\"0\" class=\"form-control input-sm\" oninput=\"sumDataByDiscPPnOngkir('3');\">";
				$trNya3 .= "</td>";
			$trNya3 .= "<tr>";
			$trNya3 .= "<tr>";
				$trNya3 .= "<td align=\"right\" colspan=\"9\">After Discount :</td>";
				$trNya3 .= "<td align=\"right\" id=\"idAfterDiscQuot3\"></td>";
			$trNya3 .= "<tr>";
			$trNya3 .= "<tr>";
				$trNya3 .= "<td align=\"right\" colspan=\"9\">PPN :</td>";
				$trNya3 .= "<td align=\"right\">";
					$trNya3 .= "<input style=\"text-align:right;\" type=\"text\" id=\"txtPPNQuot3\" value=\"0\" class=\"form-control input-sm\" oninput=\"sumDataByDiscPPnOngkir('3');\">";
				$trNya3 .= "</td>";
			$trNya3 .= "<tr>";
			$trNya3 .= "<tr>";
				$trNya3 .= "<td align=\"right\" colspan=\"9\">Delivery Cost :</td>";
				$trNya3 .= "<td align=\"right\">";
					$trNya3 .= "<input style=\"text-align:right;\" type=\"text\" id=\"txtDeliveryQuot3\" value=\"0\" class=\"form-control input-sm\" oninput=\"sumDataByDiscPPnOngkir('3');\">";
				$trNya3 .= "</td>";
			$trNya3 .= "<tr>";
			$trNya3 .= "<tr>";
				$trNya3 .= "<td align=\"right\" colspan=\"9\">Grand Total :</td>";
				$trNya3 .= "<td align=\"right\" id=\"idGrandTotalQuot3\"></td>";
			$trNya3 .= "<tr>";
			$trNya3 .= "<tr>";
				$trNya3 .= "<td align=\"right\" colspan=\"9\">Kurs :</td>";
				$trNya3 .= "<td align=\"right\">";
					$trNya3 .= "<input style=\"text-align:right;\" type=\"text\" id=\"txtKursQuot3\" value=\"0\" class=\"form-control input-sm\" oninput=\"konversiIdr('3');\">";
				$trNya3 .= "</td>";
			$trNya3 .= "<tr>";
			$trNya3 .= "<tr>";
				$trNya3 .= "<td align=\"right\" colspan=\"9\">Total After Kurs :</td>";
				$trNya3 .= "<td align=\"right\" id=\"idTotalAfterKursQuot3\"></td>";
			$trNya3 .= "<tr>";
			
			$dataOut['idReq'] = $id;
			$dataOut['trNya1'] = $trNya1;
			$dataOut['trNya2'] = $trNya2;
			$dataOut['trNya3'] = $trNya3;
		}
		else if($typeEdit == "editQuotation")
		{
			$dataQuot = array();
			$trNya1 = "";
			$trNya2 = "";
			$trNya3 = "";
			$total1 = 0;
			$total2 = 0;
			$total3 = 0;
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

			$sqlReq = "SELECT * FROM request WHERE id = '".$id."' AND sts_delete = '0' ";
			$rslReq = $this->mpurchasing->getDataQuery($sqlReq);

			$sql = "SELECT * FROM quotation WHERE id_request = '".$id."' AND sts_delete = '0' ORDER BY id ASC ";
			$rsl = $this->mpurchasing->getDataQuery($sql);

			for ($lan=0; $lan < 3; $lan++)
			{
				$key = $lan;

				if($lan < count($rsl))
				{
					$dataQuot['idQuot'.($key+1)] = $rsl[$lan]->id;
					$dataQuot['reqDate'.($key+1)] = $rsl[$lan]->request_date;
					$dataQuot['appNo'.($key+1)] = $rsl[$lan]->app_no;
					$dataQuot['picVendor'.($key+1)] = $rsl[$lan]->pic_vendor;
					$dataQuot['vendorCompany'.($key+1)] = $rsl[$lan]->vendor_company;
					$dataQuot['vendorCode'.($key+1)] = $rsl[$lan]->vendor_code;
					$dataQuot['vesselName'.($key+1)] = $rsl[$lan]->ship_name;
					$dataQuot['vesselCompany'.($key+1)] = $rsl[$lan]->ship_company;
					if($rsl[$lan]->file_name != "")
					{
						$dataQuot['linkFile'.($key+1)] = "<a href=\"".base_url('uploadFile/'.$rsl[$lan]->file_name)."\" target=\"_blank\">View File</a>";
					}

					${"disc".($key+1)} = $rsl[$lan]->discount;
					${"ppn".($key+1)} = $rsl[$lan]->ppn;
					${"ongkir".($key+1)} = $rsl[$lan]->delivery_cost;
					${"kurs".($key+1)} = $rsl[$lan]->kurs;
				}else{
					$dataQuot['idQuot'.($key+1)] = "";
					$dataQuot['reqDate'.($key+1)] = $rslReq[0]->date_request;
					$dataQuot['appNo'.($key+1)] = $rslReq[0]->app_no;
					$dataQuot['picVendor'.($key+1)] = "";
					$dataQuot['vendorCompany'.($key+1)] = "";
					$dataQuot['vendorCode'.($key+1)] = "";
					$dataQuot['vesselName'.($key+1)] = $rslReq[0]->vessel;
					$dataQuot['vesselCompany'.($key+1)] = "";
					$dataQuot['linkFile'.($key+1)] = "";
				}
			}
			
			$sql = "SELECT * FROM request_detail WHERE id_request = '".$id."' AND sts_delete = '0' ORDER BY id ASC ";
			$rslDetail = $this->mpurchasing->getDataQuery($sql);

			foreach ($rslDetail as $key => $val)
			{
				for ($lan=1; $lan <= 3; $lan++)
				{
					$stDisInput = "";
					$qty = "quot_qty".$lan;
					$price = "quot_price".$lan;
					$amount = "quot_amount".$lan;
					$curr = "quot_curr".$lan;
					$slcIdrNya = "";
					$slcUsdNya = "";
					$slcSgdNya = "";

					if($val->$curr == "idr")
					{
						$slcIdrNya = "selected = \"selected\"";
					}
					else if($val->$curr == "usd")
					{ 
						$slcUsdNya = "selected = \"selected\"";
					}
					else if($val->$curr == "sgd")
					{ 
						$slcSgdNya = "selected = \"selected\"";
					}

					${'total'.$lan} = ${'total'.$lan} + $val->$amount;

					$actName = $val->article_name;
					if($val->request_file != "")
					{
						$actName = "<a href=\"".base_url("uploadFile"."/".$val->request_file)."\" target=\"_blank\">".$val->article_name."</a>";
					}

					if($userPosition == "si")
					{
						$stDisInput = "disabled=\"disabled\"";
					}

					${'trNya'.$lan} .= "<tr>";
						${'trNya'.$lan} .= "<td align=\"center\">".$no."</td>";
						${'trNya'.$lan} .= "<td align=\"left\">".$actName."</td>";
						${'trNya'.$lan} .= "<td align=\"center\">".$val->code_no."</td>";
						${'trNya'.$lan} .= "<td align=\"center\">".$val->unit."</td>";
						${'trNya'.$lan} .= "<td align=\"center\">".$val->request."</td>";
						${'trNya'.$lan} .= "<td align=\"center\" id=\"idLblAppOrder".$lan."_".$no."\">".$val->approved_order."</td>";
						${'trNya'.$lan} .= "<td>
												<input style=\"text-align:right;\" type=\"text\" id=\"txtQty".$lan."_".$no."\" name=\"txtQty".$lan."[]\" value=\"".$val->$qty."\" class=\"form-control input-sm\" oninput=\"sumData('".$no."','".$lan."');\" ".$stDisInput.">
											</td>";
						${'trNya'.$lan} .= "<td align=\"center\">
												<input type=\"hidden\" name=\"txtIdDetailReq".$lan."[]\" id=\"txtIdDetailReq".$lan."_".$no."\" value=\"".$val->id."\">
												<select name=\"slcCurr".$lan."[]\" id=\"slcCurr".$lan."_".$no."\" class=\"form-control input-sm\" ".$stDisInput.">
													<option value=\"idr\" ".$slcIdrNya.">IDR (Rp)</option>
													<option value=\"usd\" ".$slcUsdNya.">USD ($)</option>
													<option value=\"sgd\" ".$slcSgdNya.">SGD (S$)</option>
												</select>
											</td>";
						${'trNya'.$lan} .= "<td>
												<input style=\"text-align:right;\" type=\"text\" id=\"txtPrice".$lan."_".$no."\" name=\"txtPrice".$lan."[]\" value=\"".$val->$price."\" class=\"form-control input-sm\" oninput=\"sumData('".$no."','".$lan."');\" ".$stDisInput.">
											</td>";
						${'trNya'.$lan} .= "<td>
												<input style=\"text-align:right;\" type=\"text\" id=\"txtTotal".$lan."_".$no."\" name=\"txtTotal".$lan."[]".$no."\" value=\"".$val->$amount."\" class=\"form-control input-sm\" disabled=\"disabled\">
											</td>";
					${'trNya'.$lan} .= "</tr>";
				}
				$no++;
			}
			
			$afterDisc1 = $total1 - $disc1;
			$grandTotal1 = $afterDisc1 + $ppn1 + $ongkir1;
			$afterDisc2 = $total2 - $disc2;
			$grandTotal2 = $afterDisc2 + $ppn2 + $ongkir2;
			$afterDisc3 = $total3 - $disc3;
			$grandTotal3 = $afterDisc3 + $ppn3 + $ongkir3;

			$grandAfterKurs1 = $grandTotal1;
			$grandAfterKurs2 = $grandTotal2;
			$grandAfterKurs3 = $grandTotal3;

			if($kurs1 > 0)
			{
				$grandAfterKurs1 = $grandTotal1 * $kurs1;
			}

			if($kurs2 > 0)
			{
				$grandAfterKurs2 = $grandTotal2 * $kurs2;
			}

			if($kurs3 > 0)
			{
				$grandAfterKurs3 = $grandTotal3 * $kurs3;
			}

			$trNya1 .= "<tr>";
				$trNya1 .= "<td align=\"right\" colspan=\"9\">Total :</td>";
				$trNya1 .= "<td align=\"center\" id=\"idTotalQuot1\">".number_format($total1,2)."</td>";
			$trNya1 .= "<tr>";
			$trNya1 .= "<tr>";
				$trNya1 .= "<td align=\"right\" colspan=\"9\">Discount :</td>";
				$trNya1 .= "<td align=\"right\">";
					$trNya1 .= "<input style=\"text-align:right;\" type=\"text\" id=\"txtDiscountQuot1\" value=\"".$disc1."\" class=\"form-control input-sm\" oninput=\"sumDataByDiscPPnOngkir('1');\">";
				$trNya1 .= "</td>";
			$trNya1 .= "<tr>";
			$trNya1 .= "<tr>";
				$trNya1 .= "<td align=\"right\" colspan=\"9\">After Discount :</td>";
				$trNya1 .= "<td align=\"right\" id=\"idAfterDiscQuot1\">".number_format($afterDisc1,2)."</td>";
			$trNya1 .= "<tr>";
			$trNya1 .= "<tr>";
				$trNya1 .= "<td align=\"right\" colspan=\"9\">PPN :</td>";
				$trNya1 .= "<td align=\"right\">";
					$trNya1 .= "<input style=\"text-align:right;\" type=\"text\" id=\"txtPPNQuot1\" value=\"".$ppn1."\" class=\"form-control input-sm\" oninput=\"sumDataByDiscPPnOngkir('1');\">";
				$trNya1 .= "</td>";
			$trNya1 .= "<tr>";
			$trNya1 .= "<tr>";
				$trNya1 .= "<td align=\"right\" colspan=\"9\">Delivery Cost :</td>";
				$trNya1 .= "<td align=\"right\">";
					$trNya1 .= "<input style=\"text-align:right;\" type=\"text\" id=\"txtDeliveryQuot1\" value=\"".$ongkir1."\" class=\"form-control input-sm\" oninput=\"sumDataByDiscPPnOngkir('1');\">";
				$trNya1 .= "</td>";
			$trNya1 .= "<tr>";
			$trNya1 .= "<tr>";
				$trNya1 .= "<td align=\"right\" colspan=\"9\">Grand Total :</td>";
				$trNya1 .= "<td align=\"right\" id=\"idGrandTotalQuot1\">".number_format($grandTotal1,2)."</td>";
			$trNya1 .= "<tr>";
			$trNya1 .= "<tr>";
				$trNya1 .= "<td align=\"right\" colspan=\"9\">Kurs :</td>";
				$trNya1 .= "<td align=\"right\">";
					$trNya1 .= "<input style=\"text-align:right;\" type=\"text\" id=\"txtKursQuot1\" value=\"".$kurs1."\" class=\"form-control input-sm\" oninput=\"konversiIdr('1');\">";
				$trNya1 .= "</td>";
			$trNya1 .= "<tr>";
			$trNya1 .= "<tr>";
				$trNya1 .= "<td align=\"right\" colspan=\"9\">Total After Kurs (Rp) :</td>";
				$trNya1 .= "<td align=\"right\" id=\"idTotalAfterKursQuot1\">".number_format($grandAfterKurs1,2)."</td>";
			$trNya1 .= "<tr>";
			$trNya2 .= "<tr>";
				$trNya2 .= "<td align=\"right\" colspan=\"9\">Total :</td>";
				$trNya2 .= "<td align=\"center\" id=\"idTotalQuot2\">".number_format($total2,2)."</td>";
			$trNya2 .= "<tr>";
			$trNya2 .= "<tr>";
				$trNya2 .= "<td align=\"right\" colspan=\"9\">Discount :</td>";
				$trNya2 .= "<td align=\"right\">";
					$trNya2 .= "<input style=\"text-align:right;\" type=\"text\" id=\"txtDiscountQuot2\" value=\"".$disc2."\" class=\"form-control input-sm\" oninput=\"sumDataByDiscPPnOngkir('2');\">";
				$trNya2 .= "</td>";
			$trNya2 .= "<tr>";
			$trNya2 .= "<tr>";
				$trNya2 .= "<td align=\"right\" colspan=\"9\">After Discount :</td>";
				$trNya2 .= "<td align=\"right\" id=\"idAfterDiscQuot2\">".number_format($afterDisc2,2)."</td>";
			$trNya2 .= "<tr>";
			$trNya2 .= "<tr>";
				$trNya2 .= "<td align=\"right\" colspan=\"9\">PPN :</td>";
				$trNya2 .= "<td align=\"right\">";
					$trNya2 .= "<input style=\"text-align:right;\" type=\"text\" id=\"txtPPNQuot2\" value=\"".$ppn2."\" class=\"form-control input-sm\" oninput=\"sumDataByDiscPPnOngkir('2');\">";
				$trNya2 .= "</td>";
			$trNya2 .= "<tr>";
			$trNya2 .= "<tr>";
				$trNya2 .= "<td align=\"right\" colspan=\"9\">Delivery Cost :</td>";
				$trNya2 .= "<td align=\"right\">";
					$trNya2 .= "<input style=\"text-align:right;\" type=\"text\" id=\"txtDeliveryQuot2\" value=\"".$ongkir2."\" class=\"form-control input-sm\" oninput=\"sumDataByDiscPPnOngkir('2');\">";
				$trNya2 .= "</td>";
			$trNya2 .= "<tr>";
			$trNya2 .= "<tr>";
				$trNya2 .= "<td align=\"right\" colspan=\"9\">Grand Total :</td>";
				$trNya2 .= "<td align=\"right\" id=\"idGrandTotalQuot2\">".number_format($grandTotal2,2)."</td>";
			$trNya2 .= "<tr>";
			$trNya2 .= "<tr>";
				$trNya2 .= "<td align=\"right\" colspan=\"9\">Kurs :</td>";
				$trNya2 .= "<td align=\"right\">";
					$trNya2 .= "<input style=\"text-align:right;\" type=\"text\" id=\"txtKursQuot2\" value=\"".$kurs2."\" class=\"form-control input-sm\" oninput=\"konversiIdr('2');\">";
				$trNya2 .= "</td>";
			$trNya2 .= "<tr>";
			$trNya2 .= "<tr>";
				$trNya2 .= "<td align=\"right\" colspan=\"9\">Total After Kurs (Rp) :</td>";
				$trNya2 .= "<td align=\"right\" id=\"idTotalAfterKursQuot2\">".number_format($grandAfterKurs2,2)."</td>";
			$trNya2 .= "<tr>";
			$trNya3 .= "<tr>";
				$trNya3 .= "<td align=\"right\" colspan=\"9\">Total :</td>";
				$trNya3 .= "<td align=\"center\" id=\"idTotalQuot3\">".number_format($total3,2)."</td>";
			$trNya3 .= "<tr>";
			$trNya3 .= "<tr>";
				$trNya3 .= "<td align=\"right\" colspan=\"9\">Discount :</td>";
				$trNya3 .= "<td align=\"right\">";
					$trNya3 .= "<input style=\"text-align:right;\" type=\"text\" id=\"txtDiscountQuot3\" value=\"".$disc3."\" class=\"form-control input-sm\" oninput=\"sumDataByDiscPPnOngkir('3');\">";
				$trNya3 .= "</td>";
			$trNya3 .= "<tr>";
			$trNya3 .= "<tr>";
				$trNya3 .= "<td align=\"right\" colspan=\"9\">After Discount :</td>";
				$trNya3 .= "<td align=\"right\" id=\"idAfterDiscQuot3\">".number_format($afterDisc3,2)."</td>";
			$trNya3 .= "<tr>";
			$trNya3 .= "<tr>";
				$trNya3 .= "<td align=\"right\" colspan=\"9\">PPN :</td>";
				$trNya3 .= "<td align=\"right\">";
					$trNya3 .= "<input style=\"text-align:right;\" type=\"text\" id=\"txtPPNQuot3\" value=\"".$ppn3."\" class=\"form-control input-sm\" oninput=\"sumDataByDiscPPnOngkir('3');\">";
				$trNya3 .= "</td>";
			$trNya3 .= "<tr>";
			$trNya3 .= "<tr>";
				$trNya3 .= "<td align=\"right\" colspan=\"9\">Delivery Cost :</td>";
				$trNya3 .= "<td align=\"right\">";
					$trNya3 .= "<input style=\"text-align:right;\" type=\"text\" id=\"txtDeliveryQuot3\" value=\"".$ongkir3."\" class=\"form-control input-sm\" oninput=\"sumDataByDiscPPnOngkir('3');\">";
				$trNya3 .= "</td>";
			$trNya3 .= "<tr>";
			$trNya3 .= "<tr>";
				$trNya3 .= "<td align=\"right\" colspan=\"9\">Grand Total :</td>";
				$trNya3 .= "<td align=\"right\" id=\"idGrandTotalQuot3\">".number_format($grandTotal3,2)."</td>";
			$trNya3 .= "<tr>";
			$trNya3 .= "<tr>";
				$trNya3 .= "<td align=\"right\" colspan=\"9\">Kurs :</td>";
				$trNya3 .= "<td align=\"right\">";
					$trNya3 .= "<input style=\"text-align:right;\" type=\"text\" id=\"txtKursQuot3\" value=\"".$kurs3."\" class=\"form-control input-sm\" oninput=\"konversiIdr('3');\">";
				$trNya3 .= "</td>";
			$trNya3 .= "<tr>";
			$trNya3 .= "<tr>";
				$trNya3 .= "<td align=\"right\" colspan=\"9\">Total After Kurs (Rp) :</td>";
				$trNya3 .= "<td align=\"right\" id=\"idTotalAfterKursQuot3\">".number_format($grandAfterKurs3,2)."</td>";
			$trNya3 .= "<tr>";
			
			$dataOut['idReq'] = $id;
			$dataOut['trNya1'] = $trNya1;
			$dataOut['trNya2'] = $trNya2;
			$dataOut['trNya3'] = $trNya3;
			$dataOut['dataQuot'] = $dataQuot;
			$dataOut['remarkOffered'] = $rslReq[0]->remark_offered;

			$dbNya = $erpControl->cekDbErpByCompany($rsl[0]->ship_company);
			$dataOut['optSupplier'] = $erpControl->getOptSupplierErp("",$dbNya);
		}

		print(json_encode($dataOut));
	}

	function submitData()
	{
		$idReq = $_POST['id'];
		$updateData = array();
		$status = "";
		try {

			$updateData['submit_offered'] = '1';
			$updateData['revise_offered'] = '0';
			$updateData['last_send_mail'] = "0000-00-00";
			$updateData['revise_remark_offered'] = '';
			$whereNya = "id = '".$idReq."'";

			$this->mpurchasing->updateData($whereNya,$updateData,"request");

			//$this->sendRemaindByEmail($idReq);

			$status = "Success..!!";
		} catch (Exception $ex) {
			$status = "Failed..!!";
		}
		print json_encode($status);
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
			$subjectNya = "Approve Request Purchasing For ".$rsl[0]->vessel;
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

		$isiMessage .= "<b>&nbsp;***** ".$vessel." Send Request Purchasing. It requires your Approve to process it. *****</b>";

		$isiMessage.= "<table width=\"800px\" border=\"1\" cellpadding=\"0\" cellspacing=\"0\" style=\"margin-top:30px;\">";
			$isiMessage.= $data["trNya"];
		$isiMessage.= "</table>";

		$isiMessage.= "<p style=\"margin-top:20px;\"><b><i>:::</i> Detail Offered <i>:::</i></b></p>";

		$isiMessage.= "<table width=\"800px\" border=\"1\" cellpadding=\"0\" cellspacing=\"0\">";
			$isiMessage.= "<tr>";
				$isiMessage.= "<td align=\"center\">Name of Article</td>";
				$isiMessage.= "<td align=\"center\">Code / Part No</td>";
				$isiMessage.= "<td align=\"center\">Unit</td>";
				$isiMessage.= "<td align=\"center\">Request</td>";
				$isiMessage.= "<td align=\"center\">Vendor 1</td>";
				$isiMessage.= "<td align=\"center\">Vendor 2</td>";
				$isiMessage.= "<td align=\"center\">Vendor 3</td>";
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
				$price1Nya = $value->quot_curr1." ".number_format(($value->quot_price1 * $value->approved_order),2);
			}
			if($value->quot_price2 > 0)
			{
				$price2Nya = $value->quot_curr2." ".number_format(($value->quot_price2 * $value->approved_order),2);
			}
			if($value->quot_price3 > 0)
			{
				$price3Nya = $value->quot_curr3." ".number_format(($value->quot_price3 * $value->approved_order),2);
			}

			$trDet .= "<tr>";
				$trDet .= "<td style=\"vertical-align:top;width:27%;color:#000080;font-size:11px;\">".$value->article_name."</td>";
				$trDet .= "<td align=\"center\" style=\"vertical-align:top;width:12%;color:#000080;font-size:11px;\">".$value->code_no."</td>";
				$trDet .= "<td align=\"center\" style=\"vertical-align:top;width:8%;color:#000080;font-size:11px;\">".$value->unit."</td>";
				$trDet .= "<td align=\"center\" style=\"vertical-align:top;width:8%;color:#000080;font-size:11px;\">".$value->approved_order."</td>";
				$trDet .= "<td align=\"right\" style=\"vertical-align:top;width:15%;color:#000080;font-size:11px;\">".$price1Nya."</td>";
				$trDet .= "<td align=\"right\" style=\"vertical-align:top;width:15%;color:#000080;font-size:11px;\">".$price2Nya."</td>";
				$trDet .= "<td align=\"right\" style=\"vertical-align:top;width:15%;color:#000080;font-size:11px;\">".$price3Nya."</td>";
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

	function getHandleVessel($userId = "")
	{
		$vslNya = "";
		$tempVsl = array();

		$sql = "SELECT * FROM user WHERE id = '".$userId."' ";
		$rsl = $this->mpurchasing->getDataQuery($sql);

		if(count($rsl) > 0)
		{
			$tempVsl = explode(",",$rsl[0]->vessel);

			for ($lan=0; $lan < count($tempVsl); $lan++)
			{
				if($vslNya == "")
				{
					$vslNya = "'".$tempVsl[$lan]."'";
				}else{
					$vslNya .= ",'".$tempVsl[$lan]."'";
				}
			}
		}

		return $vslNya;
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
		$rsl = $this->mpurchasing->getDataQueryDb2($sql);

		foreach ($rsl as $key => $value)
		{
			$optNya .= "<option value='".$value->name."'>".$value->name."</option>";
		}
		return $optNya;
	}

	function getSupplierErp()
	{
		$erpControl = new MasErpApi();
		$company = $_POST['companyNya'];

		$dbNya = $erpControl->cekDbErpByCompany($company);
		$optSupplier = $erpControl->getOptSupplierErp("return",$dbNya);

		print $optSupplier;
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
	














}