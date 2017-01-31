<?php
require_once('include_all.php');


$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_requestmanager'));
$PAGE->set_heading(get_string('pluginname', 'local_requestmanager'));
$PAGE->requires->js( new moodle_url($CFG->wwwroot . '/local/requestmanager/js/main.js'));
echo $OUTPUT->header();
echo('<h2 style="">'.get_string('new_course_creation', 'local_requestmanager').'</h2>');
require_once('check_capabilities.php');
echo "<hr>";

//delete course from session
if(isset($_GET['delete'])){
    unset($_SESSION['courses_to_insert'][$_GET['delete']]);
}

$form_select_corsi = new FormSelectCourses();

// save in session courses selected from usi/supsi
if ($fromform = $form_select_corsi->get_data()) {
    $_SESSION['courses_to_insert'] = local_requestmanager\CEUtil::getParsedDataFromForm($_POST);
}

$form_add = new FormAddNewCourse();

// add in session the new course added
if ($fromform = $form_add->get_data()) {

    $data = new stdClass();
    $data->idnull;
    $data->title =  $fromform->title . ", " . $fromform->code;
    $data->cat = $fromform->category;
    $data->shortname = $fromform->code;
    $data->teachers = null;

    $user = new stdClass();
    $user->id=$USER->id;
    $user->name=$USER->firstname.' '.$USER->lastname ;
    $data->editingteacher = array($user);

    if($_SESSION['new_just_added'] != serialize($data)) {
        $_SESSION['new_just_added'] = serialize($data);
        $_SESSION['courses_to_insert'][] = $data;
    }
}

if(count($_SESSION['courses_to_insert'])>0) {
    echo '<h4>' . get_string('add_another_course', 'local_requestmanager') . '</h4>';
}else {
    echo '<h4>' . get_string('add_course', 'local_requestmanager') . '</h4>';
}

echo "<p>". get_string('create_new_course_from_null','local_requestmanager')."</p>";
$se = new stdClass();
$se->title=null;
$se->code=null;
$se->category=null;
$form_add->set_data(array($se));
$form_add->display();
echo "<hr>";
if(count($_SESSION['courses_to_insert'])>0) {
    echo '<h4>' . get_string('selected_courses', 'local_requestmanager') . '</h4>';
    echo "<ul>";
    foreach ($_SESSION['courses_to_insert'] as $id=>$data) {
        echo " <li> " . $data->title . '   <a class="red" href="newadd.php?delete='.$id.'">'.get_string('delete','local_requestmanager').'</a></li>';
    }
    echo "</ul>";
    echo '<br><a type="button" class="btn btn-primary btn-lg" href="resume.php">'.get_string('next','local_requestmanager').'</a>';

}







echo $OUTPUT->footer();
?>
