<?php
	if(!defined("_H_USERS_H_") && defined("_H_CONFIG_H_"))
	{
		define("_H_USERS_H_",1);
		//require_once("config.php"); //Require it yourself!
		
		class CUsers
		{
			//=================================================================================
			//Singleton stuff
			//=================================================================================
			protected static $inst;
			protected function __construct(){}
			protected function __clone(){}
			public static function getInstance()
			{
				if(!isset(static::$inst))
				{
					static::$inst=new static;
					static::$inst->init();
				}
				
				return static::$inst;
			}
			
			//=================================================================================
			//Private stuff
			//=================================================================================
			
			private function init()
			{
				global $config,$mysqli;
				
				$config["users/table_escaped"]=$mysqli->escape_string($config["users/table"]); //Actually, not sure if this is necessary
			}
			
			//=================================================================================
			//Basic functions
			//=================================================================================
			public function checkTables()
			{
				global $config, $mysqli;
				
				$q="SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='".$config["users/table_escaped"]."' AND TABLE_SCHEMA='".$config["sql/database"]."'";
				$r=$mysqli->query($q);
				return $r->num_rows!=0;
			}
			
			public function createTables()
			{
				global $config, $mysqli;
				
				if($this->checkTables()){return false;}
				
				$q="CREATE TABLE `".$config["users/table_escaped"]."` (
					  `id` int(16) NOT NULL AUTO_INCREMENT,
					  `username` varchar(32) NOT NULL,
					  `realname` varchar(512) NOT NULL,
					  `pass` varchar(32) NOT NULL,
					  `mail` varchar(128) NOT NULL,
					  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
					  `data` text NOT NULL,
					  `status` int(11) NOT NULL,
					  PRIMARY KEY (`id`)
					)";
					
				return $mysqli->query($q);
			}
			
			//=================================================================================
			//Userhandling
			//=================================================================================
			
			//Creates a sample-user with the given data.
			//Currently, it's an array
			//Note: passwords are HASHED when acquired from other functions. You should hash them too.
			public function templateUser($id,$name,$realname,$pass,$mail,$data,$added,$status)
			{
				return Array("id" => $id, "username" => $name, "realname" => $realname, "pass" => $pass, "mail" => $mail, "added" => $added, "data" => $data, "status" => $status);
			}
			
			public function hashpass($pass)
			{
				//Todo: create the table to contain only 32 characters as pass ( an md5's length )
				return md5($pass); 
			}
			
			public function get($filter="", $sort="", $limit="")
			{
				global $mysqli,$config;
				
				$q="SELECT * FROM `".$config["users/table_escaped"]."`";
				if($filter!=""){$q.=" WHERE ".$filter;}
				if($sort!=""){$q.=" ORDER BY ".$sort;}
				if($limit!=""){$q.=" LIMIT ".$limit;}
				$r=$mysqli->query($q);
				
				if($r===FALSE){return FALSE;}
				
				$a=Array();
				for($row=$r->fetch_assoc(); $row!=NULL; $row=$r->fetch_assoc()){$row["data"]=$this->explode_data($row["data"]); $a[]=$row;}
				return $a;
			}
			
			public function add($user)
			{
				global $mysqli,$config;
				
				$user["data"]=$this->implode_data($user["data"]);
				
				$q="INSERT INTO `".$config["users/table_escaped"]."`
					(username, realname, pass, mail, data, status)
					VALUES('".$mysqli->escape_string($user["username"])."', '".$mysqli->escape_string($user["realname"])."', '".$mysqli->escape_string($user['pass'])."', '".$mysqli->escape_string($user["mail"])."', '".$mysqli->escape_string($user["data"])."', '".$mysqli->escape_string($user["status"])."')";
				return $mysqli->query($q);
			}
			
			//The user's id will be used. Provide fresh data in the remaining fields
			public function update($user)
			{
				global $mysqli, $config;
				
				$user["data"]=$this->implode_data($user["data"]);
				
				$q="UPDATE `".$config["users/table_escaped"]."`
					SET username='".$mysqli->escape_string($user["username"])."', realname='".$mysqli->escape_string($user["realname"])."', pass='".$mysqli->escape_string($user['pass'])."', mail='".$mysqli->escape_string($user["mail"])."', added='".$mysqli->escape_string($user["added"])."', data='".$mysqli->escape_string($user["data"])."', status='".$mysqli->escape_string($user["status"])."'
					WHERE id=".$user["id"];
				return $mysqli->query($q);
			}
			
			public function delete($user)
			{
				global $mysqli, $config;
				
				$q="DELETE FROM `".$config["users/table_escaped"]."` WHERE id=".$user['id'];
				return $mysqli->query($q);
			}
			
			/* Convenience functions, lower priority */
			public function add_more($users)
			{
				global $mysqli, $config;
				
				$added=0;
				foreach($users as $u){$added+=$this->add($u);}
				return $added;
			}
			
			public function delete_more($filter)
			{
				global $mysqli,$config;
				
				$q="DELETE FROM `".$config["users/table_escaped"]."` WHERE ".$filter;
				return $mysqli->query($q);
			}
			
			public function explode_data($data, $sep = "|", $sep2 = ":")
			{
			$temp = explode($sep, $data);
			foreach($temp as $val) {
				$val = explode($sep2, $val);
				$arr[$val[0]]=implode($sep2, array_slice($val, 1));
				}
			return $arr;
			}
			
			public function implode_data($data, $sep = "|", $sep2 = ":")
			{
			$str="";
			foreach($data as $key=>$val)
				if($key!="" and $val!="")$str.=($str!=""?$sep:"").$key.$sep2.$val;
			return $str;
			}
		}
	}
	else
	{
		if(!defined("_H_CONFIG_H_")){print "config.php not included!";}
	}
?>