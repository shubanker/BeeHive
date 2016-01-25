<?PHP
class upload {
	private $file_name;
	private $size;
	private $target_dir;
	private $target_file;
	private $uploadOk = 0;
	private $imageFileType;
	private $check;
	private $upload_error = array ();
	
	function __construct($target) {
		$this->target_dir = $target;
	}
	function get_target_dir() {
		return $this->target_dir;
	}
	private function set_file_name($file) {
		/*
		 * Adding random string at begining to avoid possible conflicts.
		 */
		$this->file_name = Keys::get_random_string(5,15).$_FILES ["$file"] ["name"];
	}
	private function set_file_size($file) {
		$this->size = $_FILES ["$file"] ["size"];
	}
	function get_file_name() {
		return $this->file_name;
	}
	function get_file_size() {
		return $this->size;
	}
	private function set_target_file() {
		$this->target_file = $this->target_dir . basename ( $this->file_name );
	}
	function get_target_file() {
		return $this->target_file;
	}
	private function get_image_file_type() {
		$this->imageFileType = pathinfo ( $this->target_file, PATHINFO_EXTENSION );
		return $this->imageFileType;
	}
	private function check_if_image($upload) {
		$this->check = getimagesize ( $_FILES ["$upload"] ["tmp_name"] );
		if ($this->check !== false) {
			$this->uploadOk = 1;
			return true;
		} else {
			$this->upload_error ['image_or_not'] = "File is not an image.";
			$this->uploadOk = 0;
			return false;
		}
	}
	function get_error() {
		return $this->upload_error;
	}
	private function type($file_type, $type) {
		function type($file_type, $type) {
			foreach ( $type as $key => $val ) {
				$a .= $file_type . ' !=' . $val . ' && ';
			}
			echo substr ( $a, - strlen ( $a ), + strlen ( $a ) - 3 );
		}
	}
	function upload_image_file($file, $type = NULL) {
		if (! self::check_if_image ( $file )) {
			return false;
		}
		$i=0;
		do {
			$this->set_file_name ( $file );
			$this->set_target_file ();
			if (!file_exists ( $this->target_file )){
				break;
			}
		}while ($i++<10);
		//if we can't find unique file even after several tries ,quit
		if ($i>=10){
			$this->upload_error ['exists'] = "Sorry, file already exists";
			$this->uploadOk = 0;
			return false;
		}
		$this->set_file_size ( $file );
		
		$this->get_image_file_type ();
		
		// Check file size
		if ($this->size > 500000) {
			$this->upload_error ['size'] = "Sorry, your file is too large.";
			$this->uploadOk = 0;
		} // array("jpeg","jpg","png","gif")
		  // Allow certain file formats
		if (self::type ( "$this->imageFileType", array (
				"jpeg",
				"jpg",
				"png",
				"gif" 
		) )) {
			$this->upload_error ['type'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
			$this->uploadOk = 0;
		}
		// Check if $uploadOk is set to 0 by an error
		 if ($this->uploadOk != 0) {
			if (move_uploaded_file ( $_FILES ["$file"] ["tmp_name"], $this->target_file )) {
				return true;
			} else {
				$this->upload_error ['error_uploading'] = "Sorry, there was an error uploading your file.";
				return false;
			}
		}
	}
}
?>