<?php


require_once("$CFG->libdir/formslib.php");

class FormClona extends moodleform
{
    protected function definition()
    {
        global $DB;

        $form = $this->_form;

        $eachCat = coursecat::make_categories_list();
        foreach ($eachCat as $id => $cat) {
            if (count($this->_customdata['data']) > 0) {
                $countcourses = coursecat::get($id)->get_courses_count();
                if (isset($countcourses) && $countcourses > 0) {
                    $catcourses = coursecat::get($id)->get_courses();
                    $label = true;
                    foreach ($this->_customdata['data'] as $corso) {
                        if (isset($catcourses[$corso->instanceid])) {
                            if ($label) {
                                $form->addElement('html', '<br><div style="display: table; width:100%;"><div style="border: solid 1px lightgray; background-color: lightgray; height: 30px; padding-left: 5px; display: table-cell; vertical-align: middle; margin-bottom: 20px; min-width: 100%;"><b>' . $cat . '</b></div></div>');
                                $label = false;
                            }
                            switch ($corso->archetype){
                                case 'editingteacher':
                                    $ruolo = get_string('editingteacher', 'local_courseseditor');
                                    break;
                                case 'teacher':
                                    $ruolo = get_string('teacher', 'local_courseseditor');
                            }
                            $details = '<b>' . $corso->fullname . '</b> - ' . $cat . ', Ruolo: ' . $ruolo;

                            $role = $DB->get_record('role', array('shortname' => 'editingteacher'));
                            $context = get_context_instance(CONTEXT_COURSE, $corso->instanceid);
                            $editingteachersResult = get_role_users($role->id, $context);

                            $editingteachers = array();
                            foreach ($editingteachersResult as $editingteacher) {
                                $editingteachers[] = array('id' => $editingteacher->id, 'name' => $editingteacher->lastname . ' ' . $editingteacher->firstname);
                            }

                            $role = $DB->get_record('role', array('shortname' => 'teacher'));
                            $context = get_context_instance(CONTEXT_COURSE, $corso->instanceid);
                            $editingteachersResult = get_role_users($role->id, $context);

                            $teachers = array();
                            foreach ($editingteachersResult as $teacher) {
                                $teachers[] = array('id' => $teacher->id, 'name' => $teacher->lastname . ' ' . $teacher->firstname);
                            }

                            $form->addElement('checkbox', 'name-' . $corso->instanceid, '', $details, array('value' => 'asd'), array('value' => 'asd'));
                            $data = array('id' => $corso->instanceid, 'title' => $corso->fullname, 'cat' => $id, 'teachers' => $teachers, 'editingteacher' => $editingteachers);
                            $form->addElement('hidden', 'data-' . $corso->instanceid, json_encode($data));

                        }
                    }
                }
            }

        }

        $form->addElement('html', '<br>');
        $this->add_action_buttons(false, get_string("clone_next", 'local_courseseditor'));

    }
}