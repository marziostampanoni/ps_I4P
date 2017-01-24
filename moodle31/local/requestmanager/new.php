<?php
require_once('include_all.php');


$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_requestmanager'));
$PAGE->set_heading(get_string('pluginname', 'local_requestmanager'));
$PAGE->requires->js( new moodle_url($CFG->wwwroot . '/local/requestmanager/js/main.js'));

require_login();

echo $OUTPUT->header();
echo('<h2 style="">'.get_string('Creazione nuovi corsi', 'local_requestmanager').'</h2>');

echo "<hr>";

unset($_SESSION['courses_to_insert']);

if ($_GET['user_type'] == 'usi') $ws = new local_requestmanager\UsiWebServices();
else $ws = new local_requestmanager\SupsiWebServices();

$filter_cerca = null;
$filter_netID = null;
$form_cerca = new FormSearchCoursesWs();


if ($fromform = $form_cerca->get_data()) {
    if ($fromform->string != '') $filter_cerca = $fromform->string;
    if ($fromform->onlythis == 1) $filter_netID = $USER->username;
    if ($fromform->submitsupsi) $ws = new SupsiWebServices();
    if ($fromform->submitusi) $ws = new UsiWebServices();
}

$result = $ws->getCorsi($filter_netID, $filter_cerca);

$form_select_corsi = new FormSelectCourses('newadd.php', array('corsi' => $result));

$form_cerca->display();

echo '<h4>' . get_string('Select courses', 'local_requestmanager') . '</h4>';

$form_select_corsi->display();



echo $OUTPUT->footer();
?>
