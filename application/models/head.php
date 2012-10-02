<?php

class Head extends CI_Model{
        
    public $array;  
    
    function __construct(){
               
            $this->array["title"] = "";
            $this->array["copyright"] = "&copy HiLife";
    }
    
    public function reArray($opts){
            
        foreach($opts AS $key => $value)
        {
            $this->array[$key] = $value;
        }
    }
    
}

?>
