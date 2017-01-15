<?php


require_once("$CFG->libdir/formslib.php");

class FormResume extends moodleform
{
    protected function definition()
    {
        global $USER, $DB;
        $eachCat = coursecat::make_categories_list();
        $form = $this->_form;
        $form->addElement('html', '<table class="generaltable table-bordered">');
        $form->addElement('html', '<tr><th>' . get_string('resume_tablehead_title', 'local_courseseditor') . '</th><th>' . get_string('resume_tablehead_cat', 'local_courseseditor') . '</th><th>' . get_string('resume_tablehead_teacher', 'local_courseseditor') . '</th><th>' . get_string('resume_tablehead_editingteacher', 'local_courseseditor') . '</th><th>' . get_string('resume_tablehead_note', 'local_courseseditor') . '</th></tr>');
        $fromform = $this->_customdata['data'];

        foreach ($fromform as $name => $post) {
            $prefix = substr($name, 0, 4);
            if ($prefix == 'name') {
                $form->addElement('html', '<tr>');
                $id = substr($name, strpos($name, "-") + 1);
                $datasel = 'data-' . $id;
                $trdata = json_decode($fromform->$datasel);
                foreach ($trdata as $tdkey => $tdval) {
                    if ($tdkey != 'id') {
                        if ($tdkey == 'cat') {
                            $form->addElement('html', '<td><select onchange="updateHidden(\"hiddenSelect-' . $id . '\");">');
                            foreach ($eachCat as $id => $option) {
                                if ($option == $tdval) {
                                    $form->addElement('html', '<option value="' . $id . '" selected>' . $option . "</option>");
                                } else {
                                    $form->addElement('html', '<option value="' . $id . '">' . $option . "</option>");
                                }
                            }
                            $form->addElement('html', '</select></td>');
                        } else if (is_array($tdval)) {
                            $form->addElement('html', '<td><ul>');
                            foreach ($tdval as $id => $option) {
                                $form->addElement('html', '<li value="' . $option->id . '">' . $option->name . "</li>");
                            }
                            $form->addElement('html', '</ul></td>');
                        } else {
                            $form->addElement('html', '<td><b>' . $tdval . '</b></td>');
                        }
                    }
                }
                $form->addElement('html', '<td><textarea rows="2" cols="22" style="resize: none"></textarea>');
                $form->addElement('html', '</tr>');
            }
        }
        $form->addElement('html', '</table>');
        $form->addElement('textarea', 'comments', get_string("resume_comments", "local_courseseditor"), 'rows="5" cols="80" style="resize:none; margin-left:2%;" placeholder="' . get_string('resume_comment_placeholder', 'local_courseseditor') . '"');
        $form->addElement('button', 'intro', get_string('resume_next', 'local_courseseditor'), array('onclick' => 'updateURL(\'nuovo\');'));
    }
}