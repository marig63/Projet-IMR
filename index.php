<?php 

	$path = "";
	$param1 = "";
	$param2 = "";
	$param3 = "";
	$param4 = "null";
	$param5 = "null";
	//$adresse = $_SERVER['PHP_SHELF'];
	$i = 0;
	foreach($_GET as $cle => $valeur){
		//$adresse .= ($i == 1 ? '?' : '&').$cle.($valeur ? '='.$valeur : '');
		if($i == 0){
			$path = $valeur;
		}
		if($i == 1){
			$param1 = $valeur;
		}
		if($i == 2){
			$param2 = $valeur;
		}
		if($i == 3){
			$param3 = $valeur;
		}
		if($i == 4){
			$param4 = $valeur;
		}
		if($i == 5){
			$param5 = $valeur;
		}
		$i++;
	}

//echo $_SERVER['PATH_INFO'];
//echo '<pre>';
//print_r($_SERVER);
//echo '</pre>';

//echo rand(-9, 9);

function getExempleJson(){
	$output = '[ 	{ 
			"id" : "1",
			"name" : "Billy1",
			"role" : "ble",
			"lat" : "47.642728",
			"lon" : "6.866425",
			"ok" : "true"
		},
		{ 
			"id" : "2",
			"name" : "Billy2",
			"role" : "ble",
			"lat" : "47.642660",
			"lon" : "6.862621",
			"ok" : "true"
		},
		{ 
			"id" : "3",
			"name" : "Admin",
			"role" : "admin",
			"lat" : "47.642330",
			"lon" : "6.859815",
			"ok" : "true"
		}
	]';
	return $output;
}

//$path = $_SERVER['PATH_INFO'];
//$path = substr($path,1);

function update($macAdr,$param2,$param3,$param4,$param5){
	$output = '[ 	{ 
			"id" : "1",
			"name" : "Bob",
			"role" : "ble",
			"lat" : "'.(47.642685+(rand(-9, 9)/100000)).'",
			"lon" : "'.(6.862641+(rand(-9, 9)/100000)).'",
			"ok" : "true"
		},
		{ 
			"id" : "2",
			"name" : "Billy",
			"role" : "ble",
			"lat" : "'.(47.6426060+(rand(-9, 9)/100000)).'",
			"lon" : "'.(6.862621+(rand(-9, 9)/100000)).'",
			"ok" : "true"
		},
		{ 
			"id" : "3",
			"name" : "Michel",
			"role" : "admin",
			"lat" : "'.(47.642645+(rand(-9, 9)/100000)).'",
			"lon" : "'.(6.862605+(rand(-9, 9)/100000)).'",
			"ok" : "true"
		},
		{ 
			"id" : "4",
			"name" : "'.$param5.'",
			"role" : "ping",
			"lat" : "'.(47.642645).'",
			"lon" : "'.(6.862605).'",
			"ok" : "true"
		}
	]';
	return $output;
}

function newAcc($name,$macAdr){
	
	if($name == null or $macAdr == null){
		return;
	}
	//check if already exist
	//check if new
	
	$output = '[ 	{ 
			"id" : "12",
			"name" : "'.$name.'",
			"role" : "'.$macAdr.'",
			"lat" : "47.642728",
			"lon" : "6.866425",
			"ok" : "false"
		}
	]';
	
	echo $output;
}

function addMemberToGroup($macAdrAdmin,$macAdrNewMember){
	
	if($macAdrAdmin == null or $macAdrNewMember == null){
		return;
	}
	
	$output = '[ 	{ 
			"id" : "0",
			"name" : "'.$macAdrNewMember.'",
			"role" : "result",
			"lat" : "47.642728",
			"lon" : "6.866425",
			"ok" : "true"
		}
	]';
	
	return $output;
	
}

function newGroup($nameGroup,$macAdrAdmin){
	
	if($nameGroup == null or $macAdrAdmin == null){
		return;
	}
	
	$output = '[ 	{ 
			"id" : "0",
			"name" : "'.$nameGroup.'",
			"role" : "result",
			"lat" : "47.642728",
			"lon" : "6.866425",
			"ok" : "true"
		}
	]';
	
	return $output;
	
}

function requestSignal($macAdr,$hardwareAdr,$signal){ // inutile dans le projet , a supprimer à la fin
	$output = '[ 	{ 
			"id" : "12",
			"name" : "'.$hardwareAdr.'",
			"role" : "'.$signal.'",
			"lat" : "47.642728",
			"lon" : "6.866425",
			"ok" : "true"
		}
	]';
	
	return $output;
}


if( strcmp($path,"exemple") == 0 ){
	echo getExempleJson();
}

if( strcmp($path,"init") == 0 ){
	echo newAcc($param1,$param2);
}

if( strcmp($path,"addMember") == 0 ){
	//echo addMemberToGroup($macAdrAdmin,$macAdrNewMember);
	echo addMemberToGroup($param1,$param2);
}

if( strcmp($path,"createGroup") == 0 ){
	//echo newGroup($nameGroup?,$macAdrAdmin);
	echo newGroup($param1,$param2);
}

if( strcmp($path,"requestUpdate") == 0 ){
	//echo update(macAdr);
	echo update($param1,$param2,$param3,$param4,$param5);
}






if( strcmp($path,"promote") == 0 ){
	//echo newGroup($nameGroup?,$macAdrAdmin);
	echo ($param1." -> ".$param2);
}

if( strcmp($path,"disperse") == 0 ){
	//echo newGroup($nameGroup?,$macAdrAdmin);
	echo ($param1);
}

if( strcmp($path,"leaveGroup") == 0 ){
	//echo newGroup($nameGroup?,$macAdrAdmin);
	echo ($param1);
}

if( strcmp($path,"requestSignal") == 0 ){
	//echo newGroup($nameGroup?,$macAdrAdmin);
	//echo ($param1. " -> ".$param2 . " ->> ". $param3);
	
	echo requestSignal($param1,$param2,$param3);

}


?>
