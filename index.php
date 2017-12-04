<?php 

	$path = "";
	$param1 = "";
	$param2 = "";
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
			"admin" : "false",
			"lat" : "47.642728",
			"lon" : "6.866425",
			"zone" : "hein ?"
		},
		{ 
			"id" : "2",
			"name" : "Billy2",
			"admin" : "false",
			"lat" : "47.642660",
			"lon" : "6.862621",
			"zone" : "hein ?"
		},
		{ 
			"id" : "3",
			"name" : "Admin",
			"admin" : "true",
			"lat" : "47.642330",
			"lon" : "6.859815",
			"zone" : "hein ?"
		}
	]';
	return $output;
}

//$path = $_SERVER['PATH_INFO'];
//$path = substr($path,1);

function update($macAdr){
	$output = '[ 	{ 
			"id" : "1",
			"name" : "'.$macAdr.'",
			"admin" : "false",
			"lat" : "'.(47.642728+(rand(-9, 9)/100000)).'",
			"lon" : "'.(6.866425+(rand(-9, 9)/100000)).'",
			"zone" : "hein ?"
		},
		{ 
			"id" : "2",
			"name" : "'.$macAdr.'",
			"admin" : "false",
			"lat" : "'.(47.642660+(rand(-9, 9)/100000)).'",
			"lon" : "'.(6.862621+(rand(-9, 9)/100000)).'",
			"zone" : "hein ?"
		},
		{ 
			"id" : "3",
			"name" : "'.$macAdr.'",
			"admin" : "true",
			"lat" : "'.(47.642330+(rand(-9, 9)/100000)).'",
			"lon" : "'.(6.859815+(rand(-9, 9)/100000)).'",
			"zone" : "hein ?"
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
			"admin" : "true",
			"lat" : "47.642728",
			"lon" : "6.866425",
			"zone" : "hein ?"
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
			"admin" : "'.$macAdrAdmin.'",
			"lat" : "47.642728",
			"lon" : "6.866425",
			"zone" : "hein ?"
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
			"admin" : "'.$nameGroup.'",
			"lat" : "47.642728",
			"lon" : "6.866425",
			"zone" : "hein ?"
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
	echo update($param1);
}






?>
