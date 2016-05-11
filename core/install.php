<?php

class install {
    
    /*
     * this is the connection for the database creation file
     * 
     */
    private $db_connection;
    
    /*
     * the constructor takes in the connection and sets the local connection
     * to the 1 conneciont
     * 
     * it only calls the constructor if it doesn't currently have an existing 
     * database;
     * 
     */
    
    public function __construct($connection) {
        
        $this->db_connection = $connection;
    
        $this->build_empty_tables();
        
        $this->populate_empty_tables();

    }

    /*
     * default tables built
     * 
     */
    public function build_empty_tables() {

        $this->build_user_table();
                
        $this->build_role_types_table();

        $this->build_user_roles_table();
        
        $this->build_role_permissions_table();
        
        $this->build_user_profile_table();
        
        $this->build_node_table();
        
        $this->build_regions_table();
        
        $this->build_node_types_table();

        $this->build_error_log_table();        
        
    }
    
    /*
     * populate empty tables with default values
     * 
     */
    public function populate_empty_tables() {
        $this->add_default_role_types();
    }

    /*
     * 
     *  build the table for users to add node types to the system
     * 
     */
    public function build_node_types_table() {
       $this->db_connection->exec("CREATE TABLE IF NOT EXISTS node_types ("
                          . "node_type_id int(100) auto_increment primary key, "
                          . "node_type varchar(200) not null, "
                          . 'timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,'
                          . "author_id int(20))");
               
               
    }
    /*
     * 
     */
    
    public function build_user_table() {
    
        /*
         * this table holds the machine info for the users
         */
        

        $this->db_connection->exec("CREATE TABLE IF NOT EXISTS users ("
        . "uid int(11) auto_increment primary key,"
        . "user_name varchar(100) not null,"
        . 'timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,'
        . "password varchar(100) not null,"
        . "session_id varchar(100),"
        . "email varchar(100) not null"
        . ")");

    }

    
    public function build_user_profile_table() {
        
        /*
         * the table holds the human information
         * the first name, last name, location, 
         * about
         */
        
        

        $this->db_connection->exec("CREATE TABLE IF NOT EXISTS users_profile ("
        . "uid int(11) primary key,"
        . 'timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,'
        . "first_name varchar(100) not null,"
        . "last_name varchar(100) not null,"
        . "location varchar(100) not null,"
        . "about text"        
        . ")");

    }
        
    /*
     * 
     */
    
    public function build_role_permissions_table() {
    
        /*
         * this table holds the machine info for the users
         */
        

        $this->db_connection->exec("CREATE TABLE IF NOT EXISTS role_permissions ("
        . "role_id int(11) not null,"
        . "permission text not null"
        . ")");

    }


    

    public function build_user_roles_table() {
        
        /*         
         * the roles table holds the users role in order to assign a role to 
         * a user
         * 
         */
        
        

        $this->db_connection->exec("CREATE TABLE IF NOT EXISTS user_roles ("
        . "uid int(11),"
        . "role_id int(10),"       
        . "role_name varchar(20) not null,"        
        . "primary key (uid, role_id)"
        . ")");

    }

    public function build_role_types_table() {
        
        /*         
         * the roles table holds the users role in order to assign a role to 
         * a user
         * 
         */
        
        

        $this->db_connection->exec("CREATE TABLE IF NOT EXISTS role_types ("
        . "role_id int(10) auto_increment,"       
        . "role_name varchar(20) not null,"
        . "primary key (role_id, role_name)"        
        . ")");

    }

    public function build_node_table() {
            
        /*
         * this table holds the join info for the node 
         * the author and the node id which will join to 
         * the particular node
         */
        
        $test = $this->db_connection->exec("CREATE TABLE IF NOT EXISTS nodes ("
        . "nid int(11) auto_increment primary key,"
        . 'timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,'
        . "author varchar(100) not null"
        . ")");
        
    }

    public function build_error_log_table() {
            
        /*
         * this table holds the errors info for the system
         */
        
        $test = $this->db_connection->exec("CREATE TABLE IF NOT EXISTS error_log ("
        . "eid int(11) auto_increment primary key,"
        . 'aid int(11),'
        . 'timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,'
        . "message text not null"
        . ")");
        
    }

    public function build_regions_table() {

        
       $stmt = $this->db_connection->exec("create table if not exists regions ("
               . "url varchar(255) not null,"
               . "region_id int(100) primary key auto_increment,"
               . "region_title varchar(255),"
               . "region_location varchar(255),"               
               . "region_view varchar(255),"               
               . "region_function varchar(255),"
               . "region_validate varchar(255),"
               . "region_submit varchar(255),"
               . "region_object varchar(255),"               
               . 'position_index int(100) not null check (position_index >= 0),'
               . 'timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
               . ")");
               
        
    }

    public function add_default_role_types() {

        $stmt = $this->db_connection->prepare('insert into role_types'
                . ' (role_name) values ("administrative")'
                . ', ("authenticated"), ("anonymous")');

        
        $stmt->execute();        
        
    }
}
