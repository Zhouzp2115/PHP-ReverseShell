reverse shell based on php

first, access the api.php
then, access the test.php in another tap and type your command in test.php.

test.php will send the command to api.php and recvice the result.

api.php will create a shell by using proc_open("bash -i", $descriptorspec, pipes),
read the command from pipes[0], and get the outputs by reading pipes[1].
