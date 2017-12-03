<?php 

	$path = "";
	//$adresse = $_SERVER['PHP_SHELF'];
	$i = 0;
	foreach($_GET as $cle => $valeur){
		//$adresse .= ($i == 1 ? '?' : '&').$cle.($valeur ? '='.$valeur : '');
		if($i == 0){
			$path = $valeur;
		}
		$i++;
	}

//echo $_SERVER['PATH_INFO'];
//echo '<pre>';
//print_r($_SERVER);
//echo '</pre>';

function newAcc($name, $macAdr){
	return false;
}

function getExempleJson(){
	$output = '[ 	{ 
			"id" : "1",
			"name" : "Billy1",
			"admin" : "false",
			"autre" : "hein ?"
		},
		{ 
			"id" : "2",
			"name" : "Billy2",
			"admin" : "false",
			"autre" : "hein ?"
		},
		{ 
			"id" : "3",
			"name" : "Admin",
			"admin" : "true",
			"autre" : "hein ?"
		}
	]';
	return $output;
}

//$path = $_SERVER['PATH_INFO'];
//$path = substr($path,1);

if( strcmp($path,"exemple") == 0 ){
	echo getExempleJson();
}

if( strcmp($path,"init") == 0 ){
	//echo newAcc($name,$macAdr);
}

if( strcmp($path,"addMember") == 0 ){
	//echo addMemberToGroup($macAdrAdmin,$macAdrNewMember);
}

if( strcmp($path,"createGroup") == 0 ){
	//echo newAcc($nameGroup?,$macAdrAdmin);
}

if( strcmp($path,"requestUpdate") == 0 ){
	//echo update(macAdr);
}






?>
