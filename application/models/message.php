<?php

	Class Message extends CI_Model {

		function getAllMessages($num = 10, $start = 0, $search_string = null) {

			$this->db->order_by('dateTime',"DESC");
			$this->db->where('type',"user");
			$this->db->select('*,translator.id AS translatorID')->from('ajax_chat_messages')->limit($num,$start);
			$this->db->join('translator', 'translator.id = ajax_chat_messages.trans_id');

			if ($search_string){
				$this->db->like('jobname',$search_string);
				$this->db->or_like('first_name',$search_string);
				$this->db->or_like('last_name',$search_string);
				$this->db->or_like('text',$search_string);	
			}
            $this->db->where('ajax_chat_messages.status', 'unread');

			$query = $this->db->get();

			return $query->result();

		}

		function getTotalMessages($search_string = null){

			$this->db->where('type',"user");
			$this->db->select()->from('ajax_chat_messages');

//			if ($search_string){
//				$this->db->like('jobname',$search_string);
//				$this->db->or_like('first_name',$search_string);
//				$this->db->or_like('last_name',$search_string);
//				$this->db->or_like('text',$search_string);
//			}

            $this->db->where('ajax_chat_messages.status', 'unread');
			$query = $this->db->get();
			return $query->num_rows();

		}

		function getAllTranslatorsInfo(){
			
			$this->db->order_by('id',"ASC");
			$this->db->select()->from('translator');
			$query = $this->db->get();

			return $query->result();
		}

	    function updateMessage($messageInfo) {
	    	$this->db->insert('notifications',$messageInfo);
	    	return $this->db->insert_id();
	    }

	}

?>