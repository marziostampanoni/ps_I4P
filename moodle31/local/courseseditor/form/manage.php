<?php

require_once('class/Richiesta.php');
require_once('class/Corso.php');
require_once('class/UserCorso.php');

require_once("$CFG->libdir/formslib.php");

class FormManage extends moodleform
{
    protected function definition()
    {
        global $DB;
        $res = $DB->get_records('lcl_courseseditor_richiesta');
        $users = array();
        foreach ($res as $request) {
            $req = new Richiesta($request->id);
            $req->loadFromDB();
            $users[$req->id_mdl_user][] = $req;
        }
        $eachCat = coursecat::make_categories_list();
        $form = $this->_form;
        if (count($users) > 0) {
            foreach ($users as $userId => $requests) {
                $user = get_complete_user_data('id', $userId);
                $form->addElement('html', '<h4>' . get_string('manage_request_user', 'local_courseseditor') . ': ' . $user->email . '</h4>');
                $form->addElement('html', '<table class="generaltable table-bordered">');
                $form->addElement('html', '<tr><th>' . get_string('resume_tablehead_title', 'local_courseseditor') . '</th><th>' . get_string('manage_tablehead_shortname', 'local_courseseditor') . '</th><th>' . get_string('resume_tablehead_cat', 'local_courseseditor') . '</th><th>' . get_string('resume_tablehead_teacher', 'local_courseseditor') . '</th><th>' . get_string('resume_tablehead_editingteacher', 'local_courseseditor') . '</th><th>' . get_string('resume_tablehead_note', 'local_courseseditor') . '</th><th style="width:2%;!important">' . get_string('resume_tablehead_actions', 'local_courseseditor') . '</th></tr>');
                $allReqID = '';
                foreach ($requests as $request) {
                    $allReqID .= $request->id . '_';
                    foreach ($request->corsi_richiesti as $corso) {
                        if ($corso->stato_richiesta == STATO_RICHIESTA_DA_GESTIRE) {
                            if($corso->tipo_richiesta == TIPO_RICHIESTA_CANCELLARE){
                                $form->addElement('html', '<tr style="background-color: rgba(255,0,0,0.1);">');
                            } else{
                                $form->addElement('html', '<tr style="background-color: rgba(0,255,0,0.1);">');
                            }

                            $form->addElement('html', '<td>');
                            $form->addElement('html', '<input type="text" value="' . $corso->titolo . '" size="' . strval(strlen($corso->titolo) - 2) . '">');
                            $form->addElement('html', '</td>');
                            $form->addElement('html', '<td><input type="text" value="' . $corso->shortname . '" size="16"></td>');
                            $form->addElement('html', '<td><select>');
                            foreach ($eachCat as $id => $option) {
                                $form->addElement('html', '<option value="' . $id . '">' . $option . "</option>");
                            }
                            $form->addElement('html', '</select></td>');

                            $teachers = array();
                            $editingteachers = array();
                            foreach ($corso->user_assegnati as $option) {
                                if ($option->tipo_relazione == 'Docente') {
                                    $editingteachers[] = $option;
                                } else {
                                    $teachers[] = $option;
                                }

                            }

                            $form->addElement('html', '<td><ul>');
                            foreach ($teachers as $option) {
                                $form->addElement('html', '<li value="' . $option->id . '" style="white-space: nowrap;">' . $option->nome . ' ' . $option->cognome . '</li>');
                            }
                            $form->addElement('html', '</ul></td>');

                            $form->addElement('html', '<td><ul>');
                            foreach ($editingteachers as $option) {
                                $form->addElement('html', '<li value="' . $option->id . '" style="white-space: nowrap;">' . $option->nome . ' ' . $option->cognome . '</li>');
                            }
                            $form->addElement('html', '</ul></td>');
                            $form->addElement('html', '<td><textarea rows="2" cols="15" style="resize: none">' . $corso->note . '</textarea></td>');
                            $form->addElement('html', '<td><a onclick="cancelCourse(' . $request->id . ',' . $corso->id . ')">' . get_string('manage_cancel_course', 'local_courseseditor') . '</a></td>');
                            $form->addElement('html', '</tr>');
                        }

                    }
                }
                $form->addElement('html', '</table>');
                $form->addElement('button', 'saverequest', get_string('manage_save_course', 'local_courseseditor'), array('onclick' => 'saveUserRequests("' . rtrim($allReqID, ',') . '");', 'style' => 'float:right;'));
            }
        } else {
            $form->addElement('html', '<h6>' . get_string('manage_noentry', 'local_courseseditor') . '</h6>');
        }
    }
}