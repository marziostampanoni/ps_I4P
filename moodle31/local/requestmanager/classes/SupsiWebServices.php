<?php
namespace local_requestmanager;
/**
 * Created by PhpStorm.
 * User: Rezart Lohja
 * Date: 15.01.17
 * Time: 09:05
 */

class SupsiWebServices extends WebServices
{

    function __construct()
    {
        $this->url = get_config('local_requestmanager','supsi_host');
        $this->private_key = get_config('local_requestmanager','supsi_private_key');
    }
    function resetUrl()
    {
        $this->params = array();
    }

}