var instanse = false;
var state;
var file;
function Chat () {
    this.update = updateChat;
    this.send = sendChat;
	this.getState = getStateOfChat;
}

//gets the state of the chat
function getStateOfChat(job_id, bid_id, trns_id, type){
	//alert("getStateOfChat");
	if(!instanse){
		 instanse = true;
		 $.ajax({
			   type: "POST",
			   url: "process.php",
			   data: {  
			   			'function': 'getState',
						'job_id': job_id,
						'bid_id': bid_id,
						'trns_id':  trns_id,
						'type': type,
						'file': file
						},
			   dataType: "json",
			   
			
			   success: function(data){
				   state = data.state;
				   instanse = false;
				   updateChat(job_id, bid_id, trns_id, type);

			   },
			});
	}	 
}

//Updates the chat
function updateChat(job_id, bid_id, trns_id, type){
	//alert("updateChat");
	//alert(job_id); alert(bid_id); alert(trns_id); alert(type);
	 if(!instanse){
		 instanse = true;
	     $.ajax({
			   type: "POST",
			   url: "process.php",
			   data: {  
			   			'function': 'update',
						'state': state,
						'job_id': job_id,
						'bid_id': bid_id,
						'trns_id':  trns_id,
						'type': type,
						'file': file
						},
			   dataType: "json",
			   success: function(data){
				   if(data.text){
					   //alert(data.testdata);
						for (var i = 0; i < data.text.length; i++) {
                            $('#chat-area').append($("<p>"+ data.text[i] +"</p>"));
                        }
					 document.getElementById('chat-area').scrollTop = document.getElementById('chat-area').scrollHeight;							  
				   }
				  
				   instanse = false;
				   state = data.state;
			   },
			});
	 }
	 else {
		 //alert('else');
		 //setTimeout(updateChat, 1500);
		 setTimeout(function(){updateChat(job_id, bid_id ,trns_id, type);}, 1500);
	 }
}

//send the message
function sendChat(message, nickname, job_id, bid_id ,trns_id, type)
{   
	//alert("sendChat");
	//alert(job_id); alert(bid_id); alert(trns_id);
    updateChat(job_id, bid_id, trns_id, type);
     $.ajax({
		   type: "POST",
		   url: "process.php",
		   data: {  
		   			'function': 'send',
					'message': message,
					'nickname': nickname,
					'job_id': job_id,
					'bid_id': bid_id,
					'trns_id': trns_id,
					'type': type,
					'file': file
				 },
		   dataType: "json",
		   success: function(data){
				
				//alert(file);
				$.ajax({
					type: "POST",
					url: "mail.php",
					data: {  
						
						'job_id': job_id,
						'bid_id': bid_id,
						'trns_id':  trns_id,
						'type': type,
						'file': file
					},
					dataType: "html",
					success: function(data1){
						//alert(data1);
					}
				});
					
			   updateChat(job_id, bid_id, trns_id, type);
		   },
		});
}
