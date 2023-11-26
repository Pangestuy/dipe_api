<?php  


/**
* 
*/
require APPPATH . 'libraries/REST_Controller.php';

class barang extends REST_Controller
{
	
	function __construct()
	{
		parent::__construct();
		
		#Configure limit request methods
		$this->methods['index_get']['limit']=10; #10 requests per hour per barang/key
		$this->methods['index_post']['limit']=10; #10 requests per hour per barang/key
		$this->methods['index_delete']['limit']=10; #10 requests per hour per barang/key
		$this->methods['index_put']['limit']=10; #10 requests per hour per barang/key
		
		#Configure load model api table barang
		$this->load->model('m_barang');
	}


	function index_get($id=null){	
		
		#Set response API if Success
		$response['SUCCESS'] = array('status' => TRUE, 'message' => 'Success get barang' , 'data' => null );
		
		#Set response API if Not Found
		$response['NOT_FOUND']=array('status' => FALSE, 'message' => 'No barang were found' , 'data' => null );
        
		#
		if (!empty($this->get('ID_BARANG')))
			$id=$this->get('ID_BARANG');
            

		if ($id==null) {
			#Call methode get_all from m_barang model
			$barang=$this->m_barang->get_all();
		
		}


		if ($id!=null) {
			
			#Check if id <= 0
			if ($id<=0) {
				$this->response($response['NOT_FOUND'], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
			}

			#Call methode get_by_id from m_barang model
			$barang=$this->m_barang->get_by_id($id);
		}


        # Check if the barang data store contains barang
		if ($barang) {
			if (count($barang)==1)
				if (isset($barang->IMAGE)) {
					$barang->IMAGE=explode(',', $barang->IMAGE);
				}else{
					$barang[0]->IMAGE=explode(',', $barang[0]->IMAGE);
				}
			else
				for ($i=0; $i <count($barang) ; $i++)
					$barang[$i]->IMAGE=explode(',', $barang[$i]->IMAGE);
			// exit();
			$response['SUCCESS']['data']=$barang;

			#if found barang
			$this->response($response['SUCCESS'] , REST_Controller::HTTP_OK);

		}else{

	        $this->response($response['NOT_FOUND'], REST_Controller::HTTP_NOT_FOUND); # NOT_FOUND (404) being the HTTP response code

		}

	}

	function index_post(){

		#
		$barang_data = array('NAMA_BARANG' =>$this->post('NAMA_BARANG') , 
							'MERK_BARANG' => $this->post('MERK_BARANG') ,
							'DESKRIPSI_BARANG' => $this->post('DESKRIPSI_BARANG') , 
							'TAHUN_BARANG' => $this->post('TAHUN_BARANG') ,
							'HARGA_BARANG' => $this->post('HARGA_BARANG') ,
							'WARNA_BARANG' => $this->post('WARNA_BARANG') ,
							'STATUS_SEWA'=>0,
							'STATUS_BARANG'=>$this->post('STATUS_BARANG'),
							'CREATED_BARANG'=>date('Y-m-d h:i:s'),
						);

		 

		#Initialize image name
		$image_name=round(microtime(true)).date("Ymdhis").".jpg";

		$barang_photo=null;

		#Upload avatar
		if ($this->Upload_Images($image_name)){
			$barang_photo['IMAGE']=$image_name;
			$barang_photo['ID_BARANG']=$image_name;
		}
		
		#Set response API if Success
		$response['SUCCESS'] = array('status' => TRUE, 'message' => 'Success insert data' , 'data' => $barang_data );

		#Set response API if Fail
		$response['FAIL'] = array('status' => FALSE, 'message' => 'Fail insert data' , 'data' => null );
		
		#Set response API if exist data
		$response['EXIST'] = array('status' => FALSE, 'message' => 'exist data' , 'data' => null );


		#Check if insert barang_data Success
		$id=$this->m_barang->insert($barang_data,$barang_photo);
		if ($id) {
			$barang_data["ID_BARANG"]=$id;
			$response['SUCCESS'] = array('status' => TRUE, 'message' => 'Success insert data' , 'data' => $barang_data );

			#If success
			$this->response($response['SUCCESS'],REST_Controller::HTTP_CREATED);

		}else{
			#Remove image barang
			if ($barang_data['PHOTO']!=null) {
				$this->remove_image($barang_data['PHOTO']);
			}
			
			#If fail
			$this->response($response['FAIL'],REST_Controller::HTTP_FORBIDDEN);

		}

	}

	function index_delete($id=null){

		#Set response API if Success
		$response['SUCCESS'] = array('status' => TRUE, 'message' => 'success delete barang'  );

		#Set response API if Fail
		$response['FAIL'] = array('status' => FALSE, 'message' => 'fail delete barang'  );
		
		#Set response API if barang not found
		$response['NOT_FOUND']=array('status' => FALSE, 'message' => 'no barang were found' );


		#Check available barang
		if (!$this->validate($id))
			$this->response($response['NOT_FOUND'],REST_Controller::HTTP_NOT_FOUND);
		

		if (!empty($this->get('ID_BARANG')))
			$id=$this->get('ID_BARANG');
		
		if ($this->m_barang->delete($id)) {
			
			#If success
			$this->response($response['SUCCESS'],REST_Controller::HTTP_CREATED);
		
		}else{

			#If Fail
			$this->response($response['FAIL'],REST_Controller::HTTP_CREATED);
			
		}

	}

	function index_put(){

		$id=$this->put('ID_BARANG');

		$barang_data = array('NAMA_BARANG' =>$this->put('NAMA_BARANG') , 
							'MERK_BARANG' => $this->put('MERK_BARANG') ,
							'DESKRIPSI_BARANG' => $this->put('DESKRIPSI_BARANG') , 
							'TAHUN_BARANG' => $this->put('TAHUN_BARANG') ,
							'HARGA_BARANG' => $this->put('HARGA_BARANG') ,
							'WARNA_BARANG' => $this->put('WARNA_BARANG') ,							
							'STATUS_BARANG'=>$this->put('STATUS_BARANG'),
							'CREATED_BARANG'=>date('Y-m-d h:i:s'),
						);


		#Initialize image name
		$image_name=round(microtime(true)).date("Ymdhis").".jpg";

		$barang_photo=null;

		#Upload avatar
		if ($this->Upload_Images($image_name)){
			$barang_photo['IMAGE']=$image_name;
			$barang_photo['ID_BARANG']=$image_name;
		}

		#Set response API if Success
		$response['SUCCESS'] = array('status' => TRUE, 'message' => 'success update barang' , 'data' => $barang_data );

		#Set response API if Fail
		$response['FAIL'] = array('status' => FALSE, 'message' => 'fail update barang' , 'data' => $barang_data );
		
		#Set response API if barang not found
		$response['NOT_FOUND']=array('status' => FALSE, 'message' => 'no barang were found' , 'data' => $barang_data );

		#Set response API if exist data
		$response['EXIST'] = array('status' => FALSE, 'message' => 'exist data' , 'data' => $barang_data );

		#Check available barang
		if (!$this->validate($id))
			$this->response($response['NOT_FOUND'],REST_Controller::HTTP_NOT_FOUND);

		if ($this->m_barang->get_by_plat($this->put('PLAT_NO_BARANG'))!=null&&$this->m_barang->get_by_plat($this->put('PLAT_NO_BARANG'))->ID_BARANG!=$id)
			$this->response($response['EXIST'],REST_Controller::HTTP_FORBIDDEN);

		$update=$this->m_barang->update($id,$barang_data,$barang_photo);
		if ($update) {
			
			#If success
			$this->response($response['SUCCESS'],REST_Controller::HTTP_CREATED);
		
		}else{

			#If Fail
			$this->response($response['FAIL'],REST_Controller::HTTP_CREATED);
			
		}

	}

	function validate($id){
		$barang=$this->m_barang->get_by_id($id);
		if ($barang)
			return TRUE;
		else
			return FALSE;
	}

	function Upload_Images($name) 
    {

    		if ($this->post('PHOTO')) {
	    		$strImage = str_replace('data:image/png;base64,', '', $this->post('PHOTO'));
    		}else{
    			$strImage = str_replace('data:image/png;base64,', '', $this->put('PHOTO'));

    		}
    		if (!empty($strImage)) {
    			$img = imagecreatefromstring(base64_decode($strImage));
							
				if($img != false)
				{
				   if (imagejpeg($img, './upload/barang/'.$name)) {
				   	return true;
				   }else{
				   	return false;
				   }
				}
			}
	}

	function remove_image($name){
		$path='./upload/barang/'.$name;
		unlink($path);
	}
}
?>