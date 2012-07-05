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
		$textEvidence=str_ireplace($textmining_enzyme_name,"<mark class=\"enzyme\">".$textmining_enzyme_name."</mark>",$textEvidence);
	}
	foreach($arrayCompoundsInput as $textmining_compound_input_name){
		$textEvidence=str_ireplace($textmining_compound_input_name,"<mark class=\"compound\">".$textmining_compound_input_name."</mark>",$textEvidence);
	}
	foreach($arrayCompoundsOutput as $textmining_compound_output_name){
		$textEvidence=str_ireplace($textmining_compound_output_name,"<mark class=\"compound\">".$textmining_compound_output_name."</mark>",$textEvidence);
	}
	foreach($arrayOrganisms as $textmining_organism_name){
		$textEvidence=str_ireplace($textmining_organism_name,"<mark class=\"organism\">".$textmining_organism_name."</mark>",$textEvidence);
	}
	return "<p>".$textEvidence;
}
function printPagination($whatToSearch,$lastpage,$page,$adjacents,$prev,$next){
	
	
	/* 
		Now we apply our rules and draw the pagination object. 
		We're actually saving the code to a variable in case we want to draw it more than once.
	*/
	$pagination = "";
	if($lastpage > 1)
	{	
		$pagination .= "<div class=\"pagination\">";
		//previous button
		if ($page > 1) 
			$pagination.= "<a onClick=\"return searchEvidences('$whatToSearch','search',$prev);\" href=\"#\"> previous</a>";
		else
			$pagination.= "<span class=\"disabled\">previous</span>";	
		
		//pages	
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{	
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $page)
					$pagination.= "<span class=\"current\">$counter</span>";
				else
					$pagination.= "<a onClick=\"return searchEvidences('$whatToSearch','search',$counter);\" href=\"#\">$counter</a>";					
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($page < 1 + ($adjacents * 2))		
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a onClick=\"return searchEvidences('$whatToSearch','search',$counter);\" href=\"#\" >$counter</a>";					
				}
				$pagination.= "...";
				$pagination.= "<a onClick=\"return searchEvidences('$whatToSearch','search',$lpm1);\" href=\"#\">$lpm1</a>";
				$pagination.= "<a onClick=\"return searchEvidences('$whatToSearch','search',$lastpage);\" href=\"#\" >$lastpage</a>";		
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
				$pagination.= "<a onClick=\"return searchEvidences('$whatToSearch','search',1);\" href=\"#\" >1</a>";
				$pagination.= "<a onClick=\"return searchEvidences('$whatToSearch','search',2);\" href=\"#\" >2</a>";
				$pagination.= "...";
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a onClick=\"return searchEvidences('$whatToSearch','search',$counter);\" href=\"#\">$counter</a>";					
				}
				$pagination.= "...";
				$pagination.= "<a onClick=\"return searchEvidences('$whatToSearch','search',$lpm1);\" href=\"#\" >$lpm1</a>";
				$pagination.= "<a onClick=\"return searchEvidences('$whatToSearch','search',$lastpage);\" href=\"#\" >$lastpage</a>";		
			}
			//close to end; only hide early pages
			else
			{
				$pagination.= "<a onClick=\"return searchEvidences('$whatToSearch','search',1);\" href=\"#\">1</a>";
				$pagination.= "<a onClick=\"return searchEvidences('$whatToSearch','search',2);\" href=\"#\">2</a>";
				$pagination.= "...";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a onClick=\"return searchEvidences('$whatToSearch','search',$counter);\" href=\"#\" >$counter</a>";					
				}
			}
		}
		
		//next button
		if ($page < $counter - 1) 
			$pagination.= "<a onClick=\"return searchEvidences('$whatToSearch','search',$next);\" href=\"#\">next</a>";
		else
			$pagination.= "<span class=\"disabled\">next</span>";
		$pagination.= "</div>\n";		
	}
	
	return $pagination;

}





?>