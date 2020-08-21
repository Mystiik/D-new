<?php
	if(isset($_POST['user']) and isset($_POST['modif']))
	{
		include_once('init.php');
		
		$user = htmlspecialchars($_POST['user']);
		$command_line = htmlspecialchars($_POST['modif']);
		if(isset($_POST['position'])) { $position = htmlspecialchars($_POST['position']); }
		
		$user_info = user_info($user);
		$map_info = get_map($user);
		$map = map_str_to_array($map_info['map_modif']);
		$map_name = $map_info['name'];
		
		
		$command_line = explode(";", $command_line);
		
		
		for($i=0;$i<count($command_line); $i++)
		{
			$modif = explode(" ", $command_line[$i]);
			$action = $modif[0];
			
			//DAMAGE 2-4 NEUTRE
			if($action=="DAMAGE")
			{
				$damage = explode("-", $modif[1]);
				
				//DAMAGE => USER, X Y, MIN, MAX, TYPE
				damage($user, $position, $damage[0], $damage[1], $modif[2]);
			}
			
			//RETRAIT_PM 0-1
			if($action=="RETRAIT_PM")
			{
				$retrait = explode("-", $modif[1]);
				
				//RETRAIT_PM => USER, X Y, MIN, MAX
				retrait_pm($user, $position, $retrait[0], $retrait[1]);
			}
			
			//END_TURN
			if($action=="END_TURN")
			{
				//END_TURN => USER
				end_turn($user);
			}
		}
		
		send_modif($user, $map_name, $_POST['position']."  ".$_POST['modif']);
	}
?>
















