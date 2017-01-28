<?php


require_once("$CFG->libdir/formslib.php");

class FormResume extends moodleform
{
    protected function definition()
    {
        global $DB;
        $form = $this->_form;

        //if (count($this->_customdata['data'])) {
        $eachCat = coursecat::make_categories_list();


        $i = 0;
        foreach ($this->_customdata['data'] as $trdata) {
            $form->addElement('html', '<div class="block form-group">');
            $form->addElement('html', '<table class="table">');
            $form->addElement('html', '<tr>');
            $form->addElement('html', '<td colspan="2" style="width: 70%; border: none;padding: 0px 10px;"> <label>' . get_string('resume_tablehead_title', 'local_requestmanager') . '</label>');
            $form->addElement('html', '<input style="width:100%;" id="coursetitle_' . $i . '" name="titolo-' . $i . '" type="text" value="' . $trdata->title . '" style="display:table-cell; width:95%">');
            $form->addElement('html', '</td>');
            $form->addElement('html', '<td style="border: none;padding: 0px 10px;"><label>' . get_string('manage_tablehead_shortname', 'local_requestmanager') . '</label><input name="shortname-' . $i . '" type="text" value="' . $trdata->shortname . '" size="40" style="display:table-cell; width:95%"></td>');
            $form->addElement('html', '</tr>');
            $form->addElement('html', '<tr><td colspan="2" style="border: none;padding: 0px 10px;"><label for="categoria-' . $i . '">' . get_string('resume_tablehead_cat', 'local_requestmanager') . '</label>');
            $form->addElement('html', '<br><select style="width:100%;" name="categoria-' . $i . '" id="categoria-' . $i . '">');
            foreach ($eachCat as $id => $option) {
                $form->addElement('html', '<option value="' . $id . '" ' . (($trdata->cat == $id) ? 'selected' : '') . '>' . $option . "</li>");
            }
            $form->addElement('html', '</select>');
            $form->addElement('html', '</td>');
            $form->addElement('html', '<td rowspan="2" style="border: none; padding: 0px 10px;"><label>' . get_string('resume_tablehead_note', 'local_requestmanager') . '</label><textarea name="note-' . $i . '" style="resize: none; display:table-cell; width:95%;height: 95%;">' . $trdata->note . '</textarea></td>');
            $form->addElement('html', '</tr>');
            $form->addElement('html', '<tr>');

            $form->addElement('html', '<td style="border: none;padding: 0px 10px;"><label>' . get_string('resume_tablehead_teacher', 'local_requestmanager') . '</label><ul id="teachers_list_' . $i . '">');
            if(is_array($trdata->teachers)) {
                foreach ($trdata->teachers as $option) {
                    $form->addElement('html', '<li class="teacher_' . $i. '" value="' . $option->id . '" style="white-space: nowrap;">' . $option->name . '</li>');
                    $form->addElement('html', '<input type="hidden" value="' . $option->id . '" name="teachers-' . $i . '[]"');
                }
            }
            $form->addElement('html', '</ul><button class="btn" onclick="enroll(\'' . $i. '\', \'1\');" data-toggle="modal" data-target="#enrollModal">Enroll</button></td>');

            $form->addElement('html', '<td style="border: none; padding: 0px 10px;"><label>' . get_string('resume_tablehead_editingteacher', 'local_requestmanager') . '</label><ul id="editingteachers_list_' . $i . '">');
            if(is_array($trdata->editingteacher)) {
                foreach ($trdata->editingteacher as $option) {
                    $form->addElement('html', '<li class="editingteacher_' . $i. '" value="' . $option->id . '" style="white-space: nowrap;">' . $option->name . '</li>');
                    $form->addElement('html', '<input type="hidden" value="' . $option->id . '" name="editingteachers-' . $i . '[]"');
                }
            }
            $form->addElement('html', '</ul><button class="btn" onclick="enroll(\'' . $i . '\', \'0\');" data-toggle="modal" data-target="#enrollModal">Enroll</button></td>');
            $form->addElement('html', '</tr>');
            $form->addElement('html', '</table>');
            $form->addElement('html', '</div>');
            $i++;
        }

        $form->addElement('textarea', 'note', get_string("resume_comments", "local_requestmanager"), 'rows="5" cols="80" style="resize:none; margin-left:2%;" placeholder="' . get_string('resume_comment_placeholder', 'local_requestmanager') . '"');
        $form->addElement('submit', 'insert', get_string('resume_next', 'local_requestmanager'));

//        } else {
//            $form->addElement('html','No requests');
//        }

    }
}