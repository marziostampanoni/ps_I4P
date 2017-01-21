<?php
require_once('../../config.php');
require_once('form/addnewcourse.php');
require_once('form/searchcoursesws.php');
require_once('form/selectcorses.php');
require_once('class/SupsiWebServices.php');
require_once('class/UsiWebServices.php');
require_once('class/Richiesta.php');
require_once('class/Corso.php');
require_once('class/CEUtil.php');


$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_courseseditor'));
$PAGE->set_heading(get_string('pluginname', 'local_courseseditor'));
$PAGE->requires->js( new moodle_url($CFG->wwwroot . '/local/courseseditor/js/main.js'));

require_login();

echo $OUTPUT->header();
echo('<h2 style="">'.get_string('Creazione nuovi corsi', 'local_courseseditor').'</h2>');

echo "<hr>";


    $form_select_corsi = new FormSelectCourses();


    if ($fromform = $form_select_corsi->get_data()) {

        $_SESSION['courses_to_insert'] = CEUtil::getParsedDataFromForm($_POST);
    }

    $form_add = new FormAddNewCourse();


    if ($fromform = $form_add->get_data()) {

        $data = new stdClass();
        $data->idnull;
        $data->title =  $fromform->title . ", " . $fromform->code;
        $data->cat = $fromform->category;
        $data->shortdesc = $fromform->code;
        $data->teachers = null;
        $data->editingteacher = null;

        $_SESSION['courses_to_insert'][] = $data;
        $_SESSION['courses_to_insert_just_used']=true;
    }

    if(count($_SESSION['courses_to_insert'])>0) {
        echo '<h4>' . get_string('Corsi selezionati', 'local_courseseditor') . '</h4>';
        echo "<ul>";
        foreach ($_SESSION['courses_to_insert'] as $data) {
            echo " <li> " . $data->title . "</li>";
        }
        echo "</ul>";
        echo '<br><a type="button" class="btn btn-primary btn-lg" href="resume.php">'.get_string('Avanti','local_courseseditor').'</a>';
        echo "<hr>";
    }


    echo '<h4>'. get_string('Aggiungi corso','local_courseseditor').'</h4>';
    echo "<p>". get_string('Crea un corso che non esiste nei database ufficiale dei corsi USI/SUPSI','local_courseseditor')."</p>";
    $form_add->display();




echo $OUTPUT->footer();
?>
