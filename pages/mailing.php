<!-- Mailing !-->
<div id="Mailing" style="display:none;" class="page">
	<div class="SubMenu">
		<a href="javascript:void(0);" onClick="switchPage('.page2','#NewMail');" title="Új üzenet">Új üzenet</a>
		<a href="javascript:void(0);" onClick="switchPage('.page2','#Incoming');" title="Bejövő">Bejövő (<?php echo $Mail->getUnreadedMails($_SESSION["ID"]); ?>)</a>
		<a href="javascript:void(0);" onClick="switchPage('.page2','#Outgoing');" title="Kimenő">Kimenő</a>
	</div>
	<div id="NewMail" style="display:none;" class="page2">
		<form id="Message_Form" onSubmit="">
			<div id="Mail_Error"></div>
			<label for="Mail_Searcher">Címzett(ek)</label>: <input id="Mail_Searcher" placeholder="Címzett(ek)"/>
			<br>
			<label for="Mail_Subject">Tárgy</label>: <input id="Mail_Subject" type="text" placeholder="Tárgy" required="required" autocomplete="off" pattern=".{3,16}" title="Minimum 3, maximum 16 karakter." maxlength="16"/><br>
			<label for="Mail_Content">Üzenet</label>: <textarea id="Mail_Content" cols="10" rows="10" placeholder="Üzenet..." required="required"<?php if($_SESSION['USER']['status']==2)echo ' maxlength="100"'; ?>></textarea><br>
			<input type="Submit" value="Küldés"/>
		</form>
		
		<script type="text/javascript">
			$('#Message_Form').bind
			(
				'submit',
				function()
				{
					//Check if something's wrong
					if($('#Mail_Subject').val()=='' || $('#Mail_Content').val()==''|| $('#Mail_Searcher').val()=='')
						$('#Mail_Error').addClass('error').removeClass('ok').html('Minden mező kitöltése kötelező.'); 
					else if($('#Mail_Subject').val().length<5)
						$('#Mail_Error').addClass('error').removeClass('ok').html('A tárgy túl rövid.'); 
					else if($('#Mail_Subject').val().length>16)
						$('#Mail_Error').addClass('error').removeClass('ok').html('A tárgy túl hosszú.'); 
					else if($('#Mail_Content').val().length<10)
						$('#Mail_Error').addClass('error').removeClass('ok').html('Az üzenet túl rövid.'); 
						else
					<?php if($_SESSION['USER']['status']==2){?>
						if($('#Mail_Subject').val().length>100)
						$('#Mail_Error').addClass('error').removeClass('ok').html('Az üzenet túl hosszú.'); 
					else 
						if($('#Mail_Searcher').val().split('|').length>3)$('#Mail_Error').addClass('error').removeClass('ok').html('Csak 3 címzett választható.'); else 
					<?php } ?>
					{
						//Everything's right, send form
						$('#Message_Form').children('input').attr('disabled',true); 
						$('#Mail_Error').removeClass('error').removeClass('ok').html('Kis türelmet...'); 
						
						$.post
						(
							'communication.php',
							{
								t:9,
								i:'|'+$('#Mail_Searcher').val()+'|',
								s:$('#Mail_Subject').val(),
								c:$('#Mail_Content').val(),
								g: null
							},
								
							function(r)
							{
								if(r=='1')
								{
									$('#Mail_Error').addClass('ok').removeClass('error').html('Sikeresen elküldve.'); 
									$('#Mail_Searcher').tokenInput('clear'); 
									$('#Mail_Subject,#Mail_Content').val('');
								}
								else
								{
									$('#Mail_Error').addClass('error').removeClass('ok').html(r); 
									$('#Message_Form').children('input').attr('disabled',false); 
								}
							}
						);
					} 
						
					//Return false anyways - we don't want the page to reload
					return false;
				}
			);
		</script>
	</div>
	<div id="Incoming" class="page2">
		<?php
		$mails = $Mail->listMail($_SESSION["ID"],"","","","");
		foreach($mails as $mail){
		$User=$Userman->get("id='".$mail["fromid"]."'");
		?>
		<div <?php print 'id="MAIL_'.$mail['id'].'"'.($Mail->isReaded($mail["id"],$_SESSION["ID"])>0?'':' class="unreadedMail"'); ?>>
			<?php print $User[0]['username']; ?>:
			<a href="javascript:void(0);" title="Elolvasás" onClick="mailReadMail(<?php print $mail['id']; ?>);"><?php print $mail['targy']; ?></a>
			<sup class="deleteMail"><a href="javascript:void(0);" title="Törlés" onClick="mailDeleteMail(<?php print $mail['id']; ?>)">[Törlés]</a></sup> <br>
			Érkezett: <?php print $mail['date']; ?>
		</div> <br>
		<?php
		}
		?>
	
		<script type="text/javascript">
			function mailReadMail(mail_id){$.get('communication.php',{t:8,i: mail_id, g: null},function(r){$('.page2').hide('slow'); $('#Mail').show('slow').html(r);});}
			function mailDeleteMail(mail_id){$.get('communication.php',{t:7,i: mail_id, g: null},function(r){if(r=='1'){$('#MAIL_'+mail_id).remove();}else{alert('Hiba!');}});}
		</script>
	</div>
	<div id="Outgoing" style="display:none;" class="page2">
	<?php
	$mails = $Mail->listSendedMail($_SESSION["ID"],"","","","");	
	foreach($mails as $mail){
		$Users=$Userman->get("`id`='".implode("' OR `id`='",explode("|",substr($mail["toids"], 1, -1)))."'");
		$User="";
		foreach($Users as $key)
			$User.=($User==""?"":", ").$key["username"];
		print $User; ?>: <a href="javascript:void(0);" title="Elolvasás" onClick="mailReadMail(<?php print $mail['id']; ?>);"><?php print $mail['targy']; ?></a> 
		<sup class="deleteMail"><a href="javascript:void(0);" title="Törlés" onClick="mailDeleteMail(<?php print $mail['id']; ?>);">[Törlés]</a></sup> <br>
		Elküldve: <?php print $mail['date'].' <br>';
	}
	?>
	</div>
	<div id="Mail" style="display: none;" class="page2"></div>
</div>