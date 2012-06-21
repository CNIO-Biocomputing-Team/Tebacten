<?php 
//ini_set( "display_errors", 0);
//Antes de hacer un include de este fichero tiene que estar creada la conexion a la base de datos!!!

function modificarTexto($idEvidence,$textEvidence){
	
	//Necesitamos un arrayIdEnzymes que contenga todas las enzimas de la evidencia $idEvidence. Para ello:
	$arrayEnzymes=array();
	$selectSQL="SELECT textmining_enzyme_name FROM enzymes a, evidences_enzymes b WHERE a.id_enzyme = b.id_enzyme AND id_evidence ='".$idEvidence."'";
	$result=mysql_query($selectSQL);
	while ($row = mysql_fetch_row($result)){
			$textmining_enzyme_name=$row[0];
			$arrayEnzymes[]=$textmining_enzyme_name;
	}
	//Necesitamos un arrayIdCompoundsInput que contenga todas los compoundsInput de la evidencia $idEvidence. Para ello:
	$arrayCompoundsInput=array();
	$selectSQL2="SELECT textmining_compound_name FROM compounds a, evidences_compounds b WHERE a.id_compound = b.id_compound AND id_evidence ='".$idEvidence."' AND b.input_output=0";
	//print "<br/>$selectSQL2"; 
	$result2=mysql_query($selectSQL2);
	while ($row2 = mysql_fetch_row($result2)){
			$textmining_compound_name=$row2[0];
			$arrayCompoundsInput[]=$textmining_compound_name;
	}
	//Necesitamos un arrayIdCompoundsOutput que contenga todas los compoundsOutput de la evidencia $idEvidence. Para ello:
	$arrayCompoundsOutput=array();
	$selectSQL3="SELECT textmining_compound_name FROM compounds a, evidences_compounds b WHERE a.id_compound = b.id_compound AND id_evidence ='".$idEvidence."' AND b.input_output=1";
	//print "<br/>$selectSQL3";
	$result3=mysql_query($selectSQL3);
	while ($row3 = mysql_fetch_row($result3)){
			$textmining_compound_name=$row3[0];
			$arrayCompoundsOutput[]=$textmining_compound_name;
	}
	//Necesitamos un arrayIdOrganisms que contenga todas los organismos de la evidencia $idEvidence. Para ello:
	$arrayOrganisms=array();
	$selectSQL4="SELECT textmining_organism_name FROM organisms a, evidences_organisms b WHERE a.id_organism = b.id_organism AND id_evidence ='".$idEvidence."'";
	$result4=mysql_query($selectSQL4);
	while ($row4 = mysql_fetch_row($result4)){
			$textmining_organism_name=$row4[0];
			$arrayOrganisms[]=$textmining_organism_name;
	}
	
	//Ya tenemos los cuatro arrays, los recorremos cambiando el color de lo que vayamos teniendo, en funcion del tipo de compuesto/enzima/organismo
	foreach($arrayEnzymes as $textmining_enzyme_name){
		$textEvidence=str_ireplace($textmining_enzyme_name,"<span class=\"enzyme\">".$textmining_enzyme_name."</span>",$textEvidence);
	}
	foreach($arrayCompoundsInput as $textmining_compound_input_name){
		$textEvidence=str_ireplace($textmining_compound_input_name,"<span class=\"compound\">".$textmining_compound_input_name."</span>",$textEvidence);
	}
	foreach($arrayCompoundsOutput as $textmining_compound_output_name){
		$textEvidence=str_ireplace($textmining_compound_output_name,"<span class=\"compound\">".$textmining_compound_output_name."</span>",$textEvidence);
	}
	foreach($arrayOrganisms as $textmining_organism_name){
		$textEvidence=str_ireplace($textmining_organism_name,"<span class=\"organism\">".$textmining_organism_name."</span>",$textEvidence);
	}
	return $textEvidence;
}
?>