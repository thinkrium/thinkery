<?php

require_once _ABSOLUTE_ROOT . '/modules/base/module/module.php';

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class moduleManagement {
    
    private $baseModules = [];
    
    private $coreModules = [];
    
    private $extendedModules = [];
    
    private $contentTypes = [];

    private $moduleBase;
    
    private $databaseConnection;
    
    
    public function __construct($relativeBase, $relativeCore, $relativeExtended, $connection) {
        
        $this->databaseConnection = $connection;
        
        
        /*
         * baseModuleDirectory holds the modules inside of the modules/base
         */
        
        $baseModuleDirectory = _ABSOLUTE_ROOT  . '/' . $relativeBase;
    
        /*
         * coreModuleDirectory holds the modules inside of the modules/core
         */
        
        $coreModuleDirectory = _ABSOLUTE_ROOT  . '/' . $relativeCore;
    
        /*
         * coreModuleDirectory holds the modules inside of the modules/core
         */
        
        $extendedModuleDirectory = _ABSOLUTE_ROOT  . '/' . $relativeExtended;


        /*
         * content_type_path holes the path for the content types
         */
        $content_type_directory = _ABSOLUTE_ROOT . "/content_types/";
        
        
        
        /*
         * scan both directories if both exist and populate the 2 modules
         * arrays for calling their array and instantiating the object
         * 
         */
        
        /* 
         *  calls the method directoryIterator and passes in the container and
         *  the list to go through
         */
        if(is_dir($baseModuleDirectory)) {
            $this->directoryIterator($baseModuleDirectory, $this->baseModules);
        }
        
        if(is_dir($coreModuleDirectory)) {
            $this->directoryIterator($coreModuleDirectory, $this->coreModules);
        }    

        if(is_dir($extendedModuleDirectory)) {
            $this->directoryIterator($extendedModuleDirectory, $this->extendedModules);
        }
        if(is_dir($content_type_directory)) {
            $this->directoryIterator($content_type_directory, $this->contentTypes);
        }

        /*
         * instantiate the core modules 
         */

        $this->instantiateModules($this->baseModules, $baseModuleDirectory, 'base');
        $this->instantiateModules($this->coreModules, $coreModuleDirectory, 'core');
        $this->instantiateModules($this->extendedModules, $extendedModuleDirectory, 'extended');
        $this->instantiateModules($this->contentTypes, $content_type_directory, 'content_types' );
 
    }
    
    /*
     * instantiateCoreModules is used to instantiate core modules
     */
    
    public function instantiateModules ($dirArray, $absolutePath = null, $core_or_extended = null)  {
    
        foreach ($dirArray as $module) {

            if(!is_array($module)) {
            
                $tempFilePath = $absolutePath . '/' . $module . '/' . $module . '.php';

                $tempInstallFilePath = $absolutePath . '/' . $module . '/' . $module . '_install.php';
                
                /*
                 * if the module has an install file then it will call the install file with the connection
                 * injected
                 * 
                 */
                if(file_exists($tempInstallFilePath)) {

                    $install_object = $module . "_install";
                    
                    require_once $tempInstallFilePath;
                    
                    new $install_object($this->databaseConnection);
                }
                
                
                if(file_exists($tempFilePath)) {

                    require_once $absolutePath . '/' . $module . '/' . $module . '.php';
                
                    $function = $module . "AddUrl";
                    
                    /*
                     * check if the appropriate method exists and call the function to add
                     * the url
                     * 
                     * if it doesn't exist we can skip it
                     * 
                     */
                 
                    if(method_exists($module, $function)) {
                    

                    
                        if($core_or_extended == 'core') {
                            array_push($this->coreModules, call_user_func_array(array($module, $function), array()));
                        }
                        else if($core_or_extended == 'extended') {
                            array_push($this->extendedModules, call_user_func_array(array($module, $function), array()));
                       
                        }
                        else if ($core_or_extended == 'base') {
                            array_push($this->baseModules, call_user_func_array(array($module, $function), array()));
                        }
                        else if ($core_or_extended == 'content_types') {
                            
                            array_push($this->contentTypes, call_user_func_array(array($module, $function), array()));
                        }
                    }    
                }
            }
        }        
    }
    
    /*
     * dedicated use for content type management
     * this function returns the content types for management  
     */
    public function getContentTypes() {
        return $this->contentTypes;
    }
    
    
    /*
     * iterates through the called path and pushes into the 
     * respective module array the existing respective modules
     * 
     * the directory container is where the contents of the iterator go
     */
    
    
    public function directoryIterator($path, &$directoryContainer) {
  
        $directoryContainer = array_slice(scandir($path), 2);
    }
    
    /*
     * getUrlArray takes the existing modules and combines them all into one array
     *  
     */
    
    public function getUrlArray() {
       
        /*
         * base_core combines the base and core modules
         * 
         * $modules includes the base_core module variable and the 
         * extended modules
         * 
         * then it merges all the modules with the content types with the
         * content types and returns
         * 
         * urlArray
         */
        
       $base_core = array_merge($this->baseModules, $this->coreModules);
       $modules = array_merge($base_core, $this->extendedModules);
       $urlArray = array_merge($modules, $this->contentTypes);
       
       return $urlArray; 
    }    
    
        /*
     * getUrlArray takes the existing modules and combines them all into one array
     *  
     */
    
    public function get_permissions_array() {
       
        /*
         * base_core combines the base and core modules
         * 
         * $modules includes the base_core module variable and the 
         * extended modules
         * 
         * then it returns the $module 
         */
        
       $base_core = array_merge($this->baseModules, $this->coreModules);
       $modules = array_merge($base_core, $this->extendedModules);
       return $modules; 
    }    

}
