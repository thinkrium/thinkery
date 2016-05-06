<?php

class fileManagement {
    
    private $error_code;
    
    public function __construct() {
        
    }
    
    /*
     * sets the error code from the file management object
     */
    public function set_error_code($error_code) {
        $this->error_code = $error_code;
        
    }
    
    /*
     * gets the error code from the file management object
     */
    public function get_error_code() {
        return $this->error_code;
        
    }
    
    /*
     * generates the file_name abstraction in order to protect from malicious 
     * file uploads being found and ran
     */
    public function generate_destination_name_abstraction($time_stamp, $temporary_name) {

        return md5($time_stamp . $temporary_name);
            
    }

 
    /*
     * a wrapper around move file created to dynamicaly create a resolved
     * destination file name
     */
    public function move_file_to( $source, $destination_directory, $destination_name) {
         
        $root_dir = explode('core', __DIR__)[0];
        $destination = $root_dir . $destination_directory . '/' . $destination_name;

        move_uploaded_file($source, $destination);
    }
    
    /*
     * this is the boolean return to test validation functions
     * it checks the error code and returns true only if it is validated
     */
    public function file_uploaded_sucessfully() {
        
      if($this->error_code == 0) {
          return true;
      }   
      else {
          
          return false;
      }
      
    }
    
    /*
     * returns the upload message
     */
    public function generate_upload_response_message() {
        
        if($this->error_code == 0) {
            //  UPLOAD_ERR_OK
            return "There is no error, the file uploaded with success.";
        }
        else if($this->error_code == 1 || $this->error_code == 2 ) {
            //UPLOAD_ERR_INI_SIZE

            return "The uploaded file exceeds the maximum upload size";

        }
        else if($this->error_code == 3) {
            //UPLOAD_ERR_PARTIAL
            return  "The uploaded file was only partially uploaded.";
        }
        else if($this->error_code == 4) {
            //UPLOAD_ERR_NO_FILE
            return "No file was uploaded.";
        }
        else if($this->error_code == 5) {
            //UPLOAD_ERR_NO_TMP_DIR
            return "Missing a temporary folder.";
        }
        else if($this->error_code == 7) {
            //UPLOAD_ERR_CANT_WRITE
            return "Failed to write file to disk.";
        }    
        else if($this->error_code == 8) {
            //UPLOAD_ERR_EXTENSION
            return "A PHP extension stopped the file upload.";        
        }
        else {
           return "there was an error with the upload please try again";    
        }
        
        
    }
}