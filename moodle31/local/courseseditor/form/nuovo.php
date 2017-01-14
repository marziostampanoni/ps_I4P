<?php

/**
 * Created by PhpStorm.
 * User: Rezart Lohja
 * Date: 14.01.17
 * Time: 00:29
 */

require_once("$CFG->libdir/formslib.php");

class NuovoForm extends moodleform
{
    var $corsi=null;

    function setCorsi($corsi){

        $this->corsi=$corsi;
        var_dump($this->corsi);
    }
    protected function definition()
    {
        global $USER,$DB;
        $query="
SELECT *  FROM
mdl_user u
JOIN mdl_role_assignments ra ON ra.userid = u.id
JOIN mdl_role r ON ra.roleid = r.id
JOIN mdl_context con ON ra.contextid = con.id
JOIN mdl_course c ON c.id = con.instanceid AND con.contextlevel = 50
WHERE u.id=? AND (r.shortname = 'teacher' OR r.shortname = 'editingteacher' OR r.shortname = 'manager')";

        $courses = $DB->get_records_sql($query, array($USER->id));
//var_dump($courses);

        $statesArray=array();
        foreach ($courses as $corso){
            $statesArray[$corso->instanceid]='<br>'.$corso->fullname.' Ruolo:'.$corso->archetype;
        }

        $form = $this->_form;
        $select = $form->addElement('select', 'corsi', get_string('Corsi'), $statesArray);
        $select->setMultiple(true);

        $form->addElement('button', 'intro', "Next", array('style' => 'width:50px;', 'onClick' => 'updateURL(\'nuovo\');'));
    }
}