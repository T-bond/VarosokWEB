<!-- Profile panel !-->
<div id="Profile" class="page" style="display: none;">
	<h3>Profiladatok módosítása</h3><br>
	<form id="Profile_Form" onSubmit="Modify_Profile(); return false;" >
		<div id="Profile_Error"></div>
		<label for="Profile_Username">Felhasználónév</label><sup>*</sup>: <input type="Text" placeholder="Felhasználónév" id="Profile_Username"<?php echo 'value="'.$_SESSION["USER"]["username"].'"'; if($_SESSION["USER"]["username"]!="NEW_USER_".$_SESSION["ID"])echo ' disabled="disabled"'; else{ ?> required="required" autocomplete="off" maxlength="32" pattern="[a-zA-Z0-9-_]{3,32}" title="Minimum 3 karakter. Csak betűket számokat valamint a következő speciális karaktereket használhatod: ,,- és _ ''"<?php } ?> /><br>
		<label for="Profile_Realname">Valódi név</label>: <input type="Text" placeholder="Valódi neved" id="Profile_Realname" value="<?php echo $_SESSION["USER"]["realname"]; ?>" autocomplete="off" maxlength="32" /><br>
		<label for="Profile_Email">E-mail</label><sup>*</sup>: <input type="Email" placeholder="E-mail" id="Profile_Email" value="<?php echo $_SESSION["USER"]["mail"]; ?>"<?php echo isset($_SESSION["USER"]["data"]["newmail"])?' disabled="disabled"':''; ?> required="required" autocomplete="off" maxlength="32" /><div style="display: inline;" id="New_Mail"><?php echo isset($_SESSION["USER"]["data"]["newmail"])?'Aktiválásra vár: '.$_SESSION["USER"]["data"]["newmail"]:''; ?></div><br>
		<label for="Profile_Old_Password">Jelszó</label><sup>*</sup>: <input type="Password" placeholder="Jelszó" id="Profile_Old_Password" required="required" autocomplete="off" maxlength="32" onInput="if((this.value==$('#Profile_Password').val() && this.value!='') && this.value!='' && $('#Profile_Password_Again').val()!='' && $('#Profile_Password').val()!='')setCustomValidity('A régi és az új jelszó nem egyezhet!'); else setCustomValidity('');" /><br>
		<label for="Profile_Password">Új jelszó</label>: <input type="Password" placeholder="Új jelszó" id="Profile_Password" autocomplete="off" maxlength="32" onInput="if(this.value!='' || $('#Profile_Password_Again').val()!='')$(this).add('#Profile_Password_Again').attr('required','required'); else $(this).add('#Profile_Password_Again').removeProp('required'); if(this.value!='' && $('#Profile_Password_Again').val()!='' && $('#Profile_Old_Password').val()!=''){if(this.value!=$('#Profile_Password_Again').val())setCustomValidity('A jelszavak nem egyeznek.'); else setCustomValidity(''); if(this.value==$('#Profile_Old_Password').val() && this.value!='')document.getElementById('Profile_Old_Password').setCustomValidity('A régi és az új jelszó nem egyezhet!'); else document.getElementById('Profile_Old_Password').setCustomValidity('');}else{setCustomValidity(''); document.getElementById('Profile_Old_Password').setCustomValidity('');}" /><br>
		<label for="Profile_Password_Again">Új jelszó ismét</label>: <input type="Password" placeholder="Új jelszó ismét" id="Profile_Password_Again" autocomplete="off" maxlength="32" onInput="if(this.value!='' || $('#Profile_Password').val()!='')$(this).add('#Profile_Password').attr('required','required'); else $(this).add('#Profile_Password').removeProp('required'); if(this.value!='' && $('#Profile_Password').val()!='' && $('#Profile_Old_Password').val()!=''){if(this.value!=$('#Profile_Password').val())document.getElementById('Profile_Password').setCustomValidity('A jelszavak nem egyeznek.'); else document.getElementById('Profile_Password').setCustomValidity(''); if($('#Profile_Password').val()==$('#Profile_Old_Password').val() && $('#Profile_Password').val()!='')document.getElementById('Profile_Old_Password').setCustomValidity('A régi és az új jelszó nem egyezhet!'); else document.getElementById('Profile_Old_Password').setCustomValidity('');}else{document.getElementById('Profile_Password').setCustomValidity(''); document.getElementById('Profile_Old_Password').setCustomValidity('');}" /><br>
		<input type="Submit" value="Módosít" />
	</form>
	<div class="info">* - A csillaggal (*) jelölt mezők kitöltése kötelező.<br>FIGYELEM!! A felhasználónév csak egyszer módosítható.<br>E-mail cím változtatás esetén, az  azonosítót újra be kell aktiválni,<br><?php echo $config["newmail/backup"]; ?> napon belül (<?php echo $config["newmail/backup"]*24; ?> óra), csak ezután lép érvénybe a változtatás.</div>
