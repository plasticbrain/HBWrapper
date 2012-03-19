<?php

/**
 * Example of HBWrapper class usage
 * requires php 5.2
 */

//Include HBWrapper
include 'class.hbwrapper.php';

/**
 * Set HostBill API endpoint
 * @see http://api.hostbillapp.com/info/gettingStarted/
 */
HBWrapper::setAPI('http://urltohostbill.com/admin/api.php', 'YourAPIID', 'YourAPIKEY');

/**
 * HBWrapper is singleton class with magic method __call implemented
 * So calling any method trough its instance will result in remote method call
 *
 * Lets list customers in this HostBill account:
 * @see http://api.hostbillapp.com/clients/getClients/
 */
try {
    $return = HBWrapper::singleton()->getClients();
} catch(Exception $e) {
    //something went wrong...
    exit;
}

//HBWrapper returns PHP array out of HostBill API JSON
print_r($return);



?>