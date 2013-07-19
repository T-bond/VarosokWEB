<?php
	if(!defined("_H_TODO_H_") && defined("_H_CONFIG_H_"))
	{
		class CTodos
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
			}
			
			//=================================================================================
			//Basic functions
			//=================================================================================
			
			public function checkTables()
			{
				global $mysqli, $config;
				
				$q="SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='".$config["todo/table"]."' AND TABLE_SCHEMA='".$config["sql/database"]."'";
				$r=$mysqli->query($q);
				return $r->num_rows!=0;
			}
			
			public function createTables()
			{
				global $mysqli, $config;
				
				if($this->checkTables()){return false;}
				$q="CREATE TABLE IF NOT EXISTS `".$config["todo/table"]."` (
						`id` int(10) unsigned AUTO_INCREMENT,
						`userid` int(10) unsigned NOT NULL,
						`name` varchar(128) COLLATE utf8_bin,
						`descr` text COLLATE utf8_bin NOT NULL,
						`date_added` datetime NOT NULL,
						`date_changed` datetime NOT NULL,
						`state` enum('free','taken','done') NOT NULL,
						`progress` int(3) NOT NULL,
						`tags` VARCHAR(512),
						PRIMARY KEY (`id`)
					)";
				return $mysqli->query($q);
			}
			
			//=================================================================================
			//Data handling
			//=================================================================================
			
			public function templateItem($id="", $userid="", $name="", $descr="", $date_added="", 
											$date_changed="", $state="", $progress="", $tags="")
			{
				return Array
						(
							"id" => $id,
							"userid" => $userid,
							"name" => $name,
							"descr" => $descr,
							"date_added" => $date_added,
							"date_changed" => $date_changed,
							"state" => $state,
							"progress" => $progress,
							"tags" => $tags
						);
			}
			
			public function get($filter="", $sort="", $limit="")
			{
				global $mysqli, $config;
				
				$q="SELECT * FROM `".$config["todo/table"]."`";
				if($filter!=""){$q.=" WHERE ".$filter;}
				if($sort!=""){$q.=" ORDER BY ".$sort;}
				if($limit!=""){$q.=" LIMIT ".$limit;}
				$r=$mysqli->query($q);
				
				if($r===FALSE){return FALSE;}
				
				$a=Array();
				for($row=$r->fetch_assoc(); $row!=NULL; $row=$r->fetch_assoc()){$a[]=$row;}
				return $a;
			}
			
			public function add($item)
			{
				global $mysqli, $config;
				
				$q="INSERT INTO `".$config["todo/table"]."`
					(userid, name, descr, date_added, date_changed, state, progress, tags)
					VALUES
					(
						".$item["userid"].", 
						'".ensql($item["name"])."', 
						'".ensql($item["descr"])."', 
						'".sqldatetime()."', 
						'".sqldatetime()."', 
						'".ensql($item['state'])."', 
						".$item["progress"].",
						'".ensql($item["tags"])."')";
						
				return $mysqli->query($q);
			}
			
			public function update($item)
			{
				global $mysqli, $config;
				
				$q="UPDATE `".$config["todo/table"]."` SET
						userid=".$item["userid"].", 
						name='".ensql($item["name"])."', 
						descr='".ensql($item["descr"])."',  
						date_changed='".sqldatetime()."', 
						state='".ensql($item['state'])."', 
						progress=".$item["progress"].",
						tags='".ensql($item["tags"])."')
					WHERE id=".$item['id'];
					
				return $mysqli->query($q);
			}
			
			public function delete($item)
			{
				global $mysqli, $config;
				
				$q="DELETE FROM `".$config["todo/table"]."` WHERE id=".$item['id'];
				return $mysqli->query($q);
			}
			
			/* Convenience functions, lower priority */
			public function add_more($items)
			{
				global $mysqli;
				
				$added=0;
				foreach($items as $item){$added+=$this->add($item);}
				return $added;
			}
			
			public function delete_more($filter)
			{
				global $mysqli,$config;
				
				$q="DELETE FROM `".$config["todo/table"]."` WHERE ".$filter;
				return $mysqli->query($q);
			}
		}
	}
	else
	{
		if(!defined("_H_CONFIG_H_")){print "config.php not included!";}
	}
?>