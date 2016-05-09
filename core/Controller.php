<?php
/*
 *
 * the controller class is my router of sorts. It takes the url and parses it for use as arguments and page loading
 *
 *  $session - holds the session data for use in grabbing uid, and, and checks if the user is anonymous or logged in
 *             
 *  $moduleManagement - holds the directory contents for base, core, and extended modules
 *
 *  $permissionManagemet - holds the modules various permissions
 *
 *  $errors - holds the error system for use in logging malicious intention
 *
 */
 

class Controller {

    private $url;
    
    private $homePage;
    
    private $arguments = [];
    
    private $databaseConnection;
    
    private $session;
    
    private $moduleManagement;
    
    private $regionManagement;
    
    private $permissionManagement;

    private $errors;
    
    private $fileManagement;
    
    private $mediaRequirement;

    private $data;
    
    private $renderObject;
    
    public  $pageBuilder;
    
    
    
    
    public function __construct($connection, 
                                $moduleManagementObject,
                                $regionManagmentObject,
                                $session, 
                                $permissionManagement, 
                                $errors,
                                $fileManagement,
                                $mediaRequirement,
                                $render
                                ) {
        global $page;

        /*
         * set data to an array so that you can fill it with content from functions
         * for views
         * 
         */
        $this->data = array();
        
        /*
         * maintain session status with the session object
         */
        $this->session = $session;
        
        /*
         * set the connection to the variable passed in from database connection object
         */

        $this->databaseConnection = $connection;

        /*
         * manage permisions with the permission management object
         * 
         */
        $this->permissionManagement = $permissionManagement;

        
        /*
         * region management aquires the database dynamic regions created with variable urls
         */
        $this->regionManagement = $regionManagmentObject;
        /*
         * the module management object iterates through the core and extended modules 
         * and instantiates the modules and then populates the 
         * url array to match for parsing
         */
        $this->moduleManagement = $moduleManagementObject;
        
        /*
         * create the controller error system;
         */
        $this->errors = $errors;

        /*
         * file management object 
         * 
         */
        $this->fileManagement = $fileManagement;
        
        /*
         * the whitelist for the file types
         * 
         */
        
        $this->mediaRequirements = $mediaRequirement;
        

        /*
         * call url parse for when using router
         * 
         */
        
        /*
         * call the url parsing function to create the parameters for the the rest of the 
         * functionality
         * 
         */
        
        
        $this->parseUrl();
        
        /*
         *  most of the work for the entire project is doen in route page. it sorts out the permissions
         *
         *  it takes advantage of the parseUrl function to find the proper function and page
         * 
         *  calls the proper function and sends in the proper view[s] to the output buffer
         * 
         */
        $this->routePage();
        
        /*
         * renderObject is the variable that builds the page
         * 
         * it holds the object that processes and displays the p
         */
        $this->renderObject = $render;

    }
    
    /*
     * parseArgs grabs the arguments out of the url and returns the array if tokenized url
     * 
     */
    public function parseArguments($function) {
        $args = array();
        
        return $args;
    }
    
    /*
     *
     *  parseUrl is a function takes the get request from the url and parses it into arguments
     *  
     *  it uses the constant filter_sanitize_url to make sure special characters are not allowed
     *
     */
     
    public function parseUrl()  {
       
      
      if(isset($_GET['url'])) {
          
          $this->url = rtrim($_GET['url'], '/');

          
          $this->arguments = explode('/', filter_var($this->url, FILTER_SANITIZE_URL));
          
      }
      else {
          
      }
    
    }
    
    /*
     * 
     *  buildPage is for later use when the render object is created to build the page systematically
     *
     */
     
    public function buildPage() {

       require_once _ABSOLUTE_ROOT . '/html.view';

        
    }
    
