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

    function getCorsi($netId=null,$string=null)
    {
        if($netId) $param[]="netid=$netId";
        if($string) $param[]="string=$string";
        if($param)$this->url .= "?".implode('&',$param);
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
        $res=json_decode($result);
        if(!json_last_error()) return $res;
        else return "JSON ERROR! ".json_last_error();

    }
}