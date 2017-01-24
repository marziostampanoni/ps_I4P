<?php
/**
 * Created by PhpStorm.
 * User: Rezart Lohja
 * Date: 12.01.17
 * Time: 19:07
 */


function local_requestmanager_extend_navigation(global_navigation $navigation)
{
    if (!isloggedin()) {
        return;
    }

    $node = $navigation->add(get_string('pluginname', 'local_requestmanager'), null, navigation_node::TYPE_CONTAINER);;
    $node->add(get_string('new_courses', 'local_requestmanager'), new moodle_url('/local/requestmanager/new.php'));
    $node->add(get_string('clone_courses', 'local_requestmanager'), new moodle_url('/local/requestmanager/clone.php'));
    $node->add(get_string('delete_courses', 'local_requestmanager'), new moodle_url('/local/requestmanager/delete.php'));
    $node->add(get_string('manage_courses', 'local_requestmanager'), new moodle_url('/local/requestmanager/manage.php'));

}

function local_requestmanager_extend_settings_navigation($settingsnav, $context)
{

}