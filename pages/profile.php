<!-- Profile panel !-->
<div id="Profile" class="page" style="display: none;">
	<h3>Profiladatok módosítása</h3><br><!--onSubmit="if($('#Register_Email').val()=='' || $('#Register_Username').val()=='' || $('#Register_Password').val()=='' || $('#Register_Password_Again').val()=='')$('#Register_Error').addClass('error').removeClass('ok').html('Minden mező kitöltése kötelező!'); else if(!isValidEmailAddress($('#Register_Email').val()))$('#Register_Error').addClass('error').removeClass('ok').html('Érvénytelen e-mail!'); else if(!$('#Register_Username').val().match('[a-zA-Z0-9_-]{3,}'))$('#Register_Error').addClass('error').removeClass('ok').html('A felhasználónév nem megfelelő!'); else if($('#Register_Password').val()!=$('#Register_Password_Again').val())$('#Register_Error').addClass('error').removeClass('ok').html('A jelszavak nem egyeznek.'); else{$('#Register_Form').children('input').attr('disabled',true); $('#Register_Error').removeClass('error').removeClass('ok').html('Kis türelmet...'); $.post('communication.php',{t: 2, u: $('#Register_Username').val(), m: $('#Register_Email').val(), p: $.md5($('#Register_Password').val()), g: null},function(r){if(r=='1'){$('#Register_Error').addClass('ok').removeClass('error').html('A regisztráció sikeres.');}else{$('#Register_Error').addClass('error').removeClass('ok').html(r);} $('#Register_Form').children('input').attr('disabled',false);});} return false;"-->
	<form id="Profile_Form" onSubmit="if($('#Profile_Username').val()=='' || $('#Profile_Email').val()=='' || $('#Profile_Old_Password').val()=='')$('#Profile_Error').addClass('error').removeClass('waiting').removeClass('ok').html('A csillaggal jelölt mezők kitöltése kötelező!'); else if(!isValidEmailAddress($('#Profile_Email').val()))$('#Profile_Error').addClass('error').removeClass('waiting').removeClass('ok').html('Érvénytelen e-mail!'); else if(!$('#Profile_Username').val().match('[a-zA-Z0-9_-]{3,}'))$('#Profile_Error').addClass('error').removeClass('waiting').removeClass('ok').html('A felhasználónév nem megfelelő!'); return false;" >
		<div id="Profile_Error"></div>
		<label for="Profile_Username">Felhasználónév</label><sup>*</sup>: <input type="Text" placeholder="Felhasználónév" id="Profile_Username" value="<?php echo $_SESSION["USER"]["username"]; ?>" required="required" autocomplete="off" maxlength="32" pattern="[a-zA-Z0-9-_]{3,32}" title="Minimum 3 karakter. Csak betűket számokat valamint a következő speciális karaktereket használhatod: ,,- és _ ''" /><br>
		<label for="Profile_Realname">Valódi név</label>: <input type="Text" placeholder="Valódi neved" id="Profile_Realname" value="<?php echo $_SESSION["USER"]["realname"]; ?>" autocomplete="off" maxlength="32" /><br>
		<label for="Profile_Email">E-mail</label><sup>*</sup>: <input type="Email" placeholder="E-mail" id="Profile_Email" value="<?php echo $_SESSION["USER"]["mail"]; ?>" required="required" autocomplete="off" maxlength="32" /><br>
		<label for="Profile_Old_Password">Jelszó</label><sup>*</sup>: <input type="Password" placeholder="Jelszó" id="Profile_Old_Password" required="required" autocomplete="off" maxlength="32" onInput="if((this.value==$('#Profile_Password').val() && this.value!='') && this.value!='' && $('#Profile_Password_Again').val()!='' && $('#Profile_Password').val()!='')setCustomValidity('A régi és az új jelszó nem egyezhet!'); else setCustomValidity('');" /><br>
		<label for="Profile_Password">Új jelszó</label>: <input type="Password" placeholder="Új jelszó" id="Profile_Password" autocomplete="off" maxlength="32" onInput="if(this.value!='' || $('#Profile_Password_Again').val()!='')$(this).add('#Profile_Password_Again').attr('required','required'); else $(this).add('#Profile_Password_Again').removeProp('required'); if(this.value!='' && $('#Profile_Password_Again').val()!='' && $('#Profile_Old_Password').val()!=''){if(this.value!=$('#Profile_Password_Again').val())setCustomValidity('A jelszavak nem egyeznek.'); else setCustomValidity(''); if(this.value==$('#Profile_Old_Password').val() && this.value!='')document.getElementById('Profile_Old_Password').setCustomValidity('A régi és az új jelszó nem egyezhet!'); else document.getElementById('Profile_Old_Password').setCustomValidity('');}else{setCustomValidity(''); document.getElementById('Profile_Old_Password').setCustomValidity('');}" /><br>
		<label for="Profile_Password_Again">Új jelszó ismét</label>: <input type="Password" placeholder="Új jelszó ismét" id="Profile_Password_Again" autocomplete="off" maxlength="32" onInput="if(this.value!='' || $('#Profile_Password').val()!='')$(this).add('#Profile_Password').attr('required','required'); else $(this).add('#Profile_Password').removeProp('required'); if(this.value!='' && $('#Profile_Password').val()!='' && $('#Profile_Old_Password').val()!=''){if(this.value!=$('#Profile_Password').val())document.getElementById('Profile_Password').setCustomValidity('A jelszavak nem egyeznek.'); else document.getElementById('Profile_Password').setCustomValidity(''); if($('#Profile_Password').val()==$('#Profile_Old_Password').val() && $('#Profile_Password').val()!='')document.getElementById('Profile_Old_Password').setCustomValidity('A régi és az új jelszó nem egyezhet!'); else document.getElementById('Profile_Old_Password').setCustomValidity('');}else{document.getElementById('Profile_Password').setCustomValidity(''); document.getElementById('Profile_Old_Password').setCustomValidity('');}" /><br>
		<input type="Submit" value="Módosít" />
	</form>
	<div class="info">* - A csillaggal (*) jelölt mezők kitöltése kötelező.<br>E-mail cím változtatás esetén, az azonosítót újra be kell aktiválni.<br>Ha ez nem történne meg <?php echo $config["newmail/backup"]; ?> napon belül (<?php echo $config["newmail/backup"]*24; ?> óra), akkor az előző e-mail cím lesz aktiválva.<br>Továbbá az e-mail megváltoztatása esetén automatikuson kiléptet a rendszer.</div>
</div>

<script type="text/javascript">
	function GetData(id) {
		$('#User_Editor_Div').html('<div class="waiting">Adatok lekérése a szerverről...</div>').show('slow');
		$.get(
				'communication.php',
				{
					t: 5, 
					i: id, 
					g: null
				},
				function(r)
				{
					$('#User_Editor_Div').fadeTo("fast", 0, function(){$('#User_Editor_Div').html(r).fadeTo("fast", 1);});
				}
			);
	}
</script>