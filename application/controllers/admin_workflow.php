<?php



error_reporting(1);


 
/**

* 

*/

class Admin_Workflow extends CI_Controller

{

    

    public function __construct()

    {

        parent::__construct();
        $this->load->helper(array('form', 'url', 'path'));
        $this->load->model('workflow_model');

    }

    public function index(){
        if(!$this->session->userdata('is_admin')){
            redirect('admin/login');
        } else{
            $filter_session_data="";
            $sort_type = $this->session->userdata('sort_type');
            //all the posts sent by the view  
            $years=$this->input->post('years',true);   
            $months=$this->input->post('months',true); 

            $search_string =(!empty($years))?trim($years.'-'.$months):'';
            $search_string2=$this->input->post('search_by',true);
            $order = $this->session->userdata('order'); 
            $order_type = $this->session->userdata('order_type'); 

            $per_page=$this->input->post('per_page');
            $per_page=!empty($per_page)?$per_page: $this->session->userdata('per_page_selected');
            //echo 'sdfdsfdsfsdf '.$per_page;
            //pagination settings
            $config['per_page'] =!isset($per_page)?$per_page:100;
            $config['base_url'] = base_url().'admin/workflow';
            $config['use_page_numbers'] = TRUE;
            $config['num_links'] = 20;
            $config['full_tag_open'] = '<ul>';
            $config['full_tag_close'] = '</ul>';
            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';
            $config['cur_tag_open'] = '<li class="active"><a>';
            $config['cur_tag_close'] = '</a></li>';

            //limit end
                $page = $this->uri->segment(3);

                //math to get the initial record to be select in the database
                $limit_end = ($page * $config['per_page']) - $config['per_page'];
                if ($limit_end < 0){
                    $limit_end = 0;
                } 

                $data['limit_end']=$limit_end;
                $filter_session_data['limit_end'] = $limit_end;

                //if order type was changed
                if($order_type){
                    $filter_session_data['order_type'] = $order_type;
                }
                else{
                    //we have something stored in the session? 
                    if($this->session->userdata('order_type')){
                        $order_type = $this->session->userdata('order_type');    
                    }else{
                        //if we have nothing inside session, so it's the default "Asc"
                        $order_type = 'ASC';    
                    }
                }

            //make the data type var avaible to our view
            $data['order_type_selected'] = $order_type; 

            if($search_string!='' || $search_string !== false || $search_string2 !== false || $this->uri->segment(3) == true){
                    if($order){
                        $filter_session_data['order'] = $order;
                    }
                    else{
                        $order =$this->session->userdata('order');
                    }
                    $data['order'] = $order;
                    if($search_string){
                        $filter_session_data['search_string_selected'] = $search_string;
                    }else{
                        $search_string = $this->session->userdata('search_string_selected');
                    }
                    $data['search_string_selected'] = $search_string;
                    if($search_string2){
                        $filter_session_data['search_string_selected2'] = $search_string2;
                    }else{
                        $search_string2 = $this->session->userdata('search_string_selected2');
                    }
                    $data['search_string_selected2'] = $search_string2;

                if($per_page){
                        $filter_session_data['per_page_selected'] = $per_page;
                    }else{
                        $per_page = $this->session->userdata('per_page_selected');
                    }
                    $data['per_page_selected'] = $per_page;

                    //save session data into the session
                    $this->session->set_userdata($filter_session_data);

                    $data['count_jobs']= $this->workflow_model->count_jobs($search_string, $search_string2, $order);
                    $config['total_rows'] = $data['count_jobs'];

                    //fetch sql data into arrays
                    if($search_string){
                        if($order){
                            $data['jobs'] = $this->workflow_model->get_jobposts($search_string, $search_string2, $order, $order_type, $per_page,$limit_end, $sort_type);
                        }else{
                            $data['jobs'] = $this->workflow_model->get_jobposts($search_string, $search_string2, '', $order_type, $per_page,$limit_end, $sort_type);
                        }
                    }else{
                        if($order){
                            $data['jobs'] = $this->workflow_model->get_jobposts('', '', $order, $order_type, 100,'', $sort_type);
                        }else{
                            $data['jobs'] = $this->workflow_model->get_jobposts('', '', '', $order_type, 100,'', $sort_type);
                        }
                    }
             } else {
                //clean filter data inside section;
                $filter_session_data['search_string_selected'] = null;
                $filter_session_data['search_string_selected2'] = null;
                $filter_session_data['per_page_selected'] = null;
                $filter_session_data['order'] = null;
                $filter_session_data['order_type'] = null;
                $this->session->set_userdata($filter_session_data);

                //pre selected options
                $data['search_string_selected'] = '';
                $data['search_string_selected2'] = '';
                $data['order'] = 'id';

                //fetch sql data into arrays
                $data['count_jobs']= $this->workflow_model->count_jobs(trim($cyear.'-'.$cmonth));
                $cyear=date('Y');
                $cmonth=date('m');
                $data['jobs'] = $this->workflow_model->get_jobposts(trim($cyear.'-'.$cmonth), '', $order_type,100,'', $sort_type);
                $config['total_rows'] = $data['count_jobs'];
             }

             //initializate the panination helper
            $this->pagination->initialize($config);
            $admin_id = $this->session->userdata('admin_id');
            if($admin_id){
                $data['admin_type'] = $this->db->select('admin_type')->get_where('admin',['id' =>$admin_id])->first_row()->admin_type;
            }else{
                $data['admin_type'] = '';
            }

            $data['months']=array(''=>'Select Month','01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December');
                           
            $data['years'] = array_combine(range(date('Y')-10,2050), range(date('Y')-10,2050));
            $data['per_page_data']=array('10'=>'10','50'=>'50','100'=>'100','200'=>'200','500'=>'500','1000'=>'1000','all'=>'Show All');


            $this->load->view('admin/vwAdminWorkflow',$data); 
        }
    }


   
    function reset()
    {
        //echo 'test';die;

        $this->session->unset_userdata('search_string_selected');
        $this->session->unset_userdata('search_string_selected2');
        $this->session->set_userdata('per_page_selected',100);
        redirect(base_url().'admin/workflow');
    }

