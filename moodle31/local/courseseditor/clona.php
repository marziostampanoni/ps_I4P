<?php


// The number of lines in front of config file determine the // hierarchy of files.
require_once('../../config.php');
require_once('form/clona.php');


$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('admin');
$PAGE->set_title("Course Creator");
$PAGE->set_heading("Course Creator");
$PAGE->requires->js( new moodle_url($CFG->wwwroot . '/local/courseseditor/js/main.js'));

//$PAGE->set_url($CFG->wwwroot.'/local/courseseditor/clona.php');
require_login();

echo $OUTPUT->header();
echo('<h2>'.get_string('clone_page_title','local_courseseditor').'</h2><br><div>');

$form = new FormClona(new moodle_url($CFG->wwwroot . '/local/courseseditor/resume.php'));
if ($fromform = $form->get_data()) {
    var_dump($fromform);
    //redirect($nexturl);
}





$form->display();
?>


<?php
echo('</div>');
echo $OUTPUT->footer();
?>
