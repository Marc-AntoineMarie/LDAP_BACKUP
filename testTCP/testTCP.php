<?php
	
	
	
	/*// create for tcp
	$sock = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));
	socket_bind($sock, '54.36.189.50',8001);
	socket_listen($sock);
	*/
    
    // create a streaming socket, of type TCP/IP
    $sock = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));
    
    // set the option to reuse the port
    socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1);
    
    // "bind" the socket to the address to "localhost", on port $port
    // so this means that all connections on this port are now our resposibility to send/recv data, disconnect, etc..
    socket_bind($sock, "54.36.189.50", 8001);
    
    // start listen for connections
    socket_listen($sock);
	
	$clients = array($sock);
    
    while (true) 
	{
        // create a copy, so $clients doesn't get modified by socket_select()
        $read = $clients;
		
		$write = NULL;
		$except = NULL;
        
        // get a list of all the clients that have data to be read from
        // if there are no clients with data, go to next iteration
		$test = socket_select($read, $write, $except, 0);
		
		//echo "Echec socket_select: [$errorcode] $errormsg";
		if ($test < 1) continue;
        
        // check if there is a client trying to connect
        if (in_array($sock, $read)) 
		{
            // accept the client, and add him to the $clients array
            $clients[] = $newsock = socket_accept($sock);
            
            // send the client a welcome message
            socket_write($newsock, "no noobs, but ill make an exception :)\n".
            "There are ".(count($clients) - 1)." client(s) connected to the server\n");
            
            socket_getpeername($newsock, $ip);
            echo "New client connected: {$ip}\n";
            
            // remove the listening socket from the clients-with-data array
            $key = array_search($sock, $read);
            unset($read[$key]);
        }
        
        // loop through all the clients that have data to read from
        foreach ($read as $read_sock) 
		{
            // read until newline or 1024 bytes
            // socket_read while show errors when the client is disconnected, so silence the error messages
            $data = @socket_read($read_sock, 1024, PHP_NORMAL_READ);
            
            // check if the client is disconnected
            if ($data === false) {
                // remove client for $clients array
                $key = array_search($read_sock, $clients);
                unset($clients[$key]);
                echo "client disconnected.\n";
                // continue to the next client to read from, if any
                continue;
            }
            
            // trim off the trailing/beginning white spaces
            $data = trim($data);
            
            // check if there is any data after trimming off the spaces
            if (!empty($data)) 
			{
            
                // send this to all the clients in the $clients array (except the first one, which is a listening socket)
                foreach ($clients as $send_sock) {
                
                    // if its the listening sock or the client that we got the message from, go to the next one in the list
                    if ($send_sock == $sock || $send_sock == $read_sock)
                        continue;
                    
                    // write the message to the client -- add a newline character to the end of the message
                    socket_write($send_sock, $data."\n");
                    
                } // end of broadcast foreach
                
            }
            
        } // end of reading foreach
		
    }
	
	echo "fin";

    // close the listening socket
    socket_close($sock);
	
	
	/*require_once("SocketServer.class.php"); // Include the File
	$server = new SocketServer("54.36.189.50",8001);//("54.36.189.50",8001); // Create a Server binding to the given ip address and listen to port 31337 for connections
	$server->max_clients = 10; // Allow no more than 10 people to connect at a time
	$server->hook("CONNECT","handle_connect"); // Run handle_connect every time someone connects
	$server->hook("INPUT","handle_input"); // Run handle_input whenever text is sent to the server
	$server->infinite_loop(); // Run Server Code Until Process is terminated.


	function handle_connect(&$server,&$client,$input) 
	{
		SocketServer::socket_write_smart($client->socket,"String? ","");
	}


	function handle_input(&$server,&$client,$input) 
	{
		// You probably want to sanitize your inputs here
		$trim = trim($input); // Trim the input, Remove Line Endings and Extra Whitespace.

		if(strtolower($trim) == "quit") // User Wants to quit the server
		{
			SocketServer::socket_write_smart($client->socket,"Oh... Goodbye..."); // Give the user a sad goodbye message, meany!
			$server->disconnect($client->server_clients_index); // Disconnect this client.
			return; // Ends the function
		}

		$output = strrev($trim); // Reverse the String

		SocketServer::socket_write_smart($client->socket,$output); // Send the Client back the String
		SocketServer::socket_write_smart($client->socket,"String? ",""); // Request Another String
	}*/
		
?>