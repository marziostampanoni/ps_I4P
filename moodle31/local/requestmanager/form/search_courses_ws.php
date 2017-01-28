<?php


require_once("$CFG->libdir/formslib.php");

class FormSearchCoursesWs extends moodleform
{
    protected function definition()
    {
        global $USER;
        $form = $this->_form;

        $buttonarray=array();
        $buttonarray[]= &$form->createElement('text', 'string','',array('placeholder'=>get_string('search_string','local_requestmanager')));
        $buttonarray[] = &$form->createElement('submit', 'submitusi', get_string('search').' in USI');
        $buttonarray[] = &$form->createElement('submit', 'submitsupsi', get_string('search').' in SUPSI');
        $buttonarray[] = &$form->createElement('button','reset',  get_string('reset_filter', 'local_requestmanager'));
        $form->addGroup($buttonarray, 'buttonar', get_string('search_string','local_requestmanager'), array(' '), false);
        $form->closeHeaderBefore('buttonar');
    }

    public function definition_after_data(){
        $mform = &$this->_form;

        $string_input = &$mform->get;

        echo '<pre>';
        var_dump($mform);
        echo '</pre>';
//        if ($string_input->) {
//
//                $config_text->attributes['value'] = "The checkbox is checked";
//
//        }

    }
}