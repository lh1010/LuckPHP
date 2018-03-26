<?php
/**
 * DB mysqli
 * 完全封装,只提供继承类使用
 * 
 */
namespace Luck\Luck\DB;
class Mysqli
{
	//用于存放实例化的对象
	static private $_instance = null;
	//数据库句柄
	private $db = null;
	//sql语句
	private $sql = null;
	
	//公共静态方法获取实例化的对象
	static protected function getInstance()
	{
		if (!(self::$_instance instanceof self)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	//构造链接数据库
	private function __construct()
	{
		if(empty($this->db)) {
			$mysqli = new \mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
			if(mysqli_connect_errno()) {
				exit('数据库连接错误！错误信息：'.mysqli_connect_error());
			}
			$mysqli->set_charset(DB_CHARSET);
		}
		$this->db = $mysqli;
	}
	
	/**
	 * select()
	 * 查询出多条数据assoc
	 */
	protected function select_db($table, $where = null, $field = null, $order = null, $group = null, $limit = null)
	{
		$field = $this->setField($field);
		$this->sql = "SELECT ".$field." FROM ".$table.$where.$order.$group.$limit;
		$result = $this->db->query($this->sql);
		$html = array();
		while(!!$array = $result->fetch_assoc()) {
			$html[] = $array;
		}
		return $html;
		$result->close();
		$this->db->close();
	}
	
	/**
	 * find()
	 * 查询单条数据assoc
	 */
	protected function find_db($table, $where = null, $field = null)
	{
		$field = $this->setField($field);
		$this->sql = "SELECT ".$field." FROM ".$table.$where;
		$result = $this->db->query($this->sql);
		$html = $result->fetch_assoc();
		return $html;
		$result->close();
		$this->db->close();
	}
	
	/**
	 * add()
	 * 新增数据
	 */
	protected function add_db($table, $data)
	{
		$this->sql = "INSERT INTO ".$table.$data;
		$result = $this->db->query($this->sql);
		$affected_rows = $this->db->affected_rows;
		return $affected_rows;
		$result->close();
		$this->db->close();
	}
	
	/**
	 * save()
	 * 修改数据
	 */
	protected function save_db($table, $data, $where = null, $limit = null)
	{
		$this->sql = "UPDATE ".$table.$data.$where.$limit;
		$result = $this->db->query($this->sql);
		$affected_rows = $this->db->affected_rows;
		return $affected_rows;
		$result->close();
		$this->db->close();
	}
	
   /**
	* delete()
	* 删除数据
	*/
	protected function delete_db($table, $where = null, $limit = null)
	{
		$this->sql = "DELETE FROM ".$table.$where.$limit;
		$result = $this->db->query($this->sql);
		$affected_rows = $this->db->affected_rows;
		return $affected_rows;
		$result->close();
		$this->db->close();
	}
	
	/**
	 * count()
	 * 查询记录个数
	 */
	protected function count_db($table,$where = null)
	{
		$this->sql = "SELECT COUNT(*) FROM ".$table.$where;
		$result = $this->db->query($this->sql);
		$count = $result->fetch_row();
		return $count[0];
		$result->close();
		$this->db->close();
	}

	/**
	 * getField()
	 * 获取单个数据
	 */
	protected function getField_db($table, $where = null, $data = null)
	{
		$this->sql = "SELECT * FROM ".$table.$where;
		$result = $this->db->query($this->sql);
		$html = $result->fetch_assoc();
		return $html[$data];
		$result->close();
		$this->db->close();
	}
	
	/**
	 * 获取上一条插入记录ID
	 */
	public function insertId_db($table)
	{
		return mysqli_insert_id($this->db);
		$this->db->close();
	}
	
	/**
	 * 增加单条Int类型数据
	 */
	protected function setIntPlus_db($table,$data,$num,$where)
	{
		$this->sql = "UPDATE ".$table." SET ".$data." = ".$data."+".$num.$where;
		$result = $this->db->query($this->sql);
		$affected_rows = $this->db->affected_rows;
		return $affected_rows;
		$result->close();
		$this->db->close();
	}
	
	/**
	 * 减少单条Int类型数据
	 */
	protected function setIntReduce_db($table,$data,$num,$where)
	{
		$this->sql = "UPDATE ".$table." SET ".$data." = ".$data."-".$num.$where;
		$result = $this->db->query($this->sql);
		$affected_rows = $this->db->affected_rows;
		return $affected_rows;
		$result->close();
		$this->db->close();
	}
	
	/**
	 * 查询上一条sql语句
	 */
	protected function getLastSQL()
	{
		return $this->sql;
		$this->db->close();
	}
	
	//===辅助方法===
	//处理field需要查询的值
	private function setField($str)
	{
		if(!empty($str)) {
			return $str;
		} else {
			return '*';
		}
	}
	
}