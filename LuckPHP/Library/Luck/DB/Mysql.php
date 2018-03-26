<?php
/**
 * DB mysql
 * 完全封装,只提供继承类使用
 * 支持PHP5.5以下版本(不包含PHP5.5)
 * 
 */
namespace Luck\Luck\DB;
class Mysql
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
		$conn = mysql_connect(DB_HOST, DB_USER, DB_PASS) or die("数据库连接错误！"); 
		mysql_query("set names '".DB_CHARSET."'");
		mysql_select_db(DB_NAME); 
		$this->db = $conn;
	}
	
	/**
	 * select()
	 * 查询出多条数据assoc
	 */
	protected function select_db($table, $where = null, $field = null, $order = null, $group = null, $limit = null)
	{
		$field = $this->setField($field);
		$this->sql = "SELECT ".$field." FROM ".$table.$where.$order.$group.$limit;
		$query = mysql_query($this->sql, $this->db);
		$html = array();
		while(!!$array = mysql_fetch_assoc($query)) {
			$html[] = $array;
		}
		return $html;
		mysql_close();
	}

	/**
	 * find()
	 * 查询单条数据assoc
	 */
	protected function find_db($table, $where = null, $field = null)
	{
		$field = $this->setField($field);
		$this->sql = "SELECT ".$field." FROM ".$table.$where;
		$query = mysql_query($this->sql, $this->db);
		$html = mysql_fetch_assoc($query);
		return $html;
		mysql_close();
	}
	
	/**
	 * add()
	 * 新增数据
	 */
	protected function add_db($table, $data)
	{
		$this->sql = "INSERT INTO ".$table.$data;
		$query = mysql_query($this->sql, $this->db);
		$affected_rows = mysql_affected_rows();
		return $affected_rows;
		mysql_close();
	}
	
	/**
	 * save()
	 * 修改数据
	 */
	protected function save_db($table, $data, $where = null, $limit = null)
	{
		$this->sql = "UPDATE ".$table.$data.$where.$limit;
		$query = mysql_query($this->sql, $this->db);
		$affected_rows = mysql_affected_rows();
		return $affected_rows;
		mysql_close();
	}
	
   /**
	* delete()
	* 删除数据
	*/
	protected function delete_db($table, $where = null, $limit = null)
	{
		$this->sql = "DELETE FROM ".$table.$where.$limit;
		$query = mysql_query($this->sql, $this->db);
		$affected_rows = mysql_affected_rows();
		return $affected_rows;
		mysql_close();
	}
	
	/**
	 * count()
	 * 查询记录个数
	 */
	protected function count_db($table,$where = null)
	{
		$this->sql = "SELECT * FROM ".$table.$where;
		$query = mysql_query($this->sql, $this->db);
		$count = mysql_num_rows($query);
		return $count;
		mysql_close();
	}

	/**
	 * getField()
	 * 获取单个数据
	 */
	protected function getField_db($table, $where = null, $data = null)
	{
		$this->sql = "SELECT * FROM ".$table.$where;
		$query = mysql_query($this->sql, $this->db);
		$html = mysql_fetch_assoc($query);
		return $html[$data];
		mysql_close();
	}
	
	/**
	 * 获取上一条插入记录ID
	 */
	public function insertId_db($table)
	{
		return mysql_insert_id($this->db);
		mysql_close();
	}
	
	/**
	 * 增加单条Int类型数据
	 */
	protected function setIntPlus_db($table,$data,$num,$where)
	{
		$this->sql = "UPDATE ".$table." SET ".$data." = ".$data."+".$num.$where;
		$query = mysql_query($this->sql, $this->db);
		$affected_rows = mysql_affected_rows();
		return $affected_rows;
		mysql_close();
	}
	
	/**
	 * 减少单条Int类型数据
	 */
	protected function setIntReduce_db($table,$data,$num,$where)
	{
		$this->sql = "UPDATE ".$table." SET ".$data." = ".$data."-".$num.$where;
		$query = mysql_query($this->sql, $this->db);
		$affected_rows = mysql_affected_rows();
		return $affected_rows;
		mysql_close();
	}
	
	/**
	 * 查询上一条sql语句
	 */
	protected function getLastSQL()
	{
		return $this->sql;
		mysql_close();
	}
	
	//===辅助方法===
	//处理field 需要查询的值
	private function setField($str)
	{
		if(!empty($str)) {
			return $str;
		} else {
			return '*';
		}
	}
		
}