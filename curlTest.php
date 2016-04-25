<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

    function _iscurlsupported() {
            if (in_array ('curl', get_loaded_extensions())) {
                return true;
            }
            else {
                return false;
            }
    }

    if (_iscurlsupported()) {
            echo "cURL is supported\n";
    }
    else {
            echo "cURL isn't supported\n";
    }
?>