<?php


require_once("$CFG->libdir/formslib.php");
require_once($CFG->libdir . '/coursecatlib.php');


class FormCancella extends moodleform
{
    protected function definition()
    {
        global $DB;
        $form = $this->_form;
        $eachCat = coursecat::make_categories_list();
        if (count($this->_customdata['cats']) > 0) {
            foreach ($this->_customdata['cats'] as $id_cat) {
                $form->addElement('html', '<br><div style="display: table; width:100%;"><div style="border: solid 1px lightgray; background-color: lightgray; height: 30px; padding-left: 5px; display: table-cell; vertical-align: middle; margin-bottom: 20px; min-width: 100%;"><b>' . $eachCat[$id_cat] . '</b></div></div>');
                foreach ($this->_customdata['courses'] as $corso) {
                    if($corso->category == $id_cat){
                        switch ($corso->archetype){
                            case 'editingteacher':
                                $ruolo = get_string('editingteacher', 'local_requestmanager');
                                break;
                            case 'teacher':
                                $ruolo = get_string('teacher', 'local_requestmanager');
                        }
                        $details = '<b>' . $corso->fullname . '</b> - ' . $eachCat[$id_cat] . ', Ruolo: ' . $ruolo;

                        $role = $DB->get_record('role', array('shortname' => 'editingteacher'));
                        $context = get_context_instance(CONTEXT_COURSE, $corso->instanceid);
                        $editingteachersResult = get_role_users($role->id, $context);

                        $editingteachers = array();
                        foreach ($editingteachersResult as $editingteacher) {
                            $editingteachers[] = array('id' => $editingteacher->id, 'firstname' =>$editingteacher->firstname,'lastname' => $editingteacher->lastname );
                        }

                        $role = $DB->get_record('role', array('shortname' => 'teacher'));
                        $context = get_context_instance(CONTEXT_COURSE, $corso->instanceid);
                        $teachersResult = get_role_users($role->id, $context);

                        $teachers = array();
                        foreach ($teachersResult as $teacher) {
                            $teachers[] = array('id' => $teacher->id, 'firstname' =>$teacher->firstname,'lastname' => $teacher->lastname );
                        }

                        $form->addElement('checkbox', 'name-' . $corso->instanceid, '', $details, array('value' => 'asd'), array('value' => 'asd'));
                        $data = array('id' => $corso->instanceid, 'title' => $corso->fullname, 'cat' => $corso->category, 'teachers' => $teachers, 'editingteacher' => $editingteachers);

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