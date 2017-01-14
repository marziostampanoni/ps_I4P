<?php

/**
 * Created by PhpStorm.
 * User: Rezart Lohja
 * Date: 14.01.17
 * Time: 11:06
 */
interface Ws
{
    public function getCorsi();
    public function getCorsiPerId($netID);
    public function getCorsiPerStringa($string);
}