<?php  

/**
* 
*/
class m_barang extends CI_Model
{
	private $table_name="tb_barang";
	private $table_gallery="tb_gallery_barang";
	private $primary="ID_BARANG";

	function get_all(){
		$this->db->select($this->table_name.'.*,GROUP_CONCAT(IMAGE) AS IMAGE');
		$this->db->join($this->table_gallery." tb_gal",$this->table_name.'.ID_BARANG'.'=tb_gal.ID_BARANG','left');
		return $this->db->get($this->table_name)->result();
	}

	function get_by_id($id){
		$this->db->select($this->table_name.'.*,GROUP_CONCAT(IMAGE) AS IMAGE');
		$this->db->join($this->table_gallery." tb_gal",$this->table_name.'.ID_BARANG'.'=tb_gal.ID_BARANG','left');
		$this->db->where($this->table_name.'.'.$this->primary,$id);
		return $this->db->get($this->table_name)->row();
	}

	function insert($data,$barang_photo=null){
		$insert=$this->db->insert($this->table_name,$data);
		$id=$this->db->insert_id();

		if ($barang_photo!=null&&$id) {
			$barang_photo["ID_BARANG"]=$id;
			$insert=$this->db->insert($this->table_gallery,$barang_photo);
		}
	
		return $id;
	}

	function update($id,$data,$barang_photo=null){
		$this->db->where($this->primary,$id);
		$update=$this->db->update($this->table_name,$data);

		if ($barang_photo!=null&&$update) {
			$barang_photo["ID_BARANG"]=$id;
			$this->db->where($this->primary,$id);
			$update=$this->db->update($this->table_gallery,$barang_photo);
		}
	
		return $update;
	}

	function delete($id){
		$this->db->where($this->primary,$id);
		$delete=$this->db->delete($this->table_name);
		return $delete;
	}
}

?>