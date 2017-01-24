<?php

/**
 * Created by PhpStorm.
 * User: Rezart Lohja
 * Date: 14.01.17
 * Time: 11:09
 */

require_once('Ws.php');
class WsSupsi implements Ws
{
    var $data = array(
        array('titolo' => 'Dinamica e stabilita', 'modulo' => 'Modellistica e simulazione', 'dipartimento' => 'DTI', 'corso_laurea' => 'Ingegneria informatica','docenti' =>array('admin','teacher')),
        array('titolo' => 'Sistemi dinamici discreti', 'modulo' => 'Modellistica e simulazione', 'dipartimento' => 'DTI', 'corso_laurea' =>'Ingegneria informatica','docenti' =>array('admin','teacher')),
        array('titolo' => 'Laboratorio di modellistica ', 'modulo' => 'Modellistica e simulazione', 'dipartimento' => 'DTI', 'corso_laurea' =>'Ingegneria informatica','docenti' =>array('admin','teacher')),
        array('titolo' => 'Algoritmi avanzati', 'modulo' => 'Algoritmi avanzati e ottimizzazione', 'dipartimento' => 'DTI', 'corso_laurea' =>'Ingegneria informatica','docenti' =>array('teacher2','teacher')),
        array('titolo' => 'Ottimizzazione', 'modulo' => 'Algoritmi avanzati e ottimizzazione', 'dipartimento' => 'DTI', 'corso_laurea' =>'Ingegneria informatica','docenti' =>array('teacher2','teacher')),
        array('titolo' => 'Sistemi operativi', 'modulo' => 'Sistemi operativi e di gestione dei dati', 'dipartimento' => 'DTI', 'corso_laurea' =>'Ingegneria informatica','docenti' =>array('admin','teacher')),
        array('titolo' => 'Sistemi di gestione dei dati', 'modulo' => 'Sistemi operativi e di gestione dei dati', 'dipartimento' => 'DTI', 'corso_laurea' =>'Ingegneria informatica','docenti' =>array('admin','teacher')),
        array('titolo' => 'Architetture dei computer', 'modulo' => 'Architetture dei computer', 'dipartimento' => 'DTI', 'corso_laurea' =>'Ingegneria informatica','docenti' =>array('admin','teacher2'))
    );

    public function getCorsi($netId=null,$string=null)
    {
        $corsi_trovati=$this->data;
        $base_ricerca = $this->data;
        if($netId){
            $corsi_trovati = array();
            foreach ($base_ricerca as $corso){
                if(in_array($netId,$corso['docenti'])){
                    $corsi_trovati[]= $corso;
                }
            }
            $base_ricerca=$corsi_trovati;
        }

        if($string){
            $corsi_trovati = array();
            foreach ($base_ricerca as $corso){
                if(stristr($corso['titolo'],$string)){
                    $corsi_trovati[]= $corso;
                }
            }
        }

        return $corsi_trovati;
    }


}