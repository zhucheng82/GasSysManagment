<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
// ------------------------------------------------------------------------

/**
 * 扩展CI的CI_Model类
 *
 * @package        CodeIgniter
 * @subpackage    models
 * @category    MY_Model
 * @author        South
 */
class XT_Model extends CI_Model
{

    protected $mTable;
    protected $mPkId = 'id';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        //$this->db = _get_db('default');
    }

    public function prefix()
    {
        return $this->db->dbprefix;
    }

    public function table($table = '')
    {
        if (!$table) {
            $table = $this->mTable;
        }
        return $this->db->protect_identifiers($table, TRUE);
    }

    public function set_table($table)
    {
        $this->mTable = $table;
        return $this;
    }

    public function execute($sql)
    {
        return $this->db->query($sql)->row_array();
    }

    public function execute_array($sql){
        return $this->db->query($sql)->result_array();
    }

    public function get_by_id($id, $fields = '*')
    {
        $result = $this->db->select($fields)
            ->from($this->mTable)
            ->where($this->mPkId, $id)
            ->get()
            ->row_array();
        return $result;
    }

    /**
     *根据条件查询--多表
     * 该查询相当于自己拼接sql，用的时候要注意，防止sql注入
     */
    public function get_by_where_tb($where, $fields = '*', $tb = '')
    {
//        $this->db->set_dbprefix('');
//
//        if (empty($tb)) {
//            $tb = $this->mTable;
//        }
//
//        if (empty($where)) {
//
//            $where = '1=1';
//        }
//
//        $result = $this->db->select($fields)
//            ->where($where, NULL, FALSE)
//            ->get($tb)
//            ->row_array();
        if (empty($tb)) {
            $tb = $this->mTable;
        }
        $sql = 'SELECT '.$fields.' FROM '.$tb;
        if($where){
            $sql .= ' WHERE '.$where;
        }
        $result = $this->db->query($sql)->row_array();
        return $result;
    }

    /**
     *根据条件查询--多表
     * 该查询相当于自己拼接sql，用的时候要注意，防止sql注入
     */
    public function get_by_where_tb_list($where, $fields = '*', $tb = '')
    {
        if (empty($tb)) {
            $tb = $this->mTable;
        }
        $sql = 'SELECT '.$fields.' FROM '.$tb;
        if($where){
            $sql.=' WHERE '.$where;
        }

        $result = $this->db->query($sql)->result_array();
        return $result;
    }

    /**
     *根据条件查询
     */
    public function get_by_where($where, $fields = '*')
    {
        $result = $this->db->select($fields)
            ->from($this->mTable)
            ->where($where, NULL, FALSE)
            ->get()
            ->row_array();
        return $result;
    }

    public function insert($data)
    {
        $sql = $this->db->insert_string($this->mTable, $data);
        $sql = 'INSERT IGNORE ' . ltrim($sql, 'INSERT');

        $update = array();
        foreach ($data as $key => $val) {
            $update[] = $key . '=' . $this->db->escape($val);
        }
        $sql .= ' ON duplicate KEY UPDATE ' . join(',', $update);

        return $this->db->query($sql);
    }

    public function insert_string($data)
    {
        $sql = $this->db->insert_string($this->mTable, $data);
        $this->db->query($sql);
        $id = $this->db->insert_id();
        return $id;
    }

    public function insert_ignore($data)
    {
        $sql = $this->db->insert_string($this->mTable, $data);
        $sql = 'INSERT IGNORE ' . ltrim($sql, 'INSERT');
        $this->db->query($sql);

        return $this->db->insert_id();
    }

    public function insert_id()
    {
        return $this->db->insert_id();
    }

    public function affected_rows()
    {
        return $this->db->affected_rows();
    }

    public function get_distinct_count($colum)//获取不重复数量（所有相同的$colum计数为1条）
    {
        $result = $this->db->select('COUNT(DISTINCT '.$colum.') AS count', FALSE)
            ->from($this->mTable)
            ->get()
            ->row_array();
        return (int)$result['count'];
    }

    public function get_count($where)
    {
        $result = $this->db->select('COUNT(1) AS count', FALSE)
            ->from($this->mTable)
            ->where($where)
            ->get()
            ->row_array();
        return (int)$result['count'];
    }

    public function count($arrWhere)
    {
        $this->db->select('COUNT(1) AS count', FALSE)
            ->from($this->mTable);
        if(is_array($arrWhere)){
            foreach ($arrWhere as $key => $val) {
                if (is_array($val)) {
                    $this->db->where_in($key, $val);
                } else {
                    $this->db->where($key, $val);
                }
            }
        }else{
            $this->db->where($arrWhere);
        }

        $result = $this->db->get()->row_array();
        return $result['count'];
    }


    public function sum($arrWhere, $field)
    {
        $this->db->select('SUM(' . $field . ') AS num', FALSE)
            ->from($this->mTable);
        if(is_array($arrWhere)){
            foreach ($arrWhere as $key => $val) {
                if (is_array($val)) {
                    $this->db->where_in($key, $val);
                } else {
                    $this->db->where($key, $val);
                }
            }
        }else{
            $this->db->where($arrWhere);
        }

        $result = $this->db->get()->row_array();

        return $result['num'] ? $result['num'] : 0;
    }

    public function delete_by_id($id)
    {
        if (!is_array($id)) {
            $id = array($id);
        }
        return $this->db->where_in($this->mPkId, $id)->limit(count($id))->delete($this->mTable);
    }

    public function delete_by_where($where)
    {
        return $this->db->where($where)->delete($this->mTable);
    }

    public function update_by_id($id, $data)
    {
        $where = array($this->mPkId => $id);
        $sql = $this->db->update_string($this->mTable, $data, $where);
        $res = $this->db->query($sql);
        return $this->db->affected_rows();
    }

    public function update_by_where($where, $data)
    {
        if (!$where) return false;
        if (!is_array($where))
            $this->db->where($where);
        else {
            foreach ($where as $key => $val) {
                if (is_array($val)) {
                    $this->db->where_in($key, $val);
                } else {
                    $this->db->where($key, $val);
                }
            }
        }
        $this->db->update($this->mTable, $data);
        return $this->db->affected_rows();
        //return $this->db->update($this->mTable, $data);
    }

    /**
     * a=a+1 操作
     * @return unknown_type
     */
    public function operate_by_id($id, $map)
    {
        $where = array($this->mPkId => $id);
        $this->db->where($where);
        foreach ($map as $key => $val) {
            $this->db->set($key, $val, FALSE);
        }
        $this->db->update($this->mTable);
    }

    public function get_list($where = array(), $fields = '*', $order_by = '', $limit = 0)
    {
        return $this->fetch_rows($where, $fields, $order_by, $limit);
    }

    public function fetch_row($where, $fields = '*', $order_by = '')
    {
        $this->db->select($fields)
            ->from($this->mTable)
            ->where($where);
        if ($order_by) {
            $this->db->order_by($order_by);
        }
        return $this->db->limit(1)->get()->row_array();
    }

    public function fetch_field($where, $field = '')
    {
        $arr = $this->db->select($field)
            ->from($this->mTable)
            ->where($where)
            ->get()
            ->row_array();
        return $arr[$field];
    }

    public function fetch_rows($where = array(), $fields = '*', $order_by = '', $limit = 0)
    {
        $this->db->select($fields)->from($this->mTable);
        if (!is_array($where))
            $this->db->where($where);
        else {
            foreach ($where as $key => $val) {
                if (is_array($val)) {
                    $this->db->where_in($key, $val);
                } else {
                    $this->db->where($key, $val);
                }

            }
        }

        if ($order_by) {
            $this->db->order_by($order_by);
        }
        if ($limit) {
            if (is_array($limit)) {
                $this->db->limit($limit[0], $limit[1]);
            } else {
                $this->db->limit($limit);
            }
        }

        return $this->db->get()->result_array();
    }
    /*
     * $page 当前页，
     * $pagesize 每页数量
     * $where 条件
     * $fields 字段
     * $order_by  排序
     * $tb 表 不填为默认
     *
     *
     * */
    public function fetch_page($page = 1, $pagesize = 10, $where='', $fields = '*', $order_by = '', $tb = '')
    {
        if (!$tb) $tb = $this->mTable;
        $order_by = $order_by ? $order_by : $this->mPkId . ' DESC';
        $fields_count = 'COUNT(1) AS count';
        $this->db->select($fields_count, FALSE)
            ->from($tb);
        if(is_array($where)) {
            foreach ($where as $key => $val) {
                if ($key{0} == '@' && is_array($val)) {// array('@where'=>array('a'=>1,'b'=>1))
                    $key = substr($key, 1);
                    foreach ($val as $k => $v) {
                        $this->db->$key($k, $v);
                    }
                    continue;
                }
                if (is_array($val)) {
                    $this->db->where_in($key, $val);
                } else {
                    $bAuto = true;
                    if ($tb)
                        $bAuto = false;
                    $this->db->where($key, $val, $bAuto);
                }
            }
        }else{
            $this->db->where($where);
        }
        $result = $this->db->get()->row_array();

        $num = $result['count'];
        $result['rows'] = array();
        if ($num > 0) {
            $sql = $this->db->last_query();
            $sql = str_replace($fields_count, $fields, $sql);
            $sql .= ' ORDER BY ' . $order_by;
            $sql .= ' LIMIT ' . (($page - 1) * $pagesize) . ',' . $pagesize;
            $result['rows'] = $this->db->query($sql)->result_array();
        }
        return $result;
    }

    //maoweihua add 
    /**
     * 设置记录的某个字段值
     * 支持使用数据库字段和方法
     * @access public
     * @param string|array $field  字段名
     * @param string $value  字段值
     * @return boolean
     */
    public function setField($id,$field,$value='') {
        if(is_array($field)) {
            $data = $field;
        }else{
            $data = $value;
        }
        $where = array($this->mPkId=> $id);
        $this->db->where($where);
        $this->db->set($field, $data, FALSE);
        return $this->db->update($this->mTable);
    }

    /**
     * 字段值增长
     * @access public
     * @param string $field  字段名
     * @param integer $step  增长值
     * @return boolean
     */
    public function setInc($id,$field,$step=1) {
        //return $this->setField($id,$field,array($field.'+'.$step));
        return $this->setField($id,$field,($field.'+'.$step));
    }

    /**
     * 字段值减少
     * @access public
     * @param string $field  字段名
     * @param integer $step  减少值
     * @return boolean
     */
    public function setDec($id,$field,$step=1) {
        return $this->setField($id,$field,($field.'-'.$step));
    }


}
// END XT_Model Class
