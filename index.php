<?php
header("Content-Type: text/html; charset=UTF-8");
session_start();
if(isset($_GET["exit"])){unset($_SESSION["ID"]); unset($_SESSION["USER"]);}
require_once("config.php");
include("modules/users.php");
include("modules/umail.php");
include("modules/blog.php");
$Userman=CUsers::getInstance();
$Userman->createTables();
$Mail=CUMail::getInstance();
$Mail->createTable();
$Blog=CBlog::getInstance();
if(isset($_SESSION["ID"])){$_SESSION["USER"]=$Userman->get("`id`='".$mysqli->escape_string($_SESSION["ID"])."'"); $_SESSION["USER"]=$_SESSION["USER"][0];}
?>
<!DOCTYPE html>
<html lang="hu">
	<head>
		<title>Városok</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="style.css" />
        <link href="favicon.ico" rel="icon" type="image/x-icon" />
        <script src="src/jquery-1.10.1.js"></script>
		<script src="src/md5.js"></script>
		<script type="text/javascript" src="src/tokeninput.js"></script>
		<script type="text/javascript">
		function switchPage(container, item)
		{
			$(container).not(item).slideUp('slow'); 
			$(item).slideDown('slow');
		};
		document.cookie="COOKIE_ENABLED";
		$(document).ready(function(){
			$('.Javascript').show('fast');
			if(document.cookie.indexOf("COOKIE_ENABLED")==-1)$('#Cookies_Disabled').show('fast');
		<?php if(isset($_SESSION["ID"])){ ?>
			<?php if($_SESSION["USER"]["status"]==5){ ?>
			Ranks=new Array('Tiltott felhasználó','Aktiválatlan felhasználó','Felhasználó','Moderátor','Fejlesztő','Adminisztrátor');
			$("#User_Editor").tokenInput("communication.php?t=4", {
				theme: 'facebook',
				propertyToSearch: "username",
                hintText: "Kezdj el gépelni...",
                noResultsText: "Nincs ilyen találat.",
                searchingText: "Keresés...",
				searchDelay: 200,
                minChars: 2,
                tokenLimit: 1,
				tokenDelimiter: "|",
				preventDuplicates: true,
				resultsFormatter: function(item){ return "<li>"+item.realname+" ("+item.username+", "+item.mail+") ["+Ranks[item.status]+"]</li>" },
				tokenFormatter: function(item) { return "<li>"+item.realname+" ("+item.username+") ["+Ranks[item.status]+"]</li>" },
				onAdd: function(item){GetData(item.id);},
				onDelete: function(){$('#User_Editor_Div').hide('slow');}
            });
			<?php } ?>
			$("#Mail_Searcher").tokenInput("communication.php?t=6", {
				theme: 'facebook',
				propertyToSearch: "username",
				hintText: "Kezdj el gépelni...",
				noResultsText: "Nincs ilyen találat.",
				searchingText: "Keresés...",
				searchDelay: 200,
				minChars: 2,
				tokenLimit: <?php echo $_SESSION["USER"]["status"]==2?3:'null'; ?>,
				tokenDelimiter: "|",
				preventDuplicates: true,
				resultsFormatter: function(item){ return "<li>"+item.realname+" ("+item.username+")</li>" },
				tokenFormatter: function(item) { return "<li>"+item.realname+" ("+item.username+")</li>" }
            });
		<?php } ?>
		});
		function isValidEmailAddress(emailAddress) {
			var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
			return pattern.test(emailAddress);
		};
		<?php echo (isset($_GET["i"]))?"\$('#Activate_Form').children('input').attr('disabled',true); \$('#Activate_Error').removeClass('error').removeClass('ok').html('Kis türelmet...'); \$.post('communication.php',{t: 3, i: '".$_GET["i"]."', a: '".$_GET["a"]."', g: null},function(r){if(r=='1')\$('#Activate_Error').addClass('ok').removeClass('error').html('Az aktiváció sikeres.'); else \$('#Activate_Error').addClass('error').removeClass('ok').html(r); \$('#Activate_Form').children('input').attr('disabled',false);});":''?>
		</script>
	</head>
	<body>
		<noscript>
			<h1 style="color: Red;">Kérjük engedélyezze a javascript futtatását,<br>mert az oldal megjelenítéséhez elengedhetetlen.</h1>
		</noscript>
		<div id="wrap">
			<div class="Javascript">
				<h1>Városok<sup>BÉTA</sup></h1>
				
				<!-- Menu !-->
				<?php if(isset($_SESSION["ID"])){ ?>
					<div class="menuinner">
						<a href="javascript:void(0);" onClick="switchPage('.page','#NewsInner');" title="Belső tartalom">Hírek</a>
						<a href="javascript:void(0);" onClick="switchPage('.page','#Mailing');" title="Levelezés">Levelezés<?php echo $Mail->getUnreadedMails($_SESSION["ID"])==0?'':' ('.$Mail->getUnreadedMails($_SESSION["ID"]).')'; ?></a>
					<?php if($_SESSION["USER"]["status"]==4 or $_SESSION["USER"]["status"]==5){ /*DEV_MEMBER és ADMIN menük*/ ?>
						<a href="javascript:void(0);" onClick="switchPage('.page','#DEV');" title="Fejlesztői részleg">Fejlesztés</a>
					<?php } if($_SESSION["USER"]["status"]==5){ /*ADMIN menük*/ ?>
						<a href="javascript:void(0);" onClick="switchPage('.page','#DEV_Admin');" title="Adminisztráció">Adminisztráció</a>
					<?php } ?>
					<?php if($_SESSION["USER"]["status"]!=5){ /*Csak tagoknak, de nem adminoknak*/ ?>
						<a href="javascript:void(0);" onClick="switchPage('.page','#Profile');" title="Profil">Profil</a>
					<?php } ?>
						<a href="javascript:void(0);" onClick="switchPage('.page','#Exit');" title="Kilépés">Kijelentkezés</a>
					</div>
				<?php }else{ ?>
					<div class="Menu">
						<a href="javascript:void(0);" onClick="switchPage('.page','#News');" title="Hírek">Hírek</a>
						<a href="javascript:void(0);" onClick="switchPage('.page','#Login');" title="Bejelentkezés">Bejelentkezés</a> 
						<a href="javascript:void(0);" onClick="switchPage('.page','#Register');" title="Regisztráció">Regisztráció</a> 
					</div>
				<?php } ?>
				
				<!-- Yay, content !-->
				<div class="content">
					<?php 
					if(isset($_SESSION["ID"]))
					{ 
						include('pages/news.php');
						include('pages/mailing.php');
						
						/*DEV_MEMBER és ADMIN menük*/
						if($_SESSION["USER"]["status"]==4 or $_SESSION["USER"]["status"]==5)
						{ 
						?>
					
						<!-- Dev menu !-->
						<div id="DEV" style="display:none;" class="page">
							<div class="SubMenu">
								<a href="javascript:void(0);" onClick="switchPage('.page3','#TODO');" title="TODO">TODO</a>
								<a href="javascript:void(0);" onClick="switchPage('.page3','#Blog');" title="Blog">Blog</a>
								<a href="http://www.wiki.t-bond.hu/" target="_blank" title="Wiki">Wiki</a>
							</div>
							
							<?php 
								include('pages/todo.php'); 
								include('pages/blog.php');
							?>
						</div>
						<?php 
						} /* DEV_MEMBER és ADMIN vége */
						
						if($_SESSION["USER"]["status"]!=5){include('pages/profile.php');}; 
						
						/*ADMIN tartalom*/
						if($_SESSION["USER"]["status"]==5){include('pages/admin.php');}; 
						?>
						<div id="Exit" class="page" style="display: none;">Biztosan kilépsz?<br>
							<input type="Button" onClick="$('#Exit').slideUp('slow');" value="Maradok" />
							<input type="Button" onClick="location.href='<?php echo basename(__FILE__); ?>?exit'" value="Kilépek" />
						</div>
					<?php 
					}
					else
					{ 
					?>
					<div id="News" class="page"<?php echo (isset($_GET["i"]))?' style="display: none;"':''?>>
						<?php
								$q = $Blog->getNews("desc","","");
								foreach($q as $post){
									$User=$Userman->get("id='".$post["posterid"]."'");
									print '<div class="post"><span class="postTitle">'.$post["title"].'</span><span class="postDate">'.$post["date"].'</span><br>Írta: '.$User[0]["username"].'<div id="postPost">'.strtr($post["post"],$_SMILE).'</div></div>';
								}
								if(!$q){
									print 'Hírek hamarosan';
								}
						?>
					</div>
					<div id="Login" class="page" style="display: none;">
						<form id="Login_Form" onSubmit="if($('#Login_Email').val()=='' || $('#Login_Password').val()=='')$('#Login_Error').addClass('error').removeClass('ok').html('Minden mező kitöltése kötelező!'); else if(!isValidEmailAddress($('#Login_Email').val()))$('#Login_Error').addClass('error').removeClass('ok').html('Érvénytelen e-mail!'); else{$('#Login_Form').children('input').attr('disabled',true); $('#Login_Error').removeClass('error').removeClass('ok').html('Kis türelmet...'); $.post('communication.php',{t: 1, m: $('#Login_Email').val(), p: $.md5($('#Login_Password').val()), g: null, d: null},function(r){if(r=='1'){$('#Login_Error').addClass('ok').removeClass('error').html('Sikeres bejelentkezés. Átirányítás folyamatban...'); location.href='<?php echo basename( __FILE__ ); ?>';}else{$('#Login_Error').addClass('error').removeClass('ok').html(r);} $('#Login_Form').children('input').attr('disabled',false);});} return false;">
							<h3 style="color: Red; display: none; border: dotted; border-color: Red;" id="Cookies_Disabled">Kérjük engedélyezd a cookie-kat, mert a belépéshez szükségesek.</h3>
							<div id="Login_Error"></div>
							<label for="Login_Email">E-mail</label>: <input type="Email" placeholder="E-mail" id="Login_Email" required="required" autocomplete="off" autofocus="autofocus" maxlength="32" /><br>
							<label for="Login_Password">Jelszó</label>: <input type="Password" placeholder="Jelszó" id="Login_Password" required="required" autocomplete="off" maxlength="32" /><br>
							<input type="Submit" value="Bejelentkezés" />
						</form>						
					</div>
					<div id="Register" class="page"<?php echo (!isset($_GET["i"]))?' style="display: none;"':''?>>
						<form id="Register_Form" onSubmit="if($('#Register_Email').val()=='')$('#Register_Error').addClass('error').removeClass('ok').html('Minden mező kitöltése kötelező!'); else if(!isValidEmailAddress($('#Register_Email').val()))$('#Register_Error').addClass('error').removeClass('ok').html('Érvénytelen e-mail!'); else $.post('communication.php',{t: 2, m: $('#Register_Email').val(), g: null},function(r){if(r=='1'){$('#Register_Error').addClass('ok').removeClass('error').html('A regisztráció sikeres.'); $('#Register_Email').val(''); }else $('#Register_Error').addClass('error').removeClass('ok').html(r); $('#Register_Form').children('input').attr('disabled',false);}); return false;">
							<div id="Register_Error"></div>
							<label for="Register_Email">E-mail</label>: <input type="Email" placeholder="E-mail" id="Register_Email" required="required" autocomplete="off" maxlength="32" /><br>
							<input type="Submit" value="Regisztrálok" />
						</form>
						<h3>Aktiváció</h3>
						<form id="Activate_Form" onSubmit="if($('#Activate_Email').val()=='' || $('#Activate_Code').val()=='')$('#Activate_Error').addClass('error').removeClass('ok').html('Minden mező kitöltése kötelező!'); else if(!isValidEmailAddress($('#Activate_Email').val()))$('#Activate_Error').addClass('error').removeClass('ok').html('Érvénytelen e-mail!'); else if(!$('#Activate_Code').val().match('[a-zA-Z0-9]{8}'))$('#Activate_Error').addClass('error').removeClass('ok').html('A kód nem megfelelő!'); else{$('#Activate_Form').children('input').attr('disabled',true); $('#Activate_Error').removeClass('error').removeClass('ok').html('Kis türelmet...'); $.post('communication.php',{t: 3, m: $('#Activate_Email').val(), a: $.md5($('#Activate_Code').val()), g: null},function(r){if(r=='1'){$('#Activate_Error').addClass('ok').removeClass('error').html('Az aktiváció sikeres.');}else{$('#Activate_Error').addClass('error').removeClass('ok').html(r);} $('#Activate_Form').children('input').attr('disabled',false);});} return false;">
							<div id="Activate_Error"></div>
							<label for="Activate_Email">E-mail</label>: <input type="Email" placeholder="E-mail" id="Activate_Email" required="required" autocomplete="off" maxlength="32" /><br>
							<label for="Activate_Code">Aktivációs kód</label>: <input type="Text" placeholder="Aktivációs kódod" id="Activate_Code" required="required" autocomplete="off" maxlength="8" pattern="[a-zA-Z0-9]{8}" title="Pontosan 8 karakter. Csak számok és betűk." /><br>
							<input type="Submit" value="Aktiválom" />
						</form>
					</div>
					<div class="aftercontent"></div>
					<?php } ?>
				</div>
			</div>
		</div>
		<div id="footer">Created by: gtx, <a href="http://users.atw.hu/horvweb/" target="_blank">Horv</a>, <a href="http://www.t-bond.hu/" target="_blank">T-bond</a><br>2013</div>
	</body>
</html>
<?php
$mysqli->close();
unset($Userman);
unset($Mail);
?>
