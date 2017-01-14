<?php


require_once("$CFG->libdir/formslib.php");

class FormClona extends moodleform
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

                        $form->addElement('checkbox', 'name-' . $corso->instanceid, '', $details, array('value' => 'asd'), array('value' => 'asd'));
                        $data = array('id' => $corso->instanceid, 'title' => $corso->fullname, 'cat' => $cat, 'teachers' => array(array('id' => 'id_teacher1', 'name' => 'name_teacher1TODO'), array('id' => 'id_teacher2', 'name' => 'name_teacher2TODO')), 'editingteacher' => array(array('id' => 'id_editingteacher1', 'name' => 'name_editingteacher1TODO'), array('id' => 'id_editingteacher2', 'name' => 'name_editingteacher2TODO')));
                        $form->addElement('hidden', 'data-' . $corso->instanceid, json_encode($data));

                    }
                }
            }

        }


        $form->addElement('submit', 'next', get_string("clone_next", 'local_courseseditor'));
    }
}