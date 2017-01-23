<?php

require_once('class/Richiesta.php');
require_once('class/Corso.php');
require_once('class/UserCorso.php');

require_once("$CFG->libdir/formslib.php");
require_once($CFG->libdir . '/coursecatlib.php');

class FormManage extends moodleform
{
    protected function definition()
    {
        global $USER;
        $eachCat = coursecat::make_categories_list();
        $form = $this->_form;
        if (count($this->_customdata['requests']) > 0) {
            foreach ($this->_customdata['users'] as $userId => $requests) {
                $thereare = false;
                $toIgnore = array();
                foreach ($requests as $request) {
                    foreach ($request->corsi_richiesti as $corso) {
                        if ($corso->stato_richiesta == STATO_RICHIESTA_DA_GESTIRE) {
                            $thereare = true;
                        } else {
                            $toIgnore[] = $request->id;
                        }
                    }
                }
                if ($thereare) {
                    $user = get_complete_user_data('id', $userId);
                    $form->addElement('html', '<h4>' . get_string('manage_request_user', 'local_courseseditor') . ': ' . $user->email . '</h4>');
                    $form->addElement('html', '<div class="container">');
                    $odd = true;
                    foreach ($requests as $request) {
                        if (!in_array($request->id, $toIgnore)) {
                            $form->addElement('html', '<table class="table" style="border: solid 1px lightgray;">');
                        }
                        if (!$odd) {
                            $odd = true;
                            $color = 'rgba(120,120,120,0.1)';
                        }
                        foreach ($request->corsi_richiesti as $corso) {
                            if ($corso->stato_richiesta == STATO_RICHIESTA_DA_GESTIRE || !is_siteadmin($USER->id)) {
                                $form->addElement('html', '<tr bgcolor="#d3d3d3"><th>' . get_string('manage_tablehead_type', 'local_courseseditor') . ' / ' . get_string('resume_tablehead_actions', 'local_courseseditor') . '</th><th colspan="3"  style="border-right: solid 1px lightgray;">' . get_string('resume_tablehead_cat', 'local_courseseditor') . '</th></tr>');
                                $action = 'save';
                                $string = 'manage_save_course';
                                if ($corso->tipo_richiesta == TIPO_RICHIESTA_CANCELLARE) {
                                    $action = 'cancel';
                                    $string = 'manage_cancel_course';
                                }
                                if (!is_siteadmin($USER->id)) {
                                    $action = 'savereq';
                                    $string = 'manage_save_req';
                                }
                                $form->addElement('html', '<tr style="background-color: ' . $color . '">');
                                $form->addElement('html', '<td rowspan="6" nowrap class="description" style="vertical-align: middle; border-right: solid 1px lightgray;">');

                                $form->addElement('html', '<span style="top: -10px;">' . $corso->tipo_richiesta . '</span>');
                                $form->addElement('html', '<br><hr>');
                                $form->addElement('html', '<a onclick="actionOnCourse(' . $corso->id . ', \'' . $action . '\')">' . get_string($string, 'local_courseseditor') . '</a>');

                                $action = 'reject';
                                $string = 'manage_reject_req';
                                if (!is_siteadmin($USER->id)) {
                                    $action = 'cancelreq';
                                    $string = 'manage_cancel_req';
                                }
                                $form->addElement('html', '<br><a onclick="actionOnCourse(' . $corso->id . ', \'' . $action . '\')">' . get_string($string, 'local_courseseditor') . '</a>');
                                $form->addElement('html', '</td>');
                                if ($corso->tipo_richiesta == TIPO_RICHIESTA_CANCELLARE) {
                                    $form->addElement('html', '<td colspan="3" style="border-right: solid 1px lightgray;">' . $eachCat[$corso->id_mdl_course_categories] . '</td></tr>');
                                } else {
                                    $form->addElement('html', '<td colspan="3" style="border-right: solid 1px lightgray;"><select>');

                                    foreach ($eachCat as $id => $option) {
                                        if ($corso->id_mdl_course_categories == $id) {
                                            $form->addElement('html', '<option selected value="' . $id . '">' . $option . "</option>");
                                        } else {
                                            $form->addElement('html', '<option value="' . $id . '">' . $option . "</option>");
                                        }
                                    }
                                    $form->addElement('html', '</select></td></tr>');
                                }

                                $form->addElement('html', '<tr  bgcolor="#d3d3d3" style="border-right: solid 1px lightgray;"><th colspan="2" style="width: 70%">' . get_string('resume_tablehead_title', 'local_courseseditor') . '</th><th colspan="2">' . get_string('manage_tablehead_shortname', 'local_courseseditor') . '</th><tr style="background-color: ' . $color . '">');
                                $form->addElement('html', '<tr style="background-color: ' . $color . '">');
                                $form->addElement('html', '<td colspan="2">');
                                $teachersBtn = '';
                                $editingteachersBtn = '';
                                if ($corso->tipo_richiesta == TIPO_RICHIESTA_CANCELLARE) {
                                    $form->addElement('html', '<span>' . $corso->titolo . '</span>');
                                    $form->addElement('html', '<input type="hidden" id="coursetitle_' . $corso->id . '" title="' . $corso->titolo . '" value="' . $corso->titolo . '" style="display:table-cell; width:95%">');
                                    $short = '<span>' . $corso->shortname . '</span>';
                                } else {
                                    $form->addElement('html', '<input id="coursetitle_' . $corso->id . '" title="' . $corso->titolo . '" type="text" value="' . $corso->titolo . '" style="display:table-cell; width:95%">');
                                    $short = '<input type="text" value="' . $corso->shortname . '" size="40" style="display:table-cell; width:95%">';
                                    $teachersBtn = '<button class="btn" onclick="enroll(\'' . $corso->id . '\', \'1\');" data-toggle="modal" data-target="#enrollModal">Enroll</button>';
                                    $editingteachersBtn = '<button class="btn" onclick="enroll(\'' . $corso->id . '\', \'0\');" data-toggle="modal" data-target="#enrollModal">Enroll</button>';
                                }
                                $form->addElement('html', '</td>');
                                $form->addElement('html', '<td colspan="2">' . $short . '</td></tr>');


                                $form->addElement('html', '<tr  bgcolor="#d3d3d3"><th style="width: 33%">' . $teachersBtn . ' - ' . get_string('resume_tablehead_teacher', 'local_courseseditor') . '</th><th style="width: 33%">' . $editingteachersBtn . ' - ' . get_string('resume_tablehead_editingteacher', 'local_courseseditor') . '</th><th style="width: 33%; border-right: solid 1px lightgray; vertical-align:middle;">' . get_string('resume_tablehead_note', 'local_courseseditor') . '</th></tr><tr style="background-color: ' . $color . '">');

                                $teachers = array();
                                $editingteachers = array();
                                foreach ($corso->user_assegnati as $option) {
                                    if ($option->tipo_relazione == 'Docente') {
                                        $editingteachers[] = $option;
                                    } else {
                                        $teachers[] = $option;
                                    }
                                }


                                $form->addElement('html', '<td><ul id="teachers_list_' . $corso->id . '">');
                                foreach ($teachers as $option) {
                                    $form->addElement('html', '<li class="teacher_' . $corso->id . '" value="' . $option->id_mdl_user . '" style="white-space: nowrap;">' . $option->nome . ' ' . $option->cognome . '</li>');
                                }
                                $form->addElement('html', '</ul></td>');

                                $form->addElement('html', '<td><ul id="editingteachers_list_' . $corso->id . '">');
                                foreach ($editingteachers as $option) {
                                    $form->addElement('html', '<li class="editingteacher_' . $corso->id . '" value="' . $option->id_mdl_user . '" style="white-space: nowrap;">' . $option->nome . ' ' . $option->cognome . '</li>');
                                }
                                $form->addElement('html', '</ul></td>');
                                $form->addElement('html', '<td style="border-right: solid 1px lightgray;"><textarea style="resize: none; display:table-cell; width:95%;height: 95%;">' . $corso->note . '</textarea></td>');
                                $form->addElement('html', '</tr>');
                                if ($odd) {
                                    $odd = false;
                                    $color = 'white';
                                } else {
                                    $odd = true;
                                    $color = 'rgba(120,120,120,0.1)';
                                }
                            }

                        }
                        $form->addElement('html', '</tr>');
                        $form->addElement('html', '</table>');
                    }
                    $form->addElement('html', '</div>');
                } else {
                    $form->addElement('html', '<b>No pending request</b>');
                }

            }
        } else {
            $form->addElement('html', '<h6>' . get_string('manage_noentry', 'local_courseseditor') . '</h6>');
        }
    }
}