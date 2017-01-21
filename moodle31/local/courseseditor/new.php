<?php
require_once('../../config.php');
require_once('form/addnewcourse.php');
require_once('form/searchcoursesws.php');
require_once('form/selectcorses.php');
require_once('class/SupsiWebServices.php');
require_once('class/UsiWebServices.php');
require_once('class/Richiesta.php');
require_once('class/Corso.php');


$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_courseseditor'));
$PAGE->set_heading(get_string('pluginname', 'local_courseseditor'));
$PAGE->requires->js( new moodle_url($CFG->wwwroot . '/local/courseseditor/js/main.js'));

require_login();

echo $OUTPUT->header();
echo('<h2 style="">'.get_string('Creazione nuovi corsi', 'local_courseseditor').'</h2>');

echo "<hr>";

unset($_SESSION['courses_to_insert']);

if ($_GET['user_type'] == 'usi') $ws = new UsiWebServices();
else $ws = new SupsiWebServices();

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

echo '<h4>' . get_string('Select courses', 'local_courseseditor') . '</h4>';

$form_select_corsi->display();



echo $OUTPUT->footer();
?>
