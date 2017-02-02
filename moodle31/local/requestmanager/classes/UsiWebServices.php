<?php
namespace local_requestmanager;
class UsiWebServices extends WebServices
{

    function __construct()
    {
        $this->url = get_config('local_requestmanager','usi_host');
        $this->private_key = get_config('local_requestmanager','usi_private_key');
    }

    function resetUrl()
    {
        $this->params = array();
    }

}