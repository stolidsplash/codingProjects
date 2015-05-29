<?php
//Turn on error reporting
ini_set('display_errors', 'On');
//Connects to the database
$mysqli = mysqli_connect("oniddb.cws.oregonstate.edu","carlsjon-db","F2s8AWCzqQOgXxD6","carlsjon-db");
if($mysqli->connect_errno){
	echo "Connection error " . $mysqli->connect_errno . " " . $mysqli->connect_error;
	}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<style>
#tabHeader ul {
	list-style: none; 
	padding:0;
	margin:0;}
#tabHeader li {
	display: inline;
	border: solid;
	border-width: 1px 1px 0 1px;
	margin: 0 0.5em 0 0;}
#tabHeader li a {
	padding: 0 1em;}
#content {
	border: 1px solid;}
</style>
<div id="tabHeader"> 
<ul class="tabs" data-tab>
  <li class="tab-title"><a href="http://web.engr.oregonstate.edu/~carlsjon/CS275FinalProject/LOTRCharacters.php">Characters</a></li>
  <li class="tab-title"><a href="http://web.engr.oregonstate.edu/~carlsjon/CS275FinalProject/LOTRRealms.php">Realms</a></li>
  <li class="tab-title"><a href="http://web.engr.oregonstate.edu/~carlsjon/CS275FinalProject/LOTRRace.php">Races</a></li>
  <li class="tab-title active"><a href="http://web.engr.oregonstate.edu/~carlsjon/CS275FinalProject/LOTRWeapons.php">Weapons</a></li>
</ul>
</div>
<head>
	<body>
		<div>
			<table>
				<tr>
					<h2>Lord of the Rings Races</h2>
				</tr>
			</table>
		</div>
		<div> 
			<form method="post" action="LOTRListRaces.php"> 
				<fieldset>
					<legend>List all the races</legend>
					<div>Races:
						<p><input type="submit" name="List" Value="List Races"/></p>
						<?php
							if(!($stmt = $mysqli->prepare("SELECT race_name FROM LOTR_race")))
									{
										echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
									}

							if(!$stmt->execute()){
								echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
							}
							if(!$stmt->bind_result($race)){
								echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
							}
							while($stmt->fetch()){
							 
							}
							$stmt->close();
						?>
					</div>
				</fieldset>
			</form>
		</div>
		<div>
			<form method="post" action="LOTRRealmRace.php">
				<fieldset>
					<legend>What is the Main Race in the Realm?</legend>
						<select name="realm">
							<?php
							if(!($stmt = $mysqli->prepare("SELECT realm_id, realm_name FROM LOTR_realm"))){
								echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
							}
							if(!$stmt->execute()){
								echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
							}
							if(!$stmt->bind_result($id, $name)){
								echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
							}
							while($stmt->fetch()){
							 echo '<option value=" '. $id . ' "> '. $name . '</option>\n';
							}
							$stmt->close();
							?>
						</select>
				</fieldset>
				<p><input type="submit" value="Find Race in Realm" /></p>
			</form>
		</div>
	</body>
</head>