    public function sort()
    {

        if ($this->input->server('REQUEST_METHOD') == 'POST' && $this->input->is_ajax_request() == true) {
           // echo "Hi";die;
            $this->session->set_userdata('sort_type', $this->input->post('sort_type'));
            echo "success";

        }
    }


//11-05-17//14-05-17
    public function add_stage(){
        $column_id=$this->input->post('column_id');
        $total_columns=$this->workflow_model->get_count_distinct_column();
        $max_sort_order=$this->workflow_model->get_max_sort_order();
        $max_column_no=$this->workflow_model->get_stage_column_no();
        //$return['m']=$max_column_no;
       // $stage_column=($total_columns!='0')?getNameFromNumber(($max_column_no+$total_columns),1):getNameFromNumber(8,1);
        //17-05-17
        $stage_column=($total_columns!='0')?getNameFromNumber(($max_column_no+1),1):getNameFromNumber(8,1);
        $stage_order=($max_sort_order!=null)?($max_sort_order+1):1;
        //$stage_column_no=(($max_column_no+$total_columns)==0)?8:($max_column_no+$total_columns);
        //17-05-17
         $stage_column_no=(($max_column_no+1)==0)?1:($max_column_no+1);

         $stage_priority=$this->input->post('stage_priority');

        if(empty($column_id)){
               $data=array(
                'stage_column'=>$stage_column,
                'stage_column_no'=>$stage_column_no,
                'stage_color'=>$this->input->post('stage_color'),
                'stage_text_color'=>$this->input->post('stage_text_color'),
                'stage_name'=>$this->input->post('stage_name'),
                'stage_order'=>$stage_order,
                'stage_priority'=>$stage_priority
                );

            $insert=$this->workflow_model->add_stage_data($data);
            if($insert){
                $return['success']='Stage added';
            } else{
                $return['error']='Stage not added';
            }
        }else{
            $reorderd=$this->input->post('reorderd');
            $insert=$this->workflow_model->update_stage_data(['stage_name'=>$this->input->post('stage_name'),'stage_color'=>$this->input->post('stage_color'),'stage_text_color'=>$this->input->post('stage_text_color'),'change_stage_color'=>$this->input->post('stage_text_color3')],['wid'=>$column_id]);
            $get_stage=$this->workflow_model->get_stage(['wid'=>$column_id]);
           $this->workflow_model->update_stage_data(['stage_name'=>$this->input->post('stage_name'),'stage_color'=>$this->input->post('stage_color'),'stage_text_color'=>$this->input->post('stage_text_color'),'change_stage_color'=>$this->input->post('stage_text_color3')],['stage_column'=>$get_stage[0]->stage_column]);
           if($insert){
                $return['success']='Stage updated';
            } else{
                $return['error']='Stage not updated';
            }

           /*$return['reorderd']=$reorderd;

           if(!empty($reorderd)){
                
                $id_ary = explode(",",$reorderd);
                  $count=1;             
                foreach($id_ary as $i) { 
                    $get_stage=$this->workflow_model->get_stage(['wid'=>$i]);

                    $this->workflow_model->update_stage_data(['stage_order'=>$count],['stage_column'=>$get_stage[0]->stage_column]);
                   $order=array($i);
                   $count++;
                }

                 $return['order']=$order;
           }*/

        }
     

        

        echo json_encode($return);
    }

    public function change_order(){
        $reorderd=$this->input->post('reorderd');
        $return['reorderd']=$reorderd;

           if(!empty($reorderd)){
                
                $id_ary = explode(",",$reorderd);
                  $count=1;             
                foreach($id_ary as $i) { 
                    $get_stage=$this->workflow_model->get_stage(['wid'=>$i]);

                    $this->workflow_model->update_stage_data(['stage_order'=>$count],['stage_column'=>$get_stage[0]->stage_column]);
                   $order=array($i);
                   $count++;
                }
           }
    }


    public function delete_stage(){
        $id=$this->uri->segment(3);
        $return['id']=$id;
        $get_stage=$this->workflow_model->get_stage(['wid'=>$id]);
        $this->workflow_model->delete_stage_data(['stage_column'=>$get_stage[0]->stage_column]);
        $deleted=$this->workflow_model->delete_stage_data(['wid'=>$id]);
        if($deleted){
            $wstages=count($this->workflow_model->get_distinct_column());
            $return['success']='Stage deleted';
            $return['stages']=$wstages;
        } else{
            $return['error']='Stage not deleted';
        }

        echo json_encode($return);
    }

    /*public function update_stage_cell_backup(){
        $wid=$this->input->post('wid');
        $key=$this->input->post('key');
        $cell=$this->input->post('cell');
        $data_id=$this->input->post('data_id');
        $stage_order=$this->input->post('stage_order');
        $stage_data=$this->input->post('stage_data');
        $stage_color=$this->input->post('stage_color');
        $stage_text_color=$this->input->post('stage_text_color');
        $stage_name=$this->input->post('stage_name');
        $same_key_found_total=$this->workflow_model->count_stage_column_by_key($key);
        $get_cell_data_by_column=$this->workflow_model->get_cell_data_by_cell_column($key);
        $get_cell_data_by_cell_column=$this->workflow_model->get_cell_data_by_cell_column($key,'',0);
        $get_cell_data_by_cell_column2=$this->workflow_model->get_cell_data_by_cell_column($key,$cell);

        if($get_cell_data_by_cell_column[0]->stage_cell==''){
            $updated=$this->workflow_model->update_stage_data(['data_id'=>$data_id,'stage_color'=>$stage_color,'stage_text_color'=>$stage_text_color,'stage_name'=>$stage_name,'stage_order'=>$stage_order,'stage_cell'=>$cell,'stage_data'=>$stage_data],['wid'=>$wid,'stage_column'=>$key]);
            $return['update1']='update 1';
        }

        if($get_cell_data_by_cell_column2[0]->stage_cell!=''){
            $updated=$this->workflow_model->update_stage_data(['data_id'=>$data_id,'stage_color'=>$stage_color,'stage_text_color'=>$stage_text_color,'stage_name'=>$stage_name,'stage_order'=>$stage_order,'stage_cell'=>$cell,'stage_data'=>$stage_data],['stage_cell'=>$cell,'stage_column'=>$key]);
            $return['update2']='update 2';
           // $this->workflow_model->delete_stage_data(['wid'=>$this->workflow_model->last_stage_column($cell)[0]->wid]);
        }

        if($get_cell_data_by_cell_column[0]->stage_cell!=''){
            $updated=$this->workflow_model->add_stage_data(['data_id'=>$data_id,'stage_name'=>$stage_name,'stage_order'=>$stage_order,'stage_column'=>$key,'stage_color'=>$stage_color,'stage_text_color'=>$stage_text_color,'stage_cell'=>$cell,'stage_data'=>$stage_data,]);
            $return['insert1']='insert 1';
        }

        if($updated){
            $return['success']='Stage data updated';
        } else{
            $return['error']='Stage data not updated';
        }

        echo json_encode($return);
    }*/

