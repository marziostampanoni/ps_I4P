<?php

/**
 * Created by PhpStorm.
 * User: Rezart Lohja
 * Date: 14.01.17
 * Time: 11:09
 */
require_once('Ws.php');

class WsUsi implements Ws
{
    var $data = array(
        array('titolo' => 'Usi Dinamica e stabilita', 'facolta' => 'DTI', 'corso_laurea' => 'Ingegneria informatica','docenti' =>array('admin','teacher'),'assistenti' =>array('teacher2')),
        array('titolo' => 'Usi Sistemi dinamici discreti',  'facolta' => 'DTI', 'corso_laurea' =>'Ingegneria informatica','docenti' =>array('admin','teacher'),'assistenti' =>array('teacher2')),
        array('titolo' => 'Usi Laboratorio di modellistica ',  'facolta' => 'DTI', 'corso_laurea' =>'Ingegneria informatica','docenti' =>array('admin','teacher'),'assistenti' =>array('teacher2')),
        array('titolo' => 'Usi Algoritmi avanzati',  'facolta' => 'DTI', 'corso_laurea' =>'Ingegneria informatica','docenti' =>array('teacher'),'assistenti' =>array('teacher2')),
        array('titolo' => 'Usi Ottimizzazione',  'facolta' => 'DTI', 'corso_laurea' =>'Ingegneria informatica','docenti' =>array('teacher'),'assistenti' =>array('teacher2')),
        array('titolo' => 'Usi Sistemi operativi',  'facolta' => 'DTI', 'corso_laurea' =>'Ingegneria informatica','docenti' =>array('admin','teacher'),'assistenti' =>array('teacher2')),
        array('titolo' => 'Usi Sistemi di gestione dei dati',  'facolta' => 'DTI', 'corso_laurea' =>'Ingegneria informatica','docenti' =>array('admin','teacher'),'assistenti' =>array('teacher2')),
        array('titolo' => 'Usi Architetture dei computer',  'facolta' => 'DTI', 'corso_laurea' =>'Ingegneria informatica','docenti' =>array('teacher2'),'assistenti' =>array('admin'))
    );

    public function getCorsi($netId=null,$string=null)
    {
        $corsi_trovati=$this->data;
        $base_ricerca = $this->data;
        if($netId){
            $corsi_trovati = array();
            foreach ($base_ricerca as $corso){
                if(in_array($netId,$corso['docenti']) || in_array($netId,$corso['assistenti'])){
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