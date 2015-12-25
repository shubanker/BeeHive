<?php
class Struct{
	/*
	 * @tables
	 * name of table
	 */
	protected $table;
	/*
	 * @fields-single dimention array
	 * contains names of the constraints.
	 */
	protected $fields=array();
	/*
	 * @data-associative array contains values of the fields in $fields
	 */
	protected $data=array();
	
	/*
	 * Boolean variable to check if it has been initialised.
	 */
	protected $is_init;
	/*
	 * @pk-Primary key field.
	 * must exist in $fields & its value in $data
	 *
	 */
	protected  $pk;
	
	protected function initialise_by_id($id=null,$db=null){
		
		// If $id or $db is empty Seting default value to null for all fields.
		if (empty($id)||empty($db)){
			foreach ($this->fields as $field){
				$this->data[$field]=null;
			}
			return false;
		}
		/*
		 * Populate fields from db in $data
		 * 
		 */
		$sql=Db::create_sql($this->fields, [$this->table],"{$this->pk}='$id'",null,null,1);
		$result=$db->qfetch($sql);
		foreach ($this->fields as $field){
			$this->data[$field]=$result[$field];
		}
		$this->data[$this->pk]=(int)$this->data[$this->pk];
		return $this->is_init=true;
	}
	function set($key,$value){
		if (in_array($key, $this->fields)){
			$this->data[$key]=$value;
			return true;
		}
		return false;
	}
	function get($key){
		if (in_array($key, $this->fields)){
			return $this->data[$key];
		}
		return null;
	}
	function get_data(){
		return $this->data;
	}
	function update($db){
		Db::update($db, $this->table, $this->data, $this->pk);
	}
	function create($db){
		$sql=Db::create_sql_insert($this->table, $this->data,$this->pk,$db);
		if ($db->query($sql)){
			return $this->data[$this->pk]=$db->last_insert_id();
		}else return false;
	}
	function delete($db){
		$sql=Db::create_sql_delete($this->table, "`$this->pk`='{$this->data[$this->pk]}'");
		return $db->query($sql);
	}
}
