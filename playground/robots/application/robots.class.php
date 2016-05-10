<?php
// 2016 Dave Williams. All rights reserved.
// Use of this code without permission from the owner will result in us getting a bit shirty.
// Created By: Dave Williams | d4v3w

class robots {

    var $Id = 1;
    var $data = array();

    public function robots() {

    }

    function doTheRobot() {
        # echo date("l jS \of F Y h:i:s A");

        $item = 0;
        if (isset($_GET['io'])) {
            $item = $_GET['io'];
        }

        $pin1 = 17;
        $pin2 = 18;

        system("gpio -g mode $pin1 out");
        system("gpio -g mode $pin2 out");

        if ($item == 1) {
            system("gpio -g write $pin1 1");
            system("gpio -g write $pin2 0");
        } elseif ($item == 2) {
            system("gpio -g write $pin1 0");
            system("gpio -g write $pin2 1");
        } else {
            system("gpio -g write $pin1 0");
            system("gpio -g write $pin2 0");
        }

        system("gpio -g cleanup");
    }

}
