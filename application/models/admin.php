<?php
class Admin extends CI_Model
{
	function __construct()
    {
      parent::__construct();
	  $this->CI = get_instance();
    }
	
	function dataList()
	{			
		$this->db->select('*');
		$this->db->from('karyawan');
		$this->db->order_by('nama');
		$this->CI->flexigrid->build_query();
		
		$return['records'] = $this->db->get();
		
		$this->db->select('count(*) as record_count');
		$this->db->from('karyawan');
		
		$this->CI->flexigrid->build_query(FALSE);
		$record_count = $this->db->get();
		$row = $record_count->row();
		
		$return['record_count'] = $row->record_count;
		
		return $return;
	}
	
	function editKaryawan($id)
	{	
		return $this->db->select('*,YEAR(tanggal_lahir) as thn,MONTH(tanggal_lahir) as bln,DAY(tanggal_lahir) as tgl')
				->from('karyawan')
				->where('id',$id)
				->get()
				->result();
	}
	
	function updateKaryawan($data)
	{
		$this->db->where('id',$data['id']);
		$this->db->update('karyawan',$data);
	}
	
	
}
?>