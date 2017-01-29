<?php


require_once("$CFG->libdir/formslib.php");

class FormSearchCoursesWs extends moodleform
{
    protected function definition()
    {
        $form = $this->_form;
        $buttonarray=array();
        $buttonarray[]= &$form->createElement('text', 'string','',array('placeholder'=>get_string('search_string','local_requestmanager')));
        $buttonarray[] = &$form->createElement('submit', 'submitusi', get_string('search').' in USI');
        $buttonarray[] = &$form->createElement('submit', 'submitsupsi', get_string('search').' in SUPSI');
        $form->addGroup($buttonarray, 'buttonar', get_string('search_string','local_requestmanager'), array(' '), false);
        $form->closeHeaderBefore('buttonar');
    }
}