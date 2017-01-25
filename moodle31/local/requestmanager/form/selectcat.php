<?php

require_once($CFG->libdir.'/coursecatlib.php');
require_once("$CFG->libdir/formslib.php");

class FormSelectCat extends moodleform
{

    protected function definition()
    {
        $eachCat = coursecat::make_categories_list();
        $eachCat = array('-1'=>get_string('filter_bycat_allcat','local_requestmanager'))+$eachCat;
        $group = array();
        $form = $this->_form;
        $group[] = &$form->createElement('select', 'cat', get_string('filter_bycat', 'local_requestmanager'), $eachCat);
        $group[] = &$form->createElement('submit', 'next', get_string('filter_bycat', 'local_requestmanager'));
        $form->addGroup($group, 'buttonar', '', array(' '), false);
        $form->closeHeaderBefore('buttonar');
    }
}