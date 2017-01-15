<?php

/**
 * Created by PhpStorm.
 * User: Rezart Lohja
 * Date: 15.01.17
 * Time: 11:07
 */
define("TIPO_RELAZIONE_DOCENTE", "Docente");
define("TIPO_RELAZIONE_ASSISTENTE", "Assistente");

class UserCorso
{
    var $id;
    var $id_lcl_courseseditor_corso;
    var $tipo_relazione;
    var $nome;
    var $cognome;
    var $id_mdl_user;

    /**
     * Salva su DB lo user del corso
     */
    public function saveToDB()
    {
        global $DB;
        $r = new stdClass();
        $r->id_lcl_courseseditor_corso=$this->id_lcl_courseseditor_corso;
        $r->tipo_relazione=$this->tipo_relazione;
        $r->nome=$this->nome;
        $r->cognome=$this->cognome;
        $r->id_mdl_user=$this->id_mdl_user;

        if($this->id>0){// update
            $r->id==$this->id;
            $DB->update_record('lcl_courseseditor_corso_user', $r, false);
        }else{// insert
            if ($this->id_lcl_courseseditor_corso > 0) {// inserisco solo se il corso è stato assegnto
               return $DB->insert_record('lcl_courseseditor_corso_user', $r, false);
            }else return false;
        }
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
    public function getIdLclCourseseditorCorso()
    {
        return $this->id_lcl_courseseditor_corso;
    }

    /**
     * @param mixed $id_lcl_courseseditor_corso
     */
    public function setIdLclCourseseditorCorso($id_lcl_courseseditor_corso)
    {
        $this->id_lcl_courseseditor_corso = $id_lcl_courseseditor_corso;
    }

    /**
     * @return mixed
     */
    public function getTipoRelazione()
    {
        return $this->tipo_relazione;
    }

    /**
     * @param mixed $tipo_relazione
     */
    public function setTipoRelazione($tipo_relazione)
    {
        $this->tipo_relazione = $tipo_relazione;
    }

    /**
     * @return mixed
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param mixed $nome
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    /**
     * @return mixed
     */
    public function getCognome()
    {
        return $this->cognome;
    }

    /**
     * @param mixed $cognome
     */
    public function setCognome($cognome)
    {
        $this->cognome = $cognome;
    }

    /**
     * @return mixed
     */
    public function getIdMdlUser()
    {
        return $this->id_mdl_user;
    }

    /**
     * @param mixed $id_mdl_user
     */
    public function setIdMdlUser($id_mdl_user)
    {
        $this->id_mdl_user = $id_mdl_user;
    }


}