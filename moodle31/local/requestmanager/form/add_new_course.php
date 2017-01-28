<?php
require_once("$CFG->libdir/formslib.php");

class FormAddNewCourse extends moodleform
{
    protected function definition()
    {
        $form = $this->_form;

        $form->addElement('text', 'title',get_string('title','local_requestmanager'),array('placeholder'=>get_string('title','local_requestmanager')));
        $form->addRule('titolo', get_string('required'), 'required', '', 'client', false, false);

        $form->addElement('text', 'code',get_string('code','local_requestmanager'),array('placeholder'=>get_string('code','local_requestmanager')));
        $form->addRule('codice', get_string('required'), 'required', '', 'client', false, false);

        $eachCat = coursecat::make_categories_list();
        $form->addElement('select','category',get_string('category'),$eachCat);

        //$form->addElement('submit', 'add', get_string('add','local_requestmanager'));
        $this->add_action_buttons(false,get_string('add','local_requestmanager'));
    }
}