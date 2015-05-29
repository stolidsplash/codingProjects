<?php
//Turn on error reporting
ini_set('display_errors', 'On');
//Connects to the database
$mysqli = new mysqli("oniddb.cws.oregonstate.edu","carlsjon-db","F2s8AWCzqQOgXxD6","carlsjon-db");

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
  <li class="tab-title active"><a href="http://web.engr.oregonstate.edu/~carlsjon/CS275FinalProject/LOTRRace.php">Races</a></li>
  <li class="tab-title"><a href="http://web.engr.oregonstate.edu/~carlsjon/CS275FinalProject/LOTRWeapons.php">Weapons</a></li>
</ul>
</div>
	<body>
		<div>
			<?php
			if(!($stmt = $mysqli->prepare("SELECT ch.character_name, we.weapon_type FROM LOTR_character ch
			INNER JOIN LOTR_char_weap cw ON ch.character_id=cw.fk_cw_char_id
			INNER JOIN LOTR_weapon we ON cw.fk_cw_weap_id=we.weapon_id
			WHERE ch.character_id=?"))){
				echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
			}
			if(!($stmt->bind_param("i",$_POST['character']))){
				echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
			}
			if(!$stmt->execute()){
				echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
			}

			if(!$stmt->store_result()){
				echo "Store result failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
			}

			if(!$stmt->bind_result($name, $weapon)){
				echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
			}

			if($stmt->num_rows > 0)
			{
				echo "<table>
					<tr>
						<td>What Weapons a Character Uses</td>
					</tr>
					<tr>
						<td>Name</td>
						<td>Weapon</td>
					</tr>";
				while($stmt->fetch()){
				 echo "<tr>\n<td>\n" . $name . "\n</td>\n<td>\n" . $weapon . "\n</td>\n</tr>";
				}
				echo "</table>";
			} else {
				echo "</table>";
				echo "Character uses no weapons.";
			}
			$stmt->close();
			?>
		</div>

	</body>
</html>