<?php
//Turn on error reporting
ini_set('display_errors', 'On');
//Connects to the database
$mysqli = new mysqli("oniddb.cws.oregonstate.edu","carlsjon-db","F2s8AWCzqQOgXxD6","carlsjon-db");
if(!$mysqli || $mysqli->connect_errno){
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
<?php

if(!($stmt = $mysqli->prepare("INSERT INTO LOTR_character(character_name, fk_race_id, fk_home_id) VALUES (?, ?, ?)"	))){
	echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
}
if(!($stmt->bind_param("sii",$_POST['Name'],$_POST['Race'],$_POST['Home']))){
	echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
}
if(!$stmt->execute()){
	echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
} 
else {
	echo "Added " . $stmt->affected_rows . " rows to LOTR_character."; 
}
?>