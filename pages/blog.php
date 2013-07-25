<!-- Blog !-->
<div id="Blog" style="display:none;" class="page3">
	<a href="javascript:void(0);" onClick="switchPage('.page4','#allPosts');" title="Összes bejegyzés">Összes bejegyzés</a>
	<a href="javascript:void(0);" onClick="switchPage('.page4','#newPost');" title="Új bejegyzés írása">Új bejegyzés írása</a>
	<a href="javascript:void(0);" onClick="switchPage('.page4','#myPosts');" title="Saját bejegyzéseim">Saját bejegyzéseim</a>
	<a href="javascript:void(0);" onClick="switchPage('.page4','#otherPosts');" title="Más bejegyzései">Más bejegyzései</a>
	
	<div id="allPosts" style="display: none;" class="page4">
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
				<?php print strtr($post['post'], $_SMILE); ?>
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
					function(r){$('.page4').hide('slow'); $('#Post').show('slow').html(r);}
				);
			}
			function newPost() {
				$('#Post_Form').children('input').attr('disabled',true);
				$('#Edit_User_Error').removeClass('error').removeClass('ok').addClass('waiting').html('Kommunikáció a szerverrel...');
				$.post(
					'communication.php',
					{
						t: 12,
						ti: $('#title').val(),
						n: $('#is_news').val(),
						p: $('#post').val(),
						g: null
					},
					function(r) {
						if(r=='1')
							$('#Post_Error').addClass('ok').removeClass('error').removeClass('waiting').html('Új bejegyzés hozzáadva.');
								else
								$('#Post_Error').addClass('error').removeClass('ok').removeClass('waiting').html(r);
						$('#Post_Form').children('input').attr('disabled',false);
					}
				);
			}
		</script>
	</div>
	
	<div id="newPost" style="display: none;" class="page4">
		<div id="Post_Error"></div>
		<form id="Post_Form" onSubmit="newPost(); return false;">
			<input type="text" id="title" placeholder="Bejegyzés címe"><br>
			<input type="checkbox" id="is_news" value="1"> Megjelenjen a hírek közt?<br>
			<textarea cols="20" rows="20" placeholder="Mi jár a fejedben?" id="post"></textarea><br> 
			<input type="Submit" value="Elküld!"><br>
		</form>
	</div>
	
	<div id="myPosts" style="display: none;" class="page4">Hamarosan...</div>
	<div id="otherPosts" style="display: none;" class="page4">Hamarosan...</div>
	<div id="Post" style="display: none;" class="page4">Hamarosan...</div><div id="Komment" style="display: none;"></div>
</div>