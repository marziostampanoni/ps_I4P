<?php
require_once("$CFG->libdir/formslib.php");

class FormStart extends moodleform
{

    function definition()
    {
        $form = $this->_form; // Don't forget the underscore!
        $form->addElement('button', 'intro', get_string('new_courses','local_courseseditor'), array('style' => 'width:200px;', 'onClick' => 'updateURL(\'nuovo\');'));
        $form->addElement('button', 'intro', get_string('clone_courses','local_courseseditor'), array('style' => 'width:200px;,', 'onClick' => 'updateURL(\'clona\');'));
        $form->addElement('button', 'intro', get_string('delete_courses','local_courseseditor'), array('style' => 'width:200px;', 'onClick' => 'updateURL(\'cancella\');'));
        if(!is_siteadmin()){
            return;
        }
        $form->addElement('button', 'intro', get_string('manage_courses','local_courseseditor'), array('style' => 'width:200px;', 'onClick' => 'updateURL(\'manage\');'));
    }
}