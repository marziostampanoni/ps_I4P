<?php
require_once('include_all.php');


$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_requestmanager'));
$PAGE->set_heading(get_string('pluginname', 'local_requestmanager'));
$PAGE->requires->js( new moodle_url($CFG->wwwroot . '/local/requestmanager/js/main.js'));

require_login();

echo $OUTPUT->header();
echo('<h2 style="">'.get_string('Creazione nuovi corsi', 'local_requestmanager').'</h2>');

echo "<hr>";


    $form_select_corsi = new FormSelectCourses();


    if ($fromform = $form_select_corsi->get_data()) {

        $_SESSION['courses_to_insert'] = local_requestmanager\CEUtil::getParsedDataFromForm($_POST);
    }

    $form_add = new FormAddNewCourse();


    if ($fromform = $form_add->get_data()) {

        $data = new stdClass();
        $data->idnull;
        $data->title =  $fromform->title . ", " . $fromform->code;
        $data->cat = $fromform->category;
        $data->shortname = $fromform->code;
        $data->teachers = null;
        $data->editingteacher = null;

        $_SESSION['courses_to_insert'][] = $data;
        $_SESSION['courses_to_insert_just_used']=true;
    }

echo '<h4>'. get_string('Aggiungi corso','local_requestmanager').'</h4>';
echo "<p>". get_string('Crea un corso che non esiste nei database ufficiale dei corsi USI/SUPSI','local_requestmanager')."</p>";
$form_add->display();
echo "<hr>";
    if(count($_SESSION['courses_to_insert'])>0) {
        echo '<h4>' . get_string('Corsi selezionati', 'local_requestmanager') . '</h4>';
        echo "<ul>";
        foreach ($_SESSION['courses_to_insert'] as $data) {
            echo " <li> " . $data->title . "</li>";
        }
        echo "</ul>";
        echo '<br><a type="button" class="btn btn-primary btn-lg" href="resume.php">'.get_string('Avanti','local_requestmanager').'</a>';

    }







echo $OUTPUT->footer();
?>
