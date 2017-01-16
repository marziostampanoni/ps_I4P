<?php


// The number of lines in front of config file determine the // hierarchy of files.
require_once('../../config.php');
require_once('form/clona.php');
require_once('form/cancella.php');
require_once('form/resume.php');



$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_courseseditor'));
$PAGE->set_heading(get_string('heading', 'local_courseseditor'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/local/courseseditor/js/main.js'));
require_login();

echo $OUTPUT->header();
echo('<h2>' . get_string('resume_page_title', 'local_courseseditor') . '</h2><br><div>');
$form = new FormClona();
$cancelForm = new FormCancella();


if ($fromform = $form->get_data()) {
    $resumeForm = new FormResume(null,array('data'=>$fromform));
} else if($fromform = $cancelForm->get_data()){
    $resumeForm = new FormResume(null,array('data'=>$fromform));
} else if($_SESSION['courses_to_insert']){
    $resumeForm = new FormResume(null,array('data'=>$_SESSION['courses_to_insert']));
}
$resumeForm->display();
?>


<?php
echo('</div>');
echo $OUTPUT->footer();
?>

