<?php


        system("gpio -g mode 17 out");
        system("gpio -g mode 27 out");
        system("gpio -g write 17 1");
        system("gpio -g write 27 1");
        
?>
