<?php


class render {

    private $page;

    public function __construct() {
       
    }
    
    public function set_page($page_data) {
      
      $this->page = $page_data;
      
    }
    
    public function get_page() {
        return $this->page;
    }
    
    public function render() {
       
    }
    
    /*
     * render->page is displayed on index.html
     */
    
    public function page() {
        
        
    }
}
