<?php

/**
 * Created by PhpStorm.
 * User: Rezart Lohja
 * Date: 15.01.17
 * Time: 09:05
 */

class UsiWebServices extends WebServices
{

    function __construct()
    {
        $this->url = get_config('local_courseseditor','usi_host');
    }

}