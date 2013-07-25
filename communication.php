<?php
header("Content-Type: text/html; charset=UTF-8");
ob_start();
session_start();
if(!isset($_POST["t"]))$_POST=$_GET;
if(!isset($_POST["t"]))die(isset($_POST["g"])?"Hiányzó paraméter.":"-1"); //Hiányzó paraméter
require_once("config.php");
$config["users/table_escaped"]=ensql($config["users/table"]);
if(!isset($_SERVER['HTTPS'])){$_SERVER['HTTPS']='off';} //It was also missing HTTPS. Probably just a localhost-issue, but here's a cheap fix anyway
switch((int)$_POST["t"]) {
	case 1: //Belépés
		if(!isset($_POST["m"]) || !isset($_POST["p"])){$mysqli->close(); die(isset($_POST["g"])?"Hiányzó paraméter!":"-3");} //Hiányzó paraméter (mail v jelszó)
		if($_POST["m"]==="" or $_POST["p"]===""){$mysqli->close(); die(isset($_POST["g"])?"Az e-mail és/vagy a jelszó üres.":"-4");} //Üres mail vagy jelszó
		if(!preg_match('/[a-zA-Z0-9_-]{3,32}/', $_POST["p"])){$mysqli->close(); die(isset($_POST["g"])?"A jelszó nincs titkosítva.":"-5");} //Nem md5 formátumú a jelszó
		if(!filter_var($_POST["m"], FILTER_VALIDATE_EMAIL)){$mysqli->close(); die(isset($_POST["g"])?"Az e-mail cím érvénytelen.":"-6");} //Érvénytelen mail
		$_QUERY=$mysqli->query("SELECT `id`, `data` , `status` FROM `".$config["users/table_escaped"]."` WHERE `mail`='".ensql($_POST["m"])."' AND `pass`='".ensql($_POST["p"])."'");
		$_RESULT=$_QUERY->fetch_object();
		$mysqli->close();
		if($_QUERY->num_rows!==1) {
			$_QUERY->close();
			die(isset($_POST["g"])?"Rossz e-mail és/vagy jelszó.":"-7"); //Rossz azonosító adatok
				}else{
				$_QUERY->close();
				switch((int)$_RESULT->status) {
					case 0:
						$_BAN="";
						$_BANT="";
						$_CODE=explode("|",$_RESULT->data);
						foreach($_CODE as $val) {
							if(substr_count($val, "ban:")==1){
								$_BAN=substr($val, strlen("ban:"));
								if($_BANT!="")break;
							}
							if(substr_count($val, "bant:")==1){
								$_BANT=substr($val, strlen("bant:"));
								if($_BAN!="")break;
							}
						}
						die(isset($_POST["g"])?"Le vagy tiltva.".(($_BANT==""?"":"<br>Tiltás lejárta: ".$_BANT).($_BAN==""?"":"<br>Indok: ".$_BAN)):"-8"); //Tiltott felhasználó
						break;
					case 1:
						die(isset($_POST["g"])?"Ez a felhasználó még nincs aktiválva.":"-9"); //Még nincs aktiválva
						break;
					case 2: case 3: case 4: case 5: //2: felhasználó, 3: moderátor 4: fejlesztő 5: Adminisztrátor
						if(isset($_POST["d"]))$_SESSION["ID"]=$_RESULT->id;
						die(isset($_POST["g"])?"1":$_RESULT->id); //A bejelentkezés sikerült
						break;
					default:
						die(isset($_POST["g"])?"Ismeretlen rang.":"-10"); //Ismeretlen státusz
						break;
				}
		}
		break;
	case 2: //Regisztráció
		if(!isset($_POST["m"])){$mysqli->close(); die(isset($_POST["g"])?"Hiányzó paraméter!":"-11");} //Hiányzó paraméter (név v jelszó v mail)
		if($_POST["m"]===""){$mysqli->close(); die(isset($_POST["g"])?"Üres e-mail cím.":"-12");} //Üres mail
		if(!filter_var($_POST["m"], FILTER_VALIDATE_EMAIL)){$mysqli->close(); die(isset($_POST["g"])?"Az e-mail cím érvénytelen.":"-16");} //Érvénytelen mail
		if($mysqli->query("SELECT `id` FROM `".$config["users/table_escaped"]."` WHERE `mail`='".ensql($_POST["m"])."'")->num_rows>0){$mysqli->close(); die(isset($_POST["g"])?"Ez az e-mail cím már foglalt.":"-18");} //Foglalt mail
		$_PASSWORD=substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',10)),0,10);//Véletlen kód (10 karakter)
		$_CODE=substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',8)),0,8);//Véletlen kód (8 karakter)
		if(!$mysqli->query("INSERT INTO `".$config["users/table_escaped"]."` (`id`, `username`, `pass`, `mail`, `added`, `data`, `status`) VALUES (NULL, '".ensql("NEW_USER_".substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',3)),0,3))."', '".ensql(md5($_PASSWORD))."', '".ensql($_POST["m"])."', NOW(), 'acst:".date("Y-m-d H:i:s")."|activation_code:".$_CODE."', '1')")){$mysqli->close(); die(isset($_POST["g"])?"A regisztráció nem sikerült.":"-19");} //Hiba a hozzáadásban
		$_ID=$mysqli->insert_id;
		$mysqli->query("UPDATE `".$config["users/table_escaped"]."` SET `username`= '".ensql("NEW_USER_".$_ID)."' WHERE `id`='".$_ID."';");
		if(!mail($_POST["m"],$config["activation/subject"],strtr($config["activation/content"], array('{USERNAME}' =>  "NEW_USER_".$_ID, '{PASSWORD}' =>  $_PASSWORD, '{ACTIVATION_LINK}'=>($_SERVER['HTTPS']=='on'?'https://':'http://').$_SERVER['SERVER_NAME']."/index.php?a=".md5($_CODE)."&i=".$_ID,'{ACTIVATION_CODE}' =>  $_CODE, '{DELETE_DAY}' => $config["activation/delete"], '{DELETE_HOUR}' => $config["activation/delete"]*24)),'MIME-Version: 1.0' . "\r\n".'Content-type: text/html; charset=utf-8' . "\r\n".'From: '.$config["mail/sender"].' <'.$config["mail/sendermail"].'>' . "\r\n")){$mysqli->close(); die(isset($_POST["g"])?"Az e-mail elküldése nem sikerült.":"-20");} //Sikertelen email küldés
		$mysqli->close();
		die("1"); //Siker :)
		break;
	case 3: //Aktiváció
		if(!isset($_POST["a"]) || (!isset($_POST["i"]) && !isset($_POST["m"]))){$mysqli->close(); die(isset($_POST["g"])?"Hiányzó paraméter!":"-21");} //Hiányzó paraméter ((id és mail) v kód)
		if($_POST["a"]=="" or ($_POST["i"]=="" and $_POST["m"]=="")){$mysqli->close(); die(isset($_POST["g"])?"Üres azonosító vagy aktivációs kód.":"-22");} //Üres (azonosító és mail) vagy aktivációs kód
		if(!preg_match('/[a-zA-Z0-9_-]{3,32}/', $_POST["a"])){$mysqli->close(); die(isset($_POST["g"])?"Az aktivációs kód nem a megfelelő formátumú.":"-23");} //Nem md5 formátumú a kód
		include("modules/users.php");
		$users=CUsers::getInstance();
		if($_POST["m"]!="" and (!isset($_POST["i"]) or $_POST["i"]==""))
			$user=$users->get("`mail`='".ensql($_POST["m"])."'");
				else
				$user=$users->get("id='".$_POST["i"]."'");
		if(count($user)!=1) {
			$mysqli->close();
			die(isset($_POST["g"])?"Ez a felhasználó nem létezik.":"-24"); //Nincs ilyen azonosító
				}else{
				$user=$user[0];
				if($user["status"]!='1' and !isset($user["data"]["newmail"])) {
					$mysqli->close();
					die(isset($_POST["g"])?"A felhasználó már aktiválva van.":"-25"); //Már aktiválva van.
						}else
						if(!isset($user["data"]["activation_code"])) {
							$mysqli->close();
							die(isset($_POST["g"])?"Nincs aktivációs kód az adatbázisban.":"-26"); //Hiányzó kód
							}else{
							if(md5($user["data"]["activation_code"])!=$_POST["a"]) {
								$mysqli->close();
								die(isset($_POST["g"])?"Az aktivációs kód hibás.":"-26"); //Nem jó az azonosító
									}else{
									if(isset($user["data"]["newmail"])){
										if(isset($user["data"]["nms"])) {
											$user["data"]["old_mail"] = $user["mail"];
											$user["mail"] = $user["data"]["newmail"];
											unset($user["data"]["nms"]);
											unset($user["data"]["nmt"]);
											unset($user["data"]["newmail"]);
											unset($user["data"]["activation_code"]);
											}else{
											$user["data"]["nms"] = 1;
											$user["data"]["nmt"] = date("Y-m-d H:i:s");
											$user["data"]["activation_code"]=substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"),0,8);//Véletlen kód (8 karakter)
											if(!mail($user["data"]["newmail"],$config["newmail/subject"],strtr($config["newmail/content"], array('{USERNAME}' =>  $user["username"], '{ACTIVATION_LINK}'=>($_SERVER['HTTPS']=='on'?'https://':'http://').$_SERVER['SERVER_NAME']."/index.php?a=".md5($user["data"]["activation_code"])."&i=".($user["id"]),'{ACTIVATION_CODE}' =>  $user["data"]["activation_code"], '{CHANGE_DAY}' => $config["newmail/backup"], '{CHANGE_HOUR}' => $config["newmail/backup"]*24)),'MIME-Version: 1.0' . "\r\n".'Content-type: text/html; charset=utf-8' . "\r\n".'From: '.$config["mail/sender"].' <'.$config["mail/sendermail"].'>' . "\r\n")){$mysqli->close(); die(isset($_POST["g"])?"Az e-mail elküldése nem sikerült.":"-26.4");} //Sikertelen email küldés
											}
									}else{
									unset($user["data"]["activation_code"]);
									unset($user["data"]["acst"]);
									$user["status"] = 2;
									}
									if(!$users->update($user)){$mysqli->close(); die(isset($_POST["g"])?"Az aktiválás nem sikerült.":"-27");}
									die("1"); //Aktiváció OK
								}
							}
		}
		break;
	case 4: //Felhasználó kereső (Adminoknak)
		if(!isset($_SESSION["ID"]) or $_SESSION["USER"]["status"]!=5){$mysqli->close(); die(isset($_POST["g"])?"Azonosítatlan felhasználó, vagy rossz felhasználói szint.":"-28");}
		include("modules/users.php");
		$user=CUsers::getInstance();
		$users=$user->get("(`username` LIKE '%".(ensql($_POST["q"]))."%' OR `mail` LIKE '%".(ensql($_POST["q"]))."%' OR `realname` LIKE '%".(ensql($_POST["q"]))."%')");
		$mysqli->close();
		unset($user);
		echo "[";
		$n=0;
		foreach($users as $user)
			echo ($n++>0?',':'').'{"id":"'.$user["id"].'","realname":"'.$user["realname"].'","username":"'.$user["username"].'","mail":"'.$user["mail"].'","status":"'.$user["status"].'"}';
		echo "]";
		break;
	case 5: //Felhasználói profil módosító form (Admin)
		if(!isset($_SESSION["ID"]) or $_SESSION["USER"]["status"]!=5){$mysqli->close(); die(isset($_POST["g"])?"Azonosítatlan felhasználó, vagy rossz felhasználói szint.":"-29");}
		if($mysqli->query("SELECT `id` FROM `".$config["users/table_escaped"]."` WHERE id='".ensql($_POST["i"])."'")->num_rows != 1){$mysqli->close(); die(isset($_POST["g"])?"Nemlétező felhasználó.":"-30");}
		include("modules/users.php");
		$user=CUsers::getInstance();
		$users=$user->get("`id`='".ensql($_POST["i"])."'");
		$mysqli->close();
		unset($user);
		$users=$users[0];
		?>
		<div id="Edit_User_Error"></div>
		<form id="Edit_User_Form" onSubmit="if($('#Edit_Username').val()=='' || $('#Edit_Email').val()=='')$('#Edit_User_Error').addClass('error').removeClass('ok').removeClass('waiting').html('Minden mező kitöltése kötelező!'); else if(!isValidEmailAddress($('#Edit_Email').val()))$('#Edit_User_Error').addClass('error').removeClass('ok').removeClass('waiting').html('Érvénytelen e-mail!'); else if(!$('#Edit_Username').val().match('[a-zA-Z0-9_-]{3,}'))$('#Edit_User_Error').addClass('error').removeClass('ok').removeClass('waiting').html('A felhasználónév nem megfelelő!'); else{$('#Edit_User_Form').children('input').attr('disabled',true); if($('#Edit_Rank_Select').val()=='0'){if($('#Ban_Infinite').is(':checked'))BT=''; else BT=$('#Ban_End_Input').val(); B=$('#Ban_Description').val();}else{BT=''; B='';} $('#Edit_User_Error').removeClass('error').removeClass('ok').addClass('waiting').html('Kommunikáció a szerverrel...'); $.post('communication.php',{t: 14, i: <?php echo $users["id"]; ?>, u: $('#Edit_Username').val(), rn: $('#Edit_Realname').val(), m: $('#Edit_Email').val(), p: $.md5($('#Edit_Password').val()), s: $('#Edit_Rank_Select').val(), des: $('#Edit_Description').val(), b: B, bt: BT, g: null},function(r){if(r=='1'){$('#Edit_User_Error').addClass('ok').removeClass('error').removeClass('waiting').html('A módosítás sikeres.');}else{$('#Edit_User_Error').addClass('error').removeClass('ok').removeClass('waiting').html(r);} $('#Edit_User_Form').children('input').attr('disabled',false);});} return false;">
			<table class="user_data_editor">
				<tr><td>Felhasználónév:<sup><a href="javascript:void(0);" title="Visszaállít" onClick="$('#Edit_Username').val('<?php echo "NEW_USER_".$users["id"]; ?>');">x</a></sup></td><td><input type="Text" value="<?php echo $users["username"]; ?>" placeholder="Felhasználónév" id="Edit_Username" required="required" autocomplete="off" maxlength="32" pattern="[a-zA-Z0-9-_]{3,32}" title="Minimum 3 karakter. Csak betűket számokat valamint a következő speciális karaktereket használhatod: ,,- és _ ''" /></td></tr>
				<tr><td>Valódi név:</td><td><input type="Text" value="<?php echo $users["realname"]; ?>" placeholder="Valódi neve" id="Edit_Realname" autocomplete="off" /></td></tr>
				<tr><td>E-mail:</td><td><input type="Email" value="<?php echo $users["mail"]; ?>" placeholder="E-mail" id="Edit_Email" required="required" autocomplete="off" maxlength="32" onKeyUp="$(this).change();" onChange="if(this.value!='<?php echo $users["mail"]; ?>'){$('#Edit_Rank_Select').val('1'); $('#Edit_Pass_tr').hide('fast');}" /></td></tr>		
				<tr id="Edit_Pass_tr"<?php echo $users["status"]==1?' style="display: none;"':''; ?>><td>Új jelszó:</td><td><input type="Password" placeholder="Új jelszó" id="Edit_Password" autocomplete="off" maxlength="32" /></td></tr>
				<tr><td>Jogosultsága:</td><td>
					<select id="Edit_Rank_Select" onChange="if(this.value=='1')$('#Edit_Pass_tr').hide('fast'); else $('#Edit_Pass_tr').show('slow'); if(this.value!='0'){$('.Edit_Ban_class').hide('fast'); if(!$('#Ban_Infinite').is(':checked'))$('#Ban_End').hide('fast');} else{ $('.Edit_Ban_class').show('slow'); if(!$('#Ban_Infinite').is(':checked'))$('#Ban_End').show('slow');}">
						<option value="0"<?php echo $users["status"]==0?' selected="selected"':''; ?>>Tiltott felhasználó</option>
						<option value="1" <?php echo $users["status"]==1?' selected="selected"':''; ?>>Azonosítatlan felhasználó</option>
						<option value="2"<?php echo $users["status"]==2?' selected="selected"':''; ?>>Felhasználó</option>
						<optgroup label="Különleges jogosultsági szintek">
							<option value="3"<?php echo $users["status"]==3?' selected="selected"':''; ?>>Moderátor</option>
							<option value="4"<?php echo $users["status"]==4?' selected="selected"':''; ?>>Fejlesztő</option>
							<option value="5"<?php echo $users["status"]==5?' selected="selected"':''; ?>>Adminisztrátor</option>
						</optgroup>
					</select></td></tr>
				<tr><td>Megjegyzés:</td><td><textarea placeholder="Megjegyzés" id="Edit_Description" autocomplete="off"><?php echo $users["data"]["des"]; ?></textarea></div></td></tr>
					<tr class="Edit_Ban_class"<?php echo $users["status"]!=0?' style="display: none;"':''; ?>><td>Tiltás oka: </td><td><input type="Text" id="Ban_Description" placeholder="Tiltás oka" value="<?php echo $users["data"]["ban"]; ?>" autocomplete="off" /></td></tr>
					<tr class="Edit_Ban_class"<?php echo $users["status"]!=0?' style="display: none;"':''; ?>><td>Tiltás hossza: </td><td><label for="Ban_Infinite_Time">Határozatlan idő</label>: <input type="Checkbox" id="Ban_Infinite" onChange="if($(this).is(':checked'))$('#Ban_End').hide('fast'); else $('#Ban_End').show('slow');"<?php echo !isset($users["data"]["bant"])?' checked="checked""':''; ?> /></td></tr>
					<tr style="display: none;" id="Ban_End"<?php echo !isset($users["data"]["bant"])?' style="display: none;"':''; ?>><td>Tiltás vége: </td><td><input type="Date" id="Ban_End_Input" value="<?php echo $users["data"]["bant"]; ?>" min="<?php echo date('Y-m-d', strtotime(date('Y-m-d').' + 1 day')); ?>" /></td></tr>
				</div>
				<tr><td colspan="2"><input type="Submit" value="Módosítás"></td></tr>
			</table>
		</form>
		<?php
		$mysqli->close();
		break;
	case 6: //Felhasználó kereső (Felhasználóknak)
		if(!isset($_SESSION["ID"])){$mysqli->close(); die(isset($_POST["g"])?"Azonosítatlan felhasználó.":"-31");}
		include("modules/users.php");
		$user=CUsers::getInstance();
		$users=$user->get("`id`!='".ensql($_SESSION["ID"])."' AND `status`!='0' AND (`username` LIKE '%".(ensql($_POST["q"]))."%' OR `realname` LIKE '%".(ensql($_POST["q"]))."%')");
		$mysqli->close();
		unset($user);
		echo "[";
		$n=0;
		foreach($users as $user)
			echo ($n++>0?',':'').'{"id":"'.$user["id"].'","realname":"'.$user["realname"].'","username":"'.$user["username"].'"}';
		echo "]";
		break;
	case 7: //Levél kidobása
		if(!isset($_SESSION["ID"])){$mysqli->close(); die(isset($_POST["g"])?"Azonosítatlan felhasználó.":"-32");}
		include("modules/umail.php");
		$Mail=CUMail::getInstance();
		die((string)$Mail->throwMail($_POST["i"],$_SESSION["ID"]));
		break;
	case 8: //Levél olvasása
		if(!isset($_SESSION["ID"])){$mysqli->close(); die(isset($_POST["g"])?"Azonosítatlan felhasználó.":"-33");}
		include("modules/umail.php");
		include("modules/users.php");
		$Mail=CUMail::getInstance();
		$User=CUsers::getInstance();
		$Mail = $Mail->readMail($_POST["i"],$_SESSION["ID"]);
		$User=$User->get("id='".$Mail["fromids"]."'");
		$mysqli->close();
		die('Feladó: '.$User[0]["username"].'<br>Tárgy: '.$Mail["targy"].'<br>Érkezett: '.$Mail["date"].'<br>'.strtr($Mail["msg"], $_SMILE).'<br><br><a href="javascript:void(0);" onClick="$(\'#Mail\').hide(\'slow\');" title="Bezár">Bezár</a>');
		break;
	case 9: //Levél küldése
		if(!isset($_SESSION["ID"])){$mysqli->close(); die(isset($_POST["g"])?"Azonosítatlan felhasználó.":"-34");}
		if((strlen($_POST["s"])<5 or strlen($_POST["s"])>16) and (string)$_SESSION["USER"]["status"]=="2")die(isset($_POST["g"])?"A tárgy hossza nem megfelelő.":"-35");
		if((strlen($_POST["c"])<10 or strlen($_POST["c"])>100) and (string)$_SESSION["USER"]["status"]=="2")die(isset($_POST["g"])?"Az üzenet hossza nem megfelelő.":"-36");
		if((string)$_SESSION["USER"]["status"]=="2" and substr_count($_POST["i"])-2>3)die(isset($_POST["g"])?"A címzettek száma túl sok.":"-37");
		include("modules/umail.php");
		$Mail=CUMAIL::getInstance();
		die((string)$Mail->sendMail(array(0,$_SESSION["ID"],$_POST["i"],$_POST["s"],$_POST["c"])));
		break;
	case 10: //Blog bejegyzés olvasása
		include("modules/blog.php");
		include("modules/users.php");
		$Blog=CBlog::getInstance();
		$User=CUsers::getInstance();
		$post = $Blog->getPostData("id",$_POST["i"]);
		$User = $User->get("id='".$post["posterid"]."'");
		$mysqli->close();
		die('<div class="post"><span class="postTitle">'.$post["title"].'</span><span class="postDate">'.$post["date"].'</span><br>Írta: '.$User[0]["username"].'<div id="postPost">'.strtr($post["post"], $_SMILE).'</div></div><br><a href="javascript:void(0);" onClick="$(\'#Post\').hide(\'slow\');$(\'#NewsPost\').show(\'slow\'); $(\'#Komment\').hide(\'slow\');" title="Bezár">Bezár</a><br><a href="javascript:void(0);"  title="Elolvasás" onClick="$.get(\'communication.php\',{t:11,i:\''.$post["id"].'\'},function(r){$(\'#Komment\').show(\'slow\').html(r);});">Kommentek mutatása</a><br>');
		break;
	case 11: //Komment betöltése
		include("modules/blog.php");
		include("modules/users.php");
		$Blog=CBlog::getInstance();
		$User=CUsers::getInstance();
		$komments = "<hr>Hozzászólások:<br>";
		$q = $Blog->getComments($_POST["i"],"","");
		if($q){
		foreach($q as $komment){
			$user = $User->get("id='".$komment["posterid"]."'");
			$komments .= '<div class="kommentek">'.$user[0]["username"].' - '.$komment["date"].'<br>'.strtr($komment["post"], $_SMILE).'</div><br><br>';
		}
		$mysqli->close();
		die('<div class="komments">'.$komments.'</div><script type="text/javascript">$(".kommentek:even").addClass("kommentEven");$(".kommentek:odd").addClass("kommentOdd");</script>');
		}else{$mysqli->close(); die('<br>Még nincsenek hozzászólások.<br> <a href="javascript:void(0);">Légy te az első hozzászóló!</a>');}
		
		break;
	case 12: //Blog bejegyzés küldése
		if(!isset($_SESSION["ID"])){$mysqli->close(); die(isset($_POST["g"])?"Azonosítatlan felhasználó.":"-35");}
		include("modules/blog.php");
		$Blog=CBlog::getInstance();
		$dat[1] = $_SESSION["ID"];
		$dat[2] = $_POST["ti"];
		$dat[3] = $_POST["p"];
		$dat[5] = $_POST["n"];
		$post = $Blog->postPost($dat);
		$mysqli->close();
		if(!$post)
			die(isset($_POST["g"])?"A beküldés nem sikerült.":"-35.5");
		die("1");
		break;
	case 13: //Komment küldése
		if(!isset($_SESSION["ID"])){$mysqli->close(); die(isset($_POST["g"])?"Azonosítatlan felhasználó.":"-36");}
		break;
	case 14: //Felhasználó profil módosítása (Admin)
		if(!isset($_SESSION["ID"]) or $_SESSION["USER"]["status"]!=5){$mysqli->close(); die(isset($_POST["g"])?"Azonosítatlan felhasználó, vagy rossz felhasználói szint.":"-37");}
		if(!isset($_POST["i"]) || !isset($_POST["u"]) || !isset($_POST["rn"]) || !isset($_POST["m"]) || !isset($_POST["p"]) || !isset($_POST["s"]) || !isset($_POST["des"])){$mysqli->close(); die(isset($_POST["g"])?"Hiányzó paraméter!":"-38");}
		if($_POST["u"]==="" or $_POST["m"]===""){$mysqli->close(); die(isset($_POST["g"])?"Üres felhasználónév vagy e-mail.":"-39");} //Üres felhasználónév vagy mail
		if($_POST["p"]!="" and !preg_match('/[a-zA-Z0-9_-]{3,32}/', $_POST["p"])){$mysqli->close(); die(isset($_POST["g"])?"A jelszó nincs titkosítva.":"-40");} //Nem md5 formátumú a jelszó
		if(strlen($_POST["u"])<3){$mysqli->close(); die(isset($_POST["g"])?"A felhasználónév túl rövid.":"-41");} //A név túl rövid
		if(!preg_match('/[a-zA-Z0-9_-]{3,32}/', $_POST["u"])){$mysqli->close(); die(isset($_POST["g"])?"A név nem megfelelő.":"-42");} //Érvénytelen név
		if(!filter_var($_POST["m"], FILTER_VALIDATE_EMAIL)){$mysqli->close(); die(isset($_POST["g"])?"Az e-mail cím érvénytelen.":"-43");} //Érvénytelen mail
		include("modules/users.php");
		$user=CUsers::getInstance();
		$users=$user->get("`id`='".ensql($_POST["i"])."'");
		if(count($users)<=0){$mysqli->close(); die(isset($_POST["g"])?"A felhasználó nem található.":"-44");} //Nem található felhasználó
		$users=$users[0];
		if($_POST["p"]!="" and $_POST["p"]!="d41d8cd98f00b204e9800998ecf8427e" and $_POST["p"] != $users["pass"] and $_POST["s"]!=1)
			if(!mail($_POST["m"],$config["changepassword/subject"],strtr($config["changepassword/content"], array('{USERNAME}' =>  $_POST["u"])),'MIME-Version: 1.0' . "\r\n".'Content-type: text/html; charset=utf-8' . "\r\n".'From: '.$config["mail/sender"].' <'.$config["mail/sendermail"].'>' . "\r\n")){$mysqli->close(); die(isset($_POST["g"])?"A jelszóváltoztatásról szóló e-mail elküldése nem sikerült.":"-45");} //Sikertelen email küldés
		if((string)$_POST["s"]=="1") {
			$_CODE=substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"),0,8);//Véletlen kód (8 karakter)
			if($users["mail"]!=$_POST["m"]) {
				mail($users["mail"],$config["changemail/subject"],strtr($config["changemail/content"], array('{USERNAME}' =>  $_POST["u"])),'MIME-Version: 1.0' . "\r\n".'Content-type: text/html; charset=utf-8' . "\r\n".'From: '.$config["mail/sender"].' <'.$config["mail/sendermail"].'>' . "\r\n");
				$users["data"]["old_mail"] = $users["mail"];
			}
			if(!mail($_POST["m"],$config["newmail/subject"],strtr($config["newmail/content"], array('{USERNAME}' =>  $_POST["u"], '{ACTIVATION_LINK}'=>($_SERVER['HTTPS']=='on'?'https://':'http://').$_SERVER['SERVER_NAME']."/index.php?a=".md5($_CODE)."&i=".($users["id"]),'{ACTIVATION_CODE}' =>  $_CODE, '{CHANGE_DAY}' => $config["newmail/backup"], '{CHANGE_HOUR}' => $config["newmail/backup"]*24)),'MIME-Version: 1.0' . "\r\n".'Content-type: text/html; charset=utf-8' . "\r\n".'From: '.$config["mail/sender"].' <'.$config["mail/sendermail"].'>' . "\r\n")){$mysqli->close(); die(isset($_POST["g"])?"Az e-mail elküldése nem sikerült.":"-46");} //Sikertelen email küldés
		}
		$users["username"]=$_POST["u"];
		$users["realname"]=$_POST["rn"];
		$users["mail"]=$_POST["m"];
		if($_POST["p"]!="" and $_POST["p"]!="d41d8cd98f00b204e9800998ecf8427e" and $_POST["p"] != $users["pass"])
			$users["pass"]=$_POST["p"];
		if((string)$_POST["s"]=="1") {
			$users["data"]["activation_code"]=$_CODE;
			$users["data"]["acst"]=date("Y-m-d H:i:s");
			unset($users["data"]["newmail"]);
			unset($users["data"]["nmt"]);
			unset($users["data"]["nms"]);
		}
		if($_POST["des"]=="")
			unset($users["data"]["des"]);
				else
				$users["data"]["des"]=str_replace("|","&#124;",$_POST["des"]);
		if((string)$_POST["s"]=="0") {
			$users["data"]["ban"]=str_replace("|","&#124;",$_POST["b"]);
			$users["data"]["bant"]=$_POST["bt"];
			}else{
			unset($users["data"]["ban"]);
			unset($users["data"]["bant"]);
		}
		$users["status"]=$_POST["s"];
		if(!$user->update($users)){$mysqli->close(); die(isset($_POST["g"])?"A frissítés nem sikerült.":"-47");}
		$mysqli->close();
		die("1");
		break;
	case 15: //Felhasználó profil módosítása (Felhasználó)
		if(!isset($_SESSION["ID"]) or $_SESSION["USER"]["status"]==5){$mysqli->close(); die(isset($_POST["g"])?"Azonosítatlan felhasználó, vagy rossz felhasználói szint.":"-48");}
		if($_POST["m"]==="" or $_POST["p"]==="" or $_POST["u"]===""){$mysqli->close(); die(isset($_POST["g"])?"A felhasználónév, e-mail és/vagy a jelszó üres.":"-49");} //Üres user, mail vagy jelszó
		if(!preg_match('/[a-zA-Z0-9_-]{3,32}/', $_POST["p"])){$mysqli->close(); die(isset($_POST["g"])?"A jelszó nincs titkosítva.":"-50");} //Nem md5 formátumú a jelszó
		if(!preg_match('/[a-zA-Z0-9_-]{3,32}/', $_POST["m"])){$mysqli->close(); die(isset($_POST["g"])?"A felhasználónév nem megfelelő.":"-51");} //Nem md5 formátumú a jelszó
		if(!filter_var($_POST["m"], FILTER_VALIDATE_EMAIL)){$mysqli->close(); die(isset($_POST["g"])?"Az e-mail cím érvénytelen.":"-52");} //Érvénytelen mail
		include("modules/users.php");
		$user=CUsers::getInstance();
		$users=$user->get("`id`='".ensql($_SESSION["ID"])."'");
		$profile=$users[0];
		if($profile["pass"]!=$_POST["p"]){$mysqli->close(); die(isset($_POST["g"])?"Hibás jelszó.":"-53");} //Rossz jelszó
		if($profile["username"]=="NEW_USER_".$profile["id"] and $_POST["u"]!=$profile["username"]) {
			if(count($user->get("`username`='".ensql($_POST["u"])."' AND `id`!='".ensql($_SESSION["ID"])."'"))>0){$mysqli->close(); die(isset($_POST["g"])?"Ez a felhasználónév már foglalt.":"-54");} //Foglalt username
			$profile["username"]=$_POST["u"];
		}
		if($_POST["m"]!=$profile["mail"] and !isset($profile["data"]["newmail"])) {
			if(count($user->get("`mail`='".ensql($_POST["m"])."' AND `id`!='".ensql($_SESSION["ID"])."'"))>0){$mysqli->close(); die(isset($_POST["g"])?"Ez az e-mail cím már foglalt.":"-55");} //Foglalt mail
			$profile["data"]["newmail"] = $_POST["m"];
			$profile["data"]["nmt"] = date("Y-m-d H:i:s");
			$_CODE=substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"),0,8);//Véletlen kód (8 karakter)
			$profile["data"]["activation_code"] = $_CODE;
			if(!mail($profile["mail"],$config["newmail2/subject"],strtr($config["newmail2/content"], array('{USERNAME}' =>  $_POST["u"], '{ACTIVATION_LINK}'=>($_SERVER['HTTPS']=='on'?'https://':'http://').$_SERVER['SERVER_NAME']."/index.php?a=".md5($_CODE)."&i=".($profile["id"]),'{ACTIVATION_CODE}' =>  $_CODE, '{CHANGE_DAY}' => $config["newmail/backup"], '{CHANGE_HOUR}' => $config["newmail/backup"]*24)),'MIME-Version: 1.0' . "\r\n".'Content-type: text/html; charset=utf-8' . "\r\n".'From: '.$config["mail/sender"].' <'.$config["mail/sendermail"].'>' . "\r\n")){$mysqli->close(); die(isset($_POST["g"])?"Az e-mail elküldése nem sikerült.":"-56");} //Sikertelen email küldés
			
		}
		if($_POST["np"]!="" and $_POST["np"]!="d41d8cd98f00b204e9800998ecf8427e" and $_POST["np"] != $profile["pass"]) {
			mail($profile["mail"],$config["changepassword/subject"],strtr($config["changepassword/content"], array('{USERNAME}' =>  $profile["username"])),'MIME-Version: 1.0' . "\r\n".'Content-type: text/html; charset=utf-8' . "\r\n".'From: '.$config["mail/sender"].' <'.$config["mail/sendermail"].'>' . "\r\n");
			$profile["pass"]=$_POST["np"];
		}
		$profile["realname"]=$_POST["rn"];
		if(!$user->update($profile)){$mysqli->close(); die(isset($_POST["g"])?"A módosítás nem sikerült.":"-57");}
		$mysqli->close();
		die("1");
		break;
	default:
		$mysqli->close();
		die(isset($_POST["g"])?"Rossz paraméter.":"-2"); //Rossz paraméter
		break;
}
ob_end_flush();
?>