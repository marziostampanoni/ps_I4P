<?php


// The number of lines in front of config file determine the // hierarchy of files.
require_once('../../config.php');
require_once('form/manage.php');
require_once('form/selectuser.php');

$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_courseseditor'));
$PAGE->set_heading(get_string('heading', 'local_courseseditor'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/local/courseseditor/js/main.js'));
require_login();

echo $OUTPUT->header();
echo('<h2>' . get_string('manage_page_title', 'local_courseseditor') . '</h2><hr><br><div>');


// [Admin Actions] Test if there is a course to delete/create or a request to reject
if (isset($_GET['cancel']) && $_GET['cancel'] > 0) {
    $corso = new Corso($_GET['cancel']);
    $corso->loadFromDB();
    //TODO cancellare il corso su moodle
    $corso->stato_richiesta = STATO_RICHIESTA_FATTO;
    $corso->saveToDB();
}

if (isset($_GET['save']) && $_GET['save'] > 0) {
    $courses = array();
    foreach (explode('_', $_GET['save']) as $item) {
        $corso = new Corso($item);
        $corso->loadFromDB();
        //TODO creare il corso su moodle
        $corso->setStatoRichiesta(STATO_RICHIESTA_FATTO);
        $corso->saveToDB();
    }
}

if (isset($_GET['reject']) && $_GET['reject'] > 0) {
    $corso = new Corso($_GET['reject']);
    $corso->loadFromDB();
    $corso->stato_richiesta = STATO_RICHIESTA_SOSPESO;
    $corso->saveToDB();
}

// [Ures Actions] Test if there is a request to modify/cancel
if (isset($_GET['savereq']) && $_GET['savereq'] > 0) {
    var_dump($_GET['savereq']);

}

if (isset($_GET['cancelreq']) && $_GET['cancelreq'] > 0) {
    $corso = new Corso(substr($_GET['cancel'], strpos($_GET['cancel'], "_") + 1));
    $corso->loadFromDB();
    $corso->stato_richiesta = STATO_RICHIESTA_SOSPESO;
    $corso->saveToDB();
}

global $DB;
$res = $DB->get_records('lcl_courseseditor_richiesta');

$users = array();
foreach ($res as $request) {
    $req = new Richiesta($request->id);
    $req->loadFromDB();
    $users[$req->id_mdl_user][] = $req;
}


$select = new FormSelectUser(null, array('users' => $users));

if ($fromform = $select->get_data()) {
    if ($fromform->user != 0) {
        foreach ($users as $id => $user) {
            if ($id != $fromform->user) {
                unset($users[$id]);
            }
        }
    }
}

if (is_siteadmin($USER->id)) {
    $select->display();
} else {
    foreach ($users as $id => $user) {
        if ($id != $USER->id) {
            unset($users[$id]);
        }
    }
}

$form = new FormManage(null, array('requests' => $res, 'users' => $users));
$form->display();
?>


<?php
echo('</div>');
echo $OUTPUT->footer();
?>

<script>
    function actionOnCourse(id, action) {
        if (action === 'save' || action === 'cancel' || action === 'savereq' || action === 'cancelreq' || action === 'reject') {
            var form = document.getElementById('mform2');
            var action = '?' + action + '=' + id;
            form.action += action;
            form.submit();
        } else {
            return;
        }
    }
</script>

