<?php


// The number of lines in front of config file determine the // hierarchy of files.
require_once('../../config.php');
require_once('form/clona.php');
require_once('form/cancella.php');
require_once('form/resume.php');
require_once('class/CEUtil.php');
require_once('class/Richiesta.php');


$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_courseseditor'));
$PAGE->set_heading(get_string('heading', 'local_courseseditor'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/local/courseseditor/js/main.js'));
require_login();

echo $OUTPUT->header();
echo('<h2>' . get_string('resume_page_title', 'local_courseseditor') . '</h2><br><div>');
$form = new FormClona();
$cancelForm = new FormCancella();


if ($fromform = $form->get_data()) {
    $data = CEUtil::getParsedDataFromForm($fromform);
} else if($fromform = $cancelForm->get_data()){
    $data = CEUtil::getParsedDataFromForm($fromform);
} else if($_SESSION['courses_to_insert']){
    $data=$_SESSION['courses_to_insert'];
}

$resumeForm = new FormResume(null,array('data'=>$data));
unset($_SESSION['courses_to_insert']);
if ($fromform = $resumeForm->get_data()) {

    $data = CEUtil::getParsedDataFromFormResume($_POST);

    if(count($data['corsi']>0)){
        $r= new Richiesta();
        $r->setIdMdlUser($USER->id);
        foreach ($data['corsi'] as $corso) {
            $c=new Corso();
            $c->setIdMdlCourseCategories($corso['categoria']);
            $c->setNote($corso['note']);
            $c->setStatoRichiesta(STATO_RICHIESTA_DA_GESTIRE);
            $c->setTipoRichiesta(TIPO_RICHIESTA_INSERIRE);
            $c->setTitolo($corso['titolo']);

            if($corso['teachers'] && count($corso['teachers'])>0){
                foreach ($corso['teachers'] as $teacher){
                    $user = $DB->get_record('user', array('id'=>$teacher['id']));

                    $u=new UserCorso();
                    $u->setIdMdlUser($user->id);
                    $u->setNome($user->firstname);
                    $u->setCognome($user->lastname);
                    $u->setTipoRelazione(TIPO_RELAZIONE_ASSISTENTE);
                    $c->addUser($u);
                }
            }

            if($corso['editingteachers'] && count($corso['editingteachers'])>0){
                foreach ($corso['editingteachers'] as $teacher){
                    $user = $DB->get_record('user', array('id'=>$teacher['id']));

                    $u=new UserCorso();
                    $u->setIdMdlUser($user->id);
                    $u->setNome($user->firstname);
                    $u->setCognome($user->lastname);
                    $u->setTipoRelazione(TIPO_RELAZIONE_DOCENTE);
                    $c->addUser($u);
                }
            }
            $r->addCorso($c);
        }


       if($r->saveToDB()){

            echo '<div class="alert alert-success">
                    ' . get_string('resume_page_success', 'local_courseseditor') . '
                </div>';
       }else{
           echo '<div class="alert alert-danger">
                    ' . get_string('resume_page_error', 'local_courseseditor') . '
                </div>';
       }
        echo '<br><a type="button" class="btn btn-primary btn-lg" href="manage.php">'.get_string('manage_courses','local_courseseditor').'</a>';
        echo '  <a type="button" class="btn btn-info btn-lg" href="start.php">'.get_string('pluginname','local_courseseditor').'</a>';
    }

}else{
    $resumeForm->display();
}
?>


<?php
echo('</div>');
echo $OUTPUT->footer();
?>