    public function update_stage_cell(){
        $wid=$this->input->post('wid');
        $key=$this->input->post('key');
        $cell=$this->input->post('cell');
        $data_id=$this->input->post('data_id');
        $stage_order=$this->input->post('stage_order');
        $stage_data=$this->input->post('stage_data');
        $stage_color=$this->input->post('stage_color');
        $stage_text_color=$this->input->post('stage_text_color');
        $stage_linenumber=$this->input->post('stage_linenumber');
        $stage_name=$this->input->post('stage_name');

                
        $min_wid=$this->workflow_model->get_min_wid()[0]->min_wid;

        $stage_priority=$this->workflow_model->get_prev_priority($stage_order);
       // $stage_priority=($min_wid==$wid)?$this->workflow_model->get_prev_priority2($wid):$this->workflow_model->get_prev_priority2(($wid-1));

       $return['data']=$stage_priority;
        $return['wid']=$wid;
       $return['stage_order']=$stage_order;
       $return['cell']=$key;


        if(!empty($stage_priority) || $stage_priority!=NULL){
            foreach ($stage_priority as $key0 => $value) {
                foreach ($value as $key2 => $v) {
                    if($key2=='stage_priority'){
                        $fv[]=$v;
                    }
                }
            }

            $return['stage_priority']=$fv;
            
            $indexes = array_keys($fv, 1);
            $return['count_stage_priority']=count($fv);

            if(in_array(1,$fv)){

               // $res=$this->workflow_model->get_data_by_line_data_id($data_id,$stage_linenumber,$cell);
                $res=$this->workflow_model->get_data_by_line_data_id('',$stage_linenumber);
                foreach ($res as $key3 => $value) {
                    foreach ($value as $key4 => $v) {
                        if($key4=='stage_data'){
                            $res2[]=$v;
                        }
                    }
                }

                $return['stage_data']=$res2;

                $return['count_stage_data']=count($res2);

                if(in_array(1,$res2)){
                    $count=count($res2);
                }

                if(in_array(1,$res2)){
                    $same_key_found_total=$this->workflow_model->count_stage_column_by_key($key);
                    $get_cell_data_by_column=$this->workflow_model->get_cell_data_by_cell_column($key);
                    $get_cell_data_by_cell_column=$this->workflow_model->get_cell_data_by_cell_column($key,'',0);
                    $get_cell_data_by_cell_column2=$this->workflow_model->get_cell_data_by_cell_column($key,$cell);

                   // $return['get_cell_data_by_cell_column']=$get_cell_data_by_cell_column;

                    if($get_cell_data_by_cell_column[0]->stage_cell==''){
                        $updated=$this->workflow_model->update_stage_data(['data_id'=>$data_id,'stage_color'=>$stage_color,'stage_text_color'=>$stage_text_color,'stage_name'=>$stage_name,'stage_order'=>$stage_order,'stage_cell'=>$cell,'stage_data'=>$stage_data,'data_line_number'=>$stage_linenumber],['wid'=>$wid,'stage_column'=>$key]);
                         $return['update1']='update 1';
                         //$return['query']=$updated;
                    }

                    if($get_cell_data_by_cell_column2[0]->stage_cell!=''){
                        $updated=$this->workflow_model->update_stage_data(['data_id'=>$data_id,'stage_color'=>$stage_color,'stage_text_color'=>$stage_text_color,'stage_name'=>$stage_name,'stage_order'=>$stage_order,'stage_cell'=>$cell,'stage_data'=>$stage_data,'data_line_number'=>$stage_linenumber],['stage_cell'=>$cell,'stage_column'=>$key]);
                        $return['update2']='update 2';
                       // $this->workflow_model->delete_stage_data(['wid'=>$this->workflow_model->last_stage_column($cell)[0]->wid]);
                    }

                    if($get_cell_data_by_cell_column[0]->stage_cell!=''){
                        $updated=$this->workflow_model->add_stage_data(['data_id'=>$data_id,'stage_name'=>$stage_name,'stage_order'=>$stage_order,'stage_column'=>$key,'stage_color'=>$stage_color,'stage_text_color'=>$stage_text_color,'stage_cell'=>$cell,'stage_data'=>$stage_data,'data_line_number'=>$stage_linenumber]);
                        $return['insert1']='insert 1';
                    }

                   if($updated){
                        $return['success']='Stage data updated';
                        $return['sreturn1']='Step 2';
                    } else{
                        $return['error']='Stage data not updated';
                        $return['Ereturn1']='Step 2';
                    }
                }else{
                    $return['error']='Stage data can not be updated';
                    $return['Ereturn1']='Step 3'; 
                }

            }       
        }else{
                $same_key_found_total=$this->workflow_model->count_stage_column_by_key($key);
                $get_cell_data_by_column=$this->workflow_model->get_cell_data_by_cell_column($key);
                $get_cell_data_by_cell_column=$this->workflow_model->get_cell_data_by_cell_column($key,'',0);
                $get_cell_data_by_cell_column2=$this->workflow_model->get_cell_data_by_cell_column($key,$cell);

                if($get_cell_data_by_cell_column[0]->stage_cell==''){
                    $updated=$this->workflow_model->update_stage_data(['data_id'=>$data_id,'stage_color'=>$stage_color,'stage_text_color'=>$stage_text_color,'stage_name'=>$stage_name,'stage_order'=>$stage_order,'stage_cell'=>$cell,'stage_data'=>$stage_data,'data_line_number'=>$stage_linenumber],['wid'=>$wid,'stage_column'=>$key]);
                }

                if($get_cell_data_by_cell_column2[0]->stage_cell!=''){
                    $updated=$this->workflow_model->update_stage_data(['data_id'=>$data_id,'stage_color'=>$stage_color,'stage_text_color'=>$stage_text_color,'stage_name'=>$stage_name,'stage_order'=>$stage_order,'stage_cell'=>$cell,'stage_data'=>$stage_data,'data_line_number'=>$stage_linenumber],['stage_cell'=>$cell,'stage_column'=>$key]);
                   // $this->workflow_model->delete_stage_data(['wid'=>$this->workflow_model->last_stage_column($cell)[0]->wid]);
                }

                if($get_cell_data_by_cell_column[0]->stage_cell!=''){
                    $updated=$this->workflow_model->add_stage_data(['data_id'=>$data_id,'stage_name'=>$stage_name,'stage_order'=>$stage_order,'stage_column'=>$key,'stage_color'=>$stage_color,'stage_text_color'=>$stage_text_color,'stage_cell'=>$cell,'stage_data'=>$stage_data,'data_line_number'=>$stage_linenumber]);
                }

                if($updated){
                    $return['success']='Stage data updated';
                     $return['sreturn1']='Step 4';
                } else{
                    $return['error']='Stage data not updated';
                     $return['Ereturn1']='Step 4';
                }
       
        } 

        echo json_encode($return);
    }


