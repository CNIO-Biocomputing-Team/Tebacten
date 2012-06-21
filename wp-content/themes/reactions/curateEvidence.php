<?php
/**
 *
Template Name: Curate evidence
 *
*/

$MAX_ORGANISMS=10;
$MAX_COMPOUNDS=26;
$MAX_ENZYMES=26;
get_header('curate'); 
$idEvidence = $_GET['idEvidence'];
$idCompound= $_GET['idCompound'];
$type= $_GET['type'];
//echo "evidencia: ".$idEvidence;
//Obtenemos toda la información de la evidencia para luego poder editarla
$conn = mysql_connect ("jabba.cnio.es", "tebacten", "tebacten");
mysql_select_db("tebacten", $conn);
mysql_query("SET NAMES 'utf8'");

#$selectSQL="select a.pubmed_id, a.text_evidence, a.curated, b.id_organism_ncbi, b.ncbi_organism_name, b.textmining_organism_name, c.strain     from evidences as a, organisms as b, evidences_organisms as c where a.id_evidence='$idEvidence' and a.id_evidence=c.id_evidence and  c.id_organism=b.id_organism";
$blogInfo=get_bloginfo('home');
$selectSQL="select pubmed_id,text_evidence,curated from evidences where id_evidence='$idEvidence'";
$result= mysql_query($selectSQL);
$row = mysql_fetch_row($result);
$pubmedId=$row[0];
$textEvidence=$row[1];
$curated=$row[2];

#$pathToProgram="$blogInfo/scripts/returnPubmedInformation.py $pubmedId";
$pathToPython=" /opt/python/2.7/bin/python";
$command="$pathToPython /home/tebacten/public_html/wp-content/themes/reactions/scripts/returnPubmedInformation.py $pubmedId";
exec($command,$output,$return);
$titlePaper=$output[0];
if ($titlePaper==""){
	echo "Sorry but we are experiencing problems retrieving the Article Title from NCBI";
}
//Incluimos el fichero functions.php que entre otras funciones contiene la funcion modificarTexto para el highlight de las enzimas y los compuestos
include('scripts/functions.php');
$textoModificado=modificarTexto($idEvidence,$textEvidence);
//$tmpString.="<div class=\"text_evidence\">".$textoModificado."</div>";

#Ocurre que al empezar no tenemos valores en la tabla organisms para los id_organism_ncbi, ncbi_organism_name y por eso los datos los obtenemos de dos queries diferentes.
#Primero cogemos pubmed_id, text_evidence y curated:
#Intentamos ver si existe una entrada de esa evidencia para ese organismo, una entrada en la tabla evidences_organisms. Si existe cogemos los datos para mostrarlos y si no existe la entrada lo que mostramos será un cuadro de selección para que el usuario elija el organismo que quiera

?>
<div id="fixed_header">
	<div id="title_paper"><?php echo $titlePaper; ?></div>
	<div id="text_evidence"><?php echo $textoModificado; ?></div>
	<a href="#" class="show_hide"><small>View more sentences</small></a>
	</div>

<div id="frame">
	<div id="content_frame">
		<div id="curate">
			<form name="curateForm" method="post" id="curateForm" action="http://tebacten.bioinfo.cnio.es/wp-content/themes/reactions/scripts/modificarDatos2.py"  onsubmit="return validateForm();" accept-charset="UTF-8">
				<input type="hidden" id="idEvidence" name="idEvidence" value="<?php echo $idEvidence; ?>">	
				<input type="hidden" id="metodo" name="metodo" value="curate">
				
<?php
//Recuperamos todas las frases que aparecen vinculadas a este pubmedId
$selectTextEvidence="select text_evidence from evidences where id_evidence='$idEvidence'";
#echo $selectTextEvidence;
$resultTextEvidence= mysql_query($selectTextEvidence);
$stringTextEvidenceExtras="<ul>";
while ($rowTextEvidence = mysql_fetch_row($resultTextEvidence)){
	$textEvidence=$rowTextEvidence[0];
	$stringTextEvidenceExtras.="<li>$textEvidence</li>";

}
$stringTextEvidenceExtras="</ul>";
//Ponemos el listado de Organismos vinculados a su tax_id y que se encuentran en la tabla organisms
				echo "<div class=\"slidingDiv\"> <a href=\"#\" class=\"show_hide\"><small>hide</small></a></div>";
				echo "<div id=\"organism\">\n";
					
					echo "<div id=\"titleOrganism\">ORGANISM</div>";
					try{
						$selectSQL="select a.pubmed_id, a.text_evidence, a.curated, b.id_organism_ncbi, b.ncbi_organism_name, b.textmining_organism_name, c.strain,c.id_evidences_organisms    from evidences as a, organisms as b, evidences_organisms as c where a.id_evidence='$idEvidence' and a.id_evidence=c.id_evidence and  c.id_organism=b.id_organism";
						#print $selectSQL;
						$result= mysql_query($selectSQL);
						$row = mysql_fetch_row($result);
						$organismsCounter=0;
						if (count($row)==0){
							//No existe entrada en evidencia_organism.Así que ponemos un único espacio para un orgnismo. Luego rellenaremos con espacios vacíos.
							$existeEvidenciaOrganism=false;
							$id_organism_ncbi="0";
							$ncbi_organism_name="";
							$textminingOrganismName="";
							$strain="";
									echo "<table class=\"compoundsEnzymes\">";
										echo "<tr>";
											echo "<td>Organism: </td><td><input id=\"textminingOrganismName_$organismsCounter\" type=\"text\" NAME=\"textminingOrganismName_$organismsCounter\" maxlenght=\"255\" size=\"20\" value=\"$textminingOrganismName\">\n";
											echo "<small><a href=\"javascript:;\" onClick=\"overlayTaxonomy($organismsCounter); insertTaxonomy($organismsCounter);\">Click to search</a></small>
												<div id=\"overlayTaxonomy_$organismsCounter\">&nbsp;</div>
												<script>
						                    		$('#overlayTaxonomy_$organismsCounter').unmask();
						                    	</script>		
											</td>
									";
									echo "</tr><tr>";
									echo "</table>";
									echo "<div id=\"organism_ajax_$organismsCounter\">\n";
										echo "<table>";
											echo "<td>NCBI organism name: </td><td><select  id=\"idOrganismNCBI_$organismsCounter\" type=\"text\" name=\"idOrganismNCBI_$organismsCounter\"><option value=\"$idOrganismNCBI\">$idOrganismNCBIname</select></td>";
											echo "</tr><tr>";
											
							             	//Ponemos el strain:
							             	echo "<td>Strain: </td><td><SELECT name=\"strain_$organismsCounter\" div=\"strain_$organismsCounter\">";
							             		echo "<OPTION value=\"\" SELECTED>Select";
							             		echo "<OPTION value=\"+\">+";
								             	echo "<OPTION value=\"-\">-";
							             	echo "</SELECT>\n</div></td>";
							             	echo "</tr>";
							             	echo "<tr><td><input id=\"deleteOrganism_$organismsCounter\" type=\"button\" onclick=\"deleteOrganism('$organismsCounter')\" name=\"deleteOrganism_$organismsCounter\" value=\"Delete Organism \" class=\"\">\n</td><td></td></tr>";
							             echo "</table>";
							echo "</div><!-- End div#organism_ajax_$organismsCounter -->\n";
							$organismsCounter++;
						}
						else{
							//Si que existen entradas para la tabla evidences_organisms así que recuperamos los valores
							$result2= mysql_query($selectSQL);
								echo "<table class=\"compoundsEnzymes\">";
										echo "<tr>";
											echo "<td>Organism: </td><td>";
											echo "<SELECT id=\"textminingOrganismName_$organismsCounter\" type=\"text\" NAME=\"textminingOrganismName_$organismsCounter\" maxlenght=\"255\" size=\"1\" onChange=\"overlayTaxonomy($organismsCounter);insertTaxonomy($organismsCounter,'$idEvidence');\">\n";
											echo "<OPTION value=\"\" >Select an organism";
							while ($row2 = mysql_fetch_row($result2)){
								$existeEvidenciaOrganism=true;
								$idOrganismNCBI=$row2[3];
								$idOrganismNCBIname=$row2[4];
								$textminingOrganismName=$row2[5];
								$strain=$row2[6];
								$idEvidencesOrganisms=$row2[7];
								echo "<OPTION value=\"$idEvidencesOrganisms\" >$textminingOrganismName";
								#print "<br>*****$idOrganismNCBI*****";
								#print "<br>*****$idOrganismNCBIname*****";
								#print "<br>$textminingOrganismName";
								#print "<br>*****$strain*****";
								//Mostramos los valores para el organismo en cuestion
								$organismsCounter++;
							}
								echo "</SELECT>\n</div></td>";
					           echo "</tr>";
					          echo "</table>";
					        echo "<div id=\"organism_ajax_0\">\n";
							echo "</div><!-- End div#organism_ajax_0 -->\n";
							echo "<script>
		                    		$('#overlayTaxonomy_$organismsCounter').unmask();
		                    	</script>		
							</td>
							";
						}
					}
					catch(Exception $e){
						print $e;
					} 
             	echo "</div><!-- End div#organism -->\n";
             	
             	
             	
             	
             	
             	//Ponemos los componentes implicados que pueden ser uno o varios O NINGUNO!!!! tanto de entrada como de salida
             	echo "<div id=\"compounds\">\n";
             	echo "<div id=\"titleCompounds\">COMPOUNDS</div>";
             	//Recuperamos todos los components que estén presentes en esa evidencia, para ello hacemos
             	$selectSQL2="SELECT id_evidences_compounds, t1.id_compound, input_output, id_chebi, textmining_compound_name, substrate_score, product_score FROM evidences_compounds AS t1, compounds AS t2 WHERE t1.id_compound = t2.id_compound AND id_evidence ='$idEvidence'";
             	#print $selectSQL2;
             	$result2= mysql_query($selectSQL2);
             	$row2 = mysql_fetch_row($result2);
             	if (count($row2)==0){
             		#No existen entradas en la tabla evidences_compounds
             		$existeEvidencesCompounds=false;
             	}
             	else{
             		$existeEvidencesCompounds=true;
             	}
             	$compoundsCounter=0;
             	$result2= mysql_query($selectSQL2);
             	while ($row2 = mysql_fetch_row($result2)){
					$idEvidencesCompounds=$row2[0];
					$idCompound=$row2[1];
					$inputOutput=$row2[2];
					$idChebi=$row2[3];
					$textminingCompoundName=$row2[4];
					$substrateScore=$row2[5];
					$productScore=$row2[6];
					if(($inputOutput=="")){
						//No existe anotación para input/output entonces evaluamos los scores.
						if (($substrateScore==0)&&($productScore==0)){
							$inputOutput="";
						$strInputOutput="<td>Substrate/Product: </td><td><SELECT name=\"inputOutput_$compoundsCounter\">\n<OPTION value=\"select\" selected>Select<OPTION value=\"input\">Substrate\n<OPTION value=\"output\" >Product\n</SELECT></td>";
						}
						elseif($substrateScore>$productScore){
							$inputOutput="input";
						$strInputOutput="<td>Substrate/Product: </td><td><SELECT name=\"inputOutput_$compoundsCounter\">\n<OPTION value=\"select\">Select<OPTION value=\"input\" selected>Substrate\n<OPTION value=\"output\" >Product\n</SELECT></td>";
						}
						elseif($productScore>$substrateScore){
							$inputOutput="output";
							$strInputOutput="<td>Substrate/Product: </td><td><SELECT name=\"inputOutput_$compoundsCounter\">\n<OPTION value=\"select\">Select<OPTION value=\"input\">Substrate\n<OPTION value=\"output\" selected >Product\n</SELECT></td>";
						}
					}
					elseif ($inputOutput==0){
						//ya esta anotado como substrate(input)
						$inputOutput="input";
						$strInputOutput="<td>Substrate/Product: </td><td><SELECT name=\"inputOutput_$compoundsCounter\">\n<OPTION value=\"select\">Select<OPTION value=\"input\" selected>Substrate\n<OPTION value=\"output\" >Product\n</SELECT></td>";
					}
					elseif($inputOutput==1){
						//ya esta anotado como product(output)
						$inputOutput="output";
						$strInputOutput="<td>Substrate/Product: </td><td><SELECT name=\"inputOutput_$compoundsCounter\">\n<OPTION value=\"select\">Select<OPTION value=\"input\">Substrate\n<OPTION value=\"output\" selected >Product\n</SELECT></td>";
					}
					
					echo "<div id=\"compound_$compoundsCounter\" name=\"compound_$compoundsCounter\" style=\"display:block;\">\n";
					echo "<table class=\"compoundsEnzymes\">";
					echo "<tr>";
					echo "<td>Compound name: </td><td><input id=\"textminingCompoundName_$compoundsCounter\" type=\"text\" NAME=\"textminingCompoundName_$compoundsCounter\" maxlenght=\"255\" size=\"20\" value=\"$textminingCompoundName\">\n";
					echo "<small><a href=\"javascript:;\" onClick=\"overlayCompounds($compoundsCounter);insertChebiIds('$compoundsCounter')\">Click to search</a></small>
							<div id=\"overlayCompounds_$compoundsCounter\">&nbsp;</div>
							<script>
	                    		$('#overlayCompounds_$compoundsCounter').unmask();
	                    	</script>
						</td>";
					echo "</tr><tr>";
					if ($idChebi!=""){//Si tenemos idChebi lo mostramos normalmente
						echo "<td>ChEBI: </td><td><div id=\"listaCompoundsIds_$compoundsCounter\"><select id=\"listOfChebiIds_$compoundsCounter\" type=\"text\" NAME=\"listOfChebiIds_$compoundsCounter\" maxlenght=\"255\" ><option value=\"\">Select <option value=\"$idChebi\" SELECTED>$textminingCompoundName ($idChebi)</select></div></td>\n";
					}
					else{//Sino hay chebiId entonces mostramos un select vacio, sin options
						echo "<td>ChEBI: </td><td><div id=\"listaCompoundsIds_$compoundsCounter\"><select id=\"listOfChebiIds_$compoundsCounter\" type=\"text\" NAME=\"listOfChebiIds_$compoundsCounter\" maxlenght=\"255\" ></select></div></td>\n";
					}
					echo "</tr><tr>";
					echo $strInputOutput;
					echo "</tr><tr>";
					echo "<td><input id=\"delete_compound_$compoundsCounter\" type=\"button\" onclick=\"deleteCompound('compound_$compoundsCounter')\" name=\"delete_compound_$compoundsCounter\" value=\"Delete Compound \" class=\"\">\n</td><td></td>";
					echo "</tr>";
					echo "</table>";
					$compoundsCounter=$compoundsCounter+1;
					echo "</div><!-- End of id#compound -->";
						
				}   
				//Completamos los componentes con campos vacios que esconderemos para ir añadiendo compuestos.
				for ($i=0;$i<$MAX_COMPOUNDS-$compoundsCounter;$i++){
					
					echo "<div id=\"newCompound_$i\" style=\"display:none;\">\n";
					echo "<table class=\"compoundsEnzymes\">";
					echo "<tr>";
					echo "<td>Compound name: </td><td><input id=\"textminingCompoundName_$compoundsCounter\" type=\"text\" NAME=\"textminingCompoundName_$compoundsCounter\" maxlenght=\"255\" size=\"20\"  value=\"\">\n";
					echo "<small><a href=\"javascript:;\" onClick=\"overlayCompounds($compoundsCounter);insertChebiIds('$compoundsCounter')\">Click to search</a></small>
							<div id=\"overlayCompounds_$compoundsCounter\">&nbsp;</div>
							<script>
	                    		$('#overlayCompounds_$compoundsCounter').unmask();
	                    	</script>
						</td>";
					echo "</tr><tr>";
					//echo "ChEBI: <input id=\"chebiId_$i\" type=\"text\" NAME=\"chebiId_$compoundsCounter\" maxlenght=\"255\" size=\"20\" value=\"\">\n";
					echo "<td>ChEBI: </td><td><div id=\"listaCompoundsIds_$compoundsCounter\"><select id=listOfChebiIds_$compoundsCounter name=\"listOfChebiIds_$compoundsCounter\"></select></div></td>";
					echo "</tr><tr>";
					echo "<td>Substrate/Product: </td><td><SELECT id=\"inputOutput_$compoundsCounter\" name=\"inputOutput_$compoundsCounter\">\n<OPTION value=\"select\" SELECTED>Select\n<OPTION value=\"input\">Subtrate\n<OPTION value=\"output\" >Product\n</SELECT></td>";
					echo "</tr><tr>";
					echo "<td><input id=\"delete_newCompound_$i\" type=\"button\" onclick=\"deleteCompound('newCompound_$i')\" name=\"delete_newCompound_$i\" value=\"Delete Compound \" class=\"\">\n</td><td></td>";
					//echo "<input id=\"add_compound_$j\" type=\"button\" onclick=\"addCompound($j)\" name=\"add_compound_$j\" value=\"Add Compound \" class=\"\"><br/>";
					echo "</tr>";
					echo "</table>";
					echo "</div><!-- End of id#compound -->";
					$compoundsCounter++;
				}
				echo "<input id=\"add_compound\" type=\"button\" onclick=\"addCompound();\" name=\"add_compound_$compoundsCounter\" value=\"Add Compound \" class=\"\"><br/>";
             	echo "</div><!-- End div#compounds -->";
             	
             	
             	
             	//Ponemos las enzimas implicadas que pueden ser una o varias
             	echo "<div id=\"enzymes\">\n";
             	echo "<div id=\"titleEnzymes\">ENZYMES</div>";
             	//Recuperamos todas las enzimas que estén presentes en esa evidencia, para ello hacemos
             	//$selectSQL3="SELECT id_evidences_enzymes, t1.id_enzyme, textmining_enzyme_name, proteins_list FROM evidences_enzymes AS t1, enzymes AS t2, enzymes_proteins AS t3 WHERE t1.id_enzyme = t2.id_enzyme AND t1.id_enzymes_proteins=t3.id_enzymes_proteins AND id_evidence ='$idEvidence'";
             	$selectSQL3="SELECT id_evidences_enzymes, a.id_enzyme, textmining_enzyme_name FROM evidences_enzymes as a, enzymes as b where a.id_enzyme = b.id_enzyme AND id_evidence ='$idEvidence'";
             	$result3= mysql_query($selectSQL3);
             	$enzymesCounter=0;
             	while ($row3 = mysql_fetch_row($result3)){
             		#Tenemos una serie de enzimas y tenemos que ver si tienen o no tienen un listado de proteínas asociadas.
					$idEvidencesEnzymes=$row3[0];
					$idEnzyme=$row3[1];
					$textminingEnzymeName=$row3[2];
					
					$selectProteinList="SELECT proteins_list FROM evidences_enzymes AS t1, enzymes_proteins AS t2 WHERE t1.id_enzymes_proteins=t2.id_enzymes_proteins AND id_evidence ='$idEvidence'";
					$resultProteinList= mysql_query($selectProteinList);
					$rowProteinsList= mysql_fetch_row($resultProteinList);
					$proteinsList=$rowProteinsList[0];
					if ($proteinsList==""){
						$existeEnzymesProteins=false;
					}
					else{
						$existeEnzymesProteins=true;
						$arrayProteins=explode(",",$proteinsList);
					}
					echo "<div id=\"enzyme_$enzymesCounter\" style=\"display:block;\">\n";
					echo "<table class=\"compoundsEnzymes\">";
					echo "<tr>";
					echo "<td>Enzyme name: </td><td><input id=\"textminingEnzymeName_$enzymesCounter\" type=\"text\" NAME=\"textminingEnzymeName_$enzymesCounter\" maxlenght=\"255\" size=\"20\" value=\"$textminingEnzymeName\" >\n";
					echo "Search in:&nbsp;";
					echo "
							<small>
							<a href=\"javascript:;\" onClick=\"overlayEnzymes($enzymesCounter);insertProteinsOfEnzyme('$enzymesCounter','selected')\">Selected organism</a>&nbsp;
							<a href=\"javascript:;\" onClick=\"overlayEnzymes($enzymesCounter);insertProteinsOfEnzyme('$enzymesCounter','conventioned')\">Conventioned species</a>&nbsp;
							<a href=\"javascript:;\" onClick=\"overlayEnzymes($enzymesCounter);insertProteinsOfEnzyme('$enzymesCounter','all')\">All bacteria</a>&nbsp;
							</small>
							<div id=\"overlayEnzymes_$enzymesCounter\">&nbsp;</div>
							<script>
	                    		$('#overlayEnzymes_$enzymesCounter').unmask();
	                    	</script>
						</td>";
					echo "</tr><tr>";
					echo "<td>Proteins: </td><td><div id=\"listOfProteinsOutside_$enzymesCounter\">";
					echo "<div class=\"linksToCompounds\">";
					//colocamos un div con los links a las proteínas del selectbox para poder consultar antes de anotar.
					if ($existeEnzymesProteins==true){
						foreach($arrayProteins as $protein){
							$selectProtein="select id_uniprot,protein_name from proteins where id_protein=$protein";
							#print "<br>$selectProtein";
							$resultSelectProtein= mysql_query($selectProtein);
							$rowSelectProtein=mysql_fetch_row($resultSelectProtein);
							$idUniprot=$rowSelectProtein[0];
							echo "<a href=\"http://www.uniprot.org/uniprot/$idUniprot\" target=\"_blank\">$idUniprot</a>, ";
						}
					}
					echo "</div>";
					
					echo "<select multiple=\"multiple\" id=\"listOfProteins_$enzymesCounter\" name=\"listOfProteins_$enzymesCounter\" size=\"5\">";
					if ($existeEnzymesProteins==true){
						foreach($arrayProteins as $protein){
							#print "<br>$protein";
							//Tenemos que consultar los datos de la proteína en cuestión para mostrarla en el selectbox
							$selectProtein="select id_uniprot,protein_name from proteins where id_protein=$protein";
							$resultSelectProtein= mysql_query($selectProtein);
							$rowSelectProtein=mysql_fetch_row($resultSelectProtein);
							$idUniprot=$rowSelectProtein[0];
							$proteinName=$rowSelectProtein[1];
							echo "<option value=\"$idUniprot\" selected>($idUniprot)-> $proteinName";
						}
					}
					echo "</select></div></td>";
					echo "</tr><tr>";
					echo "<td><input id=\"delete_enzyme_$enzymesCounter\" type=\"button\" onclick=\"deleteEnzyme($enzymesCounter)\" name=\"delete_enzyme_$enzymesCounter\" value=\"Delete Enzyme \" class=\"\">\n</td><td></td>";
					echo "</tr>";
					echo "</table>";
					$enzymesCounter=$enzymesCounter+1;
					echo "</div><!-- End of id#enzyme_ -->";
						
				}   
				//Completamos las enzimas con campos vacios que esconderemos para ir añadiendo enzimas.
				for ($i=$enzymesCounter;$i<$MAX_ENZYMES;$i++){
					$j=$i+1;
					echo "<div id=\"enzyme_$i\" style=\"display:none;\">\n";
					echo "<table class=\"compoundsEnzymes\">";
					echo "<tr>";
					echo "<td>Enzyme name: </td><td><input id=\"textminingEnzymeName_$i\" type=\"text\" NAME=\"textminingEnzymeName_$i\" maxlenght=\"255\" size=\"20\" value=\"\">\n";
					echo "Search in:&nbsp;";
					echo "
							<small>
							<a href=\"javascript:;\" onClick=\"overlayEnzymes($enzymesCounter);insertProteinsOfEnzyme('$enzymesCounter','selected')\">Selected organism</a>&nbsp;
							<a href=\"javascript:;\" onClick=\"overlayEnzymes($enzymesCounter);insertProteinsOfEnzyme('$enzymesCounter','conventioned')\">Conventioned especies</a>&nbsp;
							<a href=\"javascript:;\" onClick=\"overlayEnzymes($enzymesCounter);insertProteinsOfEnzyme('$enzymesCounter','all')\">All bacteria</a>&nbsp;
							</small>
							<div id=\"overlayEnzymes_$i\">&nbsp;</div>
							<script>
	                    		$('#overlayEnzymes_$i').unmask();
	                    	</script>
						</td>";
					echo "</tr><tr>";
					echo "<td>Proteins:</td><td><div id=\"listOfProteinsOutside_$i\"><select multiple=\"multiple\" id=\"listOfProteins_$i\" name=\"listOfProteins_$i\" size=\"5\"></select></div></td>";
					echo "</tr><tr>";
					echo "<td><input id=\"delete_enzyme_$i\" type=\"button\" onclick=\"deleteEnzyme($i)\" name=\"delete_enzyme_$i\" value=\"Delete Enzyme \" class=\"\">\n</td><td></td>";
					echo "</tr>";
					echo "</table>";
					//echo "<input id=\"add_compound_$j\" type=\"button\" onclick=\"addCompound($j)\" name=\"add_compound_$j\" value=\"Add Compound \" class=\"\"><br/>";
					echo "</div><!-- End of id#enzyme -->";
				}
				echo "<input id=\"add_enzyme\" type=\"button\" onclick=\"addEnzyme();\" name=\"add_enzyme\" value=\"Add Enzyme \" class=\"\"><br/>";
             	echo "</div><!-- End div#compounds -->";   	
?>             	
             	
             	<div id="buttons">
					<input id="submitButton" class="right" type="submit" name="submitButton" value="Send">
				</div>
				
			
			</form>
		</div><!-- End class.curate  -->
	</div><!-- End id=content_frame  -->
	<div class="clear_both"></div>
	<div id="footer_frame">
		<div id="clickOutside">(Click outside this window to close it)</div>
		<!-- 
			<form name="deleteForm" method="post" id="deleteForm" action="http://tebacten.bioinfo.cnio.es/wp-content/themes/reactions/scripts/modificarDatos2.py" accept-charset="UTF-8" class="aligncenter" onsubmit="return confirmDelete();">
				<input type="hidden" id="idEvidence" name="idEvidence" value="<?php echo $idEvidence; ?>">	
				<input type="hidden" id="metodo" name="metodo" value="delete">	
				<input type="hidden" id="type" name="type" value="<?php echo $type;?>">	
				<input id="deleteEvidence" class="right" type="submit" name="deleteEvidence" value="Delete this evidence">
			</form>
		-->
	</div>
	


</div> <!-- End id=frame  -->


<?php get_sidebar(); ?>
<?php get_footer('curate-evidence'); ?>