<?php


// The number of lines in front of config file determine the // hierarchy of files.
require_once('include_all.php');

global $PAGE, $OUTPUT, $DB;

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

if (isset($_GET['updateEnroll']) && $_GET['updateEnroll'] != '') {
    if ($_GET['updateEnroll'] == '0') {
        $role = TIPO_RELAZIONE_DOCENTE;
    } else if ($_GET['updateEnroll'] == '1') {
        $role = TIPO_RELAZIONE_ASSISTENTE;
    }
    $idCorso = $_GET['id'];
    $ids = explode('_', $_GET['c']);
    $idsToDel = explode('_', $_GET['d']);

    $corso = new Corso($idCorso);
    $corso->loadFromDB();
    foreach ($ids as $id) {
        $res = $DB->get_record('user', array('id' => $id));
        $user = new UserCorso();
        $user->setNome($res->firstname);
        $user->setCognome($res->lastname);
        $user->setIdMdlUser($id);
        $user->setIdLclCourseseditorCorso($idCorso);
        $user->setTipoRelazione($role);
        $corso->addUser($user);
    }
    foreach ($idsToDel as $idToDel) {
        $DB->delete_records('lcl_courseseditor_corso_user', array('tipo_relazione' => $role, 'id_lcl_courseseditor_corso' => $idCorso, 'id_mdl_user' => $idToDel));
    }
    if (count($ids) > 0 && $ids[0] != '') {
        $corso->saveToDB();
    }
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

<div id="enrollModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo(get_string('manage_enroll_user', 'local_courseseditor')); ?></h4>
                <small id="coursetitle"></small>
                <input type="hidden" id="modal_detail_course" value="">
                <input type="hidden" id="modal_detail_role" value="">
            </div>
            <div class="modal-body" id="enroll-modal-body">
                <table class="generaltable table-bordered">
                    <tbody id="enrolled"></tbody>
                </table>
                <hr>
                <table class="generaltable table-bordered">
                    <tbody id="notenrolled">
                    <?php
                    $query = 'select u.id as id, firstname, lastname, picture, imagealt, email from mdl_role_assignments as a, mdl_user as u where roleid in (3,4) and a.userid=u.id;';
                    $res = $DB->get_records_sql($query, array());
                    foreach ($res as $id => $user) {
                        echo('<tr id="user_' . $id . '">');
                        echo('<input type="hidden" id="flag_' . $user->id . '" value="0">');
                        echo('<input type="hidden" class="althere" id="prev_' . $user->id . '" value="0" data-id="' . $user->id . '">');
                        echo('<td style="width: 80%;">');
                        echo('<strong>' . $user->lastname . ' ' . $user->firstname . '</strong>');
                        echo('<br><small>' . $user->email . '</small>');
                        echo('</td>');
                        echo('<td style="text-align: center; vertical-align: middle;"><button value="' . $user->id . '_' . $user->lastname . ' ' . $user->firstname . '" class="btn button-choice" id="btn_' . $user->id . '" onclick="swap(\'' . $id . '\')" style="width:80px;">Enroll</button></td>');
                        echo('</tr>');
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo(get_string('delete_modal_cancel', 'local_courseseditor')); ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal" onclick="updateField();"><?php echo(get_string('delete_modal_confirm', 'local_courseseditor')); ?></button>
            </div>
        </div>
    </div>
</div>


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

    function enroll(idCorso, role) {
        var coursetitle = document.getElementById('coursetitle_' + idCorso).value;
        var usersArray = new Array();
        if (role == 0) {
            var users = document.getElementsByClassName('editingteacher_' + idCorso);
        } else if (role == 1) {
            var users = document.getElementsByClassName('teacher_' + idCorso);
        }
        for (var i = 0; i < users.length; i++) {
            usersArray += users[i].value;
        }
        setupModal(coursetitle, usersArray, idCorso, role);
    }

    function setupModal(title, users, idCorso, role) {
        document.getElementById('modal_detail_course').value = idCorso;
        document.getElementById('modal_detail_role').value = role;
        document.getElementById('coursetitle').innerHTML = title;
        var enrolled = document.getElementById('enrolled');
        for (var i = 0; i < users.length; i++) {
            swap(users[i], '1')
        }
    }

    function swap(user, prev='-1') {
        var element = document.getElementById('user_' + user);
        var flag = document.getElementById('flag_' + user);
        var althere = document.getElementById('prev_' + user);
        var btn = document.getElementById('btn_' + user);
        var table;
        switch (flag.value) {
            case '1':
                table = document.getElementById('notenrolled');
                btn.innerHTML = "Enroll";
                flag.value = '0';
                break;
            case '0':
                table = document.getElementById('enrolled');
                btn.innerHTML = "Unenroll";
                flag.value = '1';
                break;
        }
        if (prev == '-1') {
            prev = althere.value;
        }
        althere.value = prev;
        element.parentNode.removeChild(element);
        table.append(element);
    }

    function updateField() {
        var enrolled = document.getElementById('enrolled');
        var btns = enrolled.getElementsByClassName('button-choice');
        var idCourse = document.getElementById('modal_detail_course').value;
        var role = document.getElementById('modal_detail_role').value;
        var ul, cl, lis;
        switch (role) {
            case '0':
                cl = 'editingteacher_' + idCourse;
                ul = document.getElementById('editingteachers_list_' + idCourse);
                lis = ul.getElementsByClassName('editingteacher_' + idCourse);
                break;
            case '1':
                cl = 'teacher_' + idCourse;
                ul = document.getElementById('teachers_list_' + idCourse);
                lis = ul.getElementsByClassName('teacher_' + idCourse);
                break;
        }
        var ids = [];
        for (var i = 0; i < btns.length; i++) {
            var str = btns[i].value;
            var id = str.substring(0, str.indexOf("_"));
            var name = str.substring(str.indexOf("_") + 1);
            var add = true;
            for (var j = 0; j < lis.length; j++) {
                if (lis[j].value == id) {
                    add = false;
                }
            }
            if (add) {
                ids.push(id);
            }
        }


        //TODO FIXARE IL DELETE DEGLI UTENTI!
        var todelete = [];
        var not = document.getElementById('notenrolled');
        var althere = not.getElementsByClassName('althere');
        for (var k = 0; k < althere.length; k++) {
            if (althere[k].value == 1) {
                todelete.push(althere[k].getAttribute('data-id'))
            }
        }
        document.getElementById('modal_detail_course').value = '';
        document.getElementById('modal_detail_role').value = '';
        var form = document.getElementById('mform2');
        var toDel = todelete.join('_');
        var toCreate = ids.join('_');
        var data = '&c=' + toCreate + '&d=' + toDel;
        form.action += '?updateEnroll=' + role + '&id=' + idCourse + data;
        form.submit();
    }
</script>

