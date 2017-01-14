<?php

/**
 * Created by PhpStorm.
 * User: Rezart Lohja
 * Date: 14.01.17
 * Time: 00:29
 */

require_once("$CFG->libdir/formslib.php");

class FormNuovo extends moodleform
{
    protected function definition()
    {
        global $USER,$DB;
        // corsi da caricare via web service da usi/supsi
        // $courses = $DB->get_records_sql($query, array($USER->id));
        //var_dump($courses);

        //$statesArray=array();
        //foreach ($courses as $corso){
        //    $statesArray[$corso->instanceid]='<br>'.$corso->fullname.' Ruolo:'.$corso->archetype;
        //}

        $form = $this->_form;
        $statesArray=array();
        foreach ($this->_customdata['corsi'] as $corso){
            var_dump($corso);
            $statesArray[]=$corso->titolo.', '.$corso->facolta.', '.$corso->corso_laurea.' Ruolo :'.implode(',',$corso->docenti);
        }
        $select = $form->addElement('select', 'corsi', get_string('Corsi'), $statesArray);
        $select->setMultiple(true);

        $form->addElement('button', 'intro', "Next", array('style' => 'width:50px;', 'onClick' => 'updateURL(\'nuovo\');'));
    }
}