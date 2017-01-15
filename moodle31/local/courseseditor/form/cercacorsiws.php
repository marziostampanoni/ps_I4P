<?php

/**
 * Created by PhpStorm.
 * User: Rezart Lohja
 * Date: 14.01.17
 * Time: 00:29
 */

require_once("$CFG->libdir/formslib.php");

class FormCercaCorsiWs extends moodleform
{
    protected function definition()
    {
        global $USER;
        $form = $this->_form;
        $buttonarray=array();
        $buttonarray[]=&$form->createElement('text', 'string','',array('placeholder'=>get_string('String di ricerca','local_courseseditor')));
        $buttonarray[]=&$form->createElement('checkbox', 'onlythis', '',get_string('Solo corsi di','local_courseseditor').' '.$USER->username,array('class'=>'form-check-input','type'=>'checkbox'));
        $buttonarray[] = &$form->createElement('submit', 'submitusi', get_string('search').' in USI');
        $buttonarray[] = &$form->createElement('submit', 'submitsupsi', get_string('search').' in SUPSI');
        $form->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $form->closeHeaderBefore('buttonar');
    }
}