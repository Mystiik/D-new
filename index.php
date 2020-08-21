<?php

include_once('init.php');
echo __DIR__;
// $user = "6wllEXObPTlGv8lSjXMH";
// $position = "22 22";
// $dmg_min = 4;
// $dmg_max = 9;
// $dmg_type = "NEUTRE";



// //$user = htmlspecialchars($_POST['user']);
// $position = "32 28";
// $command_line = "DAMAGEESPACE2-4ESPACENEUTRENEWLINERETRAIT_PMESPACE0-1NEWLINE";
// $command_line_text = str_replace("NEWLINE", ",", str_replace("ESPACE", " ", $command_line));

// $user_info = user_info($user);
// $map_info = get_map($user);
// $map = map_str_to_array($map_info['map_modif']);
// $map_name = $map_info['name'];


// $command_line = explode("NEWLINE", $command_line);


// for ($i = 0; $i < count($command_line); $i++) {
// 	$modif = explode("ESPACE", $command_line[$i]);
// 	$action = $modif[0];

// 	//DAMAGE => USER, X Y, DMG_MIN, DMG_MAX, DMG_TYPE
// 	if ($action == "DAMAGE") {
// 		$damage = explode("-", $modif[1]);
// 		damage($user, $position, $damage[0], $damage[1], $modif[2]);
// 	}
// }