</div>

<script type="text/javascript">
	CM=<?php echo isset($_SESSION["USER"]["data"]["newmail"])?1:0; ?>;
	function Modify_Profile() {
	if($('#Profile_Username').val()=='' || $('#Profile_Email').val()=='' || $('#Profile_Old_Password').val()=='')
		$('#Profile_Error').addClass('error').removeClass('waiting').removeClass('ok').html('A csillaggal jelölt mezők kitöltése kötelező!');
		else
		if(($('#Profile_Password').val()=='' && $('#Profile_Password_Again').val()!='') || ($('#Profile_Password').val()!='' && $('#Profile_Password_Again').val()==''))
			$('#Profile_Error').addClass('error').removeClass('waiting').removeClass('ok').html('Új jelszó esetén mind a kettő mező kitöltése kötelező!');
			else
			if($('#Profile_Password').val()==$('#Profile_Old_Password').val())
				$('#Profile_Error').addClass('error').removeClass('waiting').removeClass('ok').html('A régi és az új jelszó nem egyezhet.');
				else
				if($('#Profile_Password').val()!=$('#Profile_Password_Again').val())
					$('#Profile_Error').addClass('error').removeClass('waiting').removeClass('ok').html('A két jelszó nem egyezik.');
					else
					if(!isValidEmailAddress($('#Profile_Email').val()))
						$('#Profile_Error').addClass('error').removeClass('waiting').removeClass('ok').html('Érvénytelen e-mail!');
						else
						if(!$('#Profile_Username').val().match('[a-zA-Z0-9_-]{3,}'))
							$('#Profile_Error').addClass('error').removeClass('waiting').removeClass('ok').html('A felhasználónév nem megfelelő!');
							else{
							$('#Profile_Form').children('input').attr('disabled',true);
							$('#Profile_Error').removeClass('error').removeClass('ok').addClass('waiting').html('Kommunikáció a szerverrel...');
							$.post(
								'communication.php',
								{
									t: 15,
									u: $('#Profile_Username').val(),
									m: $('#Profile_Email').val(),
									p: $.md5($('#Profile_Old_Password').val()),
									np: $.md5($('#Profile_Password').val()),
									rn: $('#Profile_Realname').val(),
									g: null
								},
								function(r){
									if(r=='1')
										$('#Profile_Error').addClass('ok').removeClass('error').removeClass('waiting').html('Módosítás sikeres.');
											else
											$('#Profile_Error').addClass('error').removeClass('ok').removeClass('waiting').html(r);
									$('#Profile_Form').children('input').attr('disabled',false);
									<?php echo $_SESSION["USER"]["username"]=="NEW_USER_".$_SESSION["ID"]?"if(r=='1' && \$('#Profile_Username').val()!='".$_SESSION["USER"]["username"]."')":""; ?>
										$('#Profile_Username').attr('disabled',true).removeAttr('title');
									<?php echo !isset($_SESSION["USER"]["data"]["newmail"])?"if(CM==1)\$('#Profile_Email').attr('disabled',true); if(r=='1' && \$('#Profile_Email').val()!='".$_SESSION["USER"]["mail"]."'){\$('#New_Mail').html('Aktiválásra vár: '+\$('#Profile_Email').val()); \$('#Profile_Email').val('".$_SESSION["USER"]["mail"]."').attr('disabled',true); CM=1;}":"\$('#Profile_Email').attr('disabled',true);"; ?>
								}
							);
							$('#Profile_Old_Password,#Profile_Password,#Profile_Password_Again').val('');
						}
	}
</script>