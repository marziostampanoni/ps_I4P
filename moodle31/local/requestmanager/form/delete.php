<?php


require_once("$CFG->libdir/formslib.php");
require_once($CFG->libdir . '/coursecatlib.php');


class FormCancella extends moodleform
{
    protected function definition()
    {
        global $DB;
        if (count($this->_customdata['courses']) > 0) {
            $form = $this->_form;
            $form->disable_form_change_checker();
            $eachCat = coursecat::make_categories_list();
            foreach ($eachCat as $id => $cat) {
                $countcourses = coursecat::get($id)->get_courses_count();
                if (isset($countcourses) && $countcourses > 0) {
                    $catcourses = coursecat::get($id)->get_courses();
                    $label = true;
                    foreach ($this->_customdata['courses'] as $corso) {
                        if (isset($catcourses[$corso->instanceid])) {
                            if ($label) {
                                $form->addElement('html', '<br><div style="display: table; width:100%;"><div style="border: solid 1px lightgray; background-color: lightgray; height: 30px; padding-left: 5px; display: table-cell; vertical-align: middle; margin-bottom: 20px; min-width: 100%;"><b>' . $cat . '</b></div></div>');
                                $label = false;
                            }
                            switch ($corso->archetype) {
                                case 'editingteacher':
                                    $ruolo = get_string('editingteacher', 'local_requestmanager');
                                    break;
                                case 'teacher':
                                    $ruolo = get_string('teacher', 'local_requestmanager');
                            }
                            $details = '<b>' . $corso->fullname . '</b> - ' . $cat . ', Ruolo: ' . $ruolo;

                            $role = $DB->get_record('role', array('shortname' => 'editingteacher'));
                            $context = get_context_instance(CONTEXT_COURSE, $corso->instanceid);
                            $editingteachersResult = get_role_users($role->id, $context);

                            $editingteachers = array();
                            foreach ($editingteachersResult as $editingteacher) {
                                $editingteachers[] = array('id' => $editingteacher->id, 'lastname' => $editingteacher->lastname, 'firstname' => $editingteacher->firstname);
                            }

                            $role = $DB->get_record('role', array('shortname' => 'teacher'));
                            $context = get_context_instance(CONTEXT_COURSE, $corso->instanceid);
                            $editingteachersResult = get_role_users($role->id, $context);

                            $teachers = array();
                            foreach ($editingteachersResult as $teacher) {
                                $teachers[] = array('id' => $teacher->id, 'lastname' => $teacher->lastname, 'firstname' => $teacher->firstname);
                            }

                            $form->addElement('checkbox', 'name-' . $corso->instanceid, '', $details);
                            $data = array('id' => $corso->instanceid, 'title' => $corso->fullname, 'cat' => $id, 'teachers' => $teachers, 'editingteacher' => $editingteachers, 'id_mdl_course' => $corso->id);
                            $form->addElement('hidden', 'data-' . $corso->instanceid, json_encode($data));
                        }
                    }
                }
            }
            $group = array();
            $group[] = &$form->createElement('button', 'next', get_string("delete_next", 'local_requestmanager'), array('data-toggle' => 'modal', 'data-target' => '#deleteModal'));
            $form->addGroup($group, 'buttonar', '', array(' '), false);
            $form->closeHeaderBefore('buttonar');
        }
    }
}