<?php
if(!defined("_H_UMAIL_H_")){
		define("_H_UMAIL_H_",1);
/**
Userek közti levelező
*/

class CUMail{
	private function __construct(){}
	private function __clone(){}
	//Singleton
	public static function getInstance(){
		static $inst = null;
		if($inst == null){
			$inst = new CUMail();
		}
		$inst->Init();
		return $inst;
	}
	//Init, check table
	public function Init(){
		global $config, $mysqli;
		/*$query = $mysqli->query("select 1 from `".$config["umail/table"]."`");
		if(!$query){
			$this->createTable();
		}*/
	}
	/**
	0 - id
	1 - feladó id
	2 - fogadók idjéji(lol) elválasztó: |
	3 - tárgy
	4 - üzi
	5 - dátum
	6 - olvasott
	7 - kuka
	*/
	//Létrehozza a tábláját
	public function createTable(){
		global $config, $mysqli;
		$query = "create table if not exists `".$config["umail/table"]."`(
											`id` int(16) NOT NULL AUTO_INCREMENT, PRIMARY KEY(id),
											`fromid` int(16) NOT NULL,
											`toids` text(70) COLLATE utf8_bin NOT NULL,
											`targy` text(70) COLLATE utf8_bin NOT NULL,
											`msg` text(700) COLLATE utf8_bin NOT NULL,
											`date` datetime NOT NULL,
											`readed` text(70) COLLATE utf8_bin NOT NULL,
											`bin` text(70) COLLATE utf8_bin NOT NULL
											)";
		$q = $mysqli->query($query);
		if(!$q){
			print $mysqli->error;
			return false;
		}
		return true;
	}
	
	//Levelező functionok
	//Returns a query for list the User id's mails
	public function listMail($userid,$filter,$order,$start,$limit){
		global $config, $mysqli;
		$userId = $mysqli->real_escape_string($userid);
		$query = "select * from ".$config["umail/table"]." where toids like '%|".$userId."|%' and bin not like '%|".$userid."|%'";
		if($filter!="" && $order!=""){ $query.="order by ".$filter." ".$order." ";}
		if($limit!="" && $start!=""){ $query.="LIMIT ".$start.",".$limit." "; }
		$q = $mysqli->query($query);
		if(!$q)
			print $mysqli->error;
		$a=Array();
		for($row=$q->fetch_assoc(); $row!=NULL; $row=$q->fetch_assoc()){$a[]=$row;}
		return $a;
	}
	//Kukába dobott levelek
	/*public function listThrowedMail($userid,$filter,$order){
		global $config, $mysqli;
		$query = "select * from ".$config["umail/table"]." where toids like '%|".$userid."|%' and bin like '%|".$userid."|%' ";
		if($filter!="" && $order!=""){ $query.="order by ".$filter." ".$order." "; }
		$q = $mysqli->query($query);
		if(!$q)
			print $mysqli->error;
		$a=Array();
		for($row=$q->fetch_assoc(); $row!=NULL; $row=$q->fetch_assoc()){$a[]=$row;}
		return $a;
	}*/
	//Elküldött levelek
	public function listSendedMail($userid,$filter,$order,$start,$limit){
		global $config, $mysqli;
		$query = "select * from ".$config["umail/table"]." where fromid=".$userid." and bin not like '%|".$userid."|%'";
		if($filter!="" && $order!=""){ $query.="order by ".$filter." ".$order." "; }
		if($limit!="" && $start!=""){ $query.="LIMIT ".$start.",".$limit." "; }
		$q = $mysqli->query($query);
		if(!$q)
			print $mysqli->error;
		$a=Array();
		for($row=$q->fetch_assoc(); $row!=NULL; $row=$q->fetch_assoc()){$a[]=$row;}
		return $a;
	}
	//Get data from ONE mail
	public function readMail($mailid,$userid){
		global $config, $mysqli;
		$query = "select * from ".$config["umail/table"]." where id=".$mailid." ";
		$q = $mysqli->query($query);
		if(!$q)
			print $mysqli->error;
		$data = $q->fetch_assoc();
		$whoreaded = $data["readed"];
		if(strpos($whoreaded,'|'.$userid.'|') !== false){
			return $data;
		}else{
			$whoreaded = $data["readed"].($data["readed"]==""?'|':'').$userid.'|';
			$query = "update ".$config["umail/table"]." set readed='".$whoreaded."' where id=".$mailid."";
			$q = $mysqli->query($query);
			if(!$q)
				print $mysqli->error;	
		}	
		$query = "select * from ".$config["umail/table"]." where id=".$mailid." ";
		$q = $mysqli->query($query);
		if(!$q)
			print $mysqli->error;
		$data = $q->fetch_assoc();
		return $data;
	}
	//Send mail
	public function sendMail($data){	
		global $config, $mysqli;
		$q = $mysqli->query("insert into ".$config["umail/table"]." (fromid,toids,targy,msg,date,readed,bin) values('".$data[1]."', '".$data[2]."', '".$data[3]."', '".$data[4]."', '".date("Y-m-d H:i:s")."', '', '') ");
		if(!$q)
			print $mysqli->error;
		return $q;
	}
	public function throwMail($mailid,$userid){
		global $config, $mysqli;
		$q = $mysqli->query("select * from ".$config["umail/table"]." where id=".$mailid." ");
		if(!$q)
			print $mysqli->error;
		$data = $q->fetch_assoc();
		$userid=$_SESSION["ID"];
		$whothrowed = $data["bin"];
		if(strpos($whothrowed,'|'.$userid.'|') !== false){
			return true;
		}else{
			$whothrowed = $data["bin"].'|'.$userid.'|';
			$query = "update ".$config["umail/table"]." set bin='".$whothrowed."' where id=".$mailid."";
			$q = $mysqli->query($query);
			if(!$q)
				print $mysqli->error;
			//Ha mindneki kidobta akkor töröljük.
			$q = $mysqli->query("select * from ".$config["umail/table"]." where id=".$mailid." ");
			if(!$q)
				print $mysqli->error;
			$data = $q->fetch_assoc();
			if(strpos($data["bin"],'|'.$data["fromid"].'|') !== false){
				if(strlen($data["bin"]) == strlen($data["toids"].'|'.$data["fromid"].'|') ){
					$this->deleteMail($mailid);
				}
			}
		}
		return 1;
	}
	public function deleteMail($mailid){
		global $config, $mysqli;
		$q = $mysqli->query("delete from ".$config["umail/table"]." where id=".$mailid." ");
		if(!$q)
			print $mysqli->error;
	}
	public function getUnreadedMails($userid){
		global $config, $mysqli;
		$q = $mysqli->query("select * from ".$config["umail/table"]." where toids like '%|".$userid."|%' and readed not like '%|".$userid."|%' ");
		if(!$q)
			print $mysqli->error;
		$r = $q->num_rows;
		return $r;
	}
	public function getReadedMails($userid){
		global $config, $mysqli;
		$q = $mysqli->query("select * from ".$config["umail/table"]." where toids like'%|".$userid."|%' and readed like '%|".$userid."|%' ");
		if(!$q)
			print $mysqli->error;
		return $q->num_rows;
	}
	public function isReaded($mailid,$userid){
		global $config, $mysqli;
		$q = $mysqli->query("select * from ".$config["umail/table"]." where toids like '%|".$userid."|%' and readed like '%|".$userid."|%' and id=".$mailid."");
		if(!$q)
			print $mysqli->error;
		return $q->num_rows;
	}
}

}

?>