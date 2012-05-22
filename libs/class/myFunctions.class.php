<?php

/* 
 * PHP functions I usually use
 */

class myFunctions{

    public function pr($str){
        echo "<pre>";
        print_r($str);
        echo "</pre>";
    }

    public function isBlank($str){
        //check if $str is blank(space is regarded as blank)
        //return true if blank
        if( mb_strlen(str_replace(" ","",mb_convert_kana($str,"s"))) > 0){
            return false;
        }else{
            return true;
        }
    }

    public function h($str){
        // htmlspecialchar with ENT_QUOTES
        // $str can be array
        if(is_array($str)){
            return array_map(array($this,'h'),$str);
        }else{
            return htmlspecialchars($str,ENT_QUOTES);
        }
    }
    
    public function q($str){
        return mysql_real_escape_string($str);
    }
}