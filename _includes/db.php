<?php
/*
 * Class for Core $db operations.
 */
class Db{
	private $db;
	private $isinit;
	function __construct($user=DBUSER,$password=DBPASSWORD,$database=DATABASE,$host=DBHOST){
		$this->isinit=false;
		if (!empty($user)&&!empty($password)&&!empty($database)){
			 return $this->login($user, $password, $database,$host);
		}
	}
	function login($user,$password,$database,$host="localhost"){
		
		$this->db=@new mysqli($host, $user, $password, $database);
		return $this->isinit=!$this->db->connect_error;
// 		if (mysqli_connect_errno()) die('Could not connect: ' . mysqli_connect_error());
	}
	function isinit(){
		return $this->isinit;
	}
	function getdb(){
		return $this->db;
	}
	function escape($string,$escape_others=false){
		if ($this->isinit){
			$new_str= $this->db->real_escape_string($string);
		}else {
			$new_str=addcslashes($string, "'\\");
		}
		if ($escape_others){
			$new_str=addcslashes($new_str, '%_');
		}
		return $new_str;
	}
	
	function query($sql){
		if (!$this->isinit){
			return false;
		}
		return $this->db->query($sql);
	}
	static function queryy($sql,$db){
		return $db->query($sql);
	}
	
	static function fetch($result){
		return mysqli_fetch_assoc($result);
	}
	
	/*
	 * Function is useful to fetch array of records when only one row is returned/required.
	 */
	function qfetch($sql){
		if (!$this->isinit){
			return false;
		}
		return $this->fetch($this->query($sql));
	}
	static function qnfetch($sql,$db){
		return $db->fetch($db->query($sql));
	}
	/*
	 * To fetch a data in form of array with multiple rows.
	 */
	static function fetch_array($db,$sql){
		
		$result=$db->query($sql);
		$data=array();
		while ($row=$db->fetch($result)){
			$data[]=$row;
		}
		return $data;
	}
	static function remove_last_symbol($str,$length_lim=1){
		if (!is_numeric($length_lim)){
			$length_lim=strlen($length_lim);
		}
		$str=trim($str);
		return substr($str, 0,strlen($str)-$length_lim)." ";
	}
	
	/*
	 * Function to update row(s)
	 * @$table
	 * name of table
	 * 
	 * @$data
	 * 
	 * array of values to be updated
	 * example $data=[
	 * id=>1,
	 * "name"=>"subh",
	 * "name2"=>"vikash"
	 * ]
	 * 
	 * @$pk
	 * primary key or constant variable which can be refered in where claus,
	 * value of pk must be included in $data.
	 * 
	 * @$modify_pk
	 * to overwrite previous ruls thus allowing to update any row.
	 */
	static function create_update_sql($db,$table,$data,$pk,$modify_pk=FALSE){
		if (!is_array($pk) && !isset($data[$pk])){
			return false;
		}
		if (!is_array($pk)){
			$pk=array($pk=>$data[$pk]);
		}
		$pkkeys=array_keys($pk);
		$sql="UPDATE `{$table}` SET ";
		foreach ($data as $key=>$val){
			if ($modify_pk || !in_array($key, $pkkeys)){
				if (!empty($db)){
					$val=$db->escape($val);
				}
				$sql.="`$key`='{$val}',";
			}
		}
		$sql=self::remove_last_symbol($sql);
		$condition="";
		foreach ($pk as $key=>$val){
			if (!empty($db)){
				$val=$db->escape($val);
			}
			$condition.=" `$key`='{$val}' AND";
		}
		$condition=self::remove_last_symbol($condition,3);//removing last "AND"
		if (!empty($condition)){
			$sql.="WHERE $condition";
		}
		$sql.=";";
		return $sql;
	}
	static function create_update_sql2($table,$data,$where,$db){
		$sql="UPDATE `{$table}` SET ";
		foreach ($data as $key=>$val){
			if (!empty($db)){
				$val=$db->escape($val);
			}
			$sql.="`$key`='{$val}',";
		}
		$sql=self::remove_last_symbol($sql);
		$sql.=self::where_to_str($where);
		return $sql;
	}
	static function update($db,$table,$data,$pk){
		return $db->query(self::create_update_sql($db, $table, $data, $pk));
	}
	static function delete($db,$table,$data){
		return $db->query(self::create_sql_delete($table, $data));
		
	}
	
