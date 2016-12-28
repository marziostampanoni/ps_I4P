<?php
/**
 * Created by PhpStorm.
 * User: n0skill
 * Date: 28.12.16
 * Time: 12:24
 */


require_once("$CFG->libdir/formslib.php");

class mainchoiceform extends moodleform {

    function definition() {
        global $CFG;

        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('button', 'crea', 'Nuovi Corsi');
        $mform->addElement('button', 'clona', 'Clona corsi esistenti');
        $mform->addElement('button', 'cancella', 'Cancella corsi esistenti');

    }
}


?>