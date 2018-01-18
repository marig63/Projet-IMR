<?php 

	$path = "";
	$param1 = "";
	$param2 = "";
	$param3 = "";
	$param4 = "null";
	$param5 = "null";
	//$adresse = $_SERVER['PHP_SHELF'];
	$i = 0;
	//CHECKLIST
	/*
	- init	//OK
	- addMember -> OK
	- CreateGroupe -> OK
	- Request Update -> TODO
	- promote -> OK
	- disperse -> OK
	- leaveGroup -> OK
	- requestSignal -> OK
	
	TODO
	- finir update
	- ajuster requestSignal
	- $output avec ligne radius
	
	*/
	//POUR TESTER
	//http://88.182.69.156/projetm2/trunk/index.php?action=requestUpdate&p=88;88;88&p2=resquest&p3=12&p4=61:56:85:78:p6&p5=-90

	//http://localhost/projetm2/index.php?action=exemple
	//Test CREATION DE GROUPE
	//http://localhost/projetm2/index.php?action=createGroup&p=test&p2=1234
	
	//Test ajout de membre dans un groupe
	//http://localhost/projetm2/index.php?action=addMember&p=12&p2=NewMember //Admin non existant
	//http://localhost/projetm2/index.php?action=addMember&p=Member1&p2=NewMember
	//http://localhost/projetm2/index.php?action=init&p=nomAcc&p2=Accid
	//Maj du nom d'un accompagnant existant
	//http://localhost/projetm2/index.php?action=init&p1=nomAcc1&p2=Accid
	//Test Promote
	//http://localhost/projetm2/index.php?action=promote&p1=1234&p2=Accid
	//Test disperse
	//http://localhost/projetm2/index.php?action=disperse&p=DisperseAcc2 //Vérifier pk retourne true quand il ne fait rien
	//http://localhost/projetm2/index.php?action=disperse&p=DisperseAcc1
	//Test leaveGroup
	//http://localhost/projetm2/index.php?action=leaveGroup&p=Accid
	
	//Test Request Signal 
	//http://localhost/projetm2/index.php?action=requestSignal&p1=2&p2=1234
	//Test Request Signal BLE
	//http://localhost/projetm2/index.php?action=requestSignal&p1=2&p2=ble
	
	//Test RequestUpdate
	//http://localhost/projetm2.index.php?action=requestUpdate&lat=25.2&lon=10.2list=[ab,-20]
	
	//http://localhost/projetm2/index.php?action=requestUpdate&p1=1234&p2=25.2&p3=10.2&p4=1112&p5=2223&p6=-20&p7=-60
	
  //  $bdd = new PDO('mysql:host='"http://88.182.69.156/phpmyadmin/"';dbname=projetm2;charset=utf8', 'root', 'bluetooth');
$bdd = new PDO('mysql:host=localhost;dbname=projetm2', 'root', '');

$listeSignaux = array();

	foreach($_GET as $cle => $valeur){
		//$adresse .= ($i == 1 ? '?' : '&').$cle.($valeur ? '='.$valeur : '');
		if($i == 0){
			//truc en rouge
			$path = $valeur;
		}
		if($i == 1){
			//@mac membre
			$param1 = $valeur;
		}
		if($i == 2){
			//lat
			$param2 = $valeur;
		}
		if($i == 3){//long
			$param3 = $valeur;
		}
		
		if($i > 3){
			array_push($listeSignaux, $valeur);
		}
		$i++;
	}

//echo $_SERVER['PATH_INFO'];
//echo '<pre>';
//print_r($_SERVER);
//echo '</pre>';

//echo rand(-9, 9);

//$reponse = $bdd->query('Tapez votre requête SQL ici');

function getExempleJson(){
	$output = '[ 	{ 
			"id" : "1",
			"name" : "Billy1",
			"role" : "ble",
			"lat" : "47.642728",
			"lon" : "6.866425",
			"rad" : 10,
			"ok" : "true"
		},
		
		{ 
			"id" : "2",
			"name" : "Billy2",
			"role" : "ble",
			"lat" : "47.642660",
			"lon" : "6.862621",
			"rad" : 10,
			"ok" : "true"
		},
		{ 
			"id" : "3",
			"name" : "Admin",
			"role" : "admin",
			"lat" : "47.642330",
			"lon" : "6.859815",
			"rad" : 10,
			"ok" : "true"
		}
	]';
	return $output;
}

//$path = $_SERVER['PATH_INFO'];
//$path = substr($path,1);