	/*
	 * To convert array of column names into proper sql structure
	 */
	private static function array_to_str($arr){
		
		if (is_array($arr)){
			$op = "`".implode("`,`", $arr)."`";
			return str_replace('.', '`.`', $op);
		}
		return $arr;
	}
	private static function where_to_str($whereData,$condition_concat="AND"){
		$sql="";
		if (!empty($whereData)){
			$sql="WHERE ";
		}
		if (is_array($whereData) && !empty($whereData)){
			foreach ($whereData as $where=>$value){
				$sql.="`$where` ";
		
				if (is_array($value)){
					$sql1="IN('".implode("','", $value)."') ";
					$sql.=str_replace(".", "`.`", $sql1);
				}else {
					$sql.="='$value' ";
				}
		
				$sql.="$condition_concat ";
			}
			$sql=self::remove_last_symbol($sql,"$condition_concat ");
		}else {
			$sql.=$whereData;
		}
		return "$sql ";
	}
	private static function update_existing($update_existing){
		$sql="";
		if (!empty($update_existing)){
			$sql="ON DUPLICATE KEY UPDATE ";
			foreach ($update_existing as $to_update){
				$sql.="$to_update=VALUES($to_update),";
			}
		}
		return self::remove_last_symbol($sql)." ";
	}
	static function create_sql($rows,$from,$where=null,$orderby=null,$groupby=null,$limit=null,$distinct=false){
		
		$rows=self::array_to_str($rows);
		$from=self::array_to_str($from);
		
		$distinct_text=$distinct?"DISTINCT ":"";
		
		$sql="SELECT $distinct_text $rows FROM $from ";
		
		$sql.=self::where_to_str($where);
		
		if (!empty($groupby)){
			$groupby=self::array_to_str($groupby);
			$sql.="GROUP BY $groupby ";
		}
		
		if (!empty($orderby)){
			$orderby=self::array_to_str($orderby);
			$sql.="ORDER BY $orderby ";
		}
		
		if (!empty($limit)){
			$sql.="LIMIT $limit ";
		}
		return "$sql";
	}
	/*
	 * $pk here is the auto increment integer value
	 */
	static function create_sql_insert($table,$data,$pk=NULL,$db=NULL,$update_existing=NULL){
		if (isset($pk)){
			unset($data[$pk]);//removing any set value for this column.
		}
		if (!empty($db)){
			foreach ($data as $key=>$values){
				$data[$key]=$db->escape($data[$key]);
			}
		}
		
		$sql="INSERT INTO `$table` ";
		$sql.=" (`".implode("`, `", array_keys($data))."`) \n";
		$sql2= " VALUES ('".implode("', '", $data)."') ";
		
// 		$sql2=str_replace(",''", ",null", $sql2);
		$sql2=preg_replace('/([^\\\\])\'\'/i', '\1null', $sql2); //replacing '' values to null.
		
		$sql= $sql.$sql2;
		$sql.=self::update_existing($update_existing);
		return $sql;
	}
	/*
	 * Function to make multiple insertion/Updation at once.
	 * @data
	 * 	array with multiple values in form of associative data example
	 *  data=array(
	 *    array(
	 *    	col1=>val,
	 *    	col=>val
	 *    ),
	 *    array(
	 *    	col1=>val,
	 *    	col=>val
	 *    ),
	 *    array(
	 *    	col1=>val,
	 *    	col=>val
	 *    ),
	 *    array(
	 *    	col1=>val,
	 *    	col=>val
	 *    )
	 *  )
	 */
	static function create_sql_insert_multiple($table,$data,$update_existing=null,$db=NULL){
		$sql="INSERT INTO `$table` ";
		$sql.=" (`".implode("`, `", array_keys($data[0]))."`) VALUES ";//Geting Columns Name from first array.
		foreach ($data as $row){

			if (!empty($db)){
				foreach ($row as $key=>$values){
					$row[$key]=$db->escape($row[$key]);//escaping values.
				}
			}
			
			$sql.="('".implode("','", $row)."'),";
		}
		$sql=self::remove_last_symbol($sql)." ";
		$sql=preg_replace('/([^\\\\])\'\'/i', '\1null', $sql); //replacing '' values to null.
		$sql.=self::update_existing($update_existing);
		
		return "$sql;";
	}
	static function create_sql_delete($table,$where_data,$condition_concat="AND"){
		$sql="DELETE FROM `$table` ";
		
		$sql.=self::where_to_str($where_data,$condition_concat);
		return $sql;
	}
	function last_insert_id(){
		return $this->db->insert_id;
	}
	function affected_rows(){
		return $this->db->affected_rows;
	}
	static function last_inserted_id($db){
		return $db->last_insert_id();
	}
	static function rows_affected($db){
		return $db->affected_rows();
	}
	function dberror(){
// 		if(isLocal()){
			return mysqli_error($this->db);
// 		}
	}
	function close(){
		mysqli_close($this->db);
	}
}