<script language="javascript">
window.open('','_self','');
</script>
<?php
	ini_set('max_execution_time', '120');
	$obj = new notif();
	$dataPrint = array();
	$dateNow = date('Y-m-d');
	$obj->connect();
	$stIns = "";
	$ttlIns = 0;
	
	$obj->cekDataToSI();
	$obj->cekDataToApproveDeckOrEngine();
	$obj->cekDataToCreateQuotation();
	$obj->cekDataToApproveKadepPurch();
	$obj->cekDataToApproveKadivPurch();
	$obj->cekDataToCheckKadivShipMngmt();
	$obj->cekDataToCheckCoo();
	$obj->cekDataToCheckFinance();
	$obj->cekDataToCreatePO();
	$obj->cekDataToFinalFinance();
	
	class notif
	{
		function connect()
		{
			$host = "localhost";
			$user = "root";
			$pass = "";
			$db = "purchasing";
			
			mysql_connect($host,$user,$pass);
			mysql_select_db($db);
			if (mysqli_connect_errno())
			{
				echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}
		}
		
		function cekDataToSI()
		{
			$total = 0;
			$dataEmail = array();			
			
			$sqlMail = " SELECT vessel,email FROM send_mail WHERE sts_delete = '0' AND (vessel != 'purchasing' AND vessel != 'approve') ";
			$queryMail = $this->mysqlQuery($sqlMail);
			
			while($rslMail = mysql_fetch_array($queryMail))
			{
				$dataEmail[$rslMail['email']][] = $rslMail['vessel'];
			}
			
			foreach ($dataEmail as $key => $val)
			{
				$vesselIn = "";
				foreach ($val as $keys => $value)
				{
					if($vesselIn == "")
					{
						$vesselIn = "'".$value."'";
					}else{
						$vesselIn .= ",'".$value."'";
					}					
				}
				$this->sendRemaindByEmailToSI($key,$vesselIn);
			}
			return $total;
		}
		
		function sendRemaindByEmailToSI($mailNya = '',$vesselIn = '')
		{
			$subjectNya = "";
			$isiMessage = "";
			$trNya = "";
			$stSend = "";
			$no = 1;
			$dateNow = date("Y-m-d");
			$idReq = "";
			
			$sql = "SELECT * FROM request WHERE master_check = '1' AND chief_check = '1' AND sts_delete = '0' AND st_data NOT IN('1','2') AND check_order = '0' AND submit_check = '0' AND vessel IN (".$vesselIn.") ORDER BY vessel,date_request ASC ";
			
			$query = $this->mysqlQuery($sql);
			
			while($rsl = mysql_fetch_array($query))
			{
				if($rsl['last_send_mail'] != $dateNow)
				{
					if($idReq == ""){ $idReq = "'".$rsl['id']."'"; } else { $idReq .= ",'".$rsl['id']."'"; }
					$trNya .= "<tr>";
						$trNya .= "<td align=\"center\">".$no."</td>";
						$trNya .= "<td>&nbsp".$rsl['vessel']."</td>";
						$trNya .= "<td>&nbsp".$rsl['app_no']."</td>";
						$trNya .= "<td>&nbsp".$rsl['department']."</td>";
						$trNya .= "<td align=\"center\">&nbsp".$this->convertReturnName($rsl['date_request'])."</td>";
					$trNya .= "</tr>";
					$stSend = "send";
					$no++;
				}
			}

			if($stSend == "send")
			{
				$isiMessage = "";

				$sqlUpdate = "UPDATE request SET last_send_mail = '".$dateNow."' WHERE id IN(".$idReq.") ";
				$this->mysqlQuery($sqlUpdate);
				
				$isiMessage .= "<p>";
					$isiMessage .= "*************************************************<br>";
					$isiMessage .= "PLEASE DO NOT REPLY THIS EMAIL..!!<br>";
					$isiMessage .= "*************************************************<br>";
				$isiMessage .= "</p>";

				$isiMessage .= "<b>&nbsp;***** List of Purchase Request. Please Check & Process It. *****</b>";

				$isiMessage.= "<table width=\"800px\" border=\"1\" cellpadding=\"0\" cellspacing=\"0\" style=\"margin-top:20px;margin-bottom:20px;\">";
					$isiMessage .= "<tr style=\"background-color:#006FA2;\">";
						$isiMessage .= "<td style=\"vertical-align:middle;text-align:center;width:5%;height:30px;color:#FFF;font-size:18px;\"><b>No</b></td>";
						$isiMessage .= "<td style=\"vertical-align:middle;text-align:center;width:35%;height:30px;color:#FFF;font-size:18px;\"><b>Vessel</b></td>";
						$isiMessage .= "<td style=\"vertical-align:middle;text-align:center;width:20%;height:30px;color:#FFF;font-size:18px;\"><b>App No</b></td>";
						$isiMessage .= "<td style=\"vertical-align:middle;text-align:center;width:20%;height:30px;color:#FFF;font-size:18px;\"><b>Department</b></td>";
						$isiMessage .= "<td style=\"vertical-align:middle;text-align:center;width:20%;height:30px;color:#FFF;font-size:18px;\"><b>Tanggal</b></td>";
					$isiMessage .= "</tr>";
					$isiMessage .= $trNya;
				$isiMessage.= "</table>";

				$isiMessage .= "<p>To respon this Request, please check <a href=\"http://apps.andhika.com/purchasing\" target=\"_blank\">Purchasing System</a></p>";

				$isiMessage .= "<p>";
					$isiMessage .= "*************************************************<br>";
					$isiMessage .= "END OF NOTIFICATION<br>";
					$isiMessage .= "*************************************************<br>";
				$isiMessage .= "</p>";

				$subjectNya = "Request Purchasing from Vessel";
				// echo "<pre>";print_r($isiMessage);exit;
				mail($mailNya, $subjectNya, $isiMessage, $this->headers());
			}
		}

		function cekDataToApproveDeckOrEngine()
		{
			$total = 0;
			$no = 1;
			$tempData = array();
			$dataEmail = array();
			$email = "";
			$emailSI = "";
			$trNya = "";
			$dateNow = date("Y-m-d");
			$idReq = "";

			$sql = "SELECT * FROM request WHERE sts_delete = '0' AND check_order = '1' AND submit_check = '1' AND req_check_approve = '0' AND st_data NOT IN('1','2') ORDER BY vessel,date_request ASC ";
			$query = $this->mysqlQuery($sql);

			while($rsl = mysql_fetch_array($query))
			{
				if($rsl['last_send_mail'] != $dateNow)
				{
					if($idReq == ""){ $idReq = "'".$rsl['id']."'"; } else { $idReq .= ",'".$rsl['id']."'"; }

					$trNya .= "<tr>";
						$trNya .= "<td align=\"center\">".$no."</td>";
						$trNya .= "<td>&nbsp".$rsl['vessel']."</td>";
						$trNya .= "<td>&nbsp".$rsl['app_no']."</td>";
						$trNya .= "<td>&nbsp".$rsl['department']."</td>";
						$trNya .= "<td align=\"center\">&nbsp".$this->convertReturnName($rsl['date_request'])."</td>";
					$trNya .= "</tr>";

					if($rsl['department'] == "DECK")
					{
						$mailSelect = "email_deck";
					}else{
						$mailSelect = "email_engine";
					}

					$sqlMail = " SELECT email,".$mailSelect." FROM send_mail WHERE vessel LIKE '%".$rsl['vessel']."%' AND sts_delete = '0' ";
					$queryMail = $this->mysqlQuery($sqlMail);
					while($rslMail = mysql_fetch_array($queryMail))
					{
						$email = $rslMail[$mailSelect];
						$emailSI = $rslMail['email'];
					}

					$tempData[$rsl['department']][$no]['department'] = $rsl['department'];
					$tempData[$rsl['department']][$no]['email'] = $email;
					$tempData[$rsl['department']][$no]['emailSI'] = $emailSI;
					$tempData[$rsl['department']][$no]['trNya'] = $trNya;
					$no++;
				}
			}

			if($idReq != "")
			{
				$sqlUpdate = "UPDATE request SET last_send_mail = '".$dateNow."' WHERE id IN(".$idReq.") ";
				$this->mysqlQuery($sqlUpdate);
			}

			foreach ($tempData as $key => $value)
			{
				foreach ($value as $keys => $val)
				{
					$this->sendRemaindByEmailToApproveDeckOrEngine($val['email'],$val['trNya'],$val['emailSI']);
				}
			}
			
			return $total;
		}
		
		function sendRemaindByEmailToApproveDeckOrEngine($mailNya = "",$trNya = "",$mailSI = "")
		{
			$tempData = array();
			$subjectNya = "";
			$isiMessage = "";

			if($trNya != "")
			{
				$isiMessage = "";

				$isiMessage .= "<p>";
					$isiMessage .= "*************************************************<br>";
					$isiMessage .= "PLEASE DO NOT REPLY THIS EMAIL..!!<br>";
					$isiMessage .= "*************************************************<br>";
				$isiMessage .= "</p>";

				$isiMessage .= "<b>&nbsp;***** List of Purchase Request. It Requires your Approval. *****</b>";

				$isiMessage.= "<table width=\"800px\" border=\"1\" cellpadding=\"0\" cellspacing=\"0\" style=\"margin-top:20px;margin-bottom:20px;\">";
					$isiMessage .= "<tr style=\"background-color:#006FA2;\">";
						$isiMessage .= "<td style=\"vertical-align:middle;text-align:center;width:5%;height:30px;color:#FFF;font-size:18px;\"><b>No</b></td>";
						$isiMessage .= "<td style=\"vertical-align:middle;text-align:center;width:35%;height:30px;color:#FFF;font-size:18px;\"><b>Vessel</b></td>";
						$isiMessage .= "<td style=\"vertical-align:middle;text-align:center;width:20%;height:30px;color:#FFF;font-size:18px;\"><b>App No</b></td>";
						$isiMessage .= "<td style=\"vertical-align:middle;text-align:center;width:20%;height:30px;color:#FFF;font-size:18px;\"><b>Department</b></td>";
						$isiMessage .= "<td style=\"vertical-align:middle;text-align:center;width:20%;height:30px;color:#FFF;font-size:18px;\"><b>Tanggal</b></td>";
					$isiMessage .= "</tr>";
					$isiMessage .= $trNya;
				$isiMessage.= "</table>";

				$isiMessage .= "<p>To respon this Request, please check <a href=\"http://apps.andhika.com/purchasing\" target=\"_blank\">Purchasing System</a></p>";

				$isiMessage .= "<p>";
					$isiMessage .= "*************************************************<br>";
					$isiMessage .= "END OF NOTIFICATION<br>";
					$isiMessage .= "*************************************************<br>";
				$isiMessage .= "</p>";
			
				$subjectNya = "Approve Request List Purchasing";
				//$mailNya = "ahmad.maulana@andhika.com";
				// echo "<pre>";print_r($isiMessage);//exit;
				mail($mailNya, $subjectNya, $isiMessage, $this->headers($mailSI));
			}
		}
		
		function cekDataToCreateQuotation()
		{
			$total = 0;
			$email = "";		
			
			$sqlMail = " SELECT vessel,email FROM send_mail WHERE sts_delete = '0' AND vessel = 'purchasing' ";
			$queryMail = $this->mysqlQuery($sqlMail);
			
			while($rslMail = mysql_fetch_array($queryMail))
			{
				$email = $rslMail['email'];
			}
			
			if($email != "")
			{
				$this->sendRemaindByEmailToCreateQuotation($email);
			}
			
			return $total;
		}
		
		function sendRemaindByEmailToCreateQuotation($mailNya = '')
		{
			$subjectNya = "";
			$isiMessage = "";
			$trNya = "";
			$stSend = "";
			$no = 1;
			$dateNow = date("Y-m-d");
			$idReq = "";
			$vessel = "";
			$emailSI = "";
			
			$sql = "SELECT * FROM request WHERE sts_delete = '0' AND submit_check = '1' AND req_check_approve = '1' AND st_data NOT IN('1','2') AND create_offered = '0'";
			$query = $this->mysqlQuery($sql);
			
			while($rsl = mysql_fetch_array($query))
			{
				if($rsl['last_send_mail'] != $dateNow)
				{
					if($idReq == ""){ $idReq = "'".$rsl['id']."'"; } else { $idReq .= ",'".$rsl['id']."'"; }
					$trNya .= "<tr>";
						$trNya .= "<td align=\"center\"> ".$no." </td>";
						$trNya .= "<td>&nbsp".$rsl['vessel']."</td>";
						$trNya .= "<td>&nbsp".$rsl['app_no']."</td>";
						$trNya .= "<td>&nbsp".$rsl['department']."</td>";
						$trNya .= "<td align=\"center\">&nbsp".$this->convertReturnName($rsl['date_request'])."</td>";
					$trNya .= "</tr>";
					$stSend = "send";

					if($vessel == "")
					{
						$vessel = "'".$rsl['vessel']."'";
					}else{ 
						$vessel .= ",'".$rsl['vessel']."'";
					}

					$no++;
				}
			}

			if($vessel != "")
			{
				$sqlMail = " SELECT email FROM send_mail WHERE sts_delete = '0' AND vessel IN (".$vessel.") ";
				$queryMail = $this->mysqlQuery($sqlMail);
				
				while($rslMail = mysql_fetch_array($queryMail))
				{
					if($emailSI == "")
					{
						$emailSI = $rslMail['email'];
					}else{ 
						$emailSI .= ",".$rslMail['email'];
					}
				}
			}
			
			$isiMessage = "";

			$isiMessage .= "<p>";
				$isiMessage .= "*************************************************<br>";
				$isiMessage .= "PLEASE DO NOT REPLY THIS EMAIL..!!<br>";
				$isiMessage .= "*************************************************<br>";
			$isiMessage .= "</p>";

			$isiMessage .= "<b>&nbsp;***** List of Purchase Request. Create Quotation for it. *****</b>";

			$isiMessage.= "<table width=\"800px\" border=\"1\" cellpadding=\"0\" cellspacing=\"0\" style=\"margin-top:20px;margin-bottom:20px;\">";
				$isiMessage .= "<tr style=\"background-color:#006FA2;\">";
					$isiMessage .= "<td style=\"vertical-align:middle;text-align:center;width:5%;height:30px;color:#FFF;font-size:18px;\"><b>No</b></td>";
					$isiMessage .= "<td style=\"vertical-align:middle;text-align:center;width:35%;height:30px;color:#FFF;font-size:18px;\"><b>Vessel</b></td>";
					$isiMessage .= "<td style=\"vertical-align:middle;text-align:center;width:20%;height:30px;color:#FFF;font-size:18px;\"><b>App No</b></td>";
					$isiMessage .= "<td style=\"vertical-align:middle;text-align:center;width:20%;height:30px;color:#FFF;font-size:18px;\"><b>Department</b></td>";
					$isiMessage .= "<td style=\"vertical-align:middle;text-align:center;width:20%;height:30px;color:#FFF;font-size:18px;\"><b>Tanggal</b></td>";
				$isiMessage .= "</tr>";
				$isiMessage .= $trNya;
			$isiMessage.= "</table>";

			$isiMessage .= "<p>To respon this Request, please check <a href=\"http://apps.andhika.com/purchasing\" target=\"_blank\">Purchasing System</a></p>";

			$isiMessage .= "<p>";
				$isiMessage .= "*************************************************<br>";
				$isiMessage .= "END OF NOTIFICATION<br>";
				$isiMessage .= "*************************************************<br>";
			$isiMessage .= "</p>";
			
			if($stSend == "send")
			{
				$sqlUpdate = "UPDATE request SET last_send_mail = '".$dateNow."' WHERE id IN(".$idReq.") ";
				$this->mysqlQuery($sqlUpdate);

				$subjectNya = "Create Quotation Price";
				// $mailNya = "ahmad.maulana@andhika.com";
				// echo "<pre>";print_r($isiMessage);//exit;
				mail($mailNya, $subjectNya, $isiMessage, $this->headers($emailSI));
			}
		}
		
		function cekDataToApproveKadepPurch()
		{
			$total = 0;
			$email = "";		
			
			$sqlMail = " SELECT email_kadep_purchase FROM send_mail WHERE sts_delete = '0' AND vessel = 'purchasing' ";
			$queryMail = $this->mysqlQuery($sqlMail);
			
			while($rslMail = mysql_fetch_array($queryMail))
			{
				$email = $rslMail['email_kadep_purchase'];
			}
			
			if($email != "")
			{
				$this->sendRemaindByEmailToApproveKadepPurch($email);
			}
			
			return $total;
		}
		
		function sendRemaindByEmailToApproveKadepPurch($mailNya = '')
		{
			$subjectNya = "";
			$isiMessage = "";
			$trNya = "";
			$stSend = "";
			$no = 1;
			$dateNow = date("Y-m-d");
			$idReq = "";
			
			$sql = "SELECT * FROM request WHERE sts_delete = '0' AND submit_offered = '1' AND check_approve1 = '0' ";
			$query = $this->mysqlQuery($sql);
			
			while($rsl = mysql_fetch_array($query))
			{
				if($rsl['last_send_mail'] != $dateNow)
				{
					if($idReq == ""){ $idReq = "'".$rsl['id']."'"; } else { $idReq .= ",'".$rsl['id']."'"; }
					$trNya .= "<tr>";
						$trNya .= "<td align=\"center\"> ".$no." </td>";
						$trNya .= "<td>&nbsp".$rsl['vessel']."</td>";
						$trNya .= "<td>&nbsp".$rsl['app_no']."</td>";
						$trNya .= "<td>&nbsp".$rsl['department']."</td>";
						$trNya .= "<td align=\"center\">&nbsp".$this->convertReturnName($rsl['date_request'])."</td>";
					$trNya .= "</tr>";
					$stSend = "send";
					$no++;
				}
			}
			
			$isiMessage = "";

			$isiMessage .= "<p>";
				$isiMessage .= "*************************************************<br>";
				$isiMessage .= "PLEASE DO NOT REPLY THIS EMAIL..!!<br>";
				$isiMessage .= "*************************************************<br>";
			$isiMessage .= "</p>";

			$isiMessage .= "<b>&nbsp;***** List of Purchase Request, Please Approve This Request *****</b>";

			$isiMessage.= "<table width=\"800px\" border=\"1\" cellpadding=\"0\" cellspacing=\"0\" style=\"margin-top:20px;margin-bottom:20px;\">";
				$isiMessage .= "<tr style=\"background-color:#006FA2;\">";
					$isiMessage .= "<td style=\"vertical-align:middle;text-align:center;width:5%;height:30px;color:#FFF;font-size:18px;\"><b>No</b></td>";
					$isiMessage .= "<td style=\"vertical-align:middle;text-align:center;width:35%;height:30px;color:#FFF;font-size:18px;\"><b>Vessel</b></td>";
					$isiMessage .= "<td style=\"vertical-align:middle;text-align:center;width:20%;height:30px;color:#FFF;font-size:18px;\"><b>App No</b></td>";
					$isiMessage .= "<td style=\"vertical-align:middle;text-align:center;width:20%;height:30px;color:#FFF;font-size:18px;\"><b>Department</b></td>";
					$isiMessage .= "<td style=\"vertical-align:middle;text-align:center;width:20%;height:30px;color:#FFF;font-size:18px;\"><b>Tanggal</b></td>";
				$isiMessage .= "</tr>";
				$isiMessage .= $trNya;
			$isiMessage.= "</table>";

			$isiMessage .= "<p>To respon this Request, please check <a href=\"http://apps.andhika.com/purchasing\" target=\"_blank\">Purchasing System</a></p>";

			$isiMessage .= "<p>";
				$isiMessage .= "*************************************************<br>";
				$isiMessage .= "END OF NOTIFICATION<br>";
				$isiMessage .= "*************************************************<br>";
			$isiMessage .= "</p>";
			
			if($stSend == "send")
			{
				$sqlUpdate = "UPDATE request SET last_send_mail = '".$dateNow."' WHERE id IN(".$idReq.") ";
				$this->mysqlQuery($sqlUpdate);

				$subjectNya = "Approve Draft PO Purchasing";
				//$mailNya = "ahmad.maulana@andhika.com";
				// echo "<pre>";print_r($isiMessage);//exit;
				mail($mailNya, $subjectNya, $isiMessage, $this->headers());
			}
		}

		function cekDataToApproveKadivPurch()
		{
			$total = 0;
			$email = "";
			$trNya = "";			
			$indexNo = 0;
			$tempKadivPurch = array();
			$dateNow = date("Y-m-d");
			$idReq = "";

			$sql = "SELECT * FROM request WHERE sts_delete = '0' AND check_approve1 = '1' AND st_data NOT IN('1','2') AND st_check_kadiv = '1' AND check_approve2 = '0' ";

			$query = $this->mysqlQuery($sql);
			
			while($rsl = mysql_fetch_array($query))
			{
				if($rsl['last_send_mail'] != $dateNow)
				{
					if($idReq == ""){ $idReq = "'".$rsl['id']."'"; } else { $idReq .= ",'".$rsl['id']."'"; }

					$tempKadivPurch[$indexNo]['id'] = $rsl['id'];
					$tempKadivPurch[$indexNo]['vessel'] = $rsl['vessel'];
					$tempKadivPurch[$indexNo]['app_no'] = $rsl['app_no'];
					$tempKadivPurch[$indexNo]['department'] = $rsl['department'];
					$tempKadivPurch[$indexNo]['date_request'] = $this->convertReturnName($rsl['date_request']);

					$indexNo++;
				}
			}

			if(count($tempKadivPurch) > 0)
			{
				$sqlMail = " SELECT * FROM send_mail WHERE sts_delete = '0' AND vessel = 'purchasing' ";
				$queryMail = $this->mysqlQuery($sqlMail);
				$rslMail = mysql_fetch_array($queryMail);

				$sqlUpdate = "UPDATE request SET last_send_mail = '".$dateNow."' WHERE id IN(".$idReq.") ";
				$this->mysqlQuery($sqlUpdate);

				$no = 1;
				foreach ($tempKadivPurch as $key => $rsl)
				{
					$trNya .= "<tr>";
						$trNya .= "<td align=\"center\"> ".$no." </td>";
						$trNya .= "<td>&nbsp".$rsl['vessel']."</td>";
						$trNya .= "<td>&nbsp".$rsl['app_no']."</td>";
						$trNya .= "<td>&nbsp".$rsl['department']."</td>";
						$trNya .= "<td align=\"center\">&nbsp".$rsl['date_request']."</td>";
					$trNya .= "</tr>";

					$no++;
				}

				$subjectNya = "Approve Draft PO Purchasing";
				$listBody = "List of Purchase Request, Please Approve This Request";
				$this->sendRemaindByEmailToApproveAndCheck($subjectNya,$rslMail['email_kadiv_purchase'],$trNya,$listBody);
			}
			
			return $total;
		}

		function cekDataToCheckKadivShipMngmt()
		{
			$total = 0;
			$email = "";
			$trNya = "";			
			$indexNo = 0;
			$tempKadivShipMngmt = array();
			$listBody = "List of Purchase Request, Please Check This Request";
			$subjectNya = "Check Draft PO Purchasing";
			$dateNow = date("Y-m-d");
			$idReq = "";

			$sql = "SELECT * FROM request WHERE sts_delete = '0' AND check_approve1 = '1' AND st_data NOT IN('1','2') AND ((st_check_kadiv = '1' AND check_approve2 = '1' AND check_approve3 = '0') OR (st_check_kadiv = '0' AND check_approve2 = '0' AND check_approve3 = '0')) ";

			$query = $this->mysqlQuery($sql);
			
			while($rsl = mysql_fetch_array($query))
			{
				if($rsl['last_send_mail'] != $dateNow)
				{
					if($idReq == ""){ $idReq = "'".$rsl['id']."'"; } else { $idReq .= ",'".$rsl['id']."'"; }

					$tempKadivShipMngmt[$indexNo]['id'] = $rsl['id'];
					$tempKadivShipMngmt[$indexNo]['vessel'] = $rsl['vessel'];
					$tempKadivShipMngmt[$indexNo]['app_no'] = $rsl['app_no'];
					$tempKadivShipMngmt[$indexNo]['department'] = $rsl['department'];
					$tempKadivShipMngmt[$indexNo]['date_request'] = $this->convertReturnName($rsl['date_request']);

					$indexNo++;
				}
			}

			if(count($tempKadivShipMngmt) > 0)
			{
				$sqlMail = " SELECT * FROM send_mail WHERE sts_delete = '0' AND vessel = 'purchasing' ";
				$queryMail = $this->mysqlQuery($sqlMail);
				$rslMail = mysql_fetch_array($queryMail);

				$sqlUpdate = "UPDATE request SET last_send_mail = '".$dateNow."' WHERE id IN(".$idReq.") ";
				$this->mysqlQuery($sqlUpdate);

				$no = 1;
				foreach ($tempKadivShipMngmt as $key => $rsl)
				{
					$trNya .= "<tr>";
						$trNya .= "<td align=\"center\"> ".$no." </td>";
						$trNya .= "<td>&nbsp".$rsl['vessel']."</td>";
						$trNya .= "<td>&nbsp".$rsl['app_no']."</td>";
						$trNya .= "<td>&nbsp".$rsl['department']."</td>";
						$trNya .= "<td align=\"center\">&nbsp".$rsl['date_request']."</td>";
					$trNya .= "</tr>";

					$no++;
				}

				$this->sendRemaindByEmailToApproveAndCheck($subjectNya,$rslMail['email_kadiv_shipManagement'],$trNya,$listBody);
			}
			
			return $total;
		}

		function cekDataToCheckCoo()
		{
			$total = 0;
			$email = "";
			$trNya = "";
			$indexNo = 0;
			$tempKadivCoo = array();
			$listBody = "List of Purchase Request, Please Check This Request";
			$subjectNya = "Check Draft PO Purchasing";
			$dateNow = date("Y-m-d");
			$idReq = "";

			$sql = "SELECT * FROM request WHERE sts_delete = '0' AND check_approve1 = '1' AND st_data NOT IN('1','2') AND check_approve3 = '1' AND check_approve4 = '0' ";

			$query = $this->mysqlQuery($sql);
			
			while($rsl = mysql_fetch_array($query))
			{
				if($rsl['last_send_mail'] != $dateNow)
				{
					if($idReq == ""){ $idReq = "'".$rsl['id']."'"; } else { $idReq .= ",'".$rsl['id']."'"; }

					$tempKadivCoo[$indexNo]['id'] = $rsl['id'];
					$tempKadivCoo[$indexNo]['vessel'] = $rsl['vessel'];
					$tempKadivCoo[$indexNo]['app_no'] = $rsl['app_no'];
					$tempKadivCoo[$indexNo]['department'] = $rsl['department'];
					$tempKadivCoo[$indexNo]['date_request'] = $this->convertReturnName($rsl['date_request']);

					$indexNo++;
				}
			}

			if(count($tempKadivCoo) > 0)
			{
				$sqlMail = " SELECT * FROM send_mail WHERE sts_delete = '0' AND vessel = 'purchasing' ";
				$queryMail = $this->mysqlQuery($sqlMail);
				$rslMail = mysql_fetch_array($queryMail);

				$sqlUpdate = "UPDATE request SET last_send_mail = '".$dateNow."' WHERE id IN(".$idReq.") ";
				$this->mysqlQuery($sqlUpdate);

				$no = 1;
				foreach ($tempKadivCoo as $key => $rsl)
				{
					$trNya .= "<tr>";
						$trNya .= "<td align=\"center\"> ".$no." </td>";
						$trNya .= "<td>&nbsp".$rsl['vessel']."</td>";
						$trNya .= "<td>&nbsp".$rsl['app_no']."</td>";
						$trNya .= "<td>&nbsp".$rsl['department']."</td>";
						$trNya .= "<td align=\"center\">&nbsp".$rsl['date_request']."</td>";
					$trNya .= "</tr>";

					$no++;
				}

				$this->sendRemaindByEmailToApproveAndCheck($subjectNya,$rslMail['email_coo'],$trNya,$listBody);
			}
			
			return $total;
		}

		function cekDataToCheckFinance()
		{
			$total = 0;
			$email = "";
			$trNya = "";
			$indexNo = 0;
			$tempKadivCoo = array();
			$listBody = "List of Purchase Request, Please Check This Request";
			$subjectNya = "Check Draft PO Purchasing";
			$dateNow = date("Y-m-d");
			$idReq = "";

			$sql = "SELECT * FROM request WHERE sts_delete = '0' AND check_approve1 = '1' AND st_data NOT IN('1','2') AND st_check_finance = '1' AND check_approve4 = '1' AND check_approve5 = '0' ";

			$query = $this->mysqlQuery($sql);
			
			while($rsl = mysql_fetch_array($query))
			{
				if($rsl['last_send_mail'] != $dateNow)
				{
					if($idReq == ""){ $idReq = "'".$rsl['id']."'"; } else { $idReq .= ",'".$rsl['id']."'"; }

					$tempKadivCoo[$indexNo]['id'] = $rsl['id'];
					$tempKadivCoo[$indexNo]['vessel'] = $rsl['vessel'];
					$tempKadivCoo[$indexNo]['app_no'] = $rsl['app_no'];
					$tempKadivCoo[$indexNo]['department'] = $rsl['department'];
					$tempKadivCoo[$indexNo]['date_request'] = $this->convertReturnName($rsl['date_request']);

					$indexNo++;
				}
			}

			if(count($tempKadivCoo) > 0)
			{
				$sqlMail = " SELECT * FROM send_mail WHERE sts_delete = '0' AND vessel = 'purchasing' ";
				$queryMail = $this->mysqlQuery($sqlMail);
				$rslMail = mysql_fetch_array($queryMail);

				$sqlUpdate = "UPDATE request SET last_send_mail = '".$dateNow."' WHERE id IN(".$idReq.") ";
				$this->mysqlQuery($sqlUpdate);

				$no = 1;
				foreach ($tempKadivCoo as $key => $rsl)
				{
					$trNya .= "<tr>";
						$trNya .= "<td align=\"center\"> ".$no." </td>";
						$trNya .= "<td>&nbsp".$rsl['vessel']."</td>";
						$trNya .= "<td>&nbsp".$rsl['app_no']."</td>";
						$trNya .= "<td>&nbsp".$rsl['department']."</td>";
						$trNya .= "<td align=\"center\">&nbsp".$rsl['date_request']."</td>";
					$trNya .= "</tr>";

					$no++;
				}

				$this->sendRemaindByEmailToApproveAndCheck($subjectNya,$rslMail['email_finance'],$trNya,$listBody);
			}
			
			return $total;
		}

		function cekDataToCreatePO()
		{
			$total = 0;
			$email = "";
			$trNya = "";
			$indexNo = 0;
			$tempCreatePo = array();
			$listBody = "List of Purchase Request, Please Create Purchase Order";
			$subjectNya = "Create Purchase Order";
			$dateNow = date("Y-m-d");
			$idReq = "";

			$sql = "SELECT * FROM request WHERE sts_delete='0' AND check_approve4 = '1' AND st_data NOT IN('1','2') AND ((st_check_finance = '1' AND check_approve5 = '1') OR (st_check_finance = '0' AND check_approve4 = '1')) AND create_purchasing = '0' AND submit_purchasing = '0' ";

			$query = $this->mysqlQuery($sql);
			
			while($rsl = mysql_fetch_array($query))
			{
				if($rsl['last_send_mail'] != $dateNow)
				{
					if($idReq == ""){ $idReq = "'".$rsl['id']."'"; } else { $idReq .= ",'".$rsl['id']."'"; }

					$tempCreatePo[$indexNo]['id'] = $rsl['id'];
					$tempCreatePo[$indexNo]['vessel'] = $rsl['vessel'];
					$tempCreatePo[$indexNo]['app_no'] = $rsl['app_no'];
					$tempCreatePo[$indexNo]['department'] = $rsl['department'];
					$tempCreatePo[$indexNo]['date_request'] = $this->convertReturnName($rsl['date_request']);

					$indexNo++;
				}
			}

			if(count($tempCreatePo) > 0)
			{
				$sqlMail = " SELECT * FROM send_mail WHERE sts_delete = '0' AND vessel = 'purchasing' ";
				$queryMail = $this->mysqlQuery($sqlMail);
				$rslMail = mysql_fetch_array($queryMail);

				$sqlUpdate = "UPDATE request SET last_send_mail = '".$dateNow."' WHERE id IN(".$idReq.") ";
				$this->mysqlQuery($sqlUpdate);

				$no = 1;
				foreach ($tempCreatePo as $key => $rsl)
				{
					$trNya .= "<tr>";
						$trNya .= "<td align=\"center\"> ".$no." </td>";
						$trNya .= "<td>&nbsp".$rsl['vessel']."</td>";
						$trNya .= "<td>&nbsp".$rsl['app_no']."</td>";
						$trNya .= "<td>&nbsp".$rsl['department']."</td>";
						$trNya .= "<td align=\"center\">&nbsp".$rsl['date_request']."</td>";
					$trNya .= "</tr>";

					$no++;
				}

				$this->sendRemaindByEmailToApproveAndCheck($subjectNya,$rslMail['email'],$trNya,$listBody);
			}
			
			return $total;
		}

		function sendRemaindByEmailToApproveAndCheck($subjectNya = "",$mailNya = "",$trNya = "",$listBody = "")
		{
			$isiMessage = "";

			$isiMessage .= "<p>";
				$isiMessage .= "*************************************************<br>";
				$isiMessage .= "PLEASE DO NOT REPLY THIS EMAIL..!!<br>";
				$isiMessage .= "*************************************************<br>";
			$isiMessage .= "</p>";

			$isiMessage .= "<b>&nbsp;***** ".$listBody." *****</b>";

			$isiMessage.= "<table width=\"800px\" border=\"1\" cellpadding=\"0\" cellspacing=\"0\" style=\"margin-top:20px;margin-bottom:20px;\">";
				$isiMessage .= "<tr style=\"background-color:#006FA2;\">";
					$isiMessage .= "<td style=\"vertical-align:middle;text-align:center;width:5%;height:30px;color:#FFF;font-size:18px;\"><b>No</b></td>";
					$isiMessage .= "<td style=\"vertical-align:middle;text-align:center;width:35%;height:30px;color:#FFF;font-size:18px;\"><b>Vessel</b></td>";
					$isiMessage .= "<td style=\"vertical-align:middle;text-align:center;width:20%;height:30px;color:#FFF;font-size:18px;\"><b>App No</b></td>";
					$isiMessage .= "<td style=\"vertical-align:middle;text-align:center;width:20%;height:30px;color:#FFF;font-size:18px;\"><b>Department</b></td>";
					$isiMessage .= "<td style=\"vertical-align:middle;text-align:center;width:20%;height:30px;color:#FFF;font-size:18px;\"><b>Tanggal</b></td>";
				$isiMessage .= "</tr>";
				$isiMessage .= $trNya;
			$isiMessage.= "</table>";

			$isiMessage .= "<p>To respon this Request, please check <a href=\"http://apps.andhika.com/purchasing\" target=\"_blank\">Purchasing System</a></p>";

			$isiMessage .= "<p>";
				$isiMessage .= "*************************************************<br>";
				$isiMessage .= "END OF NOTIFICATION<br>";
				$isiMessage .= "*************************************************<br>";
			$isiMessage .= "</p>";
			
			if($trNya != "")
			{
				// echo "<pre>";print_r($isiMessage);//exit;
				mail($mailNya, $subjectNya, $isiMessage, $this->headers());
			}
		}

		function cekDataToFinalFinance()
		{
			$total = 0;
			$email = "";
			$trNya = "";			
			$no = 1;
			$tempData = array();
			$listBody = "Create PO Done";
			$subjectNya = "Daftar Purchase Order";
			$idPurc = "";
			$dateNow = date("Y-m-d");

			$sql = " SELECT * FROM list_purchase WHERE sts_delete = '0' AND st_data = '1' AND last_send_mail = '0000-00-00' ";
			$query = $this->mysqlQuery($sql);

			while($rsl = mysql_fetch_array($query))
			{
				if($idPurc == ""){ $idPurc = "'".$rsl['id']."'"; } else { $idPurc .= ",'".$rsl['id']."'"; }

				$sqlSum = "SELECT SUM(purchase_amount) AS total,purchase_curr FROM request_detail WHERE sts_delete = '0' AND purchase_id = '".$rsl['id']."'";
				$rslSm = $this->mysqlQuery($sqlSum);
				$rslSum = mysql_fetch_array($rslSm);

				$totalNya = $rslSum['total'] - $rsl['discount'];
				$totalNya = $totalNya + $rsl['ppn'] + $rsl['delivery_cost'];
				$totalNya = number_format($totalNya,0);

				$trNya .= "<tr>";
					$trNya .= "<td align=\"center\">".$no."</td>";
					$trNya .= "<td>".$rsl['ship_name']."</td>";
					$trNya .= "<td>".$rsl['order_name']."<br>".$rsl['order_company']."</td>";
					$trNya .= "<td align=\"center\">".$this->convertReturnName($rsl['po_date'])."</td>";
					$trNya .= "<td>".$rsl['po_no']."</td>";
					$trNya .= "<td align=\"right\">(".$rslSum['purchase_curr'].") ".$totalNya."</td>";
				$trNya .= "</tr>";
				$no++;
			}

			if($trNya != "")
			{
				$sqlMail = " SELECT * FROM send_mail WHERE sts_delete = '0' AND vessel = 'purchasing' ";
				$queryMail = $this->mysqlQuery($sqlMail);
				$rslMail = mysql_fetch_array($queryMail);

				$mailNya = $rslMail['email_finance'];

				$sqlUpdate = "UPDATE list_purchase SET last_send_mail = '".$dateNow."' WHERE id IN(".$idPurc.") ";
				$this->mysqlQuery($sqlUpdate);

				$isiMessage = "";

				$isiMessage .= "<p>";
					$isiMessage .= "*************************************************<br>";
					$isiMessage .= "PLEASE DO NOT REPLY THIS EMAIL..!!<br>";
					$isiMessage .= "*************************************************<br>";
				$isiMessage .= "</p>";

				$isiMessage .= "<b>&nbsp;***** ".$listBody." *****</b>";

				$isiMessage.= "<table width=\"800px\" border=\"1\" cellpadding=\"0\" cellspacing=\"0\" style=\"margin-top:20px;margin-bottom:20px;\">";
					$isiMessage .= "<tr style=\"background-color:#740400;\">";
						$isiMessage .= "<td style=\"vertical-align:middle;text-align:center;width:5%;height:30px;color:#FFF;font-size:16px;\"><b>No</b></td>";
						$isiMessage .= "<td style=\"vertical-align:middle;text-align:center;width:25%;height:30px;color:#FFF;font-size:16px;\"><b>Vessel</b></td>";
						$isiMessage .= "<td style=\"vertical-align:middle;text-align:center;width:20%;height:30px;color:#FFF;font-size:16px;\"><b>Order To</b></td>";
						$isiMessage .= "<td style=\"vertical-align:middle;text-align:center;width:15%;height:30px;color:#FFF;font-size:16px;\"><b>PO Date</b></td>";
						$isiMessage .= "<td style=\"vertical-align:middle;text-align:center;width:15%;height:30px;color:#FFF;font-size:16px;\"><b>PO No</b></td>";
						$isiMessage .= "<td style=\"vertical-align:middle;text-align:center;width:20%;height:30px;color:#FFF;font-size:16px;\"><b>Total</b></td>";
					$isiMessage .= "</tr>";
					$isiMessage .= $trNya;
				$isiMessage.= "</table>";

				$isiMessage .= "<p>To respon this Request, please check <a href=\"http://apps.andhika.com/purchasing\" target=\"_blank\">Purchasing System</a></p>";

				$isiMessage .= "<p>";
					$isiMessage .= "*************************************************<br>";
					$isiMessage .= "END OF NOTIFICATION<br>";
					$isiMessage .= "*************************************************<br>";
				$isiMessage .= "</p>";

				// echo $isiMessage;
				mail($mailNya, $subjectNya, $isiMessage, $this->headers());
			}

			return $total;
		}
		
		function headers($emailSI = "")
		{
			$headers = "";
			$headers .= "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1\n";
			$headers .= "X-Priority: 3\n";
			$headers .= "X-MSMail-Priority: Normal\n";
			$headers .= "X-Mailer: php\n";
			$headers .= "From: noreply@andhika.com\n";
			//$headers .= "CC: it@andhika.com\n";
			$ccNya = "it@andhika.com";

			if($emailSI != "")
			{
				$ccNya .= ", ".$emailSI;
			}

			// $headers .= "CC: ".$ccNya."\n";
			
			return $headers;
		}

		function mysqlQuery($result)
		{
			$this->mResult = mysql_query($result);
			if(!$this->mResult) {die(mysql_error());}			
			return $this->mResult;
		}
		
		function mysqlFetch($sql)
		{
			$this->strSQL = mysql_fetch_array($sql,MYSQL_ASSOC);			
			return $this->strSQL;
		}
		
		function mysqlNRows($sql)
		{
			$this->strSQL = mysql_num_rows($this->mResult = $sql);			
			return $this->strSQL;
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
	}

?>
<script language="javascript">
	setTimeout(function(){
	  window.close();
	}, 2000);
</script>