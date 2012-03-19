<?php
/**
 * HBWrapper - a simple, cURL-based utility to use with HostBill API http://api.hostbillapp.com/
 * Requires PHP 5.2+ with json support (http://php.net/manual/en/book.json.php), curl support (http://php.net/manual/en/book.curl.php)
 *
 * Sample usage:
 * //set api details - just once
 * HBWrapper::setAPI('http://url_to_hb/admin/api.php','API_ID','API_KEY');
 * //call api method:
 * $methods = HBWrapper::singleton()->getAPIMethods();
 *
 * @author Quality Software - HostBill
 */
class HBWrapper {

    /**
     * cURL timeout in seconds
     * @var integer
     */
    public $timeout=100;

    /**
     * Holds singleton instance of HBWrapper
     * @ignore
     * @var HBWrapper
     */
    private static $instance;

    /**
     * HostBill API url to connect, fill using HBWrapper::setAPI
     * @var string
     */
    static $apiURL='';

    /**
     * HostBill API ID to use fill using HBWrapper::setAPI
     * @var string
     */
    static $api_id='';

    /**
     * HostBill API KEY to use fill using HBWrapper::setAPI
     * @var string
     */
    static $api_key='';

    /**
     * Set API connection details
     * @param string $api_url HostBill api url. I.e.: http://hostbillapp.com/admin/api.php - use full url
     * @param string $api_id HostBill API ID to use, get API ID/KEY from Settings->Security->API
     * @param string $api_key HostBill API KEY to use, get API ID/KEY from Settings->Security->API
     */
    public static function setAPI($api_url,$api_id,$api_key) {
        self::$apiURL=$api_url;
        self::$api_id=$api_id;
        self::$api_key=$api_key;
    }


    /**
     *
     * @param <type> $response
     * @return <type>
     */
    private function parseResponse($response) {
        $re = json_decode($response, true);
        if (!is_array($re)) {
            throw new Exception('Unable to parse response, no JSON data returned, got string instead: '.$response);
        }
        return $re;
    }

    /**
     * Internal method to call remote HostBill API using cURL
     * @param array $post
     * @return array
     */
    private function makeRequest($post) {
        $post['outputformat']='json';
        $post['api_id']=  HBWrapper::$api_id;
        $post['api_key']=HBWrapper::$api_key;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, HBWrapper::$apiURL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        $data = curl_exec($ch);
        curl_close($ch);
        return $this->parseResponse($data);
    }


    /**
     * Return one, singleton instance of HBwrapper
     * @return HBWrapper
     */
    public static function singleton() {
        if( !isset( self::$instance ) ) {
            $obj = __CLASS__;
            self::$instance = new $obj;
        }
        return self::$instance;
    }


    /**
     * Set API url
     * @param string $url
     * @deprecated
     */
    public function setAPIUrl($url) {
        $this->apiURL=$url;
    }

    /**
     * Magic PHP function to call missing methods
     * @param string $name
     * @param array $arguments
     * @return array
     * @ignore
     */
     public function  __call($name, $arguments) {
          $in= isset($arguments[0])?$arguments[0]:array();
          if(is_object($in)) {
              $in = json_encode($in);
              $in = json_decode($in,true);
          }
           $in['call']=$name;
           return $this->makeRequest($in);
    }

    /*
     * INTERNAL METHODS
     */

    /**
     * Private constructor for singleton, perform self-test for compatibility
     * @ignore
     */
    private function __constructor() {
        $this->selfTest();
    }

    /**
     * Test for required libs
     * @return boolean
     */
    private function selfTest() {
        $curl=false;
        $json=false;

        if (function_exists('json_decode')) {
            $json=true;
        } else {
            throw new Exception('JSON extension is not present, HBwrapper will not work. http://php.net/manual/en/book.json.php');
        }
        if (function_exists('curl_init')) {
            $curl=true;
        } else {
            throw new Exception('cURL extension is not present, HBwrapper will not work. http://php.net/manual/en/book.curl.php');
        }

        if($curl && $json) {
            return true;
        }
        return false;
    }

    /*
     * DEPRECATED METHODS
     */

    /**
     * DO NOT USE THIS FUNCTION for HostBill > 3.0
     * @ignore
     * @deprecated
     * @param string $login
     * @param string $pass
     * @return boolean
     */
    public function LogIn($login, $pass) {
        return true;
    }

    /**
     * @ignore
     * @deprecated
     * @return boolean
     */
    public function isLoggedIn() {
        return true;
    }



}
?>