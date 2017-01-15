<?php

/**
 * Created by PhpStorm.
 * User: Rezart Lohja
 * Date: 15.01.17
 * Time: 09:05
 */
require_once "WebServicesInterface.php";
abstract class WebServices implements WebServicesInterface
{
    var $url;

    function getCorsiPerNetID($netId)
    {
        $this->url .= "?netid=$netId";
        return $this->executeCurl();
    }

    function getCorsiPerString($string)
    {
        $this->url .= "?string=$string";
        return $this->executeCurl();
    }

    function getCorsi()
    {
        return $this->executeCurl();
    }

    function executeCurl(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_URL,$this->url);
        $result=curl_exec($ch);
        curl_close($ch);
        return json_decode($result);
    }
}