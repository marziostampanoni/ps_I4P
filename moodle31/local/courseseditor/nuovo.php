<?php


// The number of lines in front of config file determine the // hierarchy of files.
require_once('../../config.php');
require_once('form/nuovo.php');


$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('admin');
$PAGE->set_title("Course Creator");
$PAGE->set_heading("Course Creator");
$PAGE->requires->js( new moodle_url($CFG->wwwroot . '/local/courseseditor/js/main.js'));

require_login();

echo $OUTPUT->header();
echo('<h2>Crea un nuovo corso</h2><br><div>');

$form = new FormNuovo();
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
