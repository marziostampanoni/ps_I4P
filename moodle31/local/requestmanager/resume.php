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
    $_SESSION['courses_to_insert']=$data;
    $_SESSION['tipo_richiesta'] = TIPO_RICHIESTA_CLONARE;
} else if ($_SESSION['courses_to_insert']) {
    $data = $_SESSION['courses_to_insert'];
}

//echo '<pre>';
//var_dump($data);
if (isset($_GET['updateEnroll']) && $_GET['updateEnroll'] != '') {
    $idCorso = $_GET['id'];
    $ids = explode('_', $_GET['c']);
    $idsToDel = explode('_', $_GET['d']);

    if ($_GET['updateEnroll'] == '0') {
        $role = TIPO_RELAZIONE_DOCENTE;
    } else if ($_GET['updateEnroll'] == '1') {
        $role = TIPO_RELAZIONE_ASSISTENTE;
    }
    // aggiunta degli user
    foreach ($ids as $id) {
        if($id!='') {
            echo '<br> ---- ';
            $res = $DB->get_record('user', array('id' => $id));

            $usr = new stdClass();
            $usr->id = $id;
            $usr->name = $res->firstname . ' ' . $res->lastname;
            if ($role == TIPO_RELAZIONE_DOCENTE) {
                $data[$idCorso]->editingteacher[] = $usr;
            } else {
                $data[$idCorso]->teachers[] = $usr;
            }
        }
    }
    // rimozione di user
    foreach ($idsToDel as $idToDel) {
        if($role==TIPO_RELAZIONE_DOCENTE){
            foreach ($data[$idCorso]->editingteacher as $key => $et){
                if($et->id == $idToDel){
                    $key_del=$key;
                    break;
                }
            }
            unset($data[$idCorso]->editingteacher[$key_del]);

        }else{
            foreach ($data[$idCorso]->teachers as $key => $et){
                if($et->id == $idToDel){
                    $key_del=$key;
                    break;
                }
            }
            unset($data[$idCorso]->teachers[$key_del]);
        }
    }
}


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
            $c->setTipoRichiesta(($_SESSION['tipo_richiesta']==TIPO_RICHIESTA_CLONARE?TIPO_RICHIESTA_CLONARE:TIPO_RICHIESTA_INSERIRE));
            $c->setTitolo($corso['titolo']);
            $c->setShortname($corso['shortname']);

            if ($corso['teachers'] && count($corso['teachers']) > 0) {
                foreach ($corso['teachers'] as $teacher) {
                    $user = $DB->get_record('user', array('id' => $teacher));

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
                    $user = $DB->get_record('user', array('id' => $teacher));

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

        unset($_SESSION['tipo_richiesta']);

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
        echo '  <a type="button" class="btn btn-info btn-lg" href="start.php">' . get_string('plugin_home', 'local_requestmanager') . '</a>';
    }

} else {
    $_SESSION['just_saved']=false;
    $resumeForm->display();
}
?>


<?php
echo('</div>');
echo $OUTPUT->footer();
include "modale_enroll.php";
?>

