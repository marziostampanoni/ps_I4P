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
echo('<h2>' . get_string('resume_page_title', 'local_requestmanager') . '</h2><br><div>');
$form = new FormClona();

if ($fromform = $form->get_data()) {
    $data = local_requestmanager\CEUtil::getParsedDataFromForm($_POST);
} else if ($_SESSION['courses_to_insert']) {
    $data = $_SESSION['courses_to_insert'];
}

//echo '<pre>';
//var_dump($data);
if (isset($_GET['updateEnroll']) && $_GET['updateEnroll'] != '') {
    if ($_GET['updateEnroll'] == '0') {
        $role = TIPO_RELAZIONE_DOCENTE;
    } else if ($_GET['updateEnroll'] == '1') {
        $role = TIPO_RELAZIONE_ASSISTENTE;
    }
    $idCorso = $_GET['id'];
    $ids = explode('_', $_GET['c']);
    $idsToDel = explode('_', $_GET['d']);

    $data[$idCorso]->teachers=array();
    $data[$idCorso]->editingteacher=array();
    foreach ($ids as $id) {
        $res = $DB->get_record('user', array('id' => $id));

        $usr = new stdClass();
        $usr->id = $id;
        $usr->name = $res->firstname.' '.$res->lastname;
        if($role==TIPO_RELAZIONE_DOCENTE){
            $data[$idCorso]->teachers[]=$usr;
        }else{
            $data[$idCorso]->editingteacher[]=$usr;
        }
    }
    foreach ($idsToDel as $idToDel) {

        if($role==TIPO_RELAZIONE_DOCENTE){
            foreach ($data[$idCorso]->teachers as $key => $et){
                if($et->id == $idToDel){
                    $key_del=$key;
                    break;
                }
            }
            unset($data[$idCorso]->teachers[$key_del]);

        }else{
            foreach ($data[$idCorso]->editingteacher as $key => $et){
                if($et->id == $idToDel){
                    $key_del=$key;
                    break;
                }
            }
            unset($data[$idCorso]->editingteacher[$key_del]);
        }
    }
}
//var_dump($data);
//echo '</pre>';

$resumeForm = new FormResume(null, array('data' => $data));

if (!isset($_GET['updateEnroll']) && $fromform = $resumeForm->get_data()) {
    if($_SESSION['just_saved']){
        redirect(new moodle_url($CFG->wwwroot . '/local/requestmanager/start.php'));
    }

    $data = local_requestmanager\CEUtil::getParsedDataFromFormResume($_POST);
    $id_cats_to_notify=array();
    if (count($data['corsi'] > 0)) {
        $r = new local_requestmanager\Richiesta();
        $r->setIdMdlUser($USER->id);
        $r->setNote($data['note']);
        foreach ($data['corsi'] as $corso) {

            $id_cats_to_notify[]=$corso['categoria'];

            $c = new local_requestmanager\Corso();
            $c->setIdMdlCourseCategories($corso['categoria']);
            $c->setNote($corso['note']);
            $c->setStatoRichiesta(STATO_RICHIESTA_DA_GESTIRE);
            $c->setTipoRichiesta(TIPO_RICHIESTA_INSERIRE);
            $c->setTitolo($corso['titolo']);
            $c->setShortname($corso['shortname']);

            if ($corso['teachers'] && count($corso['teachers']) > 0) {
                foreach ($corso['teachers'] as $teacher) {
                    $user = $DB->get_record('user', array('id' => $teacher['id']));

                    $u = new local_requestmanager\UserCorso();
                    $u->setIdMdlUser($user->id);
                    $u->setNome($user->firstname);
                    $u->setCognome($user->lastname);
                    $u->setTipoRelazione(TIPO_RELAZIONE_ASSISTENTE);
                    $c->addUser($u);
                }
            }

            if ($corso['editingteachers'] && count($corso['editingteachers']) > 0) {
                foreach ($corso['editingteachers'] as $teacher) {
                    $user = $DB->get_record('user', array('id' => $teacher['id']));

                    $u = new local_requestmanager\UserCorso();
                    $u->setIdMdlUser($user->id);
                    $u->setNome($user->firstname);
                    $u->setCognome($user->lastname);
                    $u->setTipoRelazione(TIPO_RELAZIONE_DOCENTE);
                    $c->addUser($u);
                }
            }
            $r->addCorso($c);
        }


        if ($r->saveToDB()) {
            $_SESSION['just_saved']=true;

            if(get_config('local_requestmanager','notify_manager_by_mail')==1){
                foreach ($id_cats_to_notify as $id_cat){
                    if(local_requestmanager\CEUtil::mailNotificationToManager($id_cat));
                }
            }
            echo '<div class="alert alert-success">
                    ' . get_string('resume_page_success', 'local_requestmanager') . '
                </div>';
        } else {
            echo '<div class="alert alert-danger">
                    ' . get_string('resume_page_error', 'local_requestmanager') . '
                </div>';
        }
        echo '<br><a type="button" class="btn btn-primary btn-lg" href="manage.php">' . get_string('manage_courses', 'local_requestmanager') . '</a>';
        echo '  <a type="button" class="btn btn-info btn-lg" href="start.php">' . get_string('pluginname', 'local_requestmanager') . '</a>';
    }

} else {
    $_SESSION['just_saved']=false;
    $resumeForm->display();
}
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
                <h4 class="modal-title"><?php echo(get_string('manage_enroll_user', 'local_requestmanager')); ?></h4>
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
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo(get_string('delete_modal_cancel', 'local_requestmanager')); ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal" onclick="updateField();"><?php echo(get_string('delete_modal_confirm', 'local_requestmanager')); ?></button>
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