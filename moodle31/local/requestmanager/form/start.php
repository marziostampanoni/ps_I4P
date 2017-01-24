<?php
require_once("$CFG->libdir/formslib.php");

class FormStart extends moodleform
{

    function definition()
    {
        $form = $this->_form;
        $form->addElement('button', 'intro', get_string('new_courses', 'local_requestmanager'), array('style' => 'width:200px;', 'onClick' => 'updateURL(\'new\');'));
        $form->addElement('button', 'intro', get_string('clone_courses', 'local_requestmanager'), array('style' => 'width:200px;,', 'onClick' => 'updateURL(\'clone\');'));
        $form->addElement('button', 'intro', get_string('delete_courses', 'local_requestmanager'), array('style' => 'width:200px;', 'onClick' => 'updateURL(\'delete\');'));
        if (!is_siteadmin()) {
            return;
        }
        $form->addElement('button', 'intro', get_string('manage_courses', 'local_requestmanager'), array('style' => 'width:200px;', 'onClick' => 'updateURL(\'manage\');'));
    }
}