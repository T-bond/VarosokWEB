<!-- Admin panel !-->
<div id="DEV_Admin" class="page" style="display: none;">
	<h3>Felhasználó módosítása</h3><br>
	<input id="User_Editor" />
	<div id="User_Editor_Div"></div>
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