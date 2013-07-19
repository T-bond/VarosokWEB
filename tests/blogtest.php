<?php
include("../config.php");
include("../modules/blog.php");

if(isset($_GET['p'])){$p = $_GET['p'];} //Page num
else{$p='1';}
if(isset($_GET['id'])){$id = $_GET['id'];} //Post id
else{$id='1';}

$blog = CBlog::getInstance();
?>
<html>
	<head>
		<meta charset="utf-8"/>
		<link rel="stylesheet" type="text/css" href="../style.css" />
		<script src="../jquery-1.10.1.js"></script>
	</head>
	<body>
<?php
//Bejegyzés írása
if($p==1){
?>
		<a href="blogtest.php?p=2">Bejegyzések megtekintése</a><br>
		<form method="POST">
			<input type="text" name="title" placeholder="Post tilte"><br>
			<input type="num" name="poster" placeholder="Author id"><br>
			<textarea cols="20" rows="20" name="post">Mi jár a fejedben?</textarea><br> 
			<input type="submit" name="go" value="Post!"><br>
		</form>
<?php
	if(isset($_POST["go"])){
		$dat[1] = $_POST["poster"];
		$dat[2] = $_POST["title"];
		$dat[3] = $_POST["post"];
		$post = $blog->postPost($dat);
		if($post){
			print 'Posted!';
		}else{
			print 'Hiba!';
		}
	}
}

//Bejegyzések megtekintése
if($p==2){
	$q = $blog->getPostAll("DESC");
	foreach($q as $post){
		$rovid = substr($post["post"],"...",200);
		print '<div class="post"><span class="postTitle"><a href="blogtest.php?p=3&id='.$post["id"].'">'.$post["title"].'</a></span><span class="postDate">'.$post["date"].'</span><br><div id="postPost">'.$post["post"].'</div></div><hr>';
	}
}
//View one bejegyzés
if($p==3){
	$post = $blog->getPostData("id",$id);
	print '<div class="post"><span class="postTitle">'.$post["title"].'</span><span class="postDate">'.$post["date"].'</span><br><div id="postPost">'.$post["post"].'</div></div><br><br>Kommentek: <a href="blogtest.php?p=4&id='.$post["id"].'">Leave komment</a><br>';
	$q = $blog->getComments($id,"","");
	foreach($q as $komment){
		print '<div class="kommentek">'.$komment["date"].'<br>'.$komment["post"].'</div>';
	}
	?>
	<script type="text/javascript">
		$(".kommentek:even").addClass("kommentEven");
		$(".kommentek:odd").addClass("kommentOdd");
	</script>
	<?php
}
//Leave komment
if($p==4){
?>
	<form method="POST">
			<input type="num" name="poster" placeholder="Author id"><br>
			<textarea cols="20" rows="20" name="post">Komment....</textarea><br> 
			<input type="submit" name="go" value="Leave komment"><br>
	</form>
<?php
	if(isset($_POST["go"])){
		$dat[1] = $id;
		$dat[2] = $_POST["poster"];
		$dat[3] = $_POST["post"];
		$post = $blog->leaveComment($dat);
		if($post){
			print 'Posted! <a href="blogtest.php?p=3&id='.$id.'">Back</a> ';
		}else{
			print 'Hiba!';
		}
	}
}

?>

	</body>
</html>