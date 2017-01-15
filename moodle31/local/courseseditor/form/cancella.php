<?php


require_once("$CFG->libdir/formslib.php");

class FormCancella extends moodleform
{
    protected function definition()
    {
        global $USER, $DB;
        $query = "
SELECT *  FROM
mdl_user u
JOIN mdl_role_assignments ra ON ra.userid = u.id
JOIN mdl_role r ON ra.roleid = r.id
JOIN mdl_context con ON ra.contextid = con.id
JOIN mdl_course c ON c.id = con.instanceid AND con.contextlevel = 50
WHERE u.id=? AND (r.shortname = 'teacher' OR r.shortname = 'editingteacher' OR r.shortname = 'manager')";


        $courses = $DB->get_records_sql($query, array($USER->id));
        $form = $this->_form;
        $form->disable_form_change_checker();
        $eachCat = coursecat::make_categories_list();
        foreach ($eachCat as $id => $cat) {
            $countcourses = coursecat::get($id)->get_courses_count();
            if (isset($countcourses) && $countcourses > 0) {
                $catcourses = coursecat::get($id)->get_courses();
                $label = true;
                foreach ($courses as $corso) {
                    if (isset($catcourses[$corso->instanceid])) {
                        if ($label) {
                            $form->addElement('html', '<br><hr><b>' . $cat . '</b><br><hr>');
                            $label = false;
                        }
                        $details = '<b>' . $corso->fullname . '</b> - ' . $cat . ', Ruolo: ' . $corso->archetype;

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

                        $form->addElement('checkbox', 'name-' . $corso->instanceid, '', $details);
                        $data = array('id' => $corso->instanceid, 'title' => $corso->fullname, 'cat' => $cat, 'teachers' => $teachers, 'editingteacher' => $editingteachers);
                        $form->addElement('hidden', 'data-' . $corso->instanceid, json_encode($data));

                    }
                }
            }
        }
        $form->addElement('html', '<br>');
        $form->addElement('button', 'next', get_string("delete_next", 'local_courseseditor'), array('data-toggle' => 'modal', 'data-target' => '#deleteModal'));
    }
}