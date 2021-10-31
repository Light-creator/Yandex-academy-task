<?php
namespace Services;

use \DateTime;

class Validation {

    public static function date_valid($date) {
        $date = explode('.', $date);

        if(checkdate((int) $date[1], (int) $date[0], (int) $date[2])) {
            return true;
        }
    }

    public static function relatives_valid($citizens) {
        foreach($citizens as $citizen) {
            foreach($citizens as $citizen2) {
                if($citizen['citizen_id'] != $citizen2['citizen_id'] && in_array($citizen2['citizen_id'], $citizen['relatives'])) {
                    if(!in_array($citizen['citizen_id'], $citizen2['relatives'])) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

}