    public function get_stage_data(){
        $data=$this->workflow_model->get_stage(['wid'=>$this->uri->segment(3)]);
        if($data){
            $return['success']='ok';
            $return['stage_name']=$data[0]->stage_name;
            $return['stage_color']=$data[0]->stage_color;
            $return['stage_text_color']=$data[0]->stage_text_color;
            $return['change_stage_color']=$data[0]->change_stage_color;
        } else{
            $return['error']='no';
        }

        echo json_encode($return);
    }


    public function get_stage_list(){
        $stages=$this->workflow_model->get_distinct_column();
        if($stages){
            echo '<ul id="SortMe" class="Nodot">';    
            foreach ($variable as $key => $value) {
               echo '<li class="ListItem">'.$value->stage_name.'</li>';
            }
            echo '</ul>';
        }
    }

    //14-05-17
    public function show_hide_stages($status){
        $this->db->where('title','show_hide');
        $this->db->delete('settings');
        $this->db->insert('settings',['show_hide'=>$status,'title'=>'show_hide']);
    }

//11-05-17//14-05-17
    public function change_stage_order($id,$stage_order){
       // $id=$this->uri->segment(3);
        $get_stage=$this->workflow_model->get_stage(['wid'=>$id]);
        $id_ary = explode(",",$stage_order);
        for($i=0;$i<count($id_ary);$i++) {      
            $this->workflow_model->update_stage_data(['stage_order'=>$i],['stage_column'=>$get_stage[0]->stage_column]);
        }
    
        //$deleted=$this->workflow_model->delete_stage_data(['wid'=>$id]);
       /* if($updated){
            $return['success']='Stage order changed';
        } else{
            $return['error']='Stage not order changed';
        }

        echo json_encode($return);*/
   }

    //16-05-17

