<?php

if( ! function_exists('md5_hash') ){
    
    function md5_hash($pass){
        return md5( md5('saltsalt') . $pass . 'saltsalt');
    }
    
}

?>
