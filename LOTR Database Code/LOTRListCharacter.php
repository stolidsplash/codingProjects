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
  <li class="tab-title active"><a href="http://web.engr.oregonstate.edu/~carlsjon/CS275FinalProject/LOTRRace.php">Races</a></li>
  <li class="tab-title"><a href="http://web.engr.oregonstate.edu/~carlsjon/CS275FinalProject/LOTRWeapons.php">Weapons</a></li>
</ul>
</div>
	<body>
		<div>
			<?php
				if(!($stmt = $mysqli->prepare("SELECT DISTINCT ch.character_name, ra.race_name, re.realm_name  FROM LOTR_character ch
					INNER JOIN LOTR_race ra ON ch.fk_race_id=ra.race_id
					INNER JOIN LOTR_realm re ON ch.fk_home_id=re.realm_id"))){
					echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
				}
				if(!$stmt->execute()){
					echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
				}
				if(!$stmt->bind_result($name, $race, $home)){
					echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
				}

				echo "<table>
					<tr>
						<td>List the Characters</td>
					</tr>
					<tr>
						<td>Name</td>
						<td>Race</td>
						<td>Home</td>
					</tr>";
				while($stmt->fetch()){
				 echo "<tr>\n<td>\n" . $name . "\n</td>\n<td>\n" . $race . "\n</td>\n<td>\n" . $home . "\n</td>\n</tr>";
				}
				echo "</table>";

				$stmt->close();
			?>
		</div>
	</body>
</html>
	