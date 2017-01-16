<?php

/**
 * Created by PhpStorm.
 * User: Rezart Lohja
 * Date: 14.01.17
 * Time: 12:36
 */
class CEUtil
{
    static function getParsedDataFromForm($data){
        $parsed_data=array();
        foreach ($data as $name => $post) {
            $prefix = substr($name, 0, 4);
            if ($prefix == 'name') {

                $id = substr($name, strpos($name, "-") + 1);
                $datasel = 'data-' . $id;
                $parsed_data[] = json_decode($data->$datasel);
            }
        }
        return $parsed_data;
    }
}