<?php
require_once("$CFG->libdir/formslib.php");

class FormSelectCourses extends moodleform
{
    protected function definition()
    {
        global $USER;

        $form = $this->_form;
        $i=0;
        if($this->_customdata['corsi']) {
            foreach ($this->_customdata['corsi'] as $corso) {
                $arr_c = get_object_vars($corso);
                $ruoli = null;
                foreach ($arr_c as $param => $valore) {
                    switch ($param) {
                        case 'titolo':
                            $titolo = "$valore";
                            break;
                        case 'facolta':
                            $facolta = ", $valore";
                            break;
                        case 'corso_laurea':
                            $cdl = ", $valore";
                            break;
                        case 'modulo':
                            $modulo = ", $valore";
                            break;
                        case 'dipartimento':
                            $dip = ", $valore";
                            break;
                        case 'docenti':
                            foreach ($valore as $docente) {
                                if ($docente == $USER->username) $ruoli[] = \local_requestmanager\CEUtil::tipoUtente(TIPO_RELAZIONE_DOCENTE);
                                break;
                            }
                            break;
                        case 'assistenti':
                            foreach ($valore as $docente) {
                                if ($docente == $USER->username) $ruoli[] = \local_requestmanager\CEUtil::tipoUtente(TIPO_RELAZIONE_ASSISTENTE);
                                break;
                            }
                            break;
                    }
                }
                $details = "<b> $titolo</b>$facolta$cdl$modulo$dip" . ($ruoli ? ', '.get_string('role','local_requestmanager').': ' . implode(',', $ruoli) : '');

                $form->addElement('checkbox', 'name-' . $i, '', $details, array('value' => 'asd'), array('value' => 'asdfghj'));
                $user = new stdClass();
                $user->id=$USER->id;
                $user->name=$USER->firstname.' '.$USER->lastname ;

                $data = array('id' => null,
                    'title' => "$titolo$facolta$cdl$modulo$dip",
                    'cat' => null,
                    'teachers' => null,
                    'editingteacher' => array($user)
                );
                $form->addElement('hidden', 'data-' . $i, json_encode($data));
                $i++;
            }
        }
        $this->add_action_buttons(false,get_string('next','local_requestmanager'));
    }

}