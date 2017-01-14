<?php


// The number of lines in front of config file determine the // hierarchy of files.
require_once('../../config.php');
require_once('form/clona.php');


$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('admin');
$PAGE->set_title("Course Creator");
$PAGE->set_heading("Course Creator");
//$PAGE->set_url($CFG->wwwroot.'/local/courseseditor/clona.php');
require_login();

echo $OUTPUT->header();
echo('<h2>Clona un corso esistente</h2><br><div>');

$form = new FormClona();
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