function update($bdd,$macAdr,$lat,$lon,$listSignal){
	//Une liste remplace param 4 et 5 (1ere moitié = @mac, 2eme moitié = signaux)
	$nb = count($listSignal);
	$imax = $nb/2;
	
	for($i=0; $i<$imax; $i++){
		
		$newMacAdr = $listSignal[$i];
		$forceSignal = $listSignal[$i+$imax];
		//Bloc déterminant les différents seuils de radius selon la force du signal
		if($forceSignal <= -120){
			$radius = 30;
		}
		elseif($forceSignal >-120 && $forceSignal <=-70 ){
			$radius = 20;
		}
		else{
			$radius = 10;
		}
	
		 updateIndividuel($bdd, $newMacAdr, $lat, $lon, $radius);
		
	}
	$output = '[ ';
	$idGroup = $bdd->query('SELECT DISTINCT(Acc_Groupe) FROM Accompagnant WHERE Acc_ID = "'.$macAdr.'"');
	$idG = $idGroup->fetch();
	
	$res = $bdd->query('SELECT * FROM Accompagnant WHERE Acc_Groupe = '.$idG['Acc_Groupe']);
	
	
	while($resultat = $res->fetch()){
		$fiche = ' { 
					"id" : "'.$resultat['Acc_ID'].'",
					"name" : "'.$resultat['Acc_Nom'].'",
					"role" : "member",
					"lat" : "'.$resultat['Acc_Lat'].'",
					"lon" : "'.$resultat['Acc_Lon'].'",
					"rad" : "'.$resultat['Acc_Radius'].'",
					"ok" : "true"
					} , ';
		
		$output = $output.$fiche;
		
	}
	
	$res = $bdd->query('SELECT * FROM BLE WHERE BLE_Groupe = '.$idG['Acc_Groupe']);
	while($resultat = $res->fetch()){
		$fiche = ' { 
					"id" : "'.$resultat['BLE_ID'].'",
					"name" : "'.$resultat['BLE_Nom'].'",
					"role" : "ble",
					"lat" : "'.$resultat['BLE_Lat'].'",
					"lon" : "'.$resultat['BLE_Lon'].'",
					"rad" : "'.$resultat['BLE_Radius'].'",
					"ok" : "true"
					} , ';
		
		$output = $output.$fiche;
		
	}
	$output = substr($output, 0, -2);
	$output = $output.']';
	
	
	return $output;
}
function updateIndividuel($bdd, $macAdr, $lat, $lon, $radius){
	
	//Tester si il s'agit d'un accompagnant ou d'un ble
	
	$reponse = $bdd->query('SELECT * FROM Accompagnant WHERE ACC_ID = "'.$macAdr.'"');
	$rep = $reponse->fetch();
	
	if($rep == null){
		//macAdr d'un BLE
		$reponse = $bdd->query('SELECT * FROM ble WHERE BLE_ID = "'.$macAdr.'"');
		$rep = $reponse->fetch();
		if($rep == null){
			//Mauvaise adresse
			$output = '[ 	{ 
					"id" : "12",
					"name" : "Test",
					"role" : "'.$macAdr.'",
					"lat" : "'.$lat.'",
					"lon" : "'.$lon.'",
					"rad" : "'.$radius.'",
					"ok" : "false"
					}
					]';
			return $output;		
		
		}
		else{
		
		
		$bdd->query('UPDATE BLE SET BLE_Lat = '.$lat. ', BLE_Lon = '.$lon. ', BLE_radius = '.$radius. ' WHERE BLE_ID = "'.$macAdr.'"');
		$infos = $bdd->query('SELECT * FROM BLE WHERE BLE_ID = "'.$macAdr.'"');
		$inf = $infos->fetch();
		$output = '[ 	{ 
					"id" : "'.$macAdr.'",
					"name" : "'.$inf['BLE_Nom'].'",
					"role" : "ble",
					"lat" : "'.$lat.'",
					"lon" : "'.$lon.'",
					"rad" : "'.$radius.'",
					"ok" : "true"
					}
					]';
			return $output;
		
		}
	}
	else{
		
		$bdd->query('UPDATE Accompagnant SET Acc_Lat = '.$lat. ', Acc_Lon = '.$lon. ', Acc_radius = '.$radius. ' WHERE Acc_ID = "'.$macAdr.'"');
		$infos = $bdd->query('SELECT * FROM Accompagnant WHERE ACC_ID = "'.$macAdr.'"');
		$inf = $infos->fetch();
		$output = '[ 	{ 
					"id" : "'.$macAdr.'",
					"name" : "'.$inf['Acc_Nom'].'",
					"role" : "member",
					"lat" : "'.$lat.'",
					"lon" : "'.$lon.'",
					"rad" : "'.$radius.'",
					"ok" : "true"
					}
					]';
			return $output;
	}	
	
}
	
	
	/*
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
			"role" : "member",
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
	*/

