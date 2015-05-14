<?php

class ProfilesController extends AppController{
	
	public $components = array('RequestHandler','Paginator');
	
	public $helpers  = array('Html', 'Form');
	
	protected $imgpath = null; //image path
	
	
	/**
	 * list of profile
	 */
	public function index(){
		$this->layout = 'profile';
		$this->Paginator->settings = array(
					'limit' => 8, 
				);
		$data = $this->Paginator->paginate('Profile');

		$this->set(compact('data'));
	}
	
	public function profile_register(){
		
		$this->layout = 'profile';
		$errors = '';
		
		$data = array(
				'first_name' => '',
				'last_name' => '',
				'middle_name' => '',
				'birthdate' => '',
				'contact' => '',
				'facebook' => '',
				'picture' => '',
				'email' => '',
				'gender' => '',
				'address' => '',
				'contact_person' => '',
				'contact_person_no' => '',
				'signature' => '',
		);
		
		if($this->request->is('post')){
			$row = $this->request->data;
			pr($row);
			$this->Profile->create();
			$this->imgpath = '';
	
			$ext = $row['Profile']['picture']['type'];

			$data = array(
					'first_name' => $row['first_name'],
					'last_name' => $row['last_name'],
					'middle_name' => $row['middle_name'],
					'birthdate' => $row['birthdate'],
					'contact' => $row['contact'],
					'facebook' => $row['facebook'],
					'picture' => $this->Profile->resize($row['Profile']['picture'], 250, 250),
					'email' => $row['email'],
					'gender' => $row['gender'],
					'address' => $row['address'],
					'contact_person' => $row['contact_person'],
					'contact_person_no' => $row['contact_person_no'],
					'signature' => $row['signature'],
			);
			
			if($this->Profile->save($data)){
				$this->Profile->UploadProcess($ext);
				return $this->redirect('/');
			}else{
				$errors = $this->Profile->validationErrors;
			}
			
			
		}

		$this->set('data', $data);
		$this->set('errors', $errors);
		
	}
	
	public function profile_update($id = null){
		$this->layout = 'profile';
		$errors = '';
		
		if(!$id){
			return $this->redirect('/');
		}
		
		$data = array(
				'first_name' => '',
				'last_name' => '',
				'middle_name' => '',
				'birthdate' => '',
				'contact' => '',
				'facebook' => '',
				'picture' => '',
				'email' => '',
				'gender' => '',
				'address' => '',
				'contact_person' => '',
				'contact_person_no' => '',
				'signature' => '',
		);
		
		$data = $this->Profile->findById($id);
		
		if($data){
			
			if($this->request->is(array('post','put'))){
				$this->Profile->id = $id;
				
				$row = $this->request->data;
				
				$this->imgpath = '';
				
				if(empty($row['Profile']['picture']['name'])){
					$imgorig = $data['Profile']['picture'];
				}else{
					$imgorig = $this->file($row['Profile']['picture']);
				}
				
				$data = array(					
						'Profile' =>array(
							'first_name' => $row['first_name'],
							'last_name' => $row['last_name'],
							'middle_name' => $row['middle_name'],
							'birthdate' => $row['birthdate'],
							'contact' => $row['contact'],
							'facebook' => $row['facebook'],
							'picture' => $this->Profile->imgsrc,
							'email' => $row['email'],
							'gender' => $row['gender'],
							'address' => $row['address'],
							'contact_person' => $row['contact_person'],
							'contact_person_no' => $row['contact_person_no'],
							'signature' => $row['signature']
						)
				);
				
				if($this->Profile->save($data)){
					
					if(!empty($row['Profile']['picture']['name'])){
						$this->upload($row['Profile']['picture']['tmp_name']);
					}
					
					return $this->redirect('/');
				}else{
					$errors = $this->Profile->validationErrors;	
				}
				$this->Session->setFlash(__('Unable to update your post'));	
			}
			
			$imgPic = $data['Profile']['picture'];
			$data['Profile']['picture'] = ($imgPic)? $this->webroot.'upload/'.$imgPic : $this->webroot.'img/emptyprofile.jpg' ;

			$this->set('errors', $errors);
			
		}else{
			return $this->redirect('/');
		}

		$this->set('data',$data);
		
	}
	
	public function delete(){
		
		$this->autoRender = false;
			
		if($this->request->is('post')){
			
			$data = $this->request->data;
			$dataImg = $this->Profile->findById($data['dataID']);
			if($this->Profile->delete($data['dataID'])){
				$file = new File(WWW_ROOT .'upload/'.$dataImg['Profile']['picture'], false, 0777);
				$file->delete();
				echo '1';
			}
		}

	}
	
	public function view(){
		
		$this->autoRender = false;		
		
		if($this->request->is('ajax')){
			
			$data = $this->request->data;
			
			$result = $this->Profile->findById($data['dataId']);
			
			echo json_encode($result);
		
		}
		
		
	}
	
	/**
	 * Initialize image path 
	 * @param unknown $params
	 * @return boolean|string
	 */
	public function file($params) {
		$image = $params;

		$imageTypes = array("image/gif", "image/jpeg", "image/png");
		$uploadFolder = "upload";
		
		if(empty($image['name'])){
			return false;
		}
		
		$uploadPath = WWW_ROOT . $uploadFolder;
		foreach ($imageTypes as $type) {

			if ($type == $image['type']) {
				 
				if ($image['error'] == 0) {

					$imageName = $image['name'];

					$imageName = 'fdc'.date('His') . $imageName;
		
					$full_image_path = $uploadPath . '/' . $imageName;
					
					$this->imgpath = $full_image_path;
					
					return $imageName;

				} else {
					$this->Session->setFlash('Error uploading file.');
				}
				break;
			} else {
				$this->Session->setFlash('Unacceptable file type');
			}
		}
	}
	
	/**
	 * Upload final Image
	 * @param unknown $params
	 * @return boolean
	 */
	public function upload($params){
		
		if (move_uploaded_file($params, $this->imgpath)) {
			return true;
		} else {
			$this->Session->setFlash('There was a problem uploading file. Please try again.');
		}
	}

	public function uploadFile( $check ) {
	
		$uploadData = array_shift($check);
	
		if ( $uploadData['size'] == 0 || $uploadData['error'] !== 0) {
			return false;
		}
	
		$uploadFolder = 'files'. DS .'your_directory';
		$fileName = time() . '.pdf';
		$uploadPath =  $uploadFolder . DS . $fileName;
	
		if( !file_exists($uploadFolder) ){
			mkdir($uploadFolder);
		}
	
		if (move_uploaded_file($uploadData['tmp_name'], $uploadPath)) {
			$this->set('pdf_path', $fileName);
			return true;
		}
	
		return false;
	}
}