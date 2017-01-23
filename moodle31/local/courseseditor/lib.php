<?php
/**
 * Created by PhpStorm.
 * User: Rezart Lohja
 * Date: 12.01.17
 * Time: 19:07
 */


function local_courseseditor_extend_navigation(global_navigation $navigation)
{
    if (!isloggedin()) {
        return;
    }

    $node = $navigation->add(get_string('pluginname', 'local_courseseditor'), null, navigation_node::TYPE_CONTAINER);;
    $node->add(get_string('new_courses', 'local_courseseditor'), new moodle_url('/local/courseseditor/new.php'));
    $node->add(get_string('clone_courses', 'local_courseseditor'), new moodle_url('/local/courseseditor/clone.php'));
    $node->add(get_string('delete_courses', 'local_courseseditor'), new moodle_url('/local/courseseditor/delete.php'));
    $node->add(get_string('manage_courses', 'local_courseseditor'), new moodle_url('/local/courseseditor/manage.php'));

}

function local_courseseditor_extend_settings_navigation($settingsnav, $context)
{

}