<?php
namespace local_requestmanager;
/**
 * Created by PhpStorm.
 * User: Rezart Lohja
 * Date: 15.01.17
 * Time: 09:05
 */

abstract class WebServices implements WebServicesInterface
{
    var $url;
    var $params;
    var $private_key;

    abstract function resetUrl();

    function getCorsiPerNetID($netId)
    {
        $this->params['netid'] = "$netId";
        return $this->executeCurl();
    }

    function getCorsiPerString($string)
    {
        $this->params['string'] = "$string";
        return $this->executeCurl();
    }

    function getCorsi($netId=null,$string=null)
    {
        if($netId) $this->params['netid'] = "$netId";
        if($string) $this->params['string'] = "$string";

        return $this->executeCurl();
    }

    function executeCurl(){

        $this->params['private_key']=$this->private_key;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST,TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$this->params);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_URL,$this->url);
        $result=curl_exec($ch);
        curl_close($ch);
        $res=json_decode($result);
        if(!json_last_error()) return $res;
        else{
            echo "JSON ERROR! ".json_last_error();
            return false;
        }

    }
}