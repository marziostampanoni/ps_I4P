<?php


// The number of lines in front of config file determine the // hierarchy of files.
require_once('include_all.php');



$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('admin');
$PAGE->set_title("Course Creator");
$PAGE->set_heading("Course Creator");
$PAGE->requires->js( new moodle_url($CFG->wwwroot . '/local/requestmanager/js/main.js'));


echo $OUTPUT->header();
echo('<h2>'.get_string('clone_page_title','local_requestmanager').'</h2><hr><br><div>');
require_once('check_capabilities.php');
$query = "
SELECT *  FROM
mdl_user u
JOIN mdl_role_assignments ra ON ra.userid = u.id
JOIN mdl_role r ON ra.roleid = r.id
JOIN mdl_context con ON ra.contextid = con.id
JOIN mdl_course c ON c.id = con.instanceid
WHERE u.id=? AND (r.shortname = 'teacher' OR r.shortname = 'editingteacher' OR r.shortname = 'manager')";


$courses = $DB->get_records_sql($query, array($USER->id));
if($courses && count($courses)>0) {
//categories where the user has courses
    $cat_with_courses = array();
    foreach ($courses as $course) {
        if (!in_array($course->category, $cat_with_courses)) {
            $cat_with_courses[] = $course->category;
        }
    }

    $filter = new FormSelectCat(null, array('cats' => $cat_with_courses));
    if ($fromform = $filter->get_data()) {
        if (isset($fromform->cat) && $fromform->cat > 0) {
            $subCat = array();
            foreach (coursecat::get($fromform->cat)->get_courses(array('recursive' => true)) as $c) {
                $subCat[] = $c->category;
            }
            $subCat = array_unique($subCat);
            foreach ($courses as $id => $course) {
                if (!in_array($course->category, $subCat)) {
                    unset($courses[$id]);
                }
            }
            $cat_with_courses=$subCat;
        }
    }


    $form = new FormClona(new moodle_url($CFG->wwwroot . '/local/requestmanager/resume.php'), array('data' => $courses, 'cats' => $cat_with_courses));
    if ($fromform = $form->get_data()) {
    }


    $filter->display();
    $form->display();
}else {
    echo '<div class="alert alert-warning">
                    ' . get_string('no_courses_to_clone', 'local_requestmanager') . '
                </div>';

}
?>


<?php
echo('</div>');
echo $OUTPUT->footer();
?>
