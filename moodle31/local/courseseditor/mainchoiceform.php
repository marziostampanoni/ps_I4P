<?php
/**
 * Created by PhpStorm.
 * User: n0skill
 * Date: 28.12.16
 * Time: 12:24
 */


require_once("$CFG->libdir/formslib.php");

class mainchoiceform extends moodleform
{

    function definition()
    {
        $form = $this->_form; // Don't forget the underscore!
        $form->addElement('button', 'intro', "Nuovi corsi", array('style' => 'width:200px;', 'onClick' => 'updateURL(\'nuovo\');'));
        $form->addElement('button', 'intro', "Clona corsi esistenti", array('style' => 'width:200px;,', 'onClick' => 'updateURL(\'clona\');'));
        $form->addElement('button', 'intro', "Cancella corsi esistenti", array('style' => 'width:200px;', 'onClick' => 'updateURL(\'cancella\');'));
    }
}


?>