<?php
// 2016 Dave Williams. All rights reserved.
// Use of this code without permission from the owner will result in us getting a
// bit shirty.
// Created By: Dave Williams | d4v3w

class robots {

    var $gpioPins = array(
        'left' => array(
            'forward' => 27,
            'back' => 22
        ),
        'right' => array(
            'forward' => 17,
            'back' => 18
        )
    );
    var $command = null;
    var $data = 'test';
    var $error = array(
        'error' => 'Error',
        'message' => 'TODO',
    );
    var $isOK = true;

    public function robots($type = 'car') {
        if (isset($_GET['cmd']) && preg_match('/^(start|stop|left|right|forward|back)$/', $_GET['cmd'])) {
            $this->command = $_GET['cmd'];
        }
        $this->doTheRobot();
    }

    private function getCommand() {
        return $this->command;
    }

    private function getPins() {
        return $this->gpioPins;
    }


    private function getData() {
        return $this->data;
    }

    private function getError() {
        return $this->error;
    }

    private function isOK() {
        return $this->isOK;
    }

    private function doTheRobot() {
        if ($this->getCommand() === 'startup') {
            $this->initRobot();
        } else if ($this->getCommand() === 'stop') {
            $this->stop();
        } else if ($this->getCommand() === 'left') {
            $this->initDrive();
        } else if ($this->getCommand() === 'right') {
            $this->initDrive();
        } else if ($this->getCommand() === 'forward') {
            $this->initDrive();
        } else if ($this->getCommand() === 'back') {
            $this->initDrive();
        }
    }

    public function getResponse() {
        $response = array(
            'isOK' => $this->isOK(),
            'command' => $this->getCommand()
        );

        if ($this->isOK()) {
            $response['content'] = $this->getData();
        } else {
            $response['error'] = $this->getError();
        }

        header('Content-type: application/json');
        return json_encode($response, JSON_FORCE_OBJECT);
    }

    private function left() {
        $this->moveRobot('forward', 'left');
    }

    private function right() {
        $this->moveRobot('forward', 'right');
    }

    private function forward() {
        $this->moveRobot('forward');
    }

    private function back() {
        $this->moveRobot('back');
    }

    private function stop($cleanup = true) {
        $pins = $this->getPins();
        foreach ($pins as $side) {
            foreach ($side as $pin) {
                $this->runCommand($pin, false);
            }
        }
        if ($cleanup) {
            $this->cleanupCommand();
        }
    }

    private function initRobot() {
        $pins = $this->getPins();
        foreach ($pins as $side) {
            foreach ($side as $direction => $pin) {
                $this->startupCommand($pin);
            }
        }
    }

    private function moveRobot($direction, $side = null) {
        // Set all pins to off before running next command
        $this->stop();
        $pins = $this->getPins();

        if ($side) {
            // Loop over on  enabling turning
            foreach ($pins[$side] as $dir => $pin) {
                if ($dir == $direction) {
                    $this->runCommand($pin, true);
                }
            }
        } else {
            // Loop over all pins setting according to direction
            foreach ($pins as $side) {
                foreach ($side as $dir => $pin) {
                    if ($dir = $direction) {
                        $this->runCommand($pin, true);
                    }
                }
            }
        }
    }

    private function runCommand($pin, $enable = true) {
        # echo date("l jS \of F Y h:i:s A");
        if ($enable) {
            system("gpio -g write $pin 1");
        } else {
            system("gpio -g write $pin 0");
        }
    }

    private function startupCommand($pin) {
        system("gpio -g mode $pin out");
    }

    private function cleanupCommand() {
        system("gpio -g cleanup");
    }

}
