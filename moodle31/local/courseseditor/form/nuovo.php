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
        $form = $this->_form;

        $form->addElement('text', 'titolo',get_string('Titolo','local_courseseditor'),array('placeholder'=>get_string('Titolo','local_courseseditor')));
        $form->addRule('titolo', get_string('required'), 'required', '', 'client', false, false);

        $form->addElement('text', 'codice',get_string('Codice','local_courseseditor'),array('placeholder'=>get_string('Codice','local_courseseditor')));
        $form->addRule('codice', get_string('required'), 'required', '', 'client', false, false);

        $eachCat = coursecat::make_categories_list();
        $form->addElement('select','categoria',get_string('category'),$eachCat);

        $form->addElement('submit', 'add', get_string('Aggiungi','local_courseseditor'));
    }
}