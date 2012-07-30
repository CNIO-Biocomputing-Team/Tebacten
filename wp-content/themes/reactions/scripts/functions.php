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

function printOrganismsTable($organismsCounter,$textminingOrganismName,$id_organism_ncbi,$orgs){
	echo "<table class=\"compoundsEnzymes\">";
		echo "<tr>";
		echo "<td>Organism: </td>";
		echo "<td>";
			echo "<SELECT id=\"textminingOrganismName_$organismsCounter\" type=\"text\" NAME=\"textminingOrganismName_$organismsCounter\" maxlenght=\"255\" size=\"1\" onChange=\"insertTaxonomy($organismsCounter,'$idEvidence');\">";
				echo "<OPTION value=\"\" >Select an organism";
				echo $orgs;
				echo "</SELECT>\n</div></td>";
		echo "</tr>";
	echo "</table>";
	echo "<div id=\"organism_ajax_$organismsCounter\">\n";
	echo "</div><!-- End div#organism_ajax_$organismsCounter -->\n</td>";
}

function createStringInputOutput($inputOutput,$substrateScore,$productScore,$compoundsCounter){
	if(($inputOutput=="")){
		//No existe anotación para input/output entonces evaluamos los scores.
		if (($substrateScore==0)&&($productScore==0)){
			$inputOutput="";
			$strInputOutput="<td>Substrate/Product: </td><td><SELECT id=\"inputOutput_$compoundsCounter\" name=\"inputOutput_$compoundsCounter\">\n<OPTION value=\"select\" selected>Select<OPTION value=\"input\">Substrate\n<OPTION value=\"output\" >Product\n</SELECT></td>";
		}
		elseif($substrateScore>$productScore){
			$inputOutput="input";
			$strInputOutput="<td>Substrate/Product: </td><td><SELECT id=\"inputOutput_$compoundsCounter\" name=\"inputOutput_$compoundsCounter\">\n<OPTION value=\"select\">Select<OPTION value=\"input\" selected>Substrate\n<OPTION value=\"output\" >Product\n</SELECT></td>";
		}
		elseif($productScore>$substrateScore){
			$inputOutput="output";
			$strInputOutput="<td>Substrate/Product: </td><td><SELECT id=\"inputOutput_$compoundsCounter\" name=\"inputOutput_$compoundsCounter\">\n<OPTION value=\"select\">Select<OPTION value=\"input\">Substrate\n<OPTION value=\"output\" selected >Product\n</SELECT></td>";
		}
	}
	elseif ($inputOutput==0){
		//ya esta anotado como substrate(input)
		$inputOutput="input";
		$strInputOutput="<td>Substrate/Product: </td><td><SELECT id=\"inputOutput_$compoundsCounter\" name=\"inputOutput_$compoundsCounter\">\n<OPTION value=\"select\">Select<OPTION value=\"input\" selected>Substrate\n<OPTION value=\"output\" >Product\n</SELECT></td>";
	}
	elseif($inputOutput==1){
		//ya esta anotado como product(output)
		$inputOutput="output";
		$strInputOutput="<td>Substrate/Product: </td><td><SELECT id=\"inputOutput_$compoundsCounter\" name=\"inputOutput_$compoundsCounter\">\n<OPTION value=\"select\">Select<OPTION value=\"input\">Substrate\n<OPTION value=\"output\" selected >Product\n</SELECT></td>";
	}
	return $strInputOutput;	
}

function printCompoundsTable($compoundsCounter,$textminingCompoundName,$idChebi,$strInputOutput){
	echo "<div id=\"compound_$compoundsCounter\" name=\"compound_$compoundsCounter\" style=\"display:block;\">\n";
		echo "<table class=\"compoundsEnzymes\">";
			echo "<tr>";
				echo "<td>Compound name: </td>";
				echo "<td><input id=\"textminingCompoundName_$compoundsCounter\" type=\"text\" NAME=\"textminingCompoundName_$compoundsCounter\" maxlenght=\"255\" size=\"20\" value=\"$textminingCompoundName\">";
					echo "<small><a href=\"javascript:;\" onClick=\"insertChebiIds('$compoundsCounter')\">Click to search</a></small></td>";
			echo "</tr>";
			echo "<tr>";
				if ($idChebi!=""){//Si tenemos idChebi lo mostramos normalmente
					echo "<td>ChEBI: </td>";
					echo "<td>";
						echo "<div id=\"listaCompoundsIds_$compoundsCounter\">";
							echo "<select id=\"listOfChebiIds_$compoundsCounter\" type=\"text\" NAME=\"listOfChebiIds_$compoundsCounter\" maxlenght=\"255\" >";
								echo "<option value=\"\">Select <option value=\"$idChebi\" SELECTED>$textminingCompoundName ($idChebi)";
							echo "</select>";
						echo "</div>";
					echo "</td>";
				}
				else{//Sino hay chebiId entonces mostramos un select vacio, sin options
					echo "<td>ChEBI: </td>";
					echo "<td>";
						echo "<div id=\"listaCompoundsIds_$compoundsCounter\">";
							echo "<select id=\"listOfChebiIds_$compoundsCounter\" type=\"text\" NAME=\"listOfChebiIds_$compoundsCounter\" maxlenght=\"255\" >";
							echo "</select>";
						echo "</div>";
					echo "</td>";
				}
			echo "</tr>";
			echo "<tr>";
			echo $strInputOutput;
			echo "</tr>";
			echo "<tr>";
				echo "<td>";
					echo "<input id=\"delete_compound_$compoundsCounter\" type=\"button\" onclick=\"deleteCompound('compound_$compoundsCounter')\" name=\"delete_compound_$compoundsCounter\" value=\"Delete Compound \" class=\"\">";
				echo "</td>";
				echo "<td></td>";
			echo "</tr>";
		echo "</table>";
	echo "</div><!-- end div#compound_$compoundsCounter -->";
}

function printBlankCompounds($MAX_COMPOUNDS,$compoundsCounter){
	for ($compoundsCounter;$compoundsCounter<$MAX_COMPOUNDS;$compoundsCounter++){
		echo "<div id=\"compound_$compoundsCounter\" style=\"display:none;\">";
			echo "<table class=\"compoundsEnzymes\">";
				echo "<tr>";
					echo "<td>Compound name: </td>";
					echo "<td>";
						echo "<input id=\"textminingCompoundName_$compoundsCounter\" type=\"text\" NAME=\"textminingCompoundName_$compoundsCounter\" maxlenght=\"255\" size=\"20\" value=\"\">";
						echo "<small><a href=\"javascript:;\" onClick=\"insertChebiIds('$compoundsCounter')\">Click to search</a></small>";
					echo "</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>ChEBI: </td>";
					echo "<td>";
						echo "<div id=\"listaCompoundsIds_$compoundsCounter\">";
							echo "<select id=listOfChebiIds_$compoundsCounter name=\"listOfChebiIds_$compoundsCounter\"></select>";
						echo "</div>";
					echo "</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Substrate/Product: </td>";
					echo "<td>";
						echo "<SELECT id=\"inputOutput_$compoundsCounter\" name=\"inputOutput_$compoundsCounter\">";
							echo "<OPTION value=\"select\" SELECTED>Select\n<OPTION value=\"input\">Subtrate\n<OPTION value=\"output\" >Product";
						echo "</SELECT>";
					echo "</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td><input id=\"delete_compound_$compoundsCounter \" type=\"button\" onclick=\"deleteCompound('compound_$compoundsCounter')\" name=\"delete_compound_$compoundsCounter\" value=\"Delete Compound \" class=\"\"></td>";
					echo "<td></td>";
				echo "</tr>";
			echo "</table>";
		echo "</div><!-- End of id#compound_$compoundsCounter -->";
	}
}


function printEnzymesTable($textminingEnzymeName,$enzymesCounter,$linksToProteins,$optionsProteins){
	
	echo "<div id=\"enzyme_$enzymesCounter\" style=\"display:block;\">";
		echo "<table class=\"compoundsEnzymes\">";
			echo "<tr>";
				echo "<td>Enzyme name: </td>";
				echo "<td>";
					echo "<input id=\"textminingEnzymeName_$enzymesCounter\" type=\"text\" NAME=\"textminingEnzymeName_$enzymesCounter\" maxlenght=\"255\" size=\"20\" value=\"$textminingEnzymeName\" >";
					echo "Search in:&nbsp;";
					echo "
						<small>
							<a href=\"javascript:;\" onClick=\"insertProteinsOfEnzyme('$enzymesCounter','selected')\">Selected organism</a>&nbsp;
							<a href=\"javascript:;\" onClick=\"insertProteinsOfEnzyme('$enzymesCounter','conventioned')\">Conventioned species</a>&nbsp;
							<a href=\"javascript:;\" onClick=\"insertProteinsOfEnzyme('$enzymesCounter','all')\">All bacteria</a>&nbsp;
						</small>";
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>Proteins: </td>";
				echo "<td>";
					echo "<div id=\"listOfProteinsOutside_$enzymesCounter\">";
						echo "<div class=\"linksToCompounds\">";
							//colocamos un div con los links a las proteínas del selectbox para poder consultar antes de anotar.
							echo $linksToProteins;
						echo "</div>";
						echo "<select multiple=\"multiple\" id=\"listOfProteins_$enzymesCounter\" name=\"listOfProteins_$enzymesCounter\" size=\"5\">";
							echo $optionsProteins;
						echo "</select>";
					echo "</div>";
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td><input id=\"delete_enzyme_$enzymesCounter\" type=\"button\" onclick=\"deleteEnzyme($enzymesCounter)\" name=\"delete_enzyme_$enzymesCounter\" value=\"Delete Enzyme \" class=\"\"></td>";
				echo "<td></td>";
			echo "</tr>";
		echo "</table>";
		$enzymesCounter=$enzymesCounter+1;
	echo "</div><!-- End of id#enzyme_ -->";
}

function printBlankEnzymes($enzymesCounter,$MAX_ENZYMES){
	
	for ($i=$enzymesCounter;$i<$MAX_ENZYMES;$i++){
		$j=$i+1;
		echo "<div id=\"enzyme_$i\" style=\"display:none;\">";
			echo "<table class=\"compoundsEnzymes\">";
				echo "<tr>";
					echo "<td>Enzyme name: </td>";
					echo "<td><input id=\"textminingEnzymeName_$i\" type=\"text\" NAME=\"textminingEnzymeName_$i\" maxlenght=\"255\" size=\"20\" value=\"\">";
						echo "Search in:&nbsp;";
						echo "
							<small>
								<a href=\"javascript:;\" onClick=\"insertProteinsOfEnzyme('$enzymesCounter','selected')\">Selected organism</a>&nbsp;
								<a href=\"javascript:;\" onClick=\"insertProteinsOfEnzyme('$enzymesCounter','conventioned')\">Conventioned species</a>&nbsp;
								<a href=\"javascript:;\" onClick=\"insertProteinsOfEnzyme('$enzymesCounter','all')\">All bacteria</a>&nbsp;
							</small>";
					echo "</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Proteins:</td>";
					echo "<td>";
						echo "<div id=\"listOfProteinsOutside_$i\">";
							echo "<select multiple=\"multiple\" id=\"listOfProteins_$i\" name=\"listOfProteins_$i\" size=\"5\">";
							echo "</select>";
						echo "</div>";
					echo "</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td><input id=\"delete_enzyme_$i\" type=\"button\" onclick=\"deleteEnzyme($i)\" name=\"delete_enzyme_$i\" value=\"Delete Enzyme \" class=\"\">\n</td>";
					echo "<td></td>";
				echo "</tr>";
			echo "</table>";
		echo "</div><!-- End of id#enzyme_$i -->";
	}
	
}

?>