//Changer parametre BD -> zone latitude longitude radius	le radius est prédéfini pour les antennes
function requestSignal($bdd, $macAdrAnt, $macAdr){
	//CHANGER PARAM DANS BD LAT LON EN FLOAT
	$radius = 20;
	if($macAdr == null or $macAdrAnt == null){
		$output = '[ 	{ 
			"id" : "12",
			"name" : "Test",
			"role" : "'.$macAdr.'",
			"lat" : "47.642728",
			"lon" : "6.866425",
			"rad" : 10,
			"ok" : "false"
		}
	]';
		return $output;
	}
	else{
		$reponse = $bdd->query('SELECT * FROM Antenne WHERE Ant_ID = '.$macAdrAnt);
		$rep = $reponse->fetch();
		$lat = $rep['Ant_Lat'];
		$lon = $rep['Ant_Lon'];
		$rad = $rep['Ant_Radius'];
		if($rep == null){
			$output = '[ 	{ 
				"id" : "12",
				"name" : "Test",
				"role" : "'.$macAdrAnt.'",
				"lat" : "47.642728",
				"lon" : "6.866425",
				"rad" : 10,
				"ok" : "false"
			}
			]';
			return $output;
		}
		else {
			$recup = $bdd->query('SELECT * FROM Accompagnant WHERE ACC_ID = "'.$macAdr.'"');
			$rec = $recup->fetch();
			
			if($rec == null){
				//CAS où il ne s'agit pas d'un Acc
				$recup = $bdd->query('SELECT * FROM BLE WHERE BLE_ID = "'.$macAdr.'"');
				$rec = $recup->fetch();
				
				if($rec == null){
					$output = '[ 	{ 
						"id" : "12",
						"name" : "Test",
						"role" : "'.$macAdr.'",
						"lat" : "47.642728",
						"lon" : "6.866425",
						"rad" : 10,
						"ok" : "false"
					}
					]';
					return $output;
					
				}
				else {
					
					$bdd->query('UPDATE BLE SET BLE_Lat = '.$lat. ', BLE_Lon = '.$lon.', BLE_radius = '.$rad.' WHERE BLE_ID = "'.$macAdr.'"');
					
				}
			}
			else{
				$bdd->query('UPDATE Accompagnant SET Acc_Lat = '.$lat. ', Acc_Lon = '.$lon.', Acc_radius = '.$rad.' WHERE Acc_ID = "'.$macAdr.'"');
				
			}
		
			$output = '[ 	{ 
				"id" : "12",
				"name" : "Test",
				"role" : "'.$macAdr.'",
				"lat" : "'.$lat.'",	
				"lon" : "'.$lon.'",
				"rad" : "'.$rad.'",
				"ok" : "true"
				}
				]';
				return $output;
		}
				
	
	}
	
}

function newAcc($bdd, $name,$macAdr){
	
	if($name == null or $macAdr == null){
		$output = '[ 	{ 
			"id" : "12",
			"name" : "'.$name.'",
			"role" : "'.$macAdr.'",
			"lat" : "47.642728",
			"lon" : "6.866425",
			"rad" : 10,
			"ok" : "false"
		}
	]';
		return $output;
	}
	//check if already exist
	//check if new
	$reponse = $bdd->query('SELECT * from Accompagnant WHERE ACC_ID = "'.$macAdr.'"');
	$rep = $reponse->fetch();
	if($rep != null){
		//Compte déja existant
				$output = '[ 	{ 
			"id" : "12",
			"name" : "'.$name.'",
			"role" : "'.$macAdr.'",
			"lat" : "47.642728",
			"lon" : "6.866425",
			"rad" : 10,
			"ok" : "MajNom"
		}
	]';
		//mettre à jour son nom
		//OK
		$bdd->query('UPDATE Accompagnant SET Acc_nom = "'.$name. '" WHERE ACC_ID = "'.$macAdr.'"');
		
	}
	
	
	else {
	
	
	//voir Acc_groupe
	//ajoute un accompagnant
		$bdd->query('INSERT INTO Accompagnant (ACC_id, Acc_nom) VALUES ("'.$macAdr.'", "'.$name.'")');	
		$output = '[ 	{ 
				"id" : "12",
				"name" : "'.$name.'",
				"role" : "'.$macAdr.'",
				"lat" : "47.642728",
				"lon" : "6.866425",
				"rad" : 10,
				"ok" : "NewAcc"
			}
		]';	
		
	}
	return $output;
	
}

