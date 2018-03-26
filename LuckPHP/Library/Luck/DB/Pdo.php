<?php
/**
 * DB PDO
 * 完全封装,只提供继承类使用
 * 系统默认使用
 * 
 */
namespace Luck\Luck\DB;
class Pdo
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
		if(!(self::$_instance instanceof self)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	//构造链接数据库
	private function __construct()
	{ 
		$dsn = DB_TYPE.":host=".DB_HOST.";dbname=".DB_NAME; 
		try {
		    $this->db = new \PDO($dsn, DB_USER, DB_PASS, array(\PDO::ATTR_PERSISTENT => true));
		    $this->db->query("set character set '".DB_CHARSET."'");
		} catch (\PDOException $e) {
		    exit("数据库连接错误！错误信息：".$e->getMessage());
		}
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
		while(!!$array = $result->fetch(\PDO::FETCH_ASSOC)) {
			$html[] = $array;
		}
		return $html;
		$this->db->null;
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
		$html = $result->fetch(\PDO::FETCH_ASSOC);
		return $html;
		$this->db->null;
	}
	
	/**
	 * add()
	 * 新增数据
	 */
	protected function add_db($table, $data)
	{
		$this->sql = "INSERT INTO ".$table.$data;
		return $this->db->exec($this->sql); 
		$this->db->null;
	}
	
	/**
	 * save()
	 * 修改数据
	 */
	protected function save_db($table, $data, $where = null, $limit = null)
	{
		$this->sql = "UPDATE ".$table.$data.$where.$limit;
		return $this->db->exec($this->sql); 
		$this->db->null;
	}
	
   /**
	* delete()
	* 删除数据
	*/
	protected function delete_db($table, $where = null, $limit = null)
	{
		$this->sql = "DELETE FROM ".$table.$where.$limit;
		return $this->db->exec($this->sql); 
		$this->db->null;
	}
	
	/**
	 * count()
	 * 查询记录个数
	 */
	protected function count_db($table,$where = null)
	{
		$this->sql = "SELECT COUNT(*) FROM ".$table.$where;
		$result = $this->db->query($this->sql);
		$count = $result->fetch();
		return $count[0];
		$this->db->null;
	}

	/**
	 * getField()
	 * 获取单个数据
	 */
	protected function getField_db($table, $where = null, $data = null)
	{
		$this->sql = "SELECT * FROM ".$table.$where;
		$result = $this->db->query($this->sql);
		$html = $result->fetch(\PDO::FETCH_ASSOC);
		return $html[$data];
		$this->db->null;
	}
	
	/**
	 * 获取上一条插入记录ID
	 */
	public function insertId_db($table)
	{
		 return $this->db->lastInsertId();
		 $this->db->null;
	}
	
	/**
	 * 增加单条Int类型数据
	 */
	protected function setIntPlus_db($table,$data,$num,$where)
	{
		$this->sql = "UPDATE ".$table." SET ".$data." = ".$data."+".$num.$where;
		return $this->db->exec($this->sql); 
		$this->db->null;
	}
	
	/**
	 * 减少单条Int类型数据
	 */
	protected function setIntReduce_db($table,$data,$num,$where)
	{
		$this->sql = "UPDATE ".$table." SET ".$data." = ".$data."-".$num.$where;
		return $this->db->exec($this->sql); 
		$this->db->null;
	}
	
	/**
	 * 查询上一条sql语句
	 */
	protected function getLastSQL()
	{
		return $this->sql;
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