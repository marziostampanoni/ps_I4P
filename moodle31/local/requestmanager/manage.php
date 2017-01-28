<?php


// The number of lines in front of config file determine the // hierarchy of files.
require_once('include_all.php');

global $PAGE, $OUTPUT, $DB;

$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_requestmanager'));
$PAGE->set_heading(get_string('heading', 'local_requestmanager'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/local/requestmanager/js/main.js'));
require_login();

echo $OUTPUT->header();
echo('<h2>' . get_string('manage_page_title', 'local_requestmanager') . '</h2><hr><br><div>');


// [Admin Actions] Test if there is a course to delete/create or a request to reject
if (isset($_GET['cancel']) && $_GET['cancel'] > 0) {
    $corso = new local_requestmanager\Corso($_GET['cancel']);
    $corso->loadFromDB();
    //TODO cancellare il corso su moodle
    $corso->stato_richiesta = STATO_RICHIESTA_FATTO;
    $corso->saveToDB();
}
$enroll_update_key=$_GET['updateEnroll'].$_GET['id']. $_GET['c'].$_GET['d'];
//echo '<pre>';
if ($_SESSION['just_saved_enroll']!=$enroll_update_key && isset($_GET['updateEnroll']) && $_GET['updateEnroll'] != '') {
    //gestione del refresh per non rifare insert appena fatta
    $_SESSION['just_saved_enroll']=$enroll_update_key;

    if ($_GET['updateEnroll'] == '0') {
        $role = TIPO_RELAZIONE_DOCENTE;
    } else if ($_GET['updateEnroll'] == '1') {
        $role = TIPO_RELAZIONE_ASSISTENTE;
    }
    $idCorso = $_GET['id'];
    $ids = explode('_', $_GET['c']);
    $idsToDel = explode('_', $_GET['d']);

    $corso = new local_requestmanager\Corso($idCorso);
    $corso->loadFromDB();
    foreach ($ids as $id) {
        $res = $DB->get_record('user', array('id' => $id));
        $user = new local_requestmanager\UserCorso();
        $user->setNome($res->firstname);
        $user->setCognome($res->lastname);
        $user->setIdMdlUser($id);
        $user->setIdLclCourseseditorCorso($idCorso);
        $user->setTipoRelazione($role);
        $corso->addUser($user);
    }
    foreach ($idsToDel as $idToDel) {
        $DB->delete_records('requestmanager_corso_user', array('tipo_relazione' => $role, 'id_requestmanager_corso' => $idCorso, 'id_mdl_user' => $idToDel));
    }
    if (count($ids) > 0 && $ids[0] != '') {
        $corso->saveToDB();
    }
}


if (isset($_GET['save']) && $_GET['save'] > 0) {
    $courses = array();
    foreach (explode('_', $_GET['save']) as $item) {
        $corso = new local_requestmanager\Corso($item);
        $corso->loadFromDB();
        //TODO creare il corso su moodle
        $corso->setStatoRichiesta(STATO_RICHIESTA_FATTO);
        $corso->saveToDB();
    }
}

if (isset($_GET['reject']) && $_GET['reject'] > 0) {
    $corso = new local_requestmanager\Corso($_GET['reject']);
    $corso->loadFromDB();
    $corso->stato_richiesta = STATO_RICHIESTA_SOSPESO;
    $corso->saveToDB();
}

// [Ures Actions] Test if there is a request to modify/cancel
if (isset($_GET['savereq']) && $_GET['savereq'] > 0) {

}

if (isset($_GET['cancelreq']) && $_GET['cancelreq'] > 0) {
    $corso = new local_requestmanager\Corso(substr($_GET['cancel'], strpos($_GET['cancel'], "_") + 1));
    $corso->loadFromDB();
    $corso->stato_richiesta = STATO_RICHIESTA_SOSPESO;
    $corso->saveToDB();
}

global $DB;
$res = $DB->get_records('requestmanager_richiesta');

$users = array();
foreach ($res as $request) {
    $req = new local_requestmanager\Richiesta($request->id);
    $req->loadFromDB();
    $users[$req->id_mdl_user][] = $req;
}
//echo '</pre>';

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
include "modale_enroll.php";
?>

