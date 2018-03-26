<?php
/**
 * @Author: LuckPHP
 * @Explain: Model
 */
namespace Luck\Luck;
use Luck\Luck\DB\Pdo;
class Model extends Pdo
{
	private $table = null;
	private $db = null;
	private $where = null;
	private $field = null;
	private $order = null;
	private $group = null;
	private $limit = null;
	private $tables = null;
	private $str = null;

	public function __construct($table) {
		$this->db = parent::getInstance();
		$this->table = DB_FREFIX.$table;
	}

	/**
	 * select
	 * 查询多条数据
	 */
	public function select() {
		return $this->db->select_db($this->table, $this->where, $this->field, $this->order, $this->limit);
	}

	/**
	 * find
	 * 查询单条数据
	 */
	public function find() {
		return $this->db->find_db($this->table, $this->where, $this->field);
	}

	/**
	 * add
	 * 新增数据
	 */
	public function add($data) {
		if(!empty($data)) $data = $this->setAddData($data);
		return $this->db->add_db($this->table, $data);
	}

	/**
	 * save
	 * 更新数据
	 */
	public function save($data) {
		if(!empty($data)) $data = $this->setSaveData($data);
		return $this->db->save_db($this->table, $data, $this->where, $this->limit);
	}

	/**
	 * delete
	 * 删除数据
	 */
	public function delete() {
		return $this->db->delete_db($this->table,$this->where,$this->limit);
	}

	/**
	 * total
	 * 查询记录条数
	 */
	public function count() {
		return $this->db->count_db($this->table,$this->where);
	}

	/**
	 * 获取单个数据
	 */
	public function getField($data) {
		return $this->db->getField_db($this->table,$this->where,$data);
	}

	/**
	 * 获取上一条插入记录ID
	 */
	public function insertId() {
		return $this->db->insertId_db($this->table);
	}

	/**
	 * 增加单条Int类型数据
	 */
	public function setIntPlus($data,$num = null) {
		if($num == null) {
			$num = 1;
		}
		return $this->db->setIntPlus_db($this->table,$data,$num,$this->where);
	}

	/**
	 * 减少单条Int类型数据
	 */
	public function setIntReduce($data,$num = null) {
		if($num == null) {
			$num = 1;
		}
		return $this->db->setIntReduce_db($this->table,$data,$num,$this->where);
	}

	/**
	 * 查询上一条sql语句
	 */
	public function getLastSQL() {
		return $this->db->getLastSQL();
	}

	// ===table===
	public function table($str = null) {
		if($str != null) {
			$this->table = $str;
		} else {
			exit('ERROR：table()内容不能为空！');
		}
		return $this;
	}

	// ===处理where===
	public function where($str = null) {
		if($str != null) {
			$this->where = ' WHERE '.$str;
		} else {
			exit('ERROR：where()条件不能为空！');
		}
		return $this;
	}

	// ===处理field===
	public function field($str = null) {
		if($str != null) {
			$this->field = $str;
		} else {
			exit('ERROR：field()数据不能为空！');
		}
		return $this;
	}

	// ===处理order===
	public function order($str = null) {
		if($str != null) {
			$this->order = ' ORDER by '.$str;
		} else {
			exit('ERROR：order()数据不能为空！');
		}
		return $this;
	}

	// ===处理group===
	public function group($str = null) {
		if($str != null) {
			$this->group = ' GROUP BY '.$str;
		} else {
			exit('ERROR：group()数据不能为空！');
		}
		return $this;
	}

	// ===处理limit===
	public function limit($str = null) {
		if($str != null) {
			$this->limit = ' LIMIT '.$str;
		} else {
			exit('ERROR：limit()数据不能为空！');
		}
		return $this;
	}

	// ===处理添加数据===
	private function setAddData($array) {
		$str = '';
		$addFields = array();
		$addValues = array();
		foreach ($array as $key=>$value) {
			$addFields[] = $key;
			$addValues[] = $value;
		}
		$addFields = implode(',', $addFields);
		$addValues = implode("','", $addValues);
		$addValues = htmlspecialchars($addValues);
		$str = " ($addFields) VALUES ('$addValues') ";
		return $str;
	}

	// ====处理修改数据===
	private function setSaveData($array) {
		$setData = '';
		foreach ($array as $key=>$value) {
			$setData .= "$key='".htmlspecialchars($value)."',";
		}
		$setData = substr($setData, 0, -1);
		$setData = ' SET '.$setData;
		return $setData;
	}

}
