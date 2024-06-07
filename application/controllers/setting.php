<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Setting extends CI_Controller {

	function __construct()
	{
		parent::__construct();		
		$this->load->model('mpurchasing');
		$this->load->helper(array('form', 'url'));
	}
	function index()
	{
		
	}

	function getUserPurchase()
	{
		$dataOut = array();
		$trNya = "";
		$no = 1;

		$sql = " SELECT * FROM user WHERE sts_delete = '0' ORDER BY name_full ASC ";
		$rsl = $this->mpurchasing->getDataQuery($sql);

		foreach ($rsl as $key => $val)
		{
			$btnAct = "<button onclick=\"editData('".$val->id."');\" class=\"btn btn-success btn-xs\" title=\"Edit\">Edit</button>";
			$btnAct .= " <button onclick=\"delData('".$val->id."');\" class=\"btn btn-danger btn-xs\" title=\"Delete\">Delete</button>";

			$trNya .= "<tr>";
				$trNya .= "<td style=\"text-align:center;\">".$no."</td>";
				$trNya .= "<td>".$val->name_full."</td>";
				$trNya .= "<td>".$val->username."</td>";
				$trNya .= "<td style=\"text-align:center;\">".ucfirst($val->type)."</td>";
				$trNya .= "<td>".$val->position."</td>";
				$trNya .= "<td style=\"text-align:center;\">".$btnAct."</td>";
			$trNya .= "</tr>";

			$no++;
		}

		$dataOut['trNya'] = $trNya;

		$this->load->view("purchasing/user",$dataOut);
	}

	function getDataUsr()
	{
		$dataOut = array();
		$trNya = "";
		$no = 1;

		$sql = "SELECT A.*,B.menu
				FROM user_setting A
				LEFT JOIN mst_menu B ON B.id = A.id_menu
				WHERE A.username = '".$_POST['usrSrch']."' 
				ORDER BY A.id_menu ASC ";
		$rsl = $this->mpurchasing->getDataQuery($sql);

		foreach ($rsl as $key => $value)
		{
			$trNya .= "<tr>";
				$trNya .= "<td align=\"center\">".$no."</td>";
				$trNya .= "<td align=\"left\">".$value->user_full."</td>";
				$trNya .= "<td align=\"left\">".$value->menu."</td>";
				$trNya .= "<td align=\"center\">
							<button onclick=\"delData('".$value->id."');\" class=\"btn btn-danger btn-xs\" id=\"btnEdit\" type=\"button\"><i class=\"fa fa-power-off\"></i> Delete</button>
							</td>";
			$trNya .= "</tr>";

			$no++;
		}

		$dataOut['trNya'] = $trNya;
		
		print json_encode($dataOut);
	}

	function addUsrSetting()
	{
		$data = $_POST;
		$stData = "";
		$dataIns = array();

		try {
			$dataIns['user_full'] = $data['userFull'];
			$dataIns['username'] = $data['userName'];
			$dataIns['posisi'] = $data['jbtn'];
			$dataIns['type_user'] = "user";
			$dataIns['id_menu'] = $data['myMenu'];
			$dataIns['vessel'] = $data['vessel'];

			if($data['vessel'] == "")
			{
				$dataIns['user_full'] = $data['userFullPurch'];
				$dataIns['username'] = $data['userNamePurch'];
			}

			$this->mpurchasing->insData("user_setting",$dataIns);

			$stData = "Success..!!";
		} catch (Exception $ex) {
			$stData = "Failed =>".$ex;
		}
		print json_encode($stData);
	}

	function userSetting()
	{
		$dataOut = array();

		$dataOut['optMenu'] = $this->getMstMenu();
		$dataOut['optVessel'] = $this->getVessel();
		$dataOut['optUserPurc'] = $this->getUserPurch();
		$this->load->view("purchasing/userSetting",$dataOut);
	}

	function getUser()
	{
		$dataOpt = "";

		$sqlOpt = "	SELECT * FROM user WHERE type = 'user' AND A.sts_delete = '0' ORDER BY name_full ASC ";
		$rslOpt = $this->mpurchasing->getDataQueryDb2($sqlOpt);

		$dataOpt .= "<option value=\"0\">- Select -</option>";
		foreach ($rslOpt as $key => $value)
		{			
			$dataOpt .= "<option value=\"".$value->username."\">".$value->username."</option>";
		}

		print json_encode($dataOpt);
	}

	function addUser()
	{
		$data = $_POST;
		$stData = "";
		$dataIns = array();

		try {
			$dataIns['name_full'] = $data['fullName'];
			$dataIns['username'] = $data['userName'];
			
			if($data['passWord'] != "")
			{
				$dataIns['password'] = md5($data['passWord']);
			}
			
			$dataIns['type'] = $data['typeUser'];
			$dataIns['position'] = $data['position'];

			if($data['idEditUser'] == "")
			{
				$this->mpurchasing->insData("user",$dataIns);
			}else{
				$whereNya = "id = '".$data['idEditUser']."'";
				$this->mpurchasing->updateData($whereNya,$dataIns,"user");
			}

			$stData = "Success..!!";
		} catch (Exception $ex) {
			$stData = "Failed =>".$ex;
		}
		print json_encode($stData);
	}

	function getUserObs()
	{
		$dataOpt = "";
		$nmVsl = $_POST['searchNya'];

		$sqlOpt = "	SELECT A.username 
					FROM login A 
					LEFT JOIN mst_vessel B ON B.id = A.vessel
					WHERE A.user_type = 'user' AND A.sts_delete = '0' AND B.name = '".$nmVsl."'
					ORDER BY A.username ASC ";
		$rslOpt = $this->mpurchasing->getDataQueryDb2($sqlOpt);

		$dataOpt .= "<option value=\"0\">- Select -</option>";
		foreach ($rslOpt as $key => $value)
		{			
			$dataOpt .= "<option value=\"".$value->username."\">".$value->username."</option>";
		}

		print json_encode($dataOpt);
	}

	function getUserDetail()
	{
		$dataOut = array();
		$usr = $_POST['usr'];

		$sqlOpt = "	SELECT A.*,B.name as nameVsl,C.name as nameJbtn
					FROM login A 
					LEFT JOIN mst_vessel B ON B.id = A.vessel
					LEFT JOIN mst_jabatan C ON C.id = A.id_jabatan
					WHERE A.user_type = 'user' AND A.sts_delete = '0' AND A.username = '".$usr."'
					ORDER BY A.username ASC ";
		$rslOpt = $this->mpurchasing->getDataQueryDb2($sqlOpt);

		foreach ($rslOpt as $key => $value)
		{
			$dataOut['username'] = $value->username;
			$dataOut['full_name'] = $value->full_name;
			$dataOut['nameJbtn'] = $value->nameJbtn;
			$dataOut['nameVsl'] = $value->nameVsl;
		}

		print json_encode($dataOut);
	}

	function getUserPurch()
	{
		$optNya = "";

		$sql = "SELECT * FROM user WHERE type = 'user' ORDER BY name_full ASC";
		$rsl = $this->mpurchasing->getDataQuery($sql);

		foreach ($rsl as $key => $value)
		{
			$optNya .= "<option value='".$value->username."'>".$value->name_full."</option>";
		}
		return $optNya;
	}

	function getDataEdit()
	{
		$id = $_POST['id'];

		$sql = " SELECT * FROM user WHERE sts_delete = '0' AND id = '".$id."' ";
		$rsl = $this->mpurchasing->getDataQuery($sql);

		print json_encode($rsl);
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

	function getMstMenu()
	{
		$dataOpt = "";

		$sqlMenu = "SELECT * FROM mst_menu ORDER BY menu ASC ";
		$rslOpt = $this->mpurchasing->getDataQuery($sqlMenu);

		foreach ($rslOpt as $key => $value)
		{
			$dataOpt .= "<option value=\"".$value->id."\">".$value->menu."</option>";
		}

		return $dataOpt;
	}
	
	function delDataUser()
	{
		$stData = "";
		$idDel = $_POST['idDel'];
		try {
			$this->db->where('id',$idDel);
  			$this->db->delete('user');

  			$stData = "Delete Success..!!";
  		} catch (Exception $e) {
  			$stData = "Failed =>".$e;
  		}
  		print json_encode($stData);
	}

	function delData()
	{
		$stData = "";
		$idDel = $_POST['idDel'];
		try {
			$this->db->where('id',$idDel);
  			$this->db->delete('user_setting');
  			$stData = "Delete Success..!!";
  		} catch (Exception $e) {
  			$stData = "Failed =>".$e;
  		}
  		print json_encode($stData);
	}
































}