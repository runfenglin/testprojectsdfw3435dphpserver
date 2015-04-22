<?php
namespace AppBundle\Service;

use Symfony\Component\DependencyInjection\Container;

class CurlService
{
    protected $_environment;
    
    protected $_result;
    
    public function __construct($env) {
        $this->_environment = $env;
    }
    /** 
    * Send a POST requst using cURL 
    * @param string $url to request 
    * @param array $post values to send 
    * @param array $options for cURL 
    * @return string 
    */ 
    public function curlPost($url, array $post = NULL, array $options = array()) 
    { 
        $defaults = array( 
            CURLOPT_POST => 1, 
            CURLOPT_HEADER => 0, 
            CURLOPT_URL => $url, 
            CURLOPT_FRESH_CONNECT => 1, 
            CURLOPT_RETURNTRANSFER => 1, 
            CURLOPT_FORBID_REUSE => 1, 
            CURLOPT_TIMEOUT => 4, 
            CURLOPT_POSTFIELDS => http_build_query($post) 
        ); 
        
        if ($this->_environment != 'prod') {
            $defaults[CURLOPT_SSL_VERIFYPEER] = 0;
        }

        $ch = curl_init(); 
        curl_setopt_array($ch, ($options + $defaults)); 
        if( ! $this->_result = curl_exec($ch)) 
        { 
            trigger_error(curl_error($ch)); 
        } 
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $status; 
    } 

    /** 
    * Send a GET requst using cURL 
    * @param string $url to request 
    * @param array $get values to send 
    * @param array $options for cURL 
    * @return string 
    */ 
    public function curlGet($url, array $get = NULL, array $options = array()) 
    {    
        $defaults = array( 
            CURLOPT_URL => $url . (strpos($url, '?') === FALSE ? '?' : ''). http_build_query($get), 
            CURLOPT_HEADER => 0, 
            CURLOPT_RETURNTRANSFER => TRUE, 
            CURLOPT_TIMEOUT => 4 
        ); 

        if ($this->_environment != 'prod') {
            $defaults[CURLOPT_SSL_VERIFYPEER] = 0;
        }
    
        $ch = curl_init(); 
        curl_setopt_array($ch, ($options + $defaults)); 
        if( ! $this->_result = curl_exec($ch)) 
        { 
            trigger_error(curl_error($ch)); 
        } 
        
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch); 

        return $status;
    } 
    
    public function getResult()
    {
        return $this->_result;
    }
}