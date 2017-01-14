<?php


// The number of lines in front of config file determine the // hierarchy of files.
require_once('../../config.php');
require_once('form/clona.php');
require_once('form/resume.php');



$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_courseseditor'));
$PAGE->set_heading(get_string('heading', 'local_courseseditor'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/local/courseseditor/js/main.js'));
require_login();

echo $OUTPUT->header();
echo('<h2>' . get_string('resume_page_title', 'local_courseseditor') . '</h2><br><div>');
$form = new FormClona(); //puoi passare l'action del form come parametro in costruzione.ai

if ($fromform = $form->get_data()) {

    $table = new html_table();
    $table->attributes = array('style'=> 'border:solid 1px black;');
    $table->head = array(get_string('resume_tablehead_title', 'local_courseseditor'), get_string('resume_tablehead_cat', 'local_courseseditor'), get_string('resume_tablehead_teacher', 'local_courseseditor'), get_string('resume_tablehead_editingteacher', 'local_courseseditor'), get_string('resume_tablehead_note', 'local_courseseditor'));
    foreach ($fromform as $name => $post) {
        $prefix = substr($name, 0, 4);
        if ($prefix == 'name') {
            $id = substr($name, strpos($name, "-") + 1);

            $datasel = 'data-' . $id;
            $trdata = json_decode($fromform->$datasel);
            $resumeForm = new FormResume(null,array('data'=>$trdata));
            $tabledata = array();

            foreach ($trdata as $tdkey => $tdval) {
                if($tdkey!='id'){
                    if($tdkey == 'cat'){

                    }
                    if(is_array($tdval)){
                    }
                    $tabledata[] = $tdval;
                }
            }
            $tabledata[] = '';
            $table->data[] = $tabledata;
        }
    }
    echo html_writer::table($table);


}
$resumeForm->display();
?>


<?php
echo('</div>');
echo $OUTPUT->footer();
?>

