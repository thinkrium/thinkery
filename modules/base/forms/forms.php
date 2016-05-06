<?php
 /*
  *  eventually i might use this class to create forms in an automated fashion
  *  so that it is a more streamlined system but we will see
  *
  */

class forms {
    //put your code here
    
    public function __construct() {
        
    }
    
    public function formsAddUrl($params = null) {
      
        $urls = array();
        
        $urls[] = array( 'thanks' => array(
           'location' => __DIR__,
           'object' => __CLASS__,
           'function' => 'thank_you',
           'region' => 'thankYou',
           'view' => 'thanks.view',
           ) 
            
        );
        
        
        
        return $urls;
    }
    
    
    public function setFormElement() {
    }
    
    
    public function thank_you() {
        
    }
}
