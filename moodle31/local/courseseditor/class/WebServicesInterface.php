<?php

/**
 * Created by PhpStorm.
 * User: Rezart Lohja
 * Date: 15.01.17
 * Time: 09:14
 */
interface WebServicesInterface{
    /** Ricerca corsi per netID
     * @param $netId
     * @return Array
     */
    function getCorsiPerNetID($netId);

    /** Ricerca corsi per la stringa passata
     * @param $string
     * @return Array
     */
    function getCorsiPerString($string);

    /** Ritorna tutti i corsi
     * @return array
     */
    function getCorsi();
}