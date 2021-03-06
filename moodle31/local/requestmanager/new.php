<?php
require_once('include_all.php');


$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_requestmanager'));
$PAGE->set_heading(get_string('pluginname', 'local_requestmanager'));
$PAGE->requires->js( new moodle_url($CFG->wwwroot . '/local/requestmanager/js/main.js'));

echo $OUTPUT->header();
echo('<h2 style="">'.get_string('course_creation', 'local_requestmanager').'</h2>');
require_once('check_capabilities.php');
echo "<hr>";

unset($_SESSION['courses_to_insert']);

echo '<span class="nowrap" style="font-size: 18px;"> ' . get_string('create_new_course_from_null', 'local_requestmanager') . ':  <a class="btn btn-primary" href="newadd.php">' . get_string('next', 'local_requestmanager') . '</a></span>';


echo "<div style='margin: 10px 0px;'>OR</div>";

if ($_GET['user_type'] == 'usi') $ws = new local_requestmanager\UsiWebServices();
else $ws = new local_requestmanager\SupsiWebServices();

$filter_cerca = null;
$filter_netID = $USER->username;
$form_cerca = new FormSearchCoursesWs();


if ($fromform = $form_cerca->get_data()) {
    if ($fromform->string != ''){
        $filter_cerca = $fromform->string;
        $filter_netID = NULL;
    }
    if ($fromform->submitsupsi) $ws = new local_requestmanager\SupsiWebServices();
    if ($fromform->submitusi) $ws = new local_requestmanager\UsiWebServices();
}

$result = $ws->getCorsi($filter_netID, $filter_cerca);

$form_select_corsi = new FormSelectCourses('newadd.php', array('corsi' => $result));

echo '<h4>' . get_string('Select courses', 'local_requestmanager') . '</h4>';

$form_cerca->display();

if(!is_null($filter_cerca))echo '<div style="width: 100%;text-align: center;"><a class="btn btn-info" href="new.php">' . get_string('reset_filter', 'local_requestmanager') . '</a></div>';

$form_select_corsi->display();



echo $OUTPUT->footer();
?>
