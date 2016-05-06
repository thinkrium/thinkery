<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of nonce
 *
 * @author thomedy
 */
class nonce {
    

    public function __construct() {
        
    }
    
    /*
     * generate key is a function that generates the key for the form validation
     * its different every time and is comprised of 3 elements
     * 
     * it then sets it to a session form key
     */
    public function generateKey() {
        
        $time = new DateTime();

        
        $seed = 'abcdefghijklmnopqrstuvwxyz'
            . 'ABCDEFGHIJKLMNOPQRSTUVWXYZ__!!$$++--';        
        $form_key = session_id() . str_shuffle($seed) . $time->getTimestamp();
        
        $_SESSION['form_key'] = $form_key;
        
        return $form_key;

        
    }
    
}
