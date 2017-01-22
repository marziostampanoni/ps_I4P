<?php


// The number of lines in front of config file determine the // hierarchy of files.
require_once('include_all.php');

$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_courseseditor'));
$PAGE->set_heading(get_string('heading', 'local_courseseditor'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/local/courseseditor/js/main.js'));
require_login();

echo $OUTPUT->header();
echo('<h2>' . get_string('resume_page_title', 'local_courseseditor') . '</h2><br><div>');
$form = new FormClona();

if ($fromform = $form->get_data()) {
    $data = CEUtil::getParsedDataFromForm($_POST);
} else if ($_SESSION['courses_to_insert']) {
    $data = $_SESSION['courses_to_insert'];
}

$resumeForm = new FormResume(null, array('data' => $data));
//unset($_SESSION['courses_to_insert']);
if ($fromform = $resumeForm->get_data()) {
    if($_SESSION['just_saved']){
        redirect(new moodle_url($CFG->wwwroot . '/local/courseseditor/start.php'));
    }

    $data = CEUtil::getParsedDataFromFormResume($_POST);
    $id_cats_to_notify=array();
    if (count($data['corsi'] > 0)) {
        $r = new Richiesta();
        $r->setIdMdlUser($USER->id);
        foreach ($data['corsi'] as $corso) {

            $id_cats_to_notify[]=$corso['categoria'];

            $c = new Corso();
            $c->setIdMdlCourseCategories($corso['categoria']);
            $c->setNote($corso['note']);
            $c->setStatoRichiesta(STATO_RICHIESTA_DA_GESTIRE);
            $c->setTipoRichiesta(TIPO_RICHIESTA_INSERIRE);
            $c->setTitolo($corso['titolo']);
            $c->setShortname($corso['shortname']);

            if ($corso['teachers'] && count($corso['teachers']) > 0) {
                foreach ($corso['teachers'] as $teacher) {
                    $user = $DB->get_record('user', array('id' => $teacher['id']));

                    $u = new UserCorso();
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

                    $u = new UserCorso();
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

            if(get_config('local_courseseditor','notify_manager_by_mail')==1){
                foreach ($id_cats_to_notify as $id_cat){
                    if(CEUtil::mailNotificationToManager($id_cat));
                }
            }
            echo '<div class="alert alert-success">
                    ' . get_string('resume_page_success', 'local_courseseditor') . '
                </div>';
        } else {
            echo '<div class="alert alert-danger">
                    ' . get_string('resume_page_error', 'local_courseseditor') . '
                </div>';
        }
        echo '<br><a type="button" class="btn btn-primary btn-lg" href="manage.php">' . get_string('manage_courses', 'local_courseseditor') . '</a>';
        echo '  <a type="button" class="btn btn-info btn-lg" href="start.php">' . get_string('pluginname', 'local_courseseditor') . '</a>';
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
