<?php
header('Content-type: text/html; charset=utf-8');
if(!defined("_H_CONFIG_H_"))
	{
		define("_H_CONFIG_H_",1);

		$config=Array
		(

			"sql/host" => "localhost",
			"sql/user" => "cities",
			"sql/pass" => "cities2",
			"sql/database" => "cities",
	
			"users/table" => "users",
			"todo/table" => "todos",
			"blog/tableprefix" => "blog_",
			"umail/table" => "umail",
			
			"activation/delete" => 2, //Ennyi nap után törlődnek a nem aktivált regisztrációk
			"newmail/backup" => 1, //Ennyi nap után állítjuk vissza a nem aktivált e-mail címeket az eredeti értékre
			"mail/sendermail"  => "cities@t-bond.hu", //Innen küldjük az üzenetet
			"mail/sender" => "Fejlesztői csapat", //Küldő neve
			
			"activation/subject" => "Sikeres regisztáció!", //Aktivációs mail tárgya
			"activation/content" => 'Üdvözlünk a városok nagyjai közt!<br />
									<br />
									<hr>
									Felhasználói adatok:<br />
									Felhasználónév: {USERNAME}<br />
									Jelszó: {PASSWORD}<br />
									Ezek az adatok automatikusan generáltak. Bejelentkezés után megváltoztathatóak.
									<hr>
									<br />
									Kód az aktivációhoz: <i>{ACTIVATION_CODE}</i><br />
									Aktivációs linked: <a href="{ACTIVATION_LINK}" target="_blank" link="Aktiváció">Aktiváció</a><br />
									(Ha a link nem működne, másold a böngésződ címsorába a következőt: {ACTIVATION_LINK} )<br />
									<br />
									A lakosaid csak rád várnak.<br />
									Jó szórakozást kíván,<br />
										A fejlesztői csapat.
									<hr>
									Ezt a levelet azért kaptad, mert valaki a Te e-mail címeddel regisztrált.<br />
									Ha nem te voltál, akkor kérlek hagyd figyelmen kívül ezt az üzenetet.<br />
									A regisztráció {DELETE_DAY} nap múlva ({DELETE_HOUR} óra) törlésre kerül, ha nem aktiválod.<br />
									Utána ez az e-mail cím, valamint felhasználónév felszabadul az adatbázisunkból.<br />
									<br />
									Ez egy autómágikusan generált üzenet, kérjük ne válaszolj rá.', //Aktivációs mail: {USERNAME}=Felhasználónév, {PASSWORD}=Jelszó, {ACTIVATION_LINK}=Aktivációs link elérése, {ACTIVATION_CODE}=Aktivációs kód, {DELETE_DAY}=Törlési határidő (nap), {DELETE_HOUR}=Törlési határidő (óra)

			"changepassword/subject" => "Jelszóváltoztatás.", //Jelszóváltás mail tárgya
			"changepassword/content" => 'Kedves {USERNAME}!<br />
										<br />
										A jelszavad megváltozott.<br />
										Ha nem Te kérted a jelszóváltoztatást, akkor kérjük vedd fel a kapcsolatot a Support-al.<br />
										<br />
										Ez egy autómágikusan generált üzenet, kérjük ne válaszolj rá.', //Megváltozott jelszó: {USERNAME}=Felhasználónév

			"changemail/subject" => "E-mail változás.", //Jelszóváltás mail tárgya
			"changemail/content" => 'Kedves {USERNAME}!<br />
										<br />
										Az e-mail címed megváltozott.<br />
										Ha nem Te kérted ezt a változtatást, akkor kérjük vedd fel a kapcsolatot a Support-al.<br />
										<br />
										Ez egy autómágikusan generált üzenet, kérjük ne válaszolj rá.', //Megváltozott mail: {USERNAME}=Felhasználónév
			
			"newmail/subject" => "Megváltozott e-mail cím.", //Új mail tárgya
			"newmail/content" => 'Kedves {USERNAME}!<br />
									<br />
									A felhasználói azonosítódhoz tartozó e-mail címet megváltoztatták.<br />
									Ez az elektronikus levéllel tudod igazolni, hogy Te vagy a postaláda tulajdonosa.
									<hr>
									Kód az aktivációhoz: <i>{ACTIVATION_CODE}</i><br />
									Aktivációs linked: <a href="{ACTIVATION_LINK}" target="_blank" link="Aktiváció">Aktiváció</a><br />
									(Ha a link nem működne, másold a böngésződ címsorába a következőt: {ACTIVATION_LINK} )<br />
									<hr>
									Ezt a levelet azért kaptad, mert valaki a Te e-mail címedet adta meg.<br />
									Ha nem te voltál, akkor kérlek vedd fel a kapcsolatot a Support-al.<br />
									A változtatás {CHANGE_DAY} nap múlva ({CHANGE_HOUR} óra) visszaállításra kerül, ha nem aktiválod.<br />
									Utána a régi e-mail címed lesz újra az azonosítód.<br />
									<br />
									Ez egy autómágikusan generált üzenet, kérjük ne válaszolj rá.' //Új mail mail: {USERNAME}=Felhasználónév, {ACTIVATION_LINK}=Aktivációs link elérése, {ACTIVATION_CODE}=Aktivációs kód, {CHANGE_DAY}=Visszaállítási határidő (nap), {CHANGE_HOUR}=Visszaállítási határidő (óra)
		);

	$mysqli=new mysqli($config["sql/host"], $config["sql/user"], $config["sql/pass"], $config["sql/database"]);
	if($mysqli->connect_errno)die("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
	
	function ensql($data){global $mysqli; return $mysqli->real_escape_string($data);}
	function sqldatetime($time=NULL)
	{
		if($time===NULL){$time=time();}
		if(gettype($time)=="string"){$time=strtotime($time);}
		return date("Y-m-d h:i:s",$time); //YYYY-MM-DD HH:MM:SS
	}
	
	setlocale(LC_ALL,'hungarian'); //PHP fügvények kimenetének magyarra állítása
	
	$_SMILE=array();
	if(file_exists("smile.txt")) {
		foreach(file("smile.txt",FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
			list($key,$val)=explode(" ", $line, 2);
			if(file_exists($val))$_SMILE[$key]='<img src="'.$val.'" alt="'.$key.'" />';
		}
	}
	}
?>