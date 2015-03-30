<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class UtilityHelper {

    /**
     * 
     * Print for testing data
     * @param mixed $var
     * @return string
     * 
     */
    function pr($var) {
        echo '<pre style="background: #000; color: #31F190;">';
        echo 'I am printing for test<br>---------------------<br>';
        echo (is_bool($var)) ? var_dump($var) : '';
        print_r($var);
        echo '</pre>';
        die();
    }

    function json_message($status, $message, $data) {
        return json_encode(array(
            'status' => $status,
            'message' => $message,
            'data' => array($data)
        ));
    }

    function jsonp_message($status, $message, $data) {
        return "successCallback(" . json_encode(array(
                    "status" => $status,
                    "message" => $message,
                    "data" => array($data)
                )) . ";";
    }
    
   
}
