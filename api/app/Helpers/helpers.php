<?php

if (! function_exists('exceeding_rate')) {

    function exceeding_rate($size) {
        /*
        - 20/hour for vehicles parked in SP;
        - 60/hour for vehicles parked in MP; and
        - 100/hour for vehicles parked in LP
         * */

         switch ($size){
             case 0:
                 return 20;
             case 1:
                 return 60;
             case 2:
                 return 100;
             default:
                 return 0;
         }
    }
}

if (! function_exists('fixed_hour')) {

    function fixed_hour() {
        return 3;
    }
}

if (! function_exists('flat_rate')) {

    function flat_rate() {
        return 40;
    }
}

if (! function_exists('whole_day_rate')) {

    function whole_day_rate() {
        return 5000;
    }
}
