<?php
//require_once('class/Richiesta.php');
//require_once('class/Corso.php');
//require_once('class/UserCorso.php');

require_once("$CFG->libdir/formslib.php");
require_once($CFG->libdir . '/coursecatlib.php');

class FormManage extends moodleform
{
    protected function definition()
    {
        global $USER,$DB;

        $eachCat = coursecat::make_categories_list();
        $form = $this->_form;
        if (count($this->_customdata['users']) > 0) {
            foreach ($this->_customdata['users'] as $userId => $requests) {
                if(is_array($requests)) {
                    $user = $DB->get_record('user',array('id'=>$userId));
                    $form->addElement('html', '<h4>' . get_string('manage_request_user', 'local_requestmanager') . " : {$user->firstname} {$user->lastname} ({$user->email})</h4>" );
                    foreach ($requests as $request) {

                        foreach ($request->corsi_richiesti as $corso) {
                            if (!is_siteadmin($USER->id)) {
                                $action = 'savereq';
                                $string = 'manage_save_req';
                            } elseif ($corso->tipo_richiesta == TIPO_RICHIESTA_CANCELLARE) {
                                $action = 'cancel';
                                $string = 'manage_cancel_course';
                            } else {
                                $action = 'save';
                                $string = 'manage_save_course';
                            }


                            $form->addElement('html', '<div class="block form-group" style="margin-bottom: 15px;">');
                            $form->addElement('html', '<table class="table" style="margin-bottom: -5px;">');
                            $form->addElement('html', '<tr>');
                            $form->addElement('html', '<td colspan="2" style="width: 70%; border: none;padding: 0px 10px;"> <label>' . get_string('resume_tablehead_title', 'local_requestmanager') . '</label>  ');
                            $form->addElement('html', '<span style="top: -10px;" class="label label-' . local_requestmanager\CEUtil::getStyleTipoRichiesta($corso->tipo_richiesta) . '">' . local_requestmanager\CEUtil::tipoRichiesta($corso->tipo_richiesta) . '</span>');
                            $form->addElement('html', '<input '.($corso->tipo_richiesta == TIPO_RICHIESTA_CANCELLARE?'disabled':'').' style="width:100%;" id="coursetitle_' . $corso->id . '" name="titolo-' . $corso->id . '" type="text" value="' . $corso->titolo . '" style="display:table-cell; width:95%">');
                            $form->addElement('html', '</td>');
                            $form->addElement('html', '<td style="border: none;padding: 0px 10px;"><label>' . get_string('manage_tablehead_shortname', 'local_requestmanager') . '</label><input '.($corso->tipo_richiesta == TIPO_RICHIESTA_CANCELLARE?'disabled':'').' name="shortname-' . $corso->id . '" type="text" value="' . $corso->shortname . '" size="40" style="display:table-cell; width:95%"></td>');
                            $form->addElement('html', '</tr>');
                            $form->addElement('html', '<tr><td colspan="2" style="border: none;padding: 0px 10px;"><label for="categoria-' . $corso->id . '">' . get_string('resume_tablehead_cat', 'local_requestmanager') . '</label>');

                            $form->addElement('html', '<br><select '.($corso->tipo_richiesta == TIPO_RICHIESTA_CANCELLARE?'disabled':'').' style="width:100%;" name="categoria-' . $corso->id . '" id="categoria-' . $corso->id . '">');
                            foreach ($eachCat as $id => $option) {
                                $form->addElement('html', '<option value="' . $id . '" ' . (($corso->cat == $id) ? 'selected' : '') . '>' . $option . "</li>");
                            }
                            $form->addElement('html', '</select>');

                            $form->addElement('html', '</td>');
                            $form->addElement('html', '<td rowspan="2" style="border: none; padding: 0px 10px;"><label>' . get_string('resume_tablehead_note', 'local_requestmanager') . '</label><textarea '.($corso->tipo_richiesta == TIPO_RICHIESTA_CANCELLARE?'disabled':'').' name="note-' . $corso->id . '" style="resize: none; display:table-cell; width:95%;height: 70%;">' . $corso->note . '</textarea></td>');
                            $form->addElement('html', '</tr>');
                            $form->addElement('html', '<tr>');

                            $docenti = array();
                            $assistenti = array();
                            if (is_array($corso->user_assegnati)) {
                                foreach ($corso->user_assegnati as $user) {
                                    if($user->getTipoRelazione()==TIPO_RELAZIONE_DOCENTE) $docenti[]=$user;
                                    else $assistenti[]=$user;
                                }
                            }

                            $form->addElement('html', '<td style="border: none; padding: 0px 10px;"><label>' . get_string('resume_tablehead_editingteacher', 'local_requestmanager') . '</label><ul id="editingteachers_list_' .  $corso->id . '">');
                            if (is_array($docenti)) {
                                foreach ($docenti as $option) {
                                    $form->addElement('html', '<li class="editingteacher_' . $corso->id . '" value="' . $option->id . '" style="white-space: nowrap;">' . $option->nome.' '.$option->cognome . '</li>');
                                }
                            }
                            if ($corso->tipo_richiesta != TIPO_RICHIESTA_CANCELLARE)$form->addElement('html', '</ul><button class="btn" onclick="enroll(\'' . $corso->id . '\', \'0\');" data-toggle="modal" data-target="#enrollModal">Enroll</button></td>');

                            $form->addElement('html', '<td style="border: none;padding: 0px 10px;"><label>' . get_string('resume_tablehead_teacher', 'local_requestmanager') . '</label><ul id="teachers_list_' . $corso->id . '">');
                            if (is_array($assistenti)) {
                                foreach ($assistenti as $option) {
                                    $form->addElement('html', '<li class="teacher_' . $corso->id . '" value="' . $option->id . '" style="white-space: nowrap;">' . $option->nome.' '.$option->cognome . '</li>');
                                }
                            }
                            if ($corso->tipo_richiesta != TIPO_RICHIESTA_CANCELLARE) $form->addElement('html', '</ul><button class="btn" onclick="enroll(\'' . $corso->id . '\', \'1\');" data-toggle="modal" data-target="#enrollModal">Enroll</button></td>');

                            $form->addElement('html', '</tr>');
                            $form->addElement('html', '<tr>');
                            $form->addElement('html', '<td style="border: none;" colspan="3">');
                            $form->addElement('html', '<div class="btn-group" role="group" aria-label="...">');
                            $form->addElement('html', '<a class="btn btn-info" onclick="actionOnCourse(' . $corso->id . ', \'' . $action . '\')">' . get_string($string, 'local_requestmanager') . '</a>');
                            $action = 'reject';
                            $string = 'manage_reject_req';
                            if (!is_siteadmin($USER->id)) {
                                $action = 'cancelreq';
                                $string = 'manage_cancel_req';
                            }
                            $form->addElement('html', '  <a class="btn btn-danger" onclick="actionOnCourse(' . $corso->id . ', \'' . $action . '\')">' . get_string($string, 'local_requestmanager') . '</a>');
                            $form->addElement('html', '</div>');
                            $form->addElement('html', '</td>');
                            $form->addElement('html', '</tr>');

                            $form->addElement('html', '</table>');
                            $form->addElement('html', '</div>');
                        }
                    }
                }


            }
        } else {
            $form->addElement('html', '<h6>' . get_string('manage_noentry', 'local_requestmanager') . '</h6>');
        }
    }
}