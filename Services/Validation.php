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

}