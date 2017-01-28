<div id="enrollModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?php echo(get_string('manage_enroll_user', 'local_requestmanager')); ?></h4>
<small id="coursetitle"></small>
<input type="hidden" id="modal_detail_course" value="">
<input type="hidden" id="modal_detail_role" value="">
</div>
<div class="modal-body" id="enroll-modal-body">
    <table class="generaltable table-bordered">
        <tbody id="enrolled"></tbody>
    </table>
    <hr>
    <table class="generaltable table-bordered">
        <tbody id="notenrolled">
        <?php
        $query = 'select u.id as id, firstname, lastname, picture, imagealt, email from mdl_role_assignments as a, mdl_user as u where roleid in (3,4) and a.userid=u.id;';
        $res = $DB->get_records_sql($query, array());
        foreach ($res as $id => $user) {
            echo('<tr id="user_' . $id . '">');
            echo('<input type="hidden" id="flag_' . $user->id . '" value="0">');
            echo('<input type="hidden" class="althere" id="prev_' . $user->id . '" value="0" data-id="' . $user->id . '">');
            echo('<td style="width: 80%;">');
            echo('<strong>' . $user->lastname . ' ' . $user->firstname . '</strong>');
            echo('<br><small>' . $user->email . '</small>');
            echo('</td>');
            echo('<td style="text-align: center; vertical-align: middle;"><button value="' . $user->id . '_' . $user->lastname . ' ' . $user->firstname . '" class="btn button-choice" id="btn_' . $user->id . '" onclick="swap(\'' . $id . '\')" style="width:80px;">Enroll</button></td>');
            echo('</tr>');
        }
        ?>
        </tbody>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal" onclick="updateField();"><?php echo(get_string('delete_modal_confirm', 'local_requestmanager')); ?></button>
</div>
</div>
</div>
</div>