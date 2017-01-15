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

        $form = $this->_form;

        foreach ($this->_customdata['corsi'] as $corso) {
            $details = '<b>' . $corso->titolo . ', '.$corso->facolta .', '.$corso->corso_laurea. ', Ruolo: ' . implode(',',$corso->docenti);
            $form->addElement('checkbox', 'name-' . $corso->instanceid, '', $details, array('value' => 'asd'), array('value' => 'asd'));
            $data = array('id' => null,
                'title' => $corso->titolo,
                'cat' => $corso->facolta,
                'teachers' => array(array('id' => 'id_teacher1', 'name' => 'name_teacher1TODO'), array('id' => 'id_teacher2', 'name' => 'name_teacher2TODO')), 'editingteacher' => array(array('id' => 'id_editingteacher1', 'name' => 'name_editingteacher1TODO'), array('id' => 'id_editingteacher2', 'name' => 'name_editingteacher2TODO')));
            $form->addElement('hidden', 'data-' . $corso->instanceid, json_encode($data));
        }


        $statesArray=array();
        foreach ($this->_customdata['corsi'] as $corso){
            $statesArray[]=$corso->titolo.', '.$corso->facolta.', '.$corso->corso_laurea.' Ruolo :'.implode(',',$corso->docenti);
        }
        $select = $form->addElement('select', 'corsi', get_string('Corsi'), $statesArray);
        $select->setMultiple(true);

        $form->addElement('submit', 'sbmt', "Next", array('style' => 'width:50px;'));
    }
}