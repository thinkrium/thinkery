<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of audio
 *
 * @author thomedy
 */
class audio {


    private $connection;

    private $session;
    
//    private $errors;

    
    public function __construct($db_connection, $session, $errors) {
        
        $this->connection = $db_connection;
        
        $this->session = $session;
        
//        $this->errors = $errors;
    }
    
    public function audioAddUrl($params = null) {
        
        $url = array();
        
        $url[] = array('audio/list' => array(
            'object' => __CLASS__,
            'location' => __DIR__,
            'region' => 'audio_list',
            'view' => 'audio_list.view',
            'function' => 'audio_list',
            )
            
        );

        $url[] = array( '!entity_name/!entity_id/audio/list' => array(
            'object' => __CLASS__,
            'location' => __DIR__,
            'region' => 'entity_audio_list',
            'view' => 'entity_name_entity_id_audio_list.view',
            'function' => 'entity_name_entity_id_audio_list',
            )
        );

        $url[] = array( '!entity_name/!entity_id/audio/add' => array(
            'object' => __CLASS__,
            'location' => __DIR__,
            'region' => 'entity_audio_add',
            'view' => 'entity_name_entity_id_audio_add.view',
            'function' => 'entity_name_entity_id_audio_add',
            'submit' => 'entity_name_entity_id_audio_add_submit',
            'validate' => 'entity_name_entity_id_audio_add_validate',
            )
        );

        return $url;
        
    }
    
    public function audio_list($params = null) {
        return $params;
    }
    
    public function entity_name_entity_id_audio_list($params) {
        return $params;
    }
    
    /*
     * sends the params to the view 
     * 
     * the params include the entity name and the entity id
     * 
     */
    public function entity_name_entity_id_audio_add($params) {
        return  $params;        
    }

    /*
     * validation function checks to see if the file is uploaded 
     *     in a non malicious manner
     *     all the way
     *     if the file extension exists in the audio whitelist
     */    
    public function entity_name_entity_id_audio_add_validate($params) {
        
        $error_system = $params['error'];

        $media_requirements = $params['media_requirements'];
        
        
        $file_management = $params['file_object'];

        
        $file_error_code = $_FILES['audio_file']['error'];

        $file_type =  $_FILES['audio_file']['type'];

        $file_extension = preg_split('/audio\//', $file_type)[1];

        $file_management->set_error_code($file_error_code ); 

        $upload_message = $file_management->generate_upload_response_message();

        $GLOBALS['page']['upload_message'] = $upload_message;
        if( 
            preg_match('/audio/', $file_type) && // the file type is an audio
            in_array($file_extension, $media_requirements->audio_whitelist()) && // the extension exists in the audio whitelist
            $file_management->file_uploaded_sucessfully() // the file was uploaded successfully
        ) {
             return true;        
        }
        else {
            return false;
        }
    }

    /*
     * the sumbit function moves the file to the files folder
     *     gathers all the variables to do the sql
     *     
     *     $time_stamp holds the current creation time_stamp
     * 
     *     $file_name holds the file name of the file
     * 
     *     $node_title comes from the user.
     *           (should be innocent or else)
     * 
     *     $node_body comes from the user 
     *           (obviously it will be completely nice and friendly)
     * 
     *     $author_id comes from the params 
     * 
     *     $author_name comes from the params too
     * 
     *     $file_iteration is the number attached in the event of unlikeyly
     *         file_name clashing
     */
    public function entity_name_entity_id_audio_add_submit($params) {

        $media_requirements = $params['media_requirements'];

        
        $db_connection = $params['connection'];

        $file_management = $params['file_object'];
        
        $date = new DateTime();
        
        

        $time_stamp = $date->getTimestamp();
        
        $file_name = $_FILES['audio_file']['name'];

        $stmt = $db_connection->prepare('select file_iteration from audio_type where '
                . 'file_name = :file_name order by file_iteration desc');
        $stmt->bindParam(":file_name", $file_name);
        
        $results = $stmt->execute();
        
        if(!$results) {
            
            $file_iteration = 0;

        }
        else {
            
             $file_iteration = (int)$stmt->fetch(PDO::FETCH_COLUMN) + 1;
            
        }
        
        if($file_iteration == 0) {
            $abstracted_file_name = $file_management->generate_destination_name_abstraction($time_stamp, $file_name);
        }
        else {
            $abstracted_file_name = $file_management->generate_destination_name_abstraction($time_stamp, $file_name)
                                    . "_" . $file_iteration;
        }
            
        $source_file = $_FILES['audio_file']['tmp_name'];
        
        $node_title = $params['title'];

        $node_body = $params['body'];
        
        $author_id = (int)$params['!entity_id'];
        
        $author_entity = $params['!entity_name'];

        
        $query = "insert into audio_type "
                . "(node_title, node_body, file_name, file_iteration, timestamp, author_entity, author_id)"
                . " values "
                . "(:node_title, :node_body, :file_name, :file_iteration, :time_stamp, :author_entity, :author_id)";
        
        $stmt = $db_connection->prepare($query);
        
        $stmt->bindParam(":node_title", $node_title);
        $stmt->bindParam(":node_body", $node_body);
        $stmt->bindParam(":file_name", $file_name);
        $stmt->bindParam(":file_iteration", $file_iteration);
        $stmt->bindParam(":time_stamp", $time_stamp);
        $stmt->bindParam(":author_entity", $author_entity);
        $stmt->bindParam(':author_id', $author_id);
        
        $stmt->execute();

        $file_management->move_file_to($source_file, 'files', $abstracted_file_name);
    }

    public function audio_get_permissions() {
        
        $permissions = array();
        
        $permissions['audio_list'] = array(
            
            'label' => "List all the current audio created by the author",
            'function' => 'audio_list'
            
        );
        
        $permissions['audio_list_own'] = array(
            
            'label' => "List all the current audio projects available",
            'function' => 'audio_list'
            
        );
        
        $permissions['audio_list_own'] = array(
            
            'label' => "List all the current audio projects available",
            'function' => 'audio_list'
            
        );
        
        return $permissions;
        
        
    }
}
