<?php
namespace local_requestmanager;

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