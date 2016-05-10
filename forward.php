<?php
        system("gpio -g mode 17 out");
        system("gpio -g mode 18 out");
        system("gpio -g write 17 1");
        system("gpio -g write 18 1");
?>