     public function load_stages($page){
        //$page = $this->uri->segment(3);

        //math to get the initial record to be select in the database
        $limit_end =(isset($page) && !empty($page))?($page * $this->session->userdata('per_page_selected')) - $this->session->userdata('per_page_selected'):$this->session->userdata('limit_end');
        if ($limit_end < 0){
            $limit_end = 0;
        } 
        $jobs =($this->session->userdata('search_string_selected')!='')?$this->workflow_model->get_jobposts($this->session->userdata('search_string_selected'),$this->session->userdata('search_string_selected2'), $this->session->userdata('order'), $this->session->userdata('order_type'), $this->session->userdata('per_page_selected'), $limit_end , $this->session->userdata('sort_type')):$this->workflow_model->get_jobposts('','', '', '', 100, $limit_end , 'ASC');
        $show_hide_stages=$this->workflow_model->get_show_hide();
        $counted_stage=count($wstages);
        $_width=(($show_hide_stages[0]->show_hide==2) && $counted_stage<=4)?'300px;':'200px;';
        echo '<table class="table table-striped table-bordered sticky_column" id="table_id" style="width:auto !important;"><thead>';
            echo '<th style = "text-align: center; width:70px;min-width:70px;max-width:70px;">Line No';

                echo '<div class="sort">';
                
                    $sort_type = $this->session->userdata('sort_type');
                    if(isset($sort_type) == false || $sort_type == ''){
                        $sort_type = 'ASC';
                        $this->session->set_userdata('sort_type',$sort_type);
                    }
                    $sort_asc=(isset($order) && $order == 'ASC')?'style="color:#337ab7!important"':'';
                    $sort_desc=(isset($order) && $order == 'DESC')?'style="color:#337ab7!important"':'';
                    echo '<a href="javascript:void(0);" '.$sort_asc.' onclick="sort("ASC")" class="sort-a "></a>
                    <a href="javascript:void(0);" '.$desc.' onclick="sort("DESC")" class="sort-d"></a>
                </div>
            </th>
            <th style = "text-align: center; width:'.$_width.' min-width:'.$_width.' max-width:'.$_width.'">Job';

                if(($show_hide_stages[0]->show_hide==2)){
              
                    echo '<button type="button" class="btn btn-success pull-right btn-xs" style="margin: -4px 5px 0 5px; padding:0 2px; " id="show"><i class="fa fa-eye"></i></button>';
                     
                } else if(($show_hide_stages[0]->show_hide==1)){
                   
                    echo '<button type="button" class="btn btn-warning pull-right btn-xs" style="margin: -4px 5px 0 5px; padding:0 2px; " id="hide"><i class="fa fa-eye-slash"></i></button>';  
                }

            echo '</th>';
            $show_hide=($show_hide_stages[0]->show_hide==2)?'hidecol':'';
            if($show_hide_stages[0]->show_hide==1){
             echo '<th style = " text-align: center; max-width:150px; width:150px;min-width:150px;" class="'.$show_hide.'">Date Recieved</th>
                        <th style = "text-align: center; max-width:150px; width:150px;min-width:150px;"  class="'.$show_hide.'">Due Date</th>
                        <th style = "text-align: center; width:150px;min-width:150px;max-width:150px;" class="'.$show_hide.'">Translator Name</th>
                        <th style = "text-align: center; width:150px;min-width:150px;max-width:150px;" class="'.$show_hide.'">Language From</th>
                        <th style = "text-align: center; width:150px;min-width:150px;max-width:150px;" class="'.$show_hide.'">Language To</th>';
            }
           
        
            $wstages=$this->workflow_model->get_distinct_column();
            $counted_wstages=count($this->workflow_model->get_distinct_column());
            if(!empty($wstages)){
                foreach ($wstages as $key => $value) {
                    $cname=$this->workflow_model->get_stage_name($value->stage_column);
                    $background=($cname[0]->stage_color!='')?$cname[0]->stage_color:'#ffffff';
                    //18-05-17
                    $textcolor=(!empty($cname) && $cname[0]->stage_color=='#000000'|| $cname[0]->stage_color=='#0000FF' || $cname[0]->stage_color=='#FF0000' )?'#ffffff':'#000000';
                    echo '<th style = " width:150px;min-width:150px;max-width:150px;text-align: center;background:'.$background.';color:'.$textcolor.'"  onclick="change_stage_data('.$cname[0]->wid.');load_stage();">'.$cname[0]->stage_name.'</th>';
                   
                }
            }
           echo '
        </thead>
        </table>
        <div class="body">
         <table id="header-fixed">
        <tbody>';
       // print_r($jobs);
                if(!empty($jobs)){
                    $count2=($limit_end!=0)?$limit_end+1:1;
                    foreach ($jobs as $key => $value) {
                        $lang=explode('/',$value->language);
                        $language_from=$this->workflow_model->getlanguages(['id'=>$lang[0]]);
                        $language_to=$this->workflow_model->getlanguages(['id'=>$lang[1]]);
                        $bidjob=$this->workflow_model->getbidjobs(['job_id'=>$value->id]);
                        
                        $translator=$this->workflow_model->gettransalator(['id'=>$bidjob[0]->trans_id]);

                        $compliant=$this->workflow_model->get_compliant_by_lineno($value->lineNumber);

                        $compliant_msg=($compliant!='' && $compliant[0]->marked_as==1)?'<span style="color:blue;">Marked as Edit<span>':(($compliant!='' && $compliant[0]->marked_as==2)?'<span style="color:red;">Marked as Error</span>':'&nbsp;Mark this job');
                        
                       
                            echo '<tr align="center">
                                <td class="" style="width:70px;min-width:70px;">'.$value->lineNumber.'</td>
                                <td class="" style="width:'.$_width.' min-width:'.$_width.'"><span class="pull-left">'.$value->name.'</span><br><a href="javascript:void(0);" data-toggle="modal" data-target="#myModal4" data-lineno="'.$value->lineNumber.'" data-jobid='.$value->id.' data-jobname="'.$value->name.'" class="mark" id="mark'.$value->lineNumber.'">'.$compliant_msg.'</a> <a href="javascript:void(0);" class="popup pull-right"  data-trigger="click" data-id="'.$value->id.'" data-line="'.$value->lineNumber.'" data-container="body">Notes</a></td>';
                            if($show_hide_stages[0]->show_hide==1){
                                echo '<td class="'.$show_hide.'"  style="width:150px;min-width:150px;">'.date('m-d-Y',strtotime($value->created)).'</td>
                                <td class="'.$show_hide.'" style="white-space: nowrap;width:150px;min-width:150px;">'.$value->dueDate.'</td>
                                <td class="'.$show_hide.'" style="width:150px;min-width:150px;">'.ucwords($translator[0]->first_name.' '.$translator[0]->last_name).'</td>
                                <td class="'.$show_hide.'" style="width:150px;min-width:150px;">'.$language_from[0]->name.'</td>
                                <td class="'.$show_hide.'" style="width:150px;min-width:150px;">'.$language_to[0]->name.'</td>';
                            }
                                
                                
                                 if(!empty($wstages)){

                                    $c=8;
                                    foreach ($wstages as $key => $value2) {
                                        $cname=$this->workflow_model->get_stage_name($value2->stage_column);
                                        $celldata=$this->workflow_model->get_cell_data($value->id,$value2->stage_column,$value2->stage_column.$count2.$value->id);
                                        //$cell_background=(!empty($celldata) && $celldata[0]->stage_data!=2)?$celldata[0]->stage_cell_color:$cname[0]->stage_color;
                                        $cell_background=(!empty($celldata) && $celldata[0]->stage_data==1)?'#ffffff':$cname[0]->stage_color;
                                        //$textcolor=(!empty($cname) && $cname[0]->stage_text_color!='' )?$cname[0]->stage_text_color:'#000000';
                                        //18-05-17
                                    


                                        if($celldata[0]->stage_data==''){
                                            if($cname[0]->change_stage_color==2){
                                                $textcolor='#000000';
                                            }else if($cname[0]->change_stage_color==1){
                                                $textcolor=(!empty($cname) && $cname[0]->stage_color=='#000000'|| $cname[0]->stage_color=='#0000FF' || $cname[0]->stage_color=='#FF0000')?'#ffffff':'#000000';
                                            }
                                        }else if($celldata[0]->stage_data!='' && $celldata[0]->stage_data==1){
                                            $textcolor='#000000';
                                        }else if($celldata[0]->stage_data!='' && $celldata[0]->stage_data!=1){
                                            if($cname[0]->change_stage_color==2){
                                                $textcolor='#000000';
                                            }else if($cname[0]->change_stage_color==1){
                                                $textcolor=(!empty($cname) && $cname[0]->stage_color=='#000000'|| $cname[0]->stage_color=='#0000FF' || $cname[0]->stage_color=='#FF0000')?'#ffffff':'#000000';
                                            }
                                        }

                                        $attributes=$this->workflow_model->get_stage_attributes($cname[0]->wid);

                                        if(!empty($celldata) && $celldata[0]->stage_data!=''){
                                            $selected_no=($celldata[0]->stage_data==2)?"selected='selected'":'';
                                            $selected_yes=($celldata[0]->stage_data==1)?"selected='selected'":'';
                                            $stagedata=($celldata[0]->stage_data==2)?'1':'2';
                                        echo "<td style = 'width:150px;min-width:150px; white-space: nowrap;text-align: center;background:".$cell_background.";'>
                                            <select class='form-control selector' style='font-size: 10px;font-weight: 600; height: 24px;padding: 2px 5px;background:".$cell_background.";color:".trim($textcolor)."' data-linenumber=".$value->lineNumber." data-id=".$cname[0]->wid." data-key='".$cname[0]->stage_column."' data-cell='".$value2->stage_column.$count2.$value->id."' data-dataid='".$value->id."' data-stageorder='".$cname[0]->stage_order."' data-stagecolor='".$cname[0]->stage_color."'   data-stagename='".$cname[0]->stage_name."' data-stagetextcolor='".$cname[0]->stage_text_color."'>
                                                <option value='2' ".$selected_no.">No</option>
                                                <option value='1' ".$selected_yes.">Yes</option>";
                                             
                                                if($attributes){
                                                    foreach ($attributes as $attribute) {
                                                        $selected=($celldata[0]->stage_data==$attribute->attr_value)?'selected="selected"':'';
                                                        echo "<option value=".$attribute->attr_value." ".$selected.">".$attribute->attr_name."</option>"; 
                                                    }
                                                }
                                            echo "</select>
                                            </td>";
                                        } else{
                                            
                                            echo "<td style = 'width:150px;min-width:150px; white-space: nowrap;text-align: center;background:".$cell_background.";'>
                                                <select class='form-control selector' style='font-size: 10px;font-weight: 600; height: 24px;padding: 2px 5px;background:".$cell_background.";color:".trim($textcolor)."' data-linenumber=".$value->lineNumber." data-id=".$cname[0]->wid." data-key='".$cname[0]->stage_column."' data-cell='".$value2->stage_column.$count2.$value->id."' data-dataid='".$value->id."' data-stageorder='".$cname[0]->stage_order."' data-stagecolor='".$cname[0]->stage_color."'   data-stagename='".$cname[0]->stage_name."' data-stagetextcolor='".$cname[0]->stage_text_color."'>
                                                    <option value='2'>No</option>
                                                    <option value='1'>Yes</option>";
                                                    if($attributes){
                                                        foreach ($attributes as $attribute) {
                                                            $selected=($celldata[0]->stage_data==$attribute->attr_value)?'selected="selected"':'';
                                                            echo "<option value=".$attribute->attr_value.">".$attribute->attr_name."</option>"; 
                                                        }
                                                    }
                                                echo "</select>
                                            </td>";
                                        }
                                        
                                    $c++;
                                    }
                                }
                            echo '</tr>';
                       
                        $count2++;
                    }
                }
                else{
                    
                        echo '<td colspan="'.($counted_wstages+7).'" align="center">No Records Available</td>';
                    
                }

            
        echo '</tbody></table></div>';
    }


