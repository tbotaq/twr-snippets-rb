<?php

/* 
 * my class for smarty
 */

class mySmarty extends Smarty {

    function __construct(){
        parent::__construct();

        $this->template_dir = dirname(__FILE__).'/../../templates/';
        $this->compile_dir  = dirname(__FILE__).'/../../templates_c/';
        $this->config_dir   = dirname(__FILE__).'/../../configs/';
        $this->cache_dir    = dirname(__FILE__).'/../../cache/';
        $this->left_delimiter  = '<{';
        $this->right_delimiter = '}>';

    }
}