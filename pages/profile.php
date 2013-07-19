<!-- Profile panel !-->
<div id="Profile" class="page" style="display: none;">
	<h3>Profiladatok módosítása</h3><br><!--onSubmit="if($('#Register_Email').val()=='' || $('#Register_Username').val()=='' || $('#Register_Password').val()=='' || $('#Register_Password_Again').val()=='')$('#Register_Error').addClass('error').removeClass('ok').html('Minden mező kitöltése kötelező!'); else if(!isValidEmailAddress($('#Register_Email').val()))$('#Register_Error').addClass('error').removeClass('ok').html('Érvénytelen e-mail!'); else if(!$('#Register_Username').val().match('[a-zA-Z0-9_-]{3,}'))$('#Register_Error').addClass('error').removeClass('ok').html('A felhasználónév nem megfelelő!'); else if($('#Register_Password').val()!=$('#Register_Password_Again').val())$('#Register_Error').addClass('error').removeClass('ok').html('A jelszavak nem egyeznek.'); else{$('#Register_Form').children('input').attr('disabled',true); $('#Register_Error').removeClass('error').removeClass('ok').html('Kis türelmet...'); $.post('communication.php',{t: 2, u: $('#Register_Username').val(), m: $('#Register_Email').val(), p: $.md5($('#Register_Password').val()), g: null},function(r){if(r=='1'){$('#Register_Error').addClass('ok').removeClass('error').html('A regisztráció sikeres.');}else{$('#Register_Error').addClass('error').removeClass('ok').html(r);} $('#Register_Form').children('input').attr('disabled',false);});} return false;"-->
	<form id="Profile_Form" onSubmit="return false;" >
		<div id="Register_Error"></div>
		<label for="Profile_Username">Felhasználónév</label>: <input type="Text" placeholder="Felhasználónév" id="Register_Username" value="<?php echo $_SESSION["USER"]["username"]; ?>" required="required" autocomplete="off" maxlength="32" pattern="[a-zA-Z0-9-_]{3,32}" title="Minimum 3 karakter. Csak betűket számokat valamint a következő speciális karaktereket használhatod: ,,- és _ ''" /><br>
		<label for="Profile_Realname">Valódi név</label>: <input type="Text" placeholder="Valódi neved" id="Profile_Realname" value="<?php echo $_SESSION["USER"]["realname"]; ?>" autocomplete="off" maxlength="32" /><br>
		<label for="Profile_Email">E-mail</label>: <input type="Email" placeholder="E-mail" id="Profile_Email" value="<?php echo $_SESSION["USER"]["mail"]; ?>" required="required" autocomplete="off" maxlength="32" /><br>
		<label for="Profile_Old_Password">Jelszó</label>: <input type="Password" placeholder="Jelszó" id="Profile_Old_Password" required="required" autocomplete="off" maxlength="32" /><br>
		<label for="Profile_Password">Új jelszó</label>: <input type="Password" placeholder="Új jelszó" id="Profile_Password" autocomplete="off" maxlength="32" onInput="if(this.value!=$('#Profile_Password_Again').val())setCustomValidity('A jelszavak nem egyeznek.'); else setCustomValidity('');" /><br>
		<label for="Profile_Password_Again">Új jelszó ismét</label>: <input type="Password" placeholder="Új jelszó ismét" id="Profile_Password_Again" autocomplete="off" maxlength="32" onInput="if(this.value!=$('#Profile_Password').val())document.getElementById('Profile_Password').setCustomValidity('A jelszavak nem egyeznek.'); else document.getElementById('Profile_Password').setCustomValidity('');" /><br>
		<input type="Submit" value="Módosít" />
	</form>
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