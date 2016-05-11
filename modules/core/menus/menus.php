<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of menus
 *
 * @author thomedy
 */
class menus {
    //put your code here
    
    /*
     * holds the db connection
     */
    private $connection;
    
    /*
     * holds the error object for necessary logs
     */
    private $errors;
    
    /*
     * injects the session object to make functionality like getting logged in user
     * easier
     * 
     */
    private $session;
    
    /*
     * this is the currently logged in user
     * 
     */
    private $logged_in_user;
    
    /*
     * send in the error system
     */
     
    /*
     *
     * sets up the connection for the database
     * sets up the session
     * sets up the error system 
     * 
     * all this is done with injection of the functionality
     *
     */
    
    public function __construct($connection, $session, $errors) {
        
        $this->connection = $connection;
        
        $this->session = $session;
        
   }
   
   
   /*
    * create a region 
    */
   public function menusAddUrl($params = null) {
    
       $url = array();
       
       $url[] = array( 'menus' => array(
            'object' => __CLASS__,
            'location' => __DIR__,
            'region' => 'menus',
            'function' => 'menus_show',
            'view' => 'menus_show.view',
           )
       );

       $url[] = array( 'menu/add' => array(
            'object' => __CLASS__,
            'location' => __DIR__,
            'region' => 'menu_add',
            'function' => 'menu_add',
            'validate' => 'menu_add_validate',
            'submit' => 'menu_add_submit',
            'view' => 'menu_add.view',
           )
       );

       return $url;
   }
 
   public function menus_show() {
       
      $stmt = $this->connection->prepare('select menus.* from '
              . 'menus left join menu_lists on menus.menu_id = menu_lists.menu_id');
      
      $stmt->execute();
      
      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $results;
   }
   

   public function menu_add() {
       
   }
   
   public function menu_add_validate() {
       
   }
   
   public function menu_add_submit() {
       
   }
   
   
   
}
