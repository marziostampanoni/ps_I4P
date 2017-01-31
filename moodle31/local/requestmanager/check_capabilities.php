<?php

require_login();
if (!local_requestmanager\CEUtil::isTeacherOrAssistant($USER->id)) {
    echo '<div class="alert alert-warning">
                    ' . get_string('access_denied', 'local_requestmanager') . '
                </div>';
    exit;
}
