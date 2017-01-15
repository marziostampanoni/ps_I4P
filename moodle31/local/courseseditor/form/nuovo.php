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
        $form->addElement('text', 'titolo','Titolo corso',array('placeholder'=>get_string('titolo','local_courseseditor')));
        $form->addElement('text', 'codice','',array('placeholder'=>get_string('codice','local_courseseditor')));
        $eachCat = coursecat::make_categories_list();
        $form->addElement('select','categoria',get_string('category'),$eachCat);
        $form->addElement('submit', 'sbmt',get_string('Aggiungi','local_courseseditor') , array('class' => 'btn btn-primary'));
    }
}