function addMemberToGroup($bdd, $macAdrAdmin,$macAdrNewMember){
	//Si existe déja mettre à jour dans le nouveau groupe
	if($macAdrAdmin == null or $macAdrNewMember == null){
		$output = '[ 	{ 
			"id" : "0",
			"name" : "'.$macAdrNewMember.'",
			"role" : "result",
			"lat" : "47.642728",
			"lon" : "6.866425",
			"rad" : 10,
			"ok" : "false"
		}
	]';
		
		return $output;
	}
	

	//recup du groupe de l'admin
	$reponseSql = $bdd->query('SELECT * FROM Responsable WHERE Resp_id ="' .$macAdrAdmin.'"');
	$donnees = $reponseSql->fetch();
	if($donnees ==null){
		$output = '[ 	{ 
			"id" : "0",
			"name" : "'.$macAdrNewMember.'",
			"role" : "result",
			"lat" : "47.642728",
			"lon" : "6.866425",
			"rad" : 10,
			"ok" : "pas un resp"
		}
	]';
	
		
	}
	else{
	$RecupGrp = $donnees['Resp_Groupe'];
	$bdd->query('INSERT INTO Accompagnant (ACC_ID, Acc_groupe) VALUES("'.$macAdrNewMember.'", '.$RecupGrp.')');
	
		$output = '[ 	{ 
			"id" : "0",
			"name" : "'.$macAdrNewMember.'",
			"role" : "result",
			"lat" : "47.642728",
			"lon" : "6.866425",
			"rad" : 10,
			"ok" : "true"
		}
	]';
	
	}
	
	return $output;
	
}

function newGroup($bdd,$nameGroup,$macAdrAdmin){
	
	if($nameGroup == null or $macAdrAdmin == null){
		$output = '[ 	{ 
			"id" : "0",
			"name" : "'.$nameGroup.'",
			"role" : "result",
			"lat" : "47.642728",
			"lon" : "6.866425",
			"ok" : "false"
		}
	]';
		return $output;
	}
	
	//création d'un nouveau grp + resp
	$bdd->query('INSERT INTO Groupe (Gr_name, Gr_actif) VALUES ("'.$nameGroup.'" , true )');
	$recupIDGroupe = $bdd->query('SELECT Gr_ID FROM Groupe WHERE Gr_name = "'.$nameGroup.'"');
	$donnees = $recupIDGroupe->fetch();
	$ID_grp = $donnees['Gr_ID'];
	
	//Vérifier si requete insert nécessite syntaxe ''
	$bdd->query('INSERT INTO Responsable (Resp_ID, Resp_Groupe) VALUES ("'.$macAdrAdmin.'" , '.$ID_grp.')');
	
	
	
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

/*
function requestSignal($bdd, $macAdr,$hardwareAdr,$signal){ // inutile dans le projet , a supprimer à la fin
	$output = '[ 	{ 
			"id" : "12",
			"name" : "'.$hardwareAdr.'",
			"role" : "'.$signal.'",
			"lat" : "47.642728",
			"lon" : "6.866425",
			"rad" : 10,
			"ok" : "true"
		}
	]';
	

	return $output;
}
*/

function promote($bdd, $macAdrAdmin, $macAdrNew){
		if($macAdrNew == null or $macAdrAdmin == null){
				$output = '[ 	{ 
			"id" : "12",
			"name" : "'.$macAdrNew.'",
			"role" : "result",
			"lat" : "47.642728",
			"lon" : "6.866425",
			"rad" : 10,
			"ok" : "false"
		}
	]';
		return $output;
	}
	//On récupère les informations du responsable du groupe
	$recup_grp = $bdd->query('SELECT * FROM Responsable WHERE Resp_ID = '.$macAdrAdmin);
	$recup = $recup_grp->fetch();
	if($recup != null){
	//On récupère l'id du groupe
	$ID_grp = $recup['Resp_Groupe'];
	//On change la valeur du responsable de ce groupe
	$bdd->query('UPDATE Responsable SET Resp_ID = "'.$macAdrNew. '" WHERE Resp_Groupe = '.$ID_grp);
		
		$output = '[ 	{ 
			"id" : "12",
			"name" : "'.$macAdrNew.'",
			"role" : "result",
			"lat" : "47.642728",
			"lon" : "6.866425",
			"rad" : 10,
			"ok" : "true"
		}
	]';
	
	}
	else {
			$output = '[ 	{ 
			"id" : "12",
			"name" : "'.$macAdrNew.'",
			"role" : "result",
			"lat" : "47.642728",
			"lon" : "6.866425",
			"rad" : 10,
			"ok" : "false no admin"
		
		}
	]';
	}
	return $output;
}

function disperse($bdd, $macAdrAdmin){

		
		if($macAdrAdmin == null){
		$output = '[ 	{ 
			"id" : "12",
			"name" : "'.$macAdrAdmin.'",
			"role" : "result",
			"lat" : "47.642728",
			"lon" : "6.866425",
			"rad" : 10,
			"ok" : "false"
		
		}
	]';		
		
		return $output;
	}
	$RecupID_Gr = $bdd->query('SELECT * FROM Responsable WHERE Resp_ID = "'.$macAdrAdmin. '"');
	if($RecupID_Gr == null){
		
		$output = '[ 	{ 
			"id" : "12",
			"name" : "'.$macAdrAdmin.'",
			"role" : "result",
			"lat" : "47.642728",
			"lon" : "6.866425",
			"rad" : 10,
			"ok" : "falsePasAdmin"
		
		}
	]';
	return $output;
	}
	$recup = $RecupID_Gr->fetch();
	$id_Grp = $recup['Resp_Groupe'];
	//Suppression des Accompagnants
	$bdd->query('DELETE FROM Accompagnant WHERE Acc_groupe = '.$id_Grp);
	//les BLO ont un update de la valeur du groupe à null
	$bdd->query('UPDATE BLE SET BLE_Groupe = null WHERE Br_Groupe = '.$id_Grp);
	//Suppression de l'admin
	$bdd->query('DELETE FROM Responsable WHERE Resp_ID = "'.$macAdrAdmin. '"');
	//Suppression du groupe
	$bdd->query('DELETE FROM Groupe WHERE Gr_ID = '.$id_Grp);
	
	$output = '[ 	{ 
			"id" : "12",
			"name" : "'.$macAdrAdmin.'",
			"role" : "result",
			"lat" : "47.642728",
			"lon" : "6.866425",
			"ok" : "true"
		
		}
	]';
	return $output;
}

function leaveGroup($bdd, $macAdr){
		if($macAdr == null){
				$output = '[ 	{ 
			"id" : "12",
			"name" : "'.$macAdr.'",
			"role" : "result",
			"lat" : "47.642728",
			"lon" : "6.866425",
			"rad" : 10,
			"ok" : "false"
		
		}
	]';
			
		return $output;
	}
	
	//On retire un accompagnant
	
	$bdd->query('DELETE FROM Accompagnant WHERE ACC_ID = "'.$macAdr. '"');
	
		$output = '[ 	{ 
			"id" : "12",
			"name" : "'.$macAdr.'",
			"role" : "result",
			"lat" : "47.642728",
			"lon" : "6.866425",
			"rad" : 10,
			"ok" : "true"
		
		}
	]';
	return $output;
}

if( strcmp($path,"exemple") == 0 ){
	
	echo getExempleJson();
}

if( strcmp($path,"init") == 0 ){
	
	echo newAcc($bdd, $param1,$param2);
}

if( strcmp($path,"addMember") == 0 ){
	
	echo addMemberToGroup($bdd, $param1,$param2);
}

if( strcmp($path,"createGroup") == 0 ){
	
	echo newGroup($bdd, $param1,$param2);
}

if( strcmp($path,"requestUpdate") == 0 ){
	//$param4 = array('1111','2222','3333','-120','-80','-10');
	echo update($bdd, $param1,$param2,$param3,$listeSignaux);
}

if( strcmp($path,"promote") == 0 ){
	
	echo promote($bdd, $param1, $param2);
}

if( strcmp($path,"disperse") == 0 ){
	
	
	echo disperse($bdd, $param1);
}

if( strcmp($path,"leaveGroup") == 0 ){
	
	echo leaveGroup($bdd, $param1);
}

if( strcmp($path,"requestSignal") == 0 ){
	
	echo requestSignal($bdd, $param1, $param2);

}

?>
