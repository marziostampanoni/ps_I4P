<?php
namespace local_requestmanager;

class Corso
{
    var $id;
    var $id_requestmanager_richiesta;
    var $id_mdl_course;
    var $titolo;
    var $shortname;
    var $id_mdl_course_categories;
    var $note;
    var $tipo_richiesta;
    var $stato_richiesta;
    /**
     * @var UserCorso[]
     */
    var $user_assegnati;

    function __construct($id=-1)
    {
        $this->id=$id;
    }

    /**
     * Load ogject Corso from database
     * @return bool
     */
    public function loadFromDB(){
        global $DB;
        if($this->id>0){
            $res = $DB->get_record('requestmanager_corso', array('id'=>$this->id));
            if($res){
                $arr_c = get_object_vars($res);
                foreach($arr_c as $param => $valore){
                    $this->$param=$valore;
                }

                // carico i corsi richiesti per questa richiesta da DB
                $users = $DB->get_records('requestmanager_corso_user', array('id_requestmanager_corso'=>$this->id));

                foreach ($users as $user){
                    $arr_c = get_object_vars($user);
                    $c = new UserCorso();
                    //creo oggetti UserCorso da oggetto stdClass
                    foreach($arr_c as $param => $valore){
                        $c->$param=$valore;
                    }
                    $this->addUser($c);
                }
                return true;
            }else return false;
        }else return false;
    }
    /**
     * Save on database this object Corso
     */
    public function saveToDB()
    {
        global $DB;
        $r = new \stdClass();
        $r->id_requestmanager_richiesta=$this->id_requestmanager_richiesta;
        $r->titolo=$this->titolo;
        $r->shortname=$this->shortname;
        $r->id_mdl_course_categories=$this->id_mdl_course_categories;
        $r->id_mdl_course=$this->id_mdl_course;

        $r->note=$this->note;
        $r->tipo_richiesta=$this->tipo_richiesta;

        if($this->id>0){// update
            $r->id=$this->id;
            $r->stato_richiesta=$this->stato_richiesta;
            if($DB->update_record('requestmanager_corso', $r, false)){
                foreach ($this->user_assegnati as $user) {
                    $user->saveToDB();
                }
            }
        }else{// insert

            // inserisco solo se almeno un user assegnato e c'� la relazione con la richiesta
            if ( $this->id_requestmanager_richiesta>0) {

                $r->stato_richiesta = STATO_RICHIESTA_DA_GESTIRE;
                $lastinsertid = $DB->insert_record('requestmanager_corso', $r, true);
                if ($lastinsertid && is_array($this->user_assegnati)) {// se inserimento andato bene allora inserisco i corsi
                    foreach ($this->user_assegnati as $user) {

                        $user->setIdLclCourseseditorCorso($lastinsertid);
                        $user->saveToDB();
                    }
                }
                return true;
            }else return false;
        }
    }
    /**
     * @param UserCorso $user
     */
    public function addUser(UserCorso $user){
        $this->user_assegnati[]=$user;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getIdLclCourseseditorRichiesta()
    {
        return $this->id_requestmanager_richiesta;
    }

    /**
     * @param mixed $id_requestmanager_richiesta
     */
    public function setIdLclCourseseditorRichiesta($id_requestmanager_richiesta)
    {
        $this->id_requestmanager_richiesta = $id_requestmanager_richiesta;
    }

    /**
     * @return mixed
     */
    public function getTitolo()
    {
        return $this->titolo;
    }

    /**
     * @param mixed $titolo
     */
    public function setTitolo($titolo)
    {
        $this->titolo = $titolo;
    }

    /**
     * @return mixed
     */
    public function getShortname()
    {
        return $this->shortname;
    }

    /**
     * @param mixed $shortname
     */
    public function setShortname($shortname)
    {
        $this->shortname = $shortname;
    }

    /**
     * @return mixed
     */
    public function getIdMdlCourseCategories()
    {
        return $this->id_mdl_course_categories;
    }

    /**
     * @param mixed $id_mdl_course_categories
     */
    public function setIdMdlCourseCategories($id_mdl_course_categories)
    {
        $this->id_mdl_course_categories = $id_mdl_course_categories;
    }

    /**
     * @return mixed
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param mixed $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return mixed
     */
    public function getTipoRichiesta()
    {
        return $this->tipo_richiesta;
    }

    /**
     * @param mixed $tipo_richiesta
     */
    public function setTipoRichiesta($tipo_richiesta)
    {
        $this->tipo_richiesta = $tipo_richiesta;
    }

    /**
     * @return mixed
     */
    public function getStatoRichiesta()
    {
        return $this->stato_richiesta;
    }

    /**
     * @param mixed $stato_richiesta
     */
    public function setStatoRichiesta($stato_richiesta)
    {
        $this->stato_richiesta = $stato_richiesta;
    }

    /**
     * @return mixed
     */
    public function getIdMdlCourse()
    {
        return $this->id_mdl_course;
    }

    /**
     * @param mixed $id_mdl_course
     */
    public function setIdMdlCourse($id_mdl_course)
    {
        $this->id_mdl_course = $id_mdl_course;
    }
}