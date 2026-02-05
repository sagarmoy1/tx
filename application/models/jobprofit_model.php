<?php

class Jobprofit_model extends CI_Model
{

    /**
     * Responsable for auto load the database
     * @return void
     */
    public function __construct()
    {
        $this->load->database();
    }

    public function get_jobprofit($search_string = null, $order = 'id', $order_type = 'DESC', $limit_start = '', $limit_end = '', $start_date = '', $end_date = '', $lang_from = '', $lang_to = '', $reverse_lang = false, $margin_profit_from = '', $margin_profit_to = '')
    {
        $sql = "SELECT SUM(t1.awarded_price) AS awarded_price,t1.language,t1.complete_date,(t1.job_price - SUM(t1.awarded_price)) AS profit,GROUP_CONCAT(t1.translators) AS translators,GROUP_CONCAT(t1.name) AS name,t1.lineNumberCode,t1.job_price AS job_price,t1.id,t1.stage FROM (SELECT SUM(bidjob.price) AS awarded_price, jobpost.language AS language, MAX(bidjob.complete_date) AS complete_date,MAX(invoice.id) AS invoice_id, GROUP_CONCAT(CONCAT(translator.first_name,' ',translator.last_name)) AS translators, jobpost.name AS name, jobpost.lineNumberCode AS lineNumberCode, jobpost.price AS job_price, bidjob.job_id AS id, bidjob.stage AS stage from bidjob JOIN jobpost ON jobpost.id = bidjob.job_id JOIN translator ON bidjob.trans_id = translator.id JOIN invoice ON invoice.bid_id = bidjob.id WHERE bidjob.is_done = 1 AND bidjob.stage <> 1 AND invoice.is_deleted = 0 GROUP BY jobpost.id ORDER BY bidjob.complete_date DESC ) AS t1 WHERE lineNumberCode IS NOT NULL";
        $check = 1;
        if (!is_null($search_string) and $search_string != '') {
            $sql .= " AND (name LIKE '%" . $this->db->escape_like_str($search_string) . "%' OR lineNumberCode LIKE '%" . $this->db->escape_like_str($search_string) . "%') ";
            $check = 1;
        }
        if ($start_date != '' && $end_date != '') {
            $start = date('Y-m-d', strtotime($start_date)) . ' 00:00:00';
            $end = date('Y-m-d', strtotime($end_date)) . ' 00:00:00';
            if ($check == 1) {

                $sql .= ' AND complete_date >= "' . $start . '" AND complete_date <= "' . $end . '"';
            } else {
                $sql .= ' WHERE complete_date >= "' . $start . '" AND complete_date <= "' . $end . '"';
                $check = 1;

            }
        }

        if ($lang_from != '' && $lang_to != '') {
            $lang = $lang_from . '/' . $lang_to;
            if ($check == 1) {
                $sql .= ' AND (language = "' . $lang . '"';
            } else {
                $sql .= ' WHERE (language = "' . $lang . '"';
                $check = 1;
            }
            if ($reverse_lang != false) {
                $lang_rev = $lang_to . '/' . $lang_from;
                $sql .= ' OR language = "' . $lang_rev . '")';
            } else {
                $sql .= ')';
            }
        }

        if ($margin_profit_from != '') {
            if ($check == 1) {
                $sql .= ' AND ((profit/job_price)*100) >= ' . (float)$margin_profit_from;
            } else {
                $sql .= ' WHERE ((profit/job_price)*100) >= ' . (float)$margin_profit_from;
                $check = 1;
            }
        }

        if ($margin_profit_to != '') {
            if ($check == 1) {
                $sql .= ' AND ((profit/job_price)*100) <= ' . (float)$margin_profit_to;
            } else {
                $sql .= ' WHERE ((profit/job_price)*100) <= ' . (float)$margin_profit_to;
                $check = 1;
            }
        }


        //$sql .= " order by modified desc ";
        $sql .= ' GROUP BY lineNumberCode';
        //vince change
        $sql .= " ORDER BY invoice_id DESC ";

        $sql .= "LIMIT " . (int)$limit_end . ", " . (int)$limit_start;

        $query = $this->db->query($sql);
//        print_r($this->db->last_query()); exit;
        return $query->result_array();
    }

    /**
     * Count the number of rows
     * @param int $manufacture_id
     * @param int $search_string
     * @param int $order
     * @param $is_margin int
     * @return int
     * @return object
     */
    function count_jobprofit($search_string = null, $order = 'id', $start_date = '', $end_date = '', $is_margin = 0, $lang_from = '', $lang_to = '', $reverse_lang = false, $margin_profit_from = '', $margin_profit_to = '')
    {


        $sql = "SELECT SUM(t1.awarded_price) AS awarded_price,t1.language,t1.complete_date,(t1.job_price - SUM(t1.awarded_price)) AS profit,GROUP_CONCAT(t1.translators) AS translators,GROUP_CONCAT(t1.name) AS name,t1.lineNumberCode,t1.job_price AS job_price,t1.id,t1.stage FROM (SELECT SUM(bidjob.price) AS awarded_price, jobpost.language AS language, bidjob.complete_date AS complete_date,MAX(invoice.id) AS invoice_id, GROUP_CONCAT(CONCAT(translator.first_name,' ',translator.last_name)) AS translators, jobpost.name AS name, jobpost.lineNumberCode AS lineNumberCode, jobpost.price AS job_price, bidjob.job_id AS id, bidjob.stage AS stage from bidjob JOIN jobpost ON jobpost.id = bidjob.job_id JOIN translator ON bidjob.trans_id = translator.id JOIN invoice ON invoice.bid_id = bidjob.id WHERE bidjob.is_done = 1 AND bidjob.stage <> 1 AND invoice.is_deleted = 0 GROUP BY jobpost.id ORDER BY bidjob.complete_date DESC ) AS t1 WHERE lineNumberCode IS NOT NULL";
        $check = 1;
        if (!is_null($search_string) and $search_string != '') {
            $sql .= " AND (name LIKE '%" . $this->db->escape_like_str($search_string) . "%' OR lineNumberCode LIKE '%" . $this->db->escape_like_str($search_string) . "%' ) ";
            $check = 1;
        }
        if ($start_date != '' && $end_date != '') {
            $start = date('Y-m-d', strtotime($start_date)) . ' 00:00:00';
            $end = date('Y-m-d', strtotime($end_date)) . ' 00:00:00';
            if ($check == 1) {

                $sql .= ' AND complete_date >= "' . $start . '" AND complete_date <= "' . $end . '"';
            } else {
                $sql .= ' WHERE complete_date >= "' . $start . '" AND complete_date <= "' . $end . '"';
                $check = 1;

            }
        }

        if ($lang_from != '' && $lang_to != '') {
            $lang = $lang_from . '/' . $lang_to;
            if ($check == 1) {
                $sql .= ' AND (language = "' . $lang . '"';
            } else {
                $sql .= ' WHERE (language = "' . $lang . '"';
                $check = 1;
            }
            if ($reverse_lang != false) {
                $lang_rev = $lang_to . '/' . $lang_from;
                $sql .= ' OR language = "' . $lang_rev . '")';
            } else {
                $sql .= ')';
            }
        }

        if ($margin_profit_from != '') {
            if ($check == 1) {
                $sql .= ' AND ((profit/job_price)*100) >= ' . $margin_profit_from;
            } else {
                $sql .= ' WHERE ((profit/job_price)*100) >= ' . $margin_profit_from;
                $check = 1;
            }
        }

        if ($margin_profit_to != '') {
            if ($check == 1) {
                $sql .= ' AND ((profit/job_price)*100) <= ' . $margin_profit_to;
            } else {
                $sql .= ' WHERE ((profit/job_price)*100) <= ' . $margin_profit_to;
                $check = 1;
            }
        }

        //$sql .= " order by modified desc ";
        $sql .= ' GROUP BY lineNumberCode';
        //vince change
        $sql .= " ORDER BY invoice_id DESC";

        $query = $this->db->query($sql);
        if ($is_margin == 0) {
            return $query->num_rows();
        } else {
            return $query;
        }
    }


    public function get_finance_summary($search_string = null, $start_date = '', $end_date = '', $lang_from = '', $lang_to = '', $reverse_lang = false, $margin_profit_from = '', $margin_profit_to = '')
    {
        $sql = "select sum(job_price) total_job_price, sum(awarded_price) total_awarded_price, sum(profit) total_profit, (sum(profit)/count(job_price)) average_profit, count(*) total_jobs, (sum((profit/job_price)*100)/count(job_price)) avarage_profit_margin
                from (
                SELECT SUM(bidjob.price) AS awarded_price, jobpost.language AS language, bidjob.complete_date AS complete_date, (jobpost.price - SUM(bidjob.price)) AS profit, GROUP_CONCAT(CONCAT(translator.first_name,' ',translator.last_name)) AS translators, jobpost.name AS name, jobpost.lineNumberCode AS lineNumberCode, jobpost.price AS job_price, bidjob.job_id AS id, bidjob.stage AS stage from bidjob JOIN jobpost ON jobpost.id = bidjob.job_id JOIN translator ON bidjob.trans_id = translator.id JOIN invoice ON invoice.bid_id = bidjob.id WHERE bidjob.is_done = 1 AND bidjob.stage <> 1 AND invoice.is_deleted = 0 GROUP BY bidjob.job_id
                ) t1";
        $check = 0;
        if (!is_null($search_string) and $search_string != '') {
            $sql .= " WHERE (name LIKE '%" . $this->db->escape_like_str($search_string) . "%' OR lineNumberCode LIKE '%" . $this->db->escape_like_str($search_string) . "%') ";
            $check = 1;
        }
        if ($start_date != '' && $end_date != '') {
            $start = date('Y-m-d', strtotime($start_date)) . ' 00:00:00';
            $end = date('Y-m-d', strtotime($end_date)) . ' 00:00:00';
            if ($check == 1) {

                $sql .= ' AND complete_date >= "' . $start . '" AND complete_date <= "' . $end . '"';
            } else {
                $sql .= ' WHERE complete_date >= "' . $start . '" AND complete_date <= "' . $end . '"';
                $check = 1;

            }
        }

        if ($lang_from != '' && $lang_to != '') {
            $lang = $lang_from . '/' . $lang_to;
            if ($check == 1) {
                $sql .= ' AND (language = "' . $lang . '"';
            } else {
                $sql .= ' WHERE (language = "' . $lang . '"';
                $check = 1;
            }
            if ($reverse_lang != false) {
                $lang_rev = $lang_to . '/' . $lang_from;
                $sql .= ' OR language = "' . $lang_rev . '")';
            } else {
                $sql .= ')';
            }
        }

        if ($margin_profit_from != '') {
            if ($check == 1) {
                $sql .= ' AND ((profit/job_price)*100) >= ' . $margin_profit_from;
            } else {
                $sql .= ' WHERE ((profit/job_price)*100) >= ' . $margin_profit_from;
                $check = 1;
            }
        }

        if ($margin_profit_to != '') {
            if ($check == 1) {
                $sql .= ' AND ((profit/job_price)*100) <= ' . $margin_profit_to;
            } else {
                $sql .= ' WHERE ((profit/job_price)*100) <= ' . $margin_profit_to;
                $check = 1;
            }
        }

        $query = $this->db->query($sql);
        return $query->row();
    }

    public function get_line_number_info($line_number_code)
    {

        $sql = "SELECT j.id AS job_id, b.id AS bidjob_id, j.name AS job_name, b.trans_id, j.price AS job_price, b.price AS awarded_price, b.awarded_admin_id AS awarded_admin_id, b.completed_admin_id AS completed_admin_id FROM jobpost j JOIN bidjob b ON b.job_id = j.id JOIN invoice ON invoice.bid_id = b.id WHERE j.lineNumberCode = '{$line_number_code}' AND b.awarded = 1 AND b.is_done = 1 AND invoice.is_deleted = 0 ORDER BY b.complete_date DESC ";
        $query = $this->db->query($sql);
//        echo $this->db->last_query();exit();
        return $query;
    }
}

?>