    public function loadstages(){
        $wstages=$this->workflow_model->get_distinct_column();
        foreach($wstages as $value){
            $cname=$this->workflow_model->get_stage_name($value->stage_column);
            $background=($cname[0]->stage_color!='')?$cname[0]->stage_color:'#ffffff';
            $textcolor=(!empty($cname) && $cname[0]->stage_color=='#000000'|| $cname[0]->stage_color=='#0000FF' || $cname[0]->stage_color=='#FF0000' )?'#ffffff':'#000000';
            $show_priority=($cname[0]->stage_priority==1)?' <span class="pull-right" onclick="change_stage_priority('.$cname[0]->wid.',0);">  <i class="fa fa-star fa-fw" title="" style="cursor:pointer;"></i></span>':' <span class="pull-right" onclick="change_stage_priority('.$cname[0]->wid.',1)">  <i class="fa fa-star-half-o fa-fw" title="" style="cursor:pointer;"></i></span>';
         
            echo '<li data-srtid="'.$cname[0]->wid.'" id="'.$cname[0]->stage_order.'" style="background:'.$background.';color:'.$textcolor.'" data-wid="'.$cname[0]->wid.'" data-order="'.$cname[0]->stage_order.'">'.$cname[0]->stage_name.'<span class="pull-right" onclick="delete_stage('.$cname[0]->wid.')"><i class="fa fa-times  fa-fw" title="Delete Stage" style="cursor:pointer;"></i></span><span class="pull-right" onclick="change_stage_id('.$cname[0]->wid.');load_stage_attributes('.$cname[0]->wid.');">  <i class="fa fa-pencil fa-fw" title="Edit Stage" style="cursor:pointer;"></i></span>'.$show_priority.'  <span class="pull-right" id="'.$cname[0]->wid.'spans" style="display:none;">Priority High &nbsp;</span></li>';
          
        }
    }

    //11-07-17

    public function change_stage_priority($id,$data){
        $this->workflow_model->update_stage_data(['stage_priority'=>$data],['wid'=>$id]);
    }
    //11-07-17

    public function save_notes(){
        $wid=$this->input->post('wid');
        $notes=$this->input->post('notes');
        $this->db->where('data_id',$wid);
        $query=$this->db->get('workflow_notes');
        $result=$query->result();
        if(!empty($result)){
            $this->db->where('data_id',$wid);
            $result=$this->db->update('workflow_notes',array('data_id'=>$wid,'notes'=>$notes));
        }else{
            $result=$this->db->insert('workflow_notes',array('data_id'=>$wid,'notes'=>$notes));
        }

        
        if($result){
            $return['success']='Notes Saved';
        }else{
            $return['error']='Notes not Saved';
        }

        echo json_encode($return);
    }

    public function load_saved_notes($id){
         $this->db->where('data_id',$id);
        $query=$this->db->get('workflow_notes');
        $return['notes']=$query->result()[0]->notes;
        echo json_encode($return);

    }

