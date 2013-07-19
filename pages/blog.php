<!-- Blog !-->
<div id="Blog" style="display:none;" class="page3">
	<a href="javascript:void(0);" onClick="switchPage('.page3','#allPosts');" title="Összes bejegyzés">Összes bejegyzés</a>
	<a href="javascript:void(0);" onClick="switchPage('.page3','#newPost');" title="Új bejegyzés írása">Új bejegyzés írása</a>
	<a href="javascript:void(0);" onClick="switchPage('.page3','#myPosts');" title="Saját bejegyzéseim">Saját bejegyzéseim</a>
	<a href="javascript:void(0);" onClick="switchPage('.page3','#otherPosts');" title="Más bejegyzései">Más bejegyzései</a>
	
	<div id="allPosts" class="page3">
		<?php
		$q = $Blog->getPostAll("desc","","");
		foreach($q as $post){
			$User=$Userman->get("id='".$post["posterid"]."'");
			?>
			<div class="post">
				<a class="postTitle" href="javascript:void(0);"  title="Elolvasás" onClick="blogReadPost(<?php print $post['id']; ?>);"><?php print $post['title']; ?></a>
				<span class="postDate"><?php print $post['date']; ?></span><br> 
				Írta: <?php print $User[0]['username']; ?>
				<div id="postPost">
				<?php print $post['post']; ?>
				</div>
			</div>
			<?php
		}
		?>
		
		<script type="text/javascript">
			function blogReadPost(post_id)
			{
				$.get
				(
					'communication.php',
					{t:10,i: post_id},
					function(r){$('.page3').hide('slow'); $('#Post').show('slow').html(r);}
				);
			}
		</script>
	</div>
	
	<div id="newPost" class="page3">
		<div id="Post_Error"></div>
		<form id="Post_Form" onSubmit="$.post('communication.php',{t:12,i:"<?php echo $_SESSION["ID"]; ?>",tit:$('#title').val(),n:$('#is_news').val(),p:$('#post').val(), function(r){$(\'#Post_Error\').html(r);} });">
			<input type="text" id="title" placeholder="Bejegyzés címe"><br>
			<input type="checkbox" id="is_news" value="1"> Megjelenjen a hírek közt?<br>
			<textarea cols="20" rows="20" id="post">Mi jár a fejedben?</textarea><br> 
			<input type="Submit" value="Post!"><br>
		</form>
	</div>
	
	<div id="myPosts" class="page3"></div>
	<div id="otherPosts" class="page3"></div>
	<div id="Post" style="display: none;" class="page3"></div><div id="Komment" style="display: none;"></div>
</div>