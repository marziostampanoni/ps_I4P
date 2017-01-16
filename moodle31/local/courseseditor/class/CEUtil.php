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

    static function getParsedDataFromFormResume($data){
        $parsed_data=array();
        foreach ($data as $name => $post) {
            if(strpos($name, "-")>0) {
                $id = substr($name, strpos($name, "-") + 1);
                $campo = substr($name, 0, strpos($name, "-"));
                if($campo=='teachers' || $campo=='editingteachers'){
                    if(is_array($post)){
                        foreach ($post as $t)$parsed_data['corsi'][$id][$campo][]=$t;
                    }else{$parsed_data['corsi'][$id][$campo]=array($post);}
                }else $parsed_data['corsi'][$id][$campo] = $post;
            }else{
                $parsed_data[$name]=$post;
            }
        }
        return $parsed_data;
    }

}