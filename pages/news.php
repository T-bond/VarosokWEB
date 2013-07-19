<!-- News !-->
<div id="NewsInner" class="page">
	<div id="NewsPost" class="page4">
		<?php
			$q = $Blog->getNews("desc","","");
			foreach($q as $post){
				$User=$Userman->get("id='".$post["posterid"]."'");
				?>
				<div class="post">
					<a class="postTitle" href="javascript:void(0);"  title="Elolvasás" onClick="newsReadPost(<?php echo $post['id'].",'#comment-".$post['id']."'"; ?>);"><?php print $post['title']; ?></a>
					<span class="postDate"><?php print $post['date']; ?></span><br> 
					Írta: <?php print $User[0]['username']; ?>
					<div id="postPost">
					<?php print strtr($post['post'], $_SMILE); ?>
					<div id="comment-<?php echo $post["id"]; ?>"></div>
					</div>
				</div>
				<?php
			}
			if(!$q){
				print 'Hírek hamarosan';
			}
		?>
		
		<script type="text/javascript">
			OPEN = "";
			function newsReadPost(post_id, div)
			{
			if(OPEN==div)return;
			if(OPEN!="")
				$(OPEN).hide('fast').html('');
			OPEN=div;
				$.get
				(
					'communication.php',
					{t:11,i: post_id},
					function(r){$(div).html(r+'<br /><a href="javascript:void(0);" onClick="$(\''+div+'\').hide(\'fast\').html(\'\'); OPEN=\'\'">Bezár</a>').show('slow');}
				);
			}
		</script>
	</div>
	<div id="Post" style="display: none;" class="page4">
		<!-- Sup !-->
	</div>
	
	<div id="Komment" style="display: none;">
		<!-- !-->
	</div>
</div>