<?php

/**
 * Created by PhpStorm.
 * User: Rezart Lohja
 * Date: 14.01.17
 * Time: 11:09
 */
class WsUsi implements Ws
{
    var $data = array(
        array('titolo' => 'Dinamica e stabilità', 'facolta' => 'DTI', 'corso_laurea' => 'Ingegneria informatica','docenti' =>array('admin','teacher'),'assistenti' =>array('teacher2')),
        array('titolo' => 'Sistemi dinamici discreti',  'facolta' => 'DTI', 'corso_laurea' =>'Ingegneria informatica','docenti' =>array('admin','teacher'),'assistenti' =>array('teacher2')),
        array('titolo' => 'Laboratorio di modellistica ',  'facolta' => 'DTI', 'corso_laurea' =>'Ingegneria informatica','docenti' =>array('admin','teacher'),'assistenti' =>array('teacher2')),
        array('titolo' => 'Algoritmi avanzati',  'facolta' => 'DTI', 'corso_laurea' =>'Ingegneria informatica','docenti' =>array('teacher'),'assistenti' =>array('teacher2')),
        array('titolo' => 'Ottimizzazione',  'facolta' => 'DTI', 'corso_laurea' =>'Ingegneria informatica','docenti' =>array('teacher'),'assistenti' =>array('teacher2')),
        array('titolo' => 'Sistemi operativi',  'facolta' => 'DTI', 'corso_laurea' =>'Ingegneria informatica','docenti' =>array('admin','teacher'),'assistenti' =>array('teacher2')),
        array('titolo' => 'Sistemi di gestione dei dati',  'facolta' => 'DTI', 'corso_laurea' =>'Ingegneria informatica','docenti' =>array('admin','teacher'),'assistenti' =>array('teacher2')),
        array('titolo' => 'Architetture dei computer',  'facolta' => 'DTI', 'corso_laurea' =>'Ingegneria informatica','docenti' =>array('teacher2'),'assistenti' =>array('admin'))
    );
    public function getCorsiPerId($netId)
    {
        $corsi_trovati = array();
        foreach ($this->data as $corso){
            if(in_array($netId,$corso['docenti']) || in_array($netId,$corso['assistenti'])){
                $corsi_trovati[]= $corso;
            }
        }
        return $corsi_trovati;
    }

    public function getCorsiPerStringa($string)
    {
        $corsi_trovati = array();
        foreach ($this->data as $corso){
            if(strstr($corso['titolo'],$string)){
                $corsi_trovati[]= $corso;
            }
        }
        return $corsi_trovati;
    }
}