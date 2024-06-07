<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MasErpApi extends CI_Controller {

	function __construct()
	{
		parent::__construct();		
		$this->load->model('mpurchasing');
		$this->load->helper(array('form', 'url'));
		$this->load->library('curl');
		$this->urlApiLogin = "http://10.0.2.8/api/token";
		// $this->urlApiArea = "http://10.0.2.8/api/public/area/jkt";
		// $this->urlApiGetList = "http://10.0.2.8/api/public/area?pageSize=100&pageNumber=1&searchText&columnName";
		// $this->urlApiInsertEdit = "http://10.0.2.8/api/public/area";
		// $this->urlApiDelete = "http://10.0.2.8/api/public/area?areaCode=tes";
		$this->urlApiInsertEdit = "http://10.0.2.8/api/public/purchaseOrder";
		$this->urlApiGetList = "http://10.0.2.8/api/public/purchaseOrder?pageSize=100&pageNumber=1";
	}

	function index()
	{
		echo "<pre>";
		$rsl = $this->loginErp("asi");

		if($rsl['stCek'] == "true")
		{
			//$this->getData($rsl['token'],$this->urlApiGetList);
			// $this->insertData($rsl['token'],$this->urlApiInsertEdit);
			// $this->editData($rsl['token'],$this->urlApiInsertEdit);
			// $this->deleteData($rsl['token'],$this->urlApiDelete);
		}else{
			print_r($rsl['message']);
		}

		exit;
	}

	function actionERP($initCmp = "",$type = "",$dataIns = array())
	{
		$rsl = $this->loginErp($initCmp);

		if($rsl['stCek'] == "true")
		{
			if($type == "save")
			{
				$returnNya = $this->insertData($rsl['token'],$this->urlApiInsertEdit,$dataIns);				
				return $returnNya;
			}
		}else{
			return $rsl['message'];
		}
	}

	function getData($token = "",$url = "")
	{
		$curl = curl_init();
		$responses = "";

		$data = array();

		curl_setopt_array($curl, array(
		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json',
			'Authorization: Bearer '.$token
		),
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_CUSTOMREQUEST => 'GET',
		CURLOPT_SSL_VERIFYHOST => 0,
		CURLOPT_SSL_VERIFYPEER => 0,
		CURLOPT_POSTFIELDS => json_encode($data)
		));
		
		$response = curl_exec($curl);
		curl_close($curl);

		if($response)//jika url benar
		{			
			$responses = json_decode($response);
		}else{
			$stCek = "false";
			$stMassage = "Error URL..!!";
		}

		print_r($responses);
	}

	function insertData($token = "",$url = "",$dataIns = array())
	{
		$curl = curl_init();
		
		curl_setopt_array($curl, array(
		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json',
			'Authorization: Bearer '.$token
		),
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_SSL_VERIFYHOST => 0,
		CURLOPT_SSL_VERIFYPEER => 0,
		CURLOPT_POSTFIELDS => json_encode($dataIns)
		));
		
		$response = curl_exec($curl);
		curl_close($curl);

		if($response)//jika url benar
		{			
			$responses = "Success..!!";
			// $responses = json_decode($response);
		}else{
			$responses = "Error URL..!!";
		}

		return $responses;
	}

	function editData($token = "",$url = "")
	{
		$dataOut = array();

		$curl = curl_init();

		$data = array
	    (
	    	"areaCode"=>"TES",
	   		"areaName"=>"TESAREA321"
	    );

		curl_setopt_array($curl, array(
		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json',
			'Authorization: Bearer '.$token
		),
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_CUSTOMREQUEST => 'PUT',
		CURLOPT_SSL_VERIFYHOST => 0,
		CURLOPT_SSL_VERIFYPEER => 0,
		CURLOPT_POSTFIELDS => json_encode($data)
		));
		
		$response = curl_exec($curl);
		curl_close($curl);

		if($response)//jika url benar
		{			
			$responses = json_decode($response);
		}else{
			$stCek = "false";
			$stMassage = "Error URL..!!";
		}

		print_r($responses);
	}

	function deleteData($token = "",$url = "")
	{
		$curl = curl_init();
		$responses = "";

		$data = array();

		curl_setopt_array($curl, array(
		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json',
			'Authorization: Bearer '.$token
		),
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_CUSTOMREQUEST => 'DELETE',
		CURLOPT_SSL_VERIFYHOST => 0,
		CURLOPT_SSL_VERIFYPEER => 0,
		CURLOPT_POSTFIELDS => json_encode($data)
		));
		
		$response = curl_exec($curl);
		curl_close($curl);

		if($response)//jika url benar
		{			
			$responses = json_decode($response);
		}else{
			$stCek = "false";
			$stMassage = "Error URL..!!";
		}

		print_r($responses);
	}

	function loginErp ($companyCode = "")
	{
		$dataOut = array();
		$stCek = "";
		$stMassage = "";
		$token = "";
		$usrName = "adm";
		$password = "4ndh1k4";

		$curl = curl_init();

	 	$usrName = "admin";
		$password = "ERPandhika24";

	    $data = array
	    (
	    	"companyCode"=>$companyCode,
	   		"username"=>$usrName,
	    	"password"=>$password,
	    	"hostAddress"=>"."
	    );

		curl_setopt_array($curl, array(
		CURLOPT_HTTPHEADER => array(
		    'Content-Type: application/json'
		    ),
		CURLOPT_URL => $this->urlApiLogin,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_SSL_VERIFYHOST => 0,
		CURLOPT_SSL_VERIFYPEER => 0,
		CURLOPT_POSTFIELDS => json_encode($data)
		));
		
		$response = curl_exec($curl);
		curl_close($curl);

		if($response)//jika url benar
		{			
			$responses = json_decode($response);

			if(array_key_exists('access_token', (array)$responses))//jika sukses
			{			
				$stCek = "true";
				$token = $responses->access_token;
			}else{
				$stCek = "false";

				if(array_key_exists('message', (array)$responses))
				{
					$stMassage = $responses->message;
				}else{
					$stMassage = "Error Page..!!";
				}
			}
		}else{
			$stCek = "false";
			$stMassage = "Error URL..!!";
		}

		$dataOut['stCek'] = $stCek;
		$dataOut['message'] = $stMassage;
		$dataOut['token'] = $token;
		
		return $dataOut;
	}

	function getOptSupplierErp($returnType = "",$dbNya = "",$optSlc = "")
	{
		$opt = "";

		$dbThn = $this->cekDbThn($dbNya);

		$sql = "SELECT KodeLgn,NamaLgn FROM ".$dbThn."..Vendors WHERE NonAktif = 0 ORDER BY NamaLgn ASC ";
		// $rsl = $this->mpurchasing->querySqlServerErp($sql,$dbNya);
		$rsl = $this->mpurchasing->querySqlServerErp($sql,"sqlSrvErp_and");

		if(count($rsl) > 0)
		{
			$opt .= "<option value=\"\"><=></option>";
			foreach ($rsl as $key => $value)
			{
				$slcNya = "";

				if($optSlc != "")
				{
					if($optSlc == $value->KodeLgn)
					{
						$slcNya = "selected=\"selected\"";
					}
				}

				$opt .= "<option value=\"".$value->KodeLgn."\" ".$slcNya.">".$value->KodeLgn." <=> ".$value->NamaLgn."</option>";
			}
		}

		if($returnType == "")
		{
			return $opt;
		}else{
			print json_encode($opt);
		}
	}

	function getOptItemCodeErp($inventoryId = "",$returnType = "",$dbNya = "",$slcOpt = "")
	{
		$opt = "";
		$whereNya = " WHERE SUBSTRING(KodeItem,1,3) = '513' OR SUBSTRING(KodeItem,1,5) = '11616' ";

		$dbThn = $this->cekDbThn($dbNya);

		if($inventoryId != "")
		{
			$whereNya .= " AND InventoryId = '".$inventoryId."' ";
		}

		$sql = "SELECT * FROM ".$dbThn."..Inventories ".$whereNya;
		// $rsl = $this->mpurchasing->querySqlServerErp($sql,$dbNya);
		$rsl = $this->mpurchasing->querySqlServerErp($sql,"sqlSrvErp_and");

		if(count($rsl) > 0)
		{
			foreach ($rsl as $key => $value)
			{
				$slcTedNya = "";
				if($slcOpt == $value->KodeItem)
				{
					$slcTedNya = "selected=\"selected\"";
				}
				$opt .= "<option value=\"".$value->KodeItem."\" ".$slcTedNya.">".$value->NamaBarang."</option>";
			}
		}

		if($returnType == "")
		{
			return $opt;
		}else{
			print json_encode($opt);
		}
	}

	function getOptItemSatuanErp($typeReturn = "",$itemCodeNya = "",$dbTempNya = "",$satuanNya = "")
	{
		$opt = "";

		if($typeReturn == "")
		{
			$itemCode = $_POST['itemCode'];
			$tempDBNya = $_POST['tempDBNya'];
			$dbNya = $_POST['dbErp'];
		}else{
			$itemCode = $itemCodeNya;
			$tempDBNya = "";
			$dbNya = $dbTempNya;
		}

		$dbThn = $this->cekDbThn($dbNya);

		$sql = "SELECT * FROM ".$dbThn."..Inventories WHERE KodeItem = '".$itemCode."'";
		// $rsl = $this->mpurchasing->querySqlServerErp($sql,$tempDBNya);
		$rsl = $this->mpurchasing->querySqlServerErp($sql,"sqlSrvErp_and");

		if(count($rsl) > 0)
		{
			$slcTed1 = "";
			$slcTed2 = "";
			$slcTed3 = "";
			$slcTed4 = "";

			if($satuanNya == "1"){ $slcTed1 = "selected=\"selected\""; }
			if($satuanNya == "2"){ $slcTed2 = "selected=\"selected\""; }
			if($satuanNya == "3"){ $slcTed3 = "selected=\"selected\""; }
			if($satuanNya == "4"){ $slcTed4 = "selected=\"selected\""; }

			$opt .= "<option value=\"1\" ".$slcTed1.">".$rsl[0]->KodeSatuan."</option>";

			if($rsl[0]->Konversi2 == "1")
			{
				$opt .= "<option value=\"2\" ".$slcTed2.">".$rsl[0]->Satuan2."</option>";
			}
			if($rsl[0]->Konversi3 == "1")
			{
				$opt .= "<option value=\"3\" ".$slcTed3.">".$rsl[0]->Satuan3."</option>";
			}
			if($rsl[0]->Konversi4 == "1")
			{
				$opt .= "<option value=\"4\" ".$slcTed4.">".$rsl[0]->Satuan4."</option>";
			}
		}

		if($typeReturn == "")
		{
			print json_encode($opt);
		}else{
			return $opt;
		}
	}

	function cekDbThn($dbNya = "")
	{
		$dbThnNya = "";
		$yNow = date("Y");
		$ystart = "2023";

		if($dbNya == "sqlSrvErp_ibp")
		{
			$ystart = "2024";
		}

		$sl = ($yNow - $ystart)+1;

		if($dbNya == "sqlSrvErp_and")
		{
			$dbThnNya = "ANDdb00".$sl;
		}
		else if($dbNya == "sqlSrvErp_ady")
		{
			$dbThnNya = "ADNdb00".$sl;
		}
		else if($dbNya == "sqlSrvErp_aes")
		{
			$dbThnNya = "AESdb00".$sl;
		}
		else if($dbNya == "sqlSrvErp_asi")
		{
			$dbThnNya = "ASIdb00".$sl;
		}
		else if($dbNya == "sqlSrvErp_ibp")
		{
			$dbThnNya = "IBPdb00".$sl;
		}

		return $dbThnNya;
	}

	function cekDbErpByCompany($company)
	{
		$dbName = "";		

		if(strstr(strtolower($company),"andhika line"))
		{
			$dbName = "sqlSrvErp_and";
		}
		else if(strstr(strtolower($company),"adnyana") OR strtolower($company) == "pt. adnyana")
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
















}