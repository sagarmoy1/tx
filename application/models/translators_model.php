<?php
class Translators_model extends CI_Model {

    /**
    * Validate the login's data with the database
    * @param string $user_name
    * @param string $password
    * @return void
    */
	function validate($user_name, $password)
	{
		$this->db->where('user_name', $user_name);
		$this->db->where('pass_word', $password);
		$query = $this->db->get('translator');

		if($query->num_rows == 1)
		{
			return true;
		}
	}

    /**
    * Serialize the session data stored in the database,
    * store it in a new array and return it to the controller
    * @return array
    */
	function get_db_session_data()
	{
		$query = $this->db->select('user_data')->get('ci_sessions');
		$artist = array(); /* array to store the user data we fetch */

		foreach ($query->result() as $row)
		{
		    $udata = unserialize($row->user_data);
		    /* put data in array using username as key */
		    $artist['user_name'] = $udata['user_name'];
		    $artist['is_logged_in'] = $udata['is_logged_in'];
		}
		return $artist;
	}

	function getAllLanguages() {
		$this->db->select()->from('languages')->order_by('name',"ASC");
		$query = $this->db->get();

		return $query->result();
	}

/* Dashboard */

	function fetchMyBids($num = 10, $start = 0, $translatorID,$search_string=null,$jobstatus=null,$dueFrom = null, $dueTo = null){

        // $this->db->order_by('bidjob.id',"DESC");
		$this->db->where('trans_id',$translatorID);
		$this->db->where('awarded',1);

        if ($dueFrom){
            $dateToConvert = strtotime($dueFrom);
            $dateFromNew = date( 'Y-m-d H:i:s', $dateToConvert );
            $dateToConvert = strtotime($dueTo);
            $dateToNew = date( 'Y-m-d H:i:s', $dateToConvert );

            $this->db->where('date_add(complete_date, INTERVAL 31 DAY) >=', $dateFromNew);
            $this->db->where('date_add(complete_date, INTERVAL 31 DAY) <=', $dateToNew);
        }else{

			if($search_string){
				$this->db->like('name', $search_string,'both');
			}else{
				if($jobstatus == 1){
					$this->db->where('bidjob.stage', $jobstatus);
				} else if ($jobstatus == 2) {
					$this->db->where('bidjob.stage', $jobstatus);
				} else {

				}
			}
		}

		$this->db->select('*, bidjob.stage AS bidjobstage, jobpost.stage AS jobpoststage, bidjob.price AS bidjobprice, jobpost.price AS jobpostprice, bidjob.id AS bidjobid, jobpost.id AS jobpostid, jobpost.stage as jobpoststage,bidjob.stage as bidjobstage')->from('bidjob')->limit($num,$start);
		$this->db->join('jobpost','jobpost.id = bidjob.job_id');
        $this->db->order_by('bidjob.award_date',"DESC");
		$query = $this->db->get();

//        echo $this->db->last_query(); die;
		return $query->result();
	}

	function fetchMyTotalBids($translatorID,$search_string=null,$jobstatus=null,$dueFrom = null, $dueTo = null){

		$this->db->where('trans_id',$translatorID);
		$this->db->where('awarded',1);

        if ($dueFrom){
            $dateToConvert = strtotime($dueFrom);
            $dateFromNew = date( 'Y-m-d H:i:s', $dateToConvert );
            $dateToConvert = strtotime($dueTo);
            $dateToNew = date( 'Y-m-d H:i:s', $dateToConvert );

            $this->db->where('date_add(complete_date, INTERVAL 31 DAY) >=', $dateFromNew);
            $this->db->where('date_add(complete_date, INTERVAL 31 DAY) <=', $dateToNew);
        }else{

			if($search_string){
				$this->db->like('name', $search_string,'both');
			}else{
				if($jobstatus == 1){
					$this->db->where('bidjob.stage', $jobstatus);
				} else if ($jobstatus == 2) {
					$this->db->where('bidjob.stage', $jobstatus);
				} else {

				}
			}
		}

		$this->db->select('*, bidjob.stage AS bidjobstage, jobpost.stage AS jobpoststage, bidjob.price AS bidjobprice, jobpost.price AS jobpostprice, bidjob.id AS bidjobid, jobpost.id AS jobpostid, jobpost.stage as jobpoststage,bidjob.stage as bidjobstage')->from('bidjob');
		$this->db->join('jobpost','jobpost.id = bidjob.job_id');
		$query = $this->db->get();

		return $query->result();
	}
/* Dashboard */

/* Notifications */

	function fetchMyNotifications($num = 10, $start = 0, $translatorID) {
		$this->db->order_by('created', "DESC");
		$this->db->where('translatorID', $translatorID);
		$this->db->select()->from('notifications')->limit($num,$start);
		$query = $this->db->get();

		return $query->result();
	}

	function fetchMyTotalNotifications($translatorID) {

		$this->db->where('translatorID', $translatorID);
		$this->db->select()->from('notifications');
		$query = $this->db->get();

		return $query->result();
	}

	function fetchUnReadMessage($translatorID){

		$this->db->where('trans_id',$translatorID);
		$this->db->where('type',"admin");
		$this->db->where('status',"unread");
		$this->db->select()->from('ajax_chat_messages');
		$query = $this->db->get();

		return $query->num_rows();
	}

	function getCountUnreadNotification($translatorID){

		$this->db->where('translatorID', $translatorID);
		$this->db->where('isRead',0);
		$this->db->select()->from('notifications');
		$query = $this->db->get();

		return $query->num_rows();

	}

    public function get_review_job_awarded($job_id, $translator_id)
    {
        $sql  = "SELECT * ";
        $sql .= "FROM bidjob b1";
        $sql .= " JOIN bidjob_details b2 ON b2.bidjob_id = b1.id";
        $sql .= " JOIN jobpost j ON j.id = b1.job_id";
        $sql .= " JOIN proofread_jobs p1 ON p1.job_id = j.id";
        $sql .= " JOIN proofread_jobs_docs p2 ON p2.proofread_job_id = p1.id ";
        $sql .= "WHERE ";
        $sql .= " b1.awarded = 1 AND ";
        $sql .= " b1.stage = 1 AND ";
        $sql .= " b1.trans_id = ? AND ";
        $sql .= " p2.is_awarded = 1 AND ";
        $sql .= " j.id = ?";

        $query = $this->db->query($sql, array($translator_id, $job_id));

        return $query->result_array();
    }

    function Update_data1($id,$data1)
    {
    	//echo 'update1'; die;
        $this->db->where('id',$id);
        $this->db->update('translator',$data1);
    }
    function Update_data2($id,$data2)
    {
       // echo 'update2'; die;
        $this->db->where('id',$id);
        $this->db->update('translator',$data2);
    }

    public function update_data($data,$condition=''){
    	if($condition!=''){
    		$this->db->where($condition);
    		$updated=$this->db->update('translator',$data);
    		if($updated){
    			return true;
    		} else{
    			return false;
    		}
    	}
    }

    public function check_data($condition=''){
    	if($condition!=''){
    		$this->db->select('*');
    		$this->db->where($condition);
    		$query=$this->db->get('translator');
    		return $query->result();
    	}
    }

    public function copy_table($condition=''){
    	if($condition!=''){
    		
    		$query='INSERT INTO `translator_archive` SELECT * FROM `translator` WHERE `id`=' . (int)$condition;
    		if($this->db->query($query)){
    			return true;
    		} else{
    			return false;
    		}
    	}
    }

}
?>