    //20-05-17
    public function add_new_stage_attribute(){
        $stage_id=$this->input->post('stage_id');
        $attr_name=$this->input->post('attr_name');
        $attributes=$this->workflow_model->get_stage_attributes($stage_id);
        $return['success']=$attributes;
       // if($attributes){
           // if($this->workflow_model->get_stage_attributes_byname($attr_name)[0]->attr_name!=$attr_name){
                $max_attr_value=$this->workflow_model->get_stage_max_attribute();
                $new_attr_value=($max_attr_value!=null)?($max_attr_value+1):3;
                $insert=$this->workflow_model->add_stage_attribute(['stage_id'=>$stage_id,'attr_name'=>$attr_name,'attr_value'=>$new_attr_value]);
                if($insert){
                    $return['success']='Attribute added';
                }else{
                    $return['error']='Attribute not added';
                }
           /* }

             else{
                $return['error']='Duplicate attribute entry not possible';
            }*/
                

            
       // }

        echo json_encode($return);
    }

    public function update_stage_attribute(){
        $attr_id=$this->input->post('attr_id');
        $attr_name=$this->input->post('attr_name');
        $insert=$this->workflow_model->update_stage_attribute(['attr_name'=>$attr_name],['id'=>$attr_id]);
        if($insert){
            $return['success']='Attribute updated';
        }else{
            $return['error']='Attribute not updated';
        }

        echo json_encode($return);   
    }

    public function delete_stage_attribute($id,$stage_id){
        $delete=$this->workflow_model->delete_stage_attribute(['id'=>$id,'stage_id'=>$stage_id]);
        if($delete){
            $return['success']='Stage attribute deleted';
        }else{
            $return['error']='Stage attribute not deleted';
        }

        echo json_encode($return); 
    }

    public function load_stage_attributes($id){
        $attributes=$this->workflow_model->get_stage_attributes($id);
        if($attributes){
            foreach ($attributes as $attribute) {
                echo '<li data-srtid="'.$id.'" id="'.$attribute->id.'">'.$attribute->attr_name.'<span class="pull-right" onclick="delete_stage_attribute('.$attribute->id.','.$attribute->stage_id.');"><i class="fa fa-times  fa-fw" title="Delete Attribute" style="cursor:pointer;"></i></span><span class="pull-right" onclick="edit_stage_attribute('.$attribute->id.',\''.$attribute->attr_name.'\');">  <i class="fa fa-pencil fa-fw" title="Edit Attribute" style="cursor:pointer;"></i></span></li>';
            }
        }
    }


    //13-07-17

    public function mark_on_job(){

        $compliant=$this->input->post('compliant');
        $compliant_reply=$this->input->post('compliant_reply');
        $line_no=$this->input->post('line_no');
        $marked_as=$this->input->post('stage_mark');
        $job_name=$this->input->post('job_name');
        $job_id=$this->input->post('job_id');
       // $files=comma_separated_to_array($this->input->post('fileuploader-list-files'));

        $found=$this->workflow_model->get_compliant_by_lineno($line_no);

        if($found==null || $found==''){

            $bidjobs=$this->workflow_model->getbidjobs(['job_id'=>$job_id]);

           

            foreach($bidjobs as $bidjob){
                //$_trans[]=$bidjob->trans_id;
               // $translator=$this->workflow_model->gettransalator(['id'=>$bidjob->trans_id]);
                $_trans[]=$bidjob->trans_id;//ucwords($translator[0]->first_name.' '.$translator[0]->last_name);
            }

             $return['_trans']=$_trans;

           // $translator=$this->workflow_model->gettransalator(['id'=>$job_id]);
          

            /* if(!empty($files) || $files!=NULL){
                foreach ($files as $key => $value) {
                    $filename=str_replace('0:/','', str_replace('"','',str_replace(']','',str_replace('[','',$value))));
                    $files_arr[]=$filename;

  

                    if(move_uploaded_file($_FILES['files']['tmp_name'],'./uploads/'.$_FILES['files']['name'])){
                        $return['files_uploaded']="yes";
                    }else{
                        $return['files_uploaded']="no";
                    }
                }

                $images=array_to_comma_seperated($files_arr);
             }
             else{
                $images='';
             }*/

             /*if(!empty($_FILES['files']['name'])){
                $filesCount = count($_FILES['files']['name']);
                for($i = 0; $i < $filesCount; $i++){
                    $_FILES['userFile']['name'] = $_FILES['files']['name'][$i];
                    $_FILES['userFile']['type'] = $_FILES['files']['type'][$i];
                    $_FILES['userFile']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
                    $_FILES['userFile']['error'] = $_FILES['files']['error'][$i];
                    $_FILES['userFile']['size'] = $_FILES['files']['size'][$i];
                    $uploadPath = './uploads/compliants/';
                    $config['upload_path'] = $uploadPath;
                    $config['allowed_types'] = 'gif|jpg|png';

                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);
                    if($this->upload->do_upload('userFile')){
                        $fileData = $this->upload->data();
                        $uploadData[$i]['file_name'] = $fileData['file_name'];
                        $uploadData[$i]['created'] = date("Y-m-d H:i:s");
                        $uploadData[$i]['modified'] = date("Y-m-d H:i:s");

                        //$images=array_to_comma_seperated($uploadData[$i]['file_name']);
                    }
                }
             }else{
                $images='';
             }*/

            $images = array();
            $return['files']=count($_FILES);
            if(!empty($_FILES['images']['name'])){

                $target_dir = "uploads/";
                $target_file = $target_dir.$_FILES['images']['name'];
                if(move_uploaded_file($_FILES['images']['tmp_name'],$target_file)){
                    //$images[] = $target_file;
                }

               /* foreach($_FILES['images']['name'] as $key=>$val){
                        //upload and stored images
                        $target_dir = "uploads/";
                        $target_file = $target_dir.$_FILES['images']['name'][$key];
                        if(move_uploaded_file($_FILES['images']['tmp_name'][$key],$target_file)){
                            $images[] = $target_file;
                        }
                    }*/
            }else{
                $images='';
            }
                

             $insrted=$this->workflow_model->add_client_compliant(['user_id'=>$this->session->userdata('is_admin'),'job_id'=>$job_id,'line_no'=>$line_no,'job_name'=>$job_name,'marked_as'=>$marked_as,'compliant'=>$compliant,'answer'=>$compliant_reply,'translators'=>array_to_comma_seperated($_trans)]);

                if($insrted){
                    $return['success']="Copliant registered";
                    $return['marked']=($marked_as==1)?'<span style="color:blue;">Marked as Edit<span>':'<span style="color:red;">Marked as Error</span>';
                }else{
                    $return['error']="Copliant not registered";
                }
                
        }else{
            $insrted=$this->workflow_model->update_client_compliant(['user_id'=>$this->session->userdata('is_admin'),'job_name'=>$job_name,'marked_as'=>$marked_as,'compliant'=>$compliant,'answer'=>$compliant_reply],['line_no'=>$line_no]);

            if($insrted){
                $return['success']="<div class='alert alert-success text-center'>Copliant registered</div>";
                $return['marked']=($marked_as==1)?'<span style="color:blue;">Marked as Edit<span>':'<span style="color:red;">Marked as Error</span>';
            }else{
                $return['error']="<div class='alert alert-success text-center'>Copliant not registered</div>";
            } 
        }

         echo json_encode($return); 
    }




