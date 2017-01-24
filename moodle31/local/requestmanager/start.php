<?php


// The number of lines in front of config file determine the // hierarchy of files.
require_once('include_all.php');


$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_requestmanager'));
$PAGE->set_heading(get_string('heading', 'local_requestmanager'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/local/requestmanager/js/main.js'));
require_login();

echo $OUTPUT->header();
echo('<h2>' . get_string('start_page_title', 'local_requestmanager') . '</h2><br><div>');
$form = new FormStart(); //puoi passare l'action del form come parametro in costruzione.ai
if ($fromform = $form->get_data()) {
    redirect($nexturl);
}
$form->display();
?>


<?php
echo('</div>');
echo $OUTPUT->footer();
?>