    /*
    
    /*
     * pass through the function looking for the exact key 
     * if the exact key exists then you know that you dont have to 
     * process any tokens and you can just go straight to the key
     * 
     * if it fails it will return false
     */
    public function get_literal_url($correct_url, $url_array) {

        $return = false;
        
        for($index = 0; $index < count($url_array); $index++) {
            $url = key($url_array[$index]);
            
            $function = $url_array[$index][$url]['function'];
            
            $url_parameters = $url_array[$index][$url];

 
            if($correct_url == $url && $url != '*') {
                if( 
                    (
                        ($this->permissionManagement->permission_granted($function, $url) &&
                        $this->session->get_uid() > 0) &&
                        
                        $correct_url != 'user/login' && $correct_url != 'user/register'
                    )
                                        
                        ||
  
                        ($correct_url == 'user/login' && $this->session->get_uid() == 0) 
                                        
                        ||
  
                        ($correct_url == 'user/register' && $this->session->get_uid() == 0) 
                ) {
                    
                    $return = array();
                    $return['url'] = $url;
                    $return['parameters'] = $url_parameters;
                    $this->create_content($url_parameters);

                    
                }
            }
        }
        
        return $return;
    }
    
    /*
     * iterate through the modules and find the tokenized url
     * this will only be called if the get_literal_url fails
     * 
     * it sorts through the tokenized urls and grabs the proper arrangemeent of 
     * url arguments
     */
    public function get_tokenized_url($correct_url, $url_array) {
        
        $str = '';
        
        $return = false;

        /*
         * instantiate the arguments index
         */
        $matchingArgumentsIndex = 0;
        
        /*
         * instantiate the parameters array
         */
        $functionParameters = array();
        
        for($index = 0; $index < count($url_array); $index++) {

            $url = key($url_array[$index]);
            
            $function = $url_array[$index][$url]['function'];
            
            $url_parameters = $url_array[$index][$url];

            if(preg_match('/!/', $url)) {
            
                

                $matchingArgumentsIndex = 0;


                $urlArguments = explode('/', $url);

                
                if(sizeof($this->arguments) == sizeof($urlArguments)) {

                    $str .= "</br>$url";


                    /*
                     * the url doesn't match directly but may find a supported
                     * tokened array for calling with parameters
                     * 
                     */
                    
                    $properKeys = array_keys(preg_grep("/!/", $urlArguments));


                    if(sizeof($properKeys) == 0)  {

                          
                    }     
                    else  {
                                        
                        foreach($properKeys as $proper_key) {
                                 
                            $argumentKeys[$proper_key] = $proper_key;
                        }   
                    }

                    /*
                     * check for the iteration of the argument array 
                     * if the the key isn't in the argument key array then 
                     * it checks if the index of the argument url is the same as the possible array
                     * 
                     * if the array does exist then it takes the url and sends it to the paremter with keys
                     * and the $replacement token becomes the key and is sent into the 
                     * function
                     * 
                     */
                    
                    
                    /*
                     * set up a default correctUrl to be true;
                     * 
                     * if at any point the url arguments dont match then
                     * change the default to false and then
                     * 
                     * escape the loop
                     * 
                     */
                                
                    /*
                     * the correct_url is a variable 
                     * that is validated for escaping the loop
                     * 
                     */
                    $correctUrl = true;

                    
                    for($ind = 0; $ind < sizeof($urlArguments); $ind++) {

                        /*
                         * is the index of the url a token
                         *  if so capture the token
                         * 
                         * is the argument key not a key in the array
                         *  then check if the same indexes match the value
                         */
                         
                        if(!array_key_exists($ind, $argumentKeys)) {


                            if($this->arguments[$ind] == $urlArguments[$ind]) {

                                $matchingArgumentsIndex++;
                                $correctUrl = true;
                            }
                            else {

                                $correctUrl = false;
                            }
                        }
                        else {
                            $matchingArgumentsIndex++;
                            $functionParameters[$urlArguments[$ind]] = $this->arguments[$ind];
                                                               
                        }
                            
                        if(!$correctUrl) {
                            
                            /*
                             * leave loop if it finds an ill matching element
                             */

                            break;
                        }
                            
                        if($matchingArgumentsIndex == sizeof($this->arguments)) {
                            /*
                             * find all the universal regions of information
                             * 
                             * 
                             */

                            if( 
                                  (
                                    ($this->permissionManagement->permission_granted($function, $url) &&
                                     $this->session->get_uid() > 0) &&
                        
                                      $correct_url != 'user/login' && $correct_url != 'user/register'
                                  )
                                        
                                 ||
  
                                 ($correct_url == 'user/login' && $this->session->get_uid() == 0) 
                                         
                                  ||
  
                                  ($correct_url == 'user/register' && $this->session->get_uid() == 0) 

                                ) {      

                                
                                $return = array();
                                $return['url'] = $url;
                                $return['parameters'] = $url_parameters;

                                $this->create_content($url_parameters, $functionParameters);
                            }    
                        }                    
                    }    
                }
            } 
                
        }
            
        return $return;
        
        
    }