 public function mark_on_job2(){
    //echo '<pre>';
   // print_r($_FILES);

   $compliant=$this->input->post('compliant');
    $compliant_reply=$this->input->post('compliant_reply');
    $line_no=$this->input->post('line_no');
    $marked_as=$this->input->post('stage_mark');
    $job_name=$this->input->post('job_name');
    $job_id=$this->input->post('job_id');

    $found=$this->workflow_model->get_compliant_by_lineno($line_no);
    if($found==null || $found==''){
        $bidjobs=$this->workflow_model->getbidjobs(['job_id'=>$job_id]);

        foreach($bidjobs as $bidjob){
            $_trans[]=$bidjob->trans_id;
        }

        if(!empty($_FILES['files']['name'])){
            $filesCount = count($_FILES['files']['name']);
            for($i = 0; $i < $filesCount; $i++){
                $ext = end((explode(".", $_FILES['files']['name'][$i])));
                $file_name = time().$i."_".($i+1).".".$ext;
                $_FILES['userFile']['name'] =$file_name;// $_FILES['files']['name'][$i];
                $_FILES['userFile']['type'] = $_FILES['files']['type'][$i];
                $_FILES['userFile']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
                $_FILES['userFile']['error'] = $_FILES['files']['error'][$i];
                $_FILES['userFile']['size'] = $_FILES['files']['size'][$i];
                $uploadPath = './uploads/compliants/';
                $config['upload_path'] = $uploadPath;
                $config['allowed_types'] = 'gif|jpg|png';

                $this->load->library('upload', $config);
                if(!is_dir($config['upload_path'])){
                    mkdir($config['upload_path'], 0755, TRUE);
                }
                $this->upload->initialize($config);
                if($this->upload->do_upload('userFile')){
                    $fileData = $this->upload->data();
                    $uploadData[$i]['file_name'] = $fileData['file_name'];
                    $uploadData[$i]['created'] = date("Y-m-d H:i:s");
                    $uploadData[$i]['modified'] = date("Y-m-d H:i:s");

                    $images=array_to_comma_seperated($file_name);
                }
            }
        }else{
            $images='';
        }

        $return['images']=$images;

        $insrted=$this->workflow_model->add_client_compliant(['user_id'=>$this->session->userdata('is_admin'),'job_id'=>$job_id,'line_no'=>$line_no,'job_name'=>$job_name,'marked_as'=>$marked_as,'compliant'=>$compliant,'answer'=>$compliant_reply,'translators'=>array_to_comma_seperated($_trans),'images'=>$images]);

        if($insrted){
            $return['success']="Copliant registered";
            $return['marked']=($marked_as==1)?'<span style="color:blue;">Marked as Edit<span>':'<span style="color:red;">Marked as Error</span>';
        }else{
            $return['error']="Copliant not registered";
        }

    }else{
       $insrted=$this->workflow_model->update_client_compliant(['user_id'=>$this->session->userdata('is_admin'),'job_name'=>$job_name,'marked_as'=>$marked_as,'compliant'=>$compliant,'answer'=>$compliant_reply],['line_no'=>$line_no]);

            if($insrted){
                $return['success']="<div class='alert alert-success text-center'>Copliant registered</div>";
                $return['marked']=($marked_as==1)?'<span style="color:blue;">Marked as Edit<span>':'<span style="color:red;">Marked as Error</span>';
            }else{
                $return['error']="<div class='alert alert-success text-center'>Copliant not registered</div>";
            }  
    }

     echo json_encode($return);

  // $target_dir = "uploads/compliants/";
      /*           $target_file = $target_dir.$_FILES['file']['name'];
                if(move_uploaded_file($_FILES['file']['tmp_name'],$target_file)){
                    //$images[] = $target_file;
                    echo 'uploaded';
                } else {

                    echo 'not uploaded';
                }*/
                /*foreach($_FILES['files']['name'] as $key=>$val){
                        //upload and stored images
                    $temp = explode(".", $_FILES["files"]["name"][$key]);
                    $newfilename = round(microtime(true)) . '.' . end($temp);
                    move_uploaded_file($_FILES["file"]["tmp_name"][$key], "../img/imageDirectory/" . $newfilename);
                        
                        $target_file = $target_dir.$newfilename;
                        if(move_uploaded_file($_FILES['files']['tmp_name'][$key],$target_file)){
                            $images[] = $target_file;
                            echo 'uploaded';
                        }else {

                            echo 'not uploaded';
                        }
                    }*/

            

                  //  print_r($images);


 }

        public function upload_files(){
            $job_id=$this->input->post('job_id2');
            $line_no=$this->input->post('line_no2');
            $return['files']=comma_separated_to_array($this->input->post('fileuploader-list-files'));
            foreach ($return['files'] as $key => $value) {
                $files[]=str_replace('0:/','', str_replace('"','',str_replace(']','',str_replace('[','',$value))));
            }

           foreach ($files as $key => $value) {
               
           }
           // echo json_encode($return);
        }



    public function load_compliants($line_no){
        $compliants=$this->workflow_model->get_compliant_by_lineno($line_no);
        $data['line_noe']=$line_no;
        $data['marked_as']=$compliants[0]->marked_as;
        $data['compliant']=$compliants[0]->compliant;
        $data['compliants_reply']=$compliants[0]->answer;

        echo json_encode($data);
    }


}