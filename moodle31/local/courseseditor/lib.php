<?php
/**
 * Created by PhpStorm.
 * User: Rezart Lohja
 * Date: 12.01.17
 * Time: 19:07
 */


function local_courseseditor_extend_navigation(global_navigation $navigation) {
global $PAGE;
    var_dump($PAGE->url);

    $node = $navigation->add(get_string('pluginname','local_courseseditor'), new moodle_url('/local/courseseditor/start.php'), navigation_node::TYPE_CONTAINER);;

    $node1 = $node->add(get_string('new_courses','local_courseseditor'), new moodle_url('/local/courseseditor/nuovo.php'));
    //if($PAGE){
        $node1->make_active();
    //}
    $node2 = $node->add(get_string('clone_courses','local_courseseditor'), new moodle_url('/local/courseseditor/clona.php'));
    //$node2->make_active();


    $node3 = $node->add(get_string('delete_courses','local_courseseditor'), new moodle_url('/local/courseseditor/cancella.php'));
    //$node3->make_active();
}

function local_courseseditor_extend_settings_navigation($settingsnav, $context) {

}