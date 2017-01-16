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
        $i=0;
        foreach ($this->_customdata['data'] as $trdata) {

            $form->addElement('html', '<tr>');
            //titolo
            $form->addElement('html', '<td><b>' . $trdata->title . '</b></td>');
            $form->addElement('hidden', 'titolo-'.$i, $trdata->title);
            // categoria
            $form->addElement('html', '<td><select name="categoria-'.$i.'">' );
            foreach ($eachCat as $id => $option) {

                $form->addElement('html', '<option value="' . $id . '" '.(($trdata->cat==$id)?'selected':'').'>' . $option . "</li>");
            }
            $form->addElement('html', '</select></td>');

            // teachers
            $teachers = $DB->get_records('user');
            $selected=array();
            foreach ($trdata->teachers as $teacher)$selected[]=$teacher->id;
            $form->addElement('html', '<td><select name="teachers-'.$i.'" multiple>');
            foreach ($teachers as $teacher) {
                $form->addElement('html', '<option value="' . $teacher->id . '" '.((in_array($teacher->id,$selected))?'selected':'').'>' . $teacher->lastname.' '.  $teacher->firstname . "</li>");
            }
            $form->addElement('html', '</select></td>');

            // editingteachers
            $editingteachers = $DB->get_records('user');
            $selected=array();
            foreach ($trdata->editingteacher as $teacher)$selected[]=$teacher->id;
            $form->addElement('html', '<td><select name="editingteachers-'.$i.'" multiple>');
            foreach ($editingteachers as $teacher) {
                $form->addElement('html', '<option value="' . $teacher->id . '" '.((in_array($teacher->id,$selected))?'selected':'').'>' . $teacher->lastname.' '.  $teacher->firstname . "</li>");
            }
            $form->addElement('html', '</select></td>');

            // note
            $form->addElement('html', '<td><textarea rows="2" cols="22" name="note-'.$i.'" style="resize: none"></textarea>');
            $form->addElement('html', '</tr>');
            $i++;
        }
        $form->addElement('html', '</table>');
        $form->addElement('textarea', 'comments', get_string("resume_comments", "local_courseseditor"), 'rows="5" cols="80" style="resize:none; margin-left:2%;" placeholder="' . get_string('resume_comment_placeholder', 'local_courseseditor') . '"');
        $form->addElement('submit', 'insert', get_string('resume_next', 'local_courseseditor'));
    }
}