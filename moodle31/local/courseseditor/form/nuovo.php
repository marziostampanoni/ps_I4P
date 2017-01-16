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
        //$form->setAttributes(array('class' => 'form-inline'));
        $form->addElement('text', 'titolo',get_string('Titolo','local_courseseditor'),array('placeholder'=>get_string('titolo','local_courseseditor')));
        $form->addElement('text', 'codice',get_string('Codice','local_courseseditor'),array('placeholder'=>get_string('codice','local_courseseditor')));
        $eachCat = coursecat::make_categories_list();
        $form->addElement('select','categoria',get_string('category'),$eachCat);

        $buttonarray=array();
        $buttonarray[] = &$form->createElement('submit', 'add', get_string('Aggiungi','local_courseseditor'));
        $buttonarray[] = &$form->createElement('button', 'done', get_string('Avanti','local_courseseditor'), array('style' => 'width:200px;', 'onClick' => 'updateURL(\'resume\');'));
        $form->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $form->closeHeaderBefore('buttonar');
    }
}