<html>
	<head>
		<link rel="stylesheet" type="text/css" href="../style.css" />
		<style type="text/css">
			table, td, tr
			{
				border: 1px solid #8A5E2F;
			}
		</style>
	</head>
	<body>
		<?php
			include("../config.php");
			include("../modules/todo.php");
			$Todoman=CTodos::getInstance();
		?>
		<?php
			print "Has table: ".(int)$Todoman->checkTables()."<br/>";
			print "Made table: ".(int)$Todoman->createTables()."<br/>";
		?>
		
		<hr/>
		
		Items:
		<table>
			<tr>
				<td>id</td> <td>user</td> <td>name</td> <td>description</td> <td>added</td> 
				<td>changed</td> <td>state</td> <td>progress</td> <td>tags</td>
			</tr>
			
			<?php
				$items=$Todoman->get("","userid, date_changed DESC","");
				
				if($items!=FALSE)
				{ 
					foreach($items as $item){?>
			<tr>
				<td><?php print $item['id']; ?></td> <td><?php print $item['userid']; ?></td> 
				<td><?php print $item['name']; ?></td> <td><?php print $item['descr']; ?></td> 
				<td><?php print $item['date_added']; ?></td> <td><?php print $item['date_changed']; ?></td> 
				<td><?php print $item['state']; ?></td> <td><?php print $item['progress']; ?></td> 
				<td><?php print $item['tags']; ?></td>
			</tr>
			<?php }
				}
				else
				{
					print "<tr> <td colspan=\"9\">".$mysqli->error."</td> <tr/>";
				}
			?>
		</table>
		
		<hr/>
			
		<form method="POST">
			<!-- <input type="hidden" name="act" value="add"> !-->
			<table>
				<tr><td>id: </td> <td><input type="text" name="id" /></td></tr>
				<tr><td>user: </td> <td><input type="text" name="user" /></td></tr>
				<tr><td>name: </td> <td><input type="text" name="name" /></td></tr>
				<tr><td>description: </td> <td><textarea name="descr" ></textarea></td></tr>
				<tr><td>state: </td> <td><input type="text" name="state" /></td></tr>
				<tr><td>progress: </td> <td><input type="text" name="progress" /></td></tr>
				<tr><td>tags: </td> <td><input type="text" name="tags" /></td></tr>
				<tr>
					<td colspan="2">
						<input type="submit" name="act" value="Add" /> 
						<input type="submit" name="act" value="Update" /> 
						<input type="submit" name="act" value="Delete" />
					</td>
				</tr>
			</table>
		</form>
		
		<?php
			if(isset($_POST["act"]))
			{
				$result=0; 
				
				$tags=$_POST["tags"];
				$tags=str_getcsv($tags);
				foreach($tags as &$t){$t=trim($t);}
				$tags="|".implode("|",$tags)."|";
				$_POST["tags"]=$tags;
				
				switch(strtolower($_POST["act"]))
				{
					case "add":
						$i=$Todoman->templateItem($_POST['id'],$_POST['user'],$_POST['name'],$_POST['descr'],'','',$_POST['state'],$_POST['progress'],$_POST['tags']);
						$result=$Todoman->add($i);
					break;
					
					case "update":
						$i=$Todoman->templateItem($_POST['id'],$_POST['user'],$_POST['name'],$_POST['descr'],'','',$_POST['state'],$_POST['progress'],$_POST['tags']);
						$result=$Todoman->update($i);
					break;
					
					case "delete":
						$i=$Todoman->templateItem($_POST['id'],$_POST['user'],$_POST['name'],$_POST['descr'],'','',$_POST['state'],$_POST['progress'],$_POST['tags']);
						$result=$Todoman->delete($i);
					break;
				}
				
				if($result){print "<script type=\"text/javascript\">window.location=window.location;</script>";}
			}
		?>
	</body>
</html>