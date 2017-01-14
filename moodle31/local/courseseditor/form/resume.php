<?php


require_once("$CFG->libdir/formslib.php");

class FormResume extends moodleform
{
    protected function definition()
    {
        global $USER, $DB;
        // corsi da caricare via web service da usi/supsi
        // $courses = $DB->get_records_sql($query, array($USER->id));
        //var_dump($courses);

        //$statesArray=array();
        //foreach ($courses as $corso){
        //    $statesArray[$corso->instanceid]='<br>'.$corso->fullname.' Ruolo:'.$corso->archetype;
        //}

        $eachCat = coursecat::make_categories_list();
        $form = $this->_form;
        $categories = array();
        foreach ($eachCat as $cat) {
            $categories[$cat] = $cat;
        }
        //$select = $form->addElement('select', 'categories', get_string('Corsi'), $categories);
        //var_dump($this->_customdata['data']->cat);
        //$select->setSelected('categories', $this->_customdata['data']->cat);

        $form->addElement('button', 'intro', get_string('resume_next', 'local_courseseditor'), array('onclick' => 'updateURL(\'nuovo\');'));
    }
}