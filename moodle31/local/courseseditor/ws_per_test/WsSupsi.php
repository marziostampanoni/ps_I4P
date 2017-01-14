<?php

/**
 * Created by PhpStorm.
 * User: Rezart Lohja
 * Date: 14.01.17
 * Time: 11:09
 */
class WsSupsi implements Ws
{
    var $data = array(
        array('titolo' => 'Dinamica e stabilità', 'modulo' => 'Modellistica e simulazione', 'dipartimento' => 'DTI', 'corso_laurea' => 'Ingegneria informatica','docenti' =>array('admin','teacher')),
        array('titolo' => 'Sistemi dinamici discreti', 'modulo' => 'Modellistica e simulazione', 'dipartimento' => 'DTI', 'corso_laurea' =>'Ingegneria informatica','docenti' =>array('admin','teacher')),
        array('titolo' => 'Laboratorio di modellistica ', 'modulo' => 'Modellistica e simulazione', 'dipartimento' => 'DTI', 'corso_laurea' =>'Ingegneria informatica','docenti' =>array('admin','teacher')),
        array('titolo' => 'Algoritmi avanzati', 'modulo' => 'Algoritmi avanzati e ottimizzazione', 'dipartimento' => 'DTI', 'corso_laurea' =>'Ingegneria informatica','docenti' =>array('teacher2','teacher')),
        array('titolo' => 'Ottimizzazione', 'modulo' => 'Algoritmi avanzati e ottimizzazione', 'dipartimento' => 'DTI', 'corso_laurea' =>'Ingegneria informatica','docenti' =>array('teacher2','teacher')),
        array('titolo' => 'Sistemi operativi', 'modulo' => 'Sistemi operativi e di gestione dei dati', 'dipartimento' => 'DTI', 'corso_laurea' =>'Ingegneria informatica','docenti' =>array('admin','teacher')),
        array('titolo' => 'Sistemi di gestione dei dati', 'modulo' => 'Sistemi operativi e di gestione dei dati', 'dipartimento' => 'DTI', 'corso_laurea' =>'Ingegneria informatica','docenti' =>array('admin','teacher')),
        array('titolo' => 'Architetture dei computer', 'modulo' => 'Architetture dei computer', 'dipartimento' => 'DTI', 'corso_laurea' =>'Ingegneria informatica','docenti' =>array('admin','teacher2'))
    );
    public function getCorsiPerId($netId)
    {
        $corsi_trovati = array();
        foreach ($this->data as $corso){
            if(in_array($netId,$corso['docenti'])){
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