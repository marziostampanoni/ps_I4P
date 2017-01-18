<?php


// The number of lines in front of config file determine the // hierarchy of files.
require_once('../../config.php');
require_once('form/manage.php');


$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_courseseditor'));
$PAGE->set_heading(get_string('heading', 'local_courseseditor'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/local/courseseditor/js/main.js'));
require_login();

echo $OUTPUT->header();
echo('<h2>' . get_string('manage_page_title', 'local_courseseditor') . '</h2><br><div>');

if (isset($_GET['cancel']) && $_GET['cancel'] > 0) {
    $corso = new Corso(substr($_GET['cancel'], strpos($_GET['cancel'], "_") + 1));
    $corso->loadFromDB();
    $corso->stato_richiesta = STATO_RICHIESTA_SOSPESO;
    $corso->saveToDB();
}
if (isset($_GET['save']) && $_GET['save'] > 0) {
    $courses = array();
    foreach (explode('_', $_GET['save']) as $item) {
        $corso = new Corso($item);
        $corso->loadFromDB();
        $corso->setStatoRichiesta(STATO_RICHIESTA_FATTO);
        $corso->saveToDB();
    }
}
$form = new FormManage();
$form->display();
?>


<?php
echo('</div>');
echo $OUTPUT->footer();
?>

<script>
    function saveUserRequests(idReq) {
        var form = document.getElementById('mform1');
        form.action += '?save=' + idReq;
        console.log(form.action);
        form.submit();
    }
    function cancelCourse(idReq, idCourse) {
        var form = document.getElementById('mform1');
        var req = '?cancel=' + idReq;
        var action = req.concat('_') + idCourse;
        form.action += action;
        console.log(action);
        form.submit();
    }
</script>

