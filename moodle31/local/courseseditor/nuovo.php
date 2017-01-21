<?php
require_once('../../config.php');
require_once('form/nuovo.php');
require_once('form/cercacorsiws.php');
require_once('form/selectcorsi.php');
require_once('class/SupsiWebServices.php');
require_once('class/UsiWebServices.php');
require_once('class/Richiesta.php');
require_once('class/Corso.php');


$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_courseseditor'));
$PAGE->set_heading(get_string('pluginname', 'local_courseseditor'));
$PAGE->requires->js( new moodle_url($CFG->wwwroot . '/local/courseseditor/js/main.js'));

require_login();

echo $OUTPUT->header();
echo('<h2 style="">'.get_string('Creazione nuovi corsi', 'local_courseseditor').'</h2>');

echo "<hr>";

if($_GET['new']=='true')unset($_SESSION['courses_to_insert']);
if(!$_SESSION['courses_to_insert']) {
    if ($_GET['user_type'] == 'usi') $ws = new UsiWebServices();
    else $ws = new SupsiWebServices();

    $filter_cerca = null;
    $filter_netID = null;
    $form_cerca = new FormCercaCorsiWs();


    if ($fromform = $form_cerca->get_data()) {
        if ($fromform->string != '') $filter_cerca = $fromform->string;
        if ($fromform->onlythis == 1) $filter_netID = $USER->username;
        if ($fromform->submitsupsi) $ws = new SupsiWebServices();
        if ($fromform->submitusi) $ws = new UsiWebServices();
    }

    $result = $ws->getCorsi($filter_netID, $filter_cerca);

    $form_select_corsi = new FormSelectCorsi(null, array('corsi' => $result));

    if ($fromform = $form_select_corsi->get_data()) {

        $selezionati = array();
        foreach ($fromform as $name => $item) {
            if (substr($name, 0, 4) == 'name') {
                $selezionati[] = substr($name, 5);
            }
        }
        if (count($selezionati) > 0) {
            foreach ($selezionati as $num) {
                $param = "data-$num";
                $data = json_decode($fromform->$param);
                $_SESSION['courses_to_insert'][] = $data;
            }
        }

        if(count($_SESSION['courses_to_insert'])>0) {
            echo '<h4>' . get_string('Corsi selezionati', 'local_courseseditor') . '</h4>';
            echo "<ul>";
            foreach ($_SESSION['courses_to_insert'] as $data) {
                echo " <li> " . $data->title . "</li>";
            }
            echo "</ul>";
        }
        echo '<br><a type="button" class="btn btn-primary btn-lg" href="resume.php">'.get_string('Avanti','local_courseseditor').'</a>';
        echo "<hr>";
        $form_add = new FormNuovo();
        echo '<h4>'. get_string('Aggiungi corso','local_courseseditor').'</h4>';
        echo "<p>". get_string('Crea un corso che non esiste nei database ufficiale dei corsi USI/SUPSI','local_courseseditor')."</p>";
        $form_add->display();


    } else {

        $form_cerca->display();

        echo '<h4>' . get_string('Seleziona corsi', 'local_courseseditor') . '</h4>';

        $form_select_corsi->display();
    }
}else{

    $form_add = new FormNuovo();

    if ($fromform = $form_add->get_data()) {


        $data = new stdClass();
        $data->idnull;
        $data->title =  $fromform->titolo . ", " . $fromform->codice;
        $data->cat = $fromform->categoria;
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
    }

    echo '<br><a type="button" class="btn btn-primary btn-lg" href="resume.php">'.get_string('Avanti','local_courseseditor').'</a>';
    echo "<hr>";
    $form_add->set_data(array('titolo'=>null,'codice'=>null,'categoria'=>null));
    echo '<h4>' . get_string('Aggiungi corso', 'local_courseseditor') . '</h4>';
    echo "<p>". get_string('Crea un corso che non esiste nei database ufficiale dei corsi USI/SUPSI','local_courseseditor')."</p>";
    $form_add->display();

}


echo $OUTPUT->footer();
?>
