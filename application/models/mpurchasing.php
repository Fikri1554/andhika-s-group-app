<?php
class Mpurchasing extends CI_Model
{
	function __construct()
    {
      parent::__construct();
	  $this->CI = get_instance();
    }

    function getDataQueryDb2($query = "")
    {
    	$dataOut = array();
    	$this->db2 = $this->load->database('observasi', TRUE);
    	$dataOut = $this->db2->query($query)->result();
	    return $dataOut;
    }

    function getDataQuery($query = "")
    {
    	$dataOut = array();
    	$dataOut = $this->db->query($query)->result();
    	return $dataOut;
    }

    function getData($slc = "",$db = "",$whereNya = "",$order = "",$grp = "")
	{
		$this->db->select($slc);
		$this->db->from($db);
		if($whereNya != "")
		{
			$this->db->where($whereNya);
		}
		if($order != "")
		{
			$this->db->order_by($order);
		}
		if($grp != "")
		{
			$this->db->group_by($order);
		}
		
		$query = $this->db->get();
		$dataOut = $query->result();
		return $dataOut;
	}

	function getJoin2($slc = "",$db1 = "",$db2 = "",$joinOn = "",$typeJoin = "",$whereNya = "",$order = "",$grp = "")
	{
		$this->db->select($slc);
		$this->db->from($db1);
		$this->db->join($db2,$joinOn,$typeJoin);
		if($whereNya != "")
		{
			$this->db->where($whereNya);
		}
		if($order != "")
		{
			$this->db->order_by($order);
		}
		if($grp != "")
		{
			$this->db->group_by($order);
		}
		
		$query = $this->db->get();
		$dataOut = $query->result();
		return $dataOut;
	}

	function insData($db = "",$insData = "",$return = "")
	{
		$this->db->insert($db,$insData);

		if($return != "")
		{
			return $this->db->insert_id();
		}
	}

	function delData($db = "",$idWhere = "")
	{
		$this->db->where($idWhere);
  		$this->db->delete($db);
	}

	function updateData($whereNya = "",$data = "",$tbl = "")
	{
		$this->db->where($whereNya);
		$this->db->update($tbl,$data);
	}

	function getDataQueryMyApps($query = "")
    {
    	$this->db3 = $this->load->database('myapps', TRUE);
    	$dataOut = $this->db3->query($query)->result();
	    return $dataOut;
    }

    function updateDataMyApps($tbl = "",$dataUpdate = "",$whereNya = "")
	{
		$this->db3 = $this->load->database('myapps', TRUE);

		$this->db3->where($whereNya);
		$this->db3->update($tbl,$dataUpdate);
	}

	function insDataMyApps($dataIns = "",$dbNya = "")
	{
		$this->db3 = $this->load->database('myapps', TRUE);
		$this->db3->insert($dbNya,$dataIns);
		$getIdNya = $this->db3->insert_id();
		return $getIdNya;
	}

	function insDataMyAppsDahlia($dataIns = "",$dbNya = "")
	{
		$this->db4 = $this->load->database('myappsDahlia', TRUE);
		$this->db4->insert($dbNya,$dataIns);
		$getIdNya = $this->db4->insert_id();
		return $getIdNya;
	}
	
	function querySqlServer($query = "",$typeQuery = "")
	{
		$this->dbSqlServer = $this->load->database('sqlSrvHRSYS', TRUE);
		if($typeQuery == "")//untuk select data
		{
			$dataOut = $this->dbSqlServer->query($query)->result();
			return $dataOut;
		}else{
			$this->dbSqlServer->query($query);
		}
	}

	function insDataSqlServer($tbl = "",$insData = "")
	{
		$this->dbSqlServer = $this->load->database('sqlSrvHRSYS', TRUE);
		$this->dbSqlServer->insert($tbl,$insData);
	}
	function uptDataSqlServer($tbl = "",$dataUpdate = "",$whereNya = "")
	{
		$this->dbSqlServer = $this->load->database('sqlSrvHRSYS', TRUE);

		$this->dbSqlServer->where($whereNya);
		$this->dbSqlServer->update($tbl,$dataUpdate);
	}

	function querySqlServerErp($query = "",$dbNya = "")
	{
		$this->dbSqlServer = $this->load->database($dbNya, TRUE);
		$dataOut = $this->dbSqlServer->query($query)->result();
		return $dataOut;
	}

	
	function uploadFile($fileTmpPath, $uploadDir, $fileName, $newFileName) {
		$uploadPath = $uploadDir . $newFileName;

		if (move_uploaded_file($fileTmpPath, $uploadPath)) {
			return $newFileName;
		} else {
			return false;
		}
	}

	// function getPurchasingDetailsByIdReq($idReq)
    // {
    //     $this->db->where('id_request', $idReq);
    //     $query = $this->db->get('request_detail');
    //     return $query->result_array();
    // }

}
?>