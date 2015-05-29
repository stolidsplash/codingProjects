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
  <li class="tab-title active"><a href="http://web.engr.oregonstate.edu/~carlsjon/CS275FinalProject/LOTRCharacters.php">Characters</a></li>
  <li class="tab-title"><a href="http://web.engr.oregonstate.edu/~carlsjon/CS275FinalProject/LOTRRealms.php">Realms</a></li>
  <li class="tab-title"><a href="http://web.engr.oregonstate.edu/~carlsjon/CS275FinalProject/LOTRRace.php">Races</a></li>
  <li class="tab-title"><a href="http://web.engr.oregonstate.edu/~carlsjon/CS275FinalProject/LOTRWeapons.php">Weapons</a></li>
</ul>
</div>
<head>
<div id="content">
	<h1>The Lord of the Rings</h1>
	<h3>Explore the LOTR Universe</h3> 
	<body>
		<div> 
			<form method="post" action="LOTRListCharacter.php"> 
				<fieldset>
					<legend>List all the characters</legend>
					<div>Characters:
						<p><input type="submit" name="List" Value="List Characters"/></p>
						<?php
							if(!($stmt = $mysqli->prepare("SELECT DISTINCT ch.character_name, ra.race_id, re.realm_name  FROM LOTR_character ch
									INNER JOIN LOTR_race ra ON ch.fk_race_id=ra.race_id
									INNER JOIN LOTR_realm re ON ch.fk_home_id=re.realm_id")))
									{
										echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
									}

							if(!$stmt->execute()){
								echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
							}
							if(!$stmt->bind_result($name, $race, $home)){
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
		<br />
		<div>
			<form method="post" action="LOTRAddCharacter.php"> 
				<fieldset>
					<legend>Add a new character</legend>
					<div>Name:
						<input type="text" name="Name" />
					</div>
					<div>Race: 
					<select name="Race">
						<?php
						if(!($stmt = $mysqli->prepare("SELECT race_id, race_name FROM LOTR_race"))){
							echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
						}
						if(!$stmt->execute()){
							echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
						}
						if(!$stmt->bind_result($race_id, $race_name)){
							echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
						}
						while($stmt->fetch()){
							echo '<option value=" '. $race_id . ' "> ' . $race_name . '</option>\n';
						}
						$stmt->close(); 
						?>
					</select>
					</div>
					<div>Home:
					<select name="Home">
						<?php
						if(!($stmt = $mysqli->prepare("SELECT realm_id, realm_name FROM LOTR_realm"))){
							echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
						}
						if(!$stmt->execute()){
							echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
						}
						if(!$stmt->bind_result($realm_id, $realm_name)){
							echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
						}
						while($stmt->fetch()){
							echo '<option value=" '. $realm_id . ' "> ' . $realm_name . '</option>\n';
						}
						$stmt->close();
						?>
					</select>
					</div>
				</fieldset>
				<p><input type="submit" name="Add" Value="Add Character"/></p>
			</form>
		</div>
		<div>
			<form method="post" action="LOTRWeaponWielders.php">
				<fieldset>
					<legend>What weapon(s) does a character use?</legend>
						<select name="character">
							<?php
							if(!($stmt = $mysqli->prepare("SELECT character_id, character_name FROM LOTR_character ch"))){
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
				<p><input type="submit" value="Find Weapon(s)" /></p>
			</form>
		</div>
		<div>
			<form method="post" action="LOTRCharWeap.php">
				<fieldset>
					<legend>Give Character a New Weapon</legend>
											<select name="character">
							<?php
							if(!($stmt = $mysqli->prepare("SELECT character_id, character_name FROM 
							LOTR_character")))
							{
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
						<select name="weapon">
							<?php
							if(!($stmt = $mysqli->prepare("SELECT weapon_id, weapon_type FROM 
							LOTR_weapon we")))
							{
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
				<p><input type="submit" value="Add Weapon" /></p>
			</form>
		</div>
		<div>
			<form method="post" action="LOTRDeleteCharacter.php">
				<fieldset>
					<legend>Who do you want to delete?</legend>
						<select name="character">
							<?php
							if(!($stmt = $mysqli->prepare("SELECT character_id, character_name FROM LOTR_character ch"))){
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
				<p><input type="submit" value="Delete Character" /></p>
			</form>
		</div>		

	</body>
	</div>
</head>
</html>