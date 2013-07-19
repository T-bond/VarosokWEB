<?php
if(!defined("_H_BLOG_H_")){
		define("_H_BLOG_H_",1);
/**
Blog API
*/
class CBlog{
	//We are singletons fckyea!
	private function __construct(){}
	private function __clone(){}
	public static function getInstance(){
		static $inst = NULL;
		if($inst == NULL){
			$inst = new CBlog();
		}
		$inst->Init();
		return $inst;
	}
	/**
	Blog init
	*/
	public function Init(){
		global $config, $mysqli;
		$query = $mysqli->query("select 1 from `".$config["blog/tableprefix"]."posts`");
		if(!$query){
			$this->createTables();
		}
		$query = $mysqli->query("select 1 from `".$config["blog/tableprefix"]."komment`");
		if(!$query){
			$this->createTables();
		}
	}
	/**
	Létre hozza a postoknak és a kommenteknek a táblát
	POST data tömbökhöz:
	0 - id
	1 - posterid //User id
	2 - title //Postcím
	3 - post //Amit írt
	4 - date //Dátum
	5 - Hírekben van-e
	Komment data tömbökhöz:
	0 - id
	1 - postid //Melyik posthoz tartozik
	2 - posterid //User aki kommentel
	3 - post //Maga a komment
	4 - date //Dátum
	".$config["blog/tableprefix"]."*/
	public function createTables(){
		global $config, $mysqli;
		$query = "create table if not exists `".$config["blog/tableprefix"]."posts`(
											`id` int(16) NOT NULL AUTO_INCREMENT, PRIMARY KEY(id),
											`posterid` int(16) NOT NULL,
											`title` text(70) COLLATE utf8_bin NOT NULL,
											`post` text(700) COLLATE utf8_bin NOT NULL,
											`date` datetime NOT NULL,
											`is_news` int NOT NULL
											)";
		$q = $mysqli->query($query);
		if(!$q)
			print $mysqli->error;
		$query = "create table if not exists `".$config["blog/tableprefix"]."komment`(
											 `id` int(16) NOT NULL AUTO_INCREMENT, PRIMARY KEY(id),
											 `postid` int(16) NOT NULL,
											 `posterid` int(16) NOT NULL,
											 `post` text(700) COLLATE utf8_bin NOT NULL,
											 `date` datetime NOT NULL
											 )";
		$q = $mysqli->query($query); 
	   if(!$q){
			print $mysqli->error;
			return false;
		}
		return true;
	}
	//Posterid alapján ad egy postot
	public function getPost($posterId, $order, $start, $limit){
		global $config, $mysqli;
		$query = "select * from ".$config["blog/tableprefix"]."posts where posterid = ".$posterId." order by date ".$order."";
		if($limit!="" && $start!=""){ $query.="LIMIT ".$start.",".$limit." "; }
		$q = $mysqli->query($query);
		if(!$q)
			print $mysqli->error;
		
		$a=Array();
		for($row=$q->fetch_assoc(); $row!=NULL; $row=$q->fetch_assoc()){$a[]=$row;}
		return $a;
	}
	//Összes post
	public function getPostAll($order,$start,$limit){
		global $config, $mysqli;
		$query = "select * from ".$config["blog/tableprefix"]."posts order by date ".$order."";
		if($limit!="" && $start!=""){ $query.="LIMIT ".$start.",".$limit." "; }
		$q = $mysqli->query($query);
		if(!$q)
			print $mysqli->error;
		$a=Array();
		for($row=$q->fetch_assoc(); $row!=NULL; $row=$q->fetch_assoc()){$a[]=$row;}
		return $a;
	}
	//Hírek
	public function getNews($order,$start,$limit){
		global $config, $mysqli;
		$query = "select * from ".$config["blog/tableprefix"]."posts where is_news=1 order by date ".$order."";
		if($limit!="" && $start!=""){ $query.="LIMIT ".$start.",".$limit." "; }
		$q = $mysqli->query($query);
		if(!$q)
			print $mysqli->error;
		$a=Array();
		for($row=$q->fetch_assoc(); $row!=NULL; $row=$q->fetch_assoc()){$a[]=$row;}
		return $a;
	}
	//Get post $data adata alapján, ha az egyenlő $val-al
	public function getPostData($data,$val){
		global $config, $mysqli;
		$query = $mysqli->query("select * from ".$config["blog/tableprefix"]."posts where ".$data." = ".$val."");
		if(!$query)
			print $mysqli->error;
		$dat = $query->fetch_assoc();
		return $dat;
	}
	//Postdata tömb alapján editel egy postot
	public function editPost($data){
		global $config, $mysqli;
		$mysqli->query("update ".$config["blog/tableprefix"]."posts set title=".$data[2].", post=".$data[3]." where id = ".$data[0]."");
	}
	//Ki postol egy postot, post datatömb alapján (POSTINCEPTIONDAWNG!)
	public function postPost($data){
		global $config, $mysqli;
		$q = $mysqli->query("insert into ".$config["blog/tableprefix"]."posts(posterid,title,post,date) values('".$data[1]."', '".$data[2]."', '".$data[3]."', '".date("Y-m-d H:i:s")."') ");
		if(!$q)
			print $mysqli->error;
		return $q;
	}
	//Kommentálni
	public function leaveComment($data){
		global $config, $mysqli;
		$q = $mysqli->query("insert into ".$config["blog/tableprefix"]."komment(postid,posterid,post,date) values('".$data[1]."', '".$data[2]."', '".$data[3]."', '".date("Y-m-d H:i:s")."') ");
		if(!$q)
			print $mysqli->error;
		return $q;
	}
	//Komment adat
	public function getComments($postid,$start,$limit){
		global $config, $mysqli;
		$query = "select * from ".$config["blog/tableprefix"]."komment where postid = ".$postid."";
		if($limit!="" && $start!=""){ $query.="LIMIT ".$start.",".$limit." "; }
		$q = $mysqli->query($query);
		//$data = mysqli_fetch_array($query);
		if(!$q)
			print $mysqli->error;
		
		$a=Array();
		for($row=$q->fetch_assoc(); $row!=NULL; $row=$q->fetch_assoc()){$a[]=$row;}
		return $a;
	}
}

}
?>