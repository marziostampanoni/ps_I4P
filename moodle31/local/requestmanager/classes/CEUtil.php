<?php
namespace local_requestmanager;
/**
 * Class that contains the utilities
 */
class CEUtil
{
    static function tipoRichiesta($integer){
        switch($integer){
            case 1:return 'New course';
            case 2:return 'Clone';
            case 3:return 'Delete';
        }
    }

    static function statoRichiesta($integer){
        switch($integer){
            case 1:return 'Done';
            case 2:return 'Suspended';
            case 3:return 'To do';
        }
    }

    static function getStyleTipoRichiesta($integer){
        switch($integer){
            case 1:return 'success';
            case 2:return 'info';
            case 3:return 'warning';
        }
    }

    static function tipoUtente($integer){
        switch($integer){
            case 1:return 'Teacher';
            case 2:return 'Assitent';
        }
    }

    static function getParsedDataFromForm($data){
        $parsed_data=array();
        foreach ($data as $name => $post) {
            $prefix = substr($name, 0, 4);
            if ($prefix == 'name') {
                $id = substr($name, strpos($name, "-") + 1);
                $datasel = 'data-' . $id;
                $parsed_data[] = json_decode($data[$datasel]);
            }
        }
        return $parsed_data;
    }

    static function getParsedDataFromFormResume($data){
        $parsed_data=array();
        foreach ($data as $name => $post) {
            if(strpos($name, "-")>0) {
                $id = substr($name, strpos($name, "-") + 1);
                $campo = substr($name, 0, strpos($name, "-"));
                if($campo=='teachers' || $campo=='editingteachers'){
                    if(is_array($post)){
                        foreach ($post as $t)$parsed_data['corsi'][$id][$campo][]=$t;
                    }else{$parsed_data['corsi'][$id][$campo]=array($post);}
                }else $parsed_data['corsi'][$id][$campo] = $post;
            }else{
                $parsed_data[$name]=$post;
            }
        }
        return $parsed_data;
    }

    /**
     * Send the notification by mail to the manager of the category
     * @param Integer $id_category
     * @return boolean If the mail is sent
     */
    static function mailNotificationToManager($id_category)
    {
        global $USER;
        // MAIL_TO  if there is a mail to use in the config I use it els I search the managers of the category
        if (get_config('local_requestmanager', 'to_mail') != 'CAT_MANAGER'){
            $to = get_config('local_requestmanager', 'to_mail');
        }else{
            $users_to_notify = CEUtil::getCategoryManager($id_category);
            $to = array();
            foreach ($users_to_notify as $user){
                $to[] = $user->email;
            }
            $to = implode(',',$to);
        }

        //MAIL FROM
        if(get_config('local_requestmanager','from_mail')!='CURRENT_USER'){
            $from=get_config('local_requestmanager','from_mail');
        }else{
            $from = " '{$USER->firstname} {$USER->lastname}' <{$USER->email}>";

        }

        //MAIL_SUBJECT
        $subject = get_config('local_requestmanager','subject_mail');
        //MAIL_MESSAGE
        $message = get_config('local_requestmanager','message_mail');
        // HEADERS
        $headers = "From: $from \r\n";

        return mail($to, $subject, $message, $headers);
    }

    /**
     * Return the manager responsable of the category, if there is not a manager returns the admin.
     * @param $id_category
     * @return stdClass[] user
     */
    static function getCategoryManager($id_category){
        global $DB;
        $context = \context_coursecat::instance($id_category);
        $query = "SELECT firstname, lastname, email
                  FROM mdl_role_assignments as a 
                    LEFT JOIN mdl_user as u ON (a.userid=u.id)
                    LEFT JOIN mdl_role as r ON (a.roleid=r.id AND r.shortname='manager')
                  WHERE contextid = {$context->id};";

        $rs = $DB->get_recordset_sql( $query );

        if($rs->valid()) {
            $arr=array();
            foreach ($rs as $r) {
                $arr[] = $r;
            }
            return $arr;
        }else{
            $cat = $DB->get_record('course_categories',array('id'=>$id_category));
            if($cat && $cat->parent>0){
                return CEUtil::getCategoryManager($cat->parent);
            }else{
                $admins = get_admins();
                $arr=array();
                foreach ($admins as $r) {
                    $arr[] = $r;
                }
                return $arr;
            }
        }
    }

    /**
     * Return true if the user with id passed is a manager of any context
     * @param $id
     * @return bool
     */
    static function isManager($id){
        global $DB;

        $query = "SELECT *
                  FROM mdl_role_assignments as a 
                    INNER JOIN mdl_role as r ON (a.roleid=r.id AND r.shortname='manager')
                  WHERE userid = {$id};";

        $rs = $DB->get_recordset_sql( $query );

        if($rs->valid()) {
            return true;
        }
        return false;
    }



}