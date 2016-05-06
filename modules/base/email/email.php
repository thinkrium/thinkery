<?php

/**
 * Description of email
 *
 * @author thomedy
 */
class email {
    //put your code here
    
    /*
     * set the constructor up just to start the class for multiple needs
     * 
     *  $author_name - email author name
     *
     *  $reciever_name - email reciever name
     *
     *  $author_address - email address of the author
     *  
     *  $reciever_address - email address of the reciever
     *
     *  $message - this is the content of the email
     *
     *  $attachments - an array to hold the attachments of the email
     *
     *  $error - holds the error system created in another object as an injection
     *
     */
    
    private $author_name;
    private $reciever_name;
    private $author_address;
    private $reciever_address;
    
    private $message;
    private $attachments = [];
    
    private $error;
    
    /*
     *
     * constructor is built in the initial load and then the create is called when necessary
     *
     *
     */
    public function __construct() {
        
    }
    
    /*
     * the create function does all the heavy lifting
     * it filters, sanitizes, checks
     * and then sends
     *
     */
    public function create(
                                $author_name = null,
                                $reciever_name = null,
                                $author_addy,
                                $reciever_addy,
                                $message,
                                $attachments = null,
                                $error
                                ) {
        
        if($author_name != Null) {
            $this->set_author_name($author_name);   
        }
        if($reciever_name != Null) {
            $this->set_reciever_name($reciever_name);      
        }
        
        $this->set_author_addy($author_addy);

        $this->set_reciever_addy($reciever_addy);

        if($message != Null) {
            $this->set_message($message);
        }

        if($attachments != Null) {
            
            $this->add_attachment($attachments);
        }
        
        $this->set_error($error);
        
    }
    
    /*
     * takes all the information in the email system that is set in the 
     * constructor and sends it
     * 
     */
    
    public function send() {
        
        
    }
    
    /*
     * deletes the email
     * 
     */
    public function delete() {
        
    }
    
    /*
     * sets the authors name
     */
    public function set_author_name($name = null) {
        
        $this->author_name = $name;
    }

    /*
     * sets the recievers name
     */
    public function set_reciever_name($name = null) {
        
        $this->reciever_name = $name;
    }

    /*
     * returns the authors name just in case
     */
    public function get_author_name() {
        return $this->author_name;
        
    }
    
    /*
     * returns the recievers name just in case
     */
    public function get_reciever_name() {
        
        return $this->reciever_name;
    }
    
    /*
     * sets the authors address
     */
    public function set_author_address($addy = null) {
        
        $this->author_address = $addy;
    }
    
    /*
     * sets the recievers address
     */
    public function set_reciever_address($addy = null) {
        
        $this->reciever_address = $addy;
    }

    /*
     * gets the authors address and returns it
     */
    public function get_author_address() {
        return $this->author_address;
        
    }
    
    /*
     * gets the recievers address and returns it
     */
    public function get_reciever_address() {
        
        return $this->reciever_address;
    }
    
    /*
     * sets the message sent
     */
    public function set_message($message) {
        
        $this->message = $message;
        
    }
    
    /*
     * returns the message sent
     */
    public function get_message() {
        
        return $this->message;
    }
    
    /*
     * pushes the attachment into the classes attachment array for 
     */
    public function add_attachment($attachment) {
        array_push($this->attachments, $attachment);
    }
    
    /*
     * returns the attachment for everyone
     */
    public function get_attachments() {
        
        return $this->attachments;
    }
    
    public function set_error($error) {

        $this->error = $error;

    }
    
    /*
     * allowing permissions sets the perms for this modules
     */
    
    public function email_get_permissions() {
        $permissions = array();
        
        $permissions['send_email'] = array(
            'label' => 'send email',
            'function' => 'send',
        );
   
        $permissions['delete_email'] = array(
            'label' => 'delete email',
            'function' => 'delete'
        );
        
        return $permissions;

    }
}