    /*
     * pass through the function looking for the '*'
     * if the 'key '*' exists then you know that you dont have to 
     * process any tokens and you can just go straight to the key
     * 
     * if it fails it will return false
     */
    public function get_regions($correct_url, $url_array) {

        /*
         *  initiate at false only to return an established array 
         */
        $return = false;

        for($index = 0; $index < count($url_array); $index++) {
            $url = key($url_array[$index]);
            
            $function = $url_array[$index][$url]['function'];
            
            $url_parameters = $url_array[$index][$url];

            if($url == '*') {
                if( 
                    $this->permissionManagement->permission_granted($function, $url)
                ) {      

                    $return = array();
                    $return['url'] = $url;
                    $return['parameters'] = $url_parameters;
                    $this->create_content($url_parameters);
                }
            }    
       }
        return $return;
    }

    /* 
     * use route page to run the primary functionality of the router
     * it sets the page in global and creates the data array
     */
    public function routePage() {
        
        $GLOBALS['page'] = array();
                
        $urls = $this->regionManagement->getUrlArray();
        

        /*
         * 
         * if the size of this->arguments is equal to 0 or as in 
         * no arguments are passed it is the home page
         * 
         */
        if(sizeof($this->arguments) ==  0) { 

            $this->create_home_page();
        }
        
        /*
         *  if the size of the array that holds the arguments is the same 
         *  size of the url key sort through the url array and find the matching key
         * 
         */
        
        else {
            
            /*
             * check to see if the exact url matches in the url array
             * if it does then return the url array for processing
             * 
             */
        
            /*
             * look for the literal url
             */
            $object = $this->get_literal_url($this->url, $urls);

            /*
             * if the literal url fails then tokenize the url and process the 
             * parameters 
             */

        
            if(!$object) {
                $object = $this->get_tokenized_url($this->url, $urls);
            }
        
            /*
             * build all the regions
             */
            $this->get_regions($this->url, $urls);

        }
    return 0;
    }
  
    /*
     * funct is the parameter that holds all that content
     * 
     * $args are sent from the it is null if it 
     * region_page is the variable that holds the decision whether to return or not
     * 
     * if its page then return if its region then it has no return;
     */
    
    public function create_content($obj, $args = null) {

        /*
        * the url matches the existing url and calls that array of
        * functions
        */
        
                            
        $object = $obj['object'];
                            
        $function = $obj['function'];
        
        $location = $obj['location'];
        
        $view = $obj['view'];
        
        $region = $obj['region'];
        
        
        $class = new $object($this->databaseConnection, $this->session, $this->errors);

        if(method_exists($class, $function)) {
            if($args == null) {
                $data = $class->$function();
            }
            else {
                $data = $class->$function($args);                
            }
        }
        else {
            exit("Your url array calls for a method that doesn't exist yet. " 
            . "Please create the method</br> <h1>Need to turn this into "
            . "  error management with 401 and 300 errors and 500 errors</h1>"
            );
        }


        require_once $location . '/' .  $view;
                                

        $GLOBALS['page'][$region] = ob_get_contents();

    }

   /*
    * create_home_page  creates the front page ... just as it says
    * 
    */
    public function create_home_page() {

        $this->homePage = true;
        /*
         * search for nodes or front page content and if it exists then output 
         * the content if not output hello site
         */
        require_once _ABSOLUTE_ROOT . "/modules/core/node/home.view";
        $GLOBALS['page']['content'] = ob_get_contents();
            
        return 0;
        
    }
    
    /*
     * get_error_page takes in an argument and sends in the simulated error page
     * as well as sends the appropriate error to the server
     *
     */
    public function get_error_page($num) {
        require_once _ABSOLUTE_ROOT . '/modules/core/node/error_pages/error_' . $num . '.view';
        $GLOBALS['page'][$num . "_error"] = ob_get_contents();
//        ob_start();
        
        return;

    }
} 