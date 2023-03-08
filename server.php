<?php
   $host = "127.0.0.1";  //ip address
   $port = 4950;        //port number
   set_time_limit(0);  //lets you set how long a script should be allowed to execute

   $sock = socket_create(AF_INET, SOCK_STREAM, 0) or die ("Could not create socket. \n");
   $result = socket_bind($sock, $host, $port) or die ("Could not bind to socket. \n");

   $result = socket_listen($sock, 3) or die ("Could not set up socket listener. \n");
   echo "Listening for connections \n";

   class Chat
   {
	   function readLine()
	   {
		   return rtrim(fgets(STDIN)); //will remove white space from the right, and return a line from an open file
	   }
	    
   }   
   
   do
   {
	   $accept = socket_accept($sock) or die ("Could not accept incoming connection.");
	   $msg = socket_read($accept, 1024) or die ("Could not read input. \n"); //contain 1 byte of the input
	   
	   $msg = trim($msg);
	   echo "Client says: \t". $msg. "\n";
	   
	   $line = new Chat();
	   echo "Enter reply: \t";
	   $reply = $line->readLine();
	   echo "\n\n";
	   socket_write($accept, $reply, strlen($reply)) or die ("Could not write output. \n");
   } while (true);
   
   socket_close($accept, $sock);

?>   