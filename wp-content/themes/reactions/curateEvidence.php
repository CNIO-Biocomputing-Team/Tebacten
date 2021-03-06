<?php 
include("scripts/config.php");
include('scripts/functions.php');

$conn = mysql_connect ($database, $db_user, $db_password);
mysql_select_db("tebacten", $conn);
mysql_query("SET NAMES 'utf8'");

$MAX_ORGANISMS=10;
$MAX_COMPOUNDS=26;
$MAX_ENZYMES=26;

$idEvidence = $_GET['idEvidence'];
$idCompound = $_GET['idCompound'];
$type		= $_GET['type'];
$blogInfo	= get_bloginfo('home');
$pageNumber	= $_GET['pageNumber'];
$whatToSearch=$_GET['whatToSearch'];
//echo "<br>type: $type";
//echo "<br>pageNumber: $pageNumber";
//echo "<br>whatToSearch: $whatToSearch";
//echo "evidencia: ".$idEvidence;
//Obtenemos toda la información de la evidencia para luego poder editarla

#$selectSQL="select a.pubmed_id, a.text_evidence, a.curated, b.id_organism_ncbi, b.ncbi_organism_name, b.textmining_organism_name, c.strain from evidences as a, organisms as b, evidences_organisms as c where a.id_evidence='$idEvidence' and a.id_evidence=c.id_evidence and c.id_organism=b.id_organism";
$blogInfo		= get_bloginfo('home');
$selectSQL		= "select pubmed_id,text_evidence,curated from evidences where id_evidence='$idEvidence'";
$result			= mysql_query($selectSQL);
$row 			= mysql_fetch_row($result);
$pubmedId		= $row[0];
$textEvidence	=$row[1];
$curated		=$row[2];

#$pathToProgram="$blogInfo/scripts/returnPubmedInformation.py $pubmedId";
#$pathToPython="/opt/python/2.7/bin/python";
#$command="$pathToPython /home/tebacten/public_html/wp-content/themes/reactions/scripts/returnPubmedInformation.py $pubmedId";
#exec($command,$output,$return);
#$titlePaper=$output[0];
#if ($titlePaper==""){
#echo "Sorry but we are experiencing problems retrieving the Article Title from NCBI";
#}
//Incluimos el fichero functions.php que entre otras funciones contiene la funcion modificarTexto para el highlight de las enzimas y los compuestos
$textoModificado=modificarTexto($idEvidence,$textEvidence);
//$tmpString.="<div class=\"text_evidence\">".$textoModificado."</div>";

#Ocurre que al empezar no tenemos valores en la tabla organisms para los id_organism_ncbi, ncbi_organism_name y por eso los datos los obtenemos de dos queries diferentes.
#Primero cogemos pubmed_id, text_evidence y curated:
#Intentamos ver si existe una entrada de esa evidencia para ese organismo, una entrada en la tabla evidences_organisms. Si existe cogemos los datos para mostrarlos y si no existe la entrada lo que mostramos será un cuadro de selección para que el usuario elija el organismo que quiera

?>
<div id="fixed_header">
<div id="title_paper"><?php echo $titlePaper; ?></div>
<div id="text_evidence"><?php echo $textoModificado; ?></div>
<!--  <a href="#" class="show_hide"><small>View more sentences</small></a> -->
</div>

<div id="frame">
<div id="content_frame">
<div id="curate">
<form name="curateForm" method="post" id="curateForm" action="" onsubmit="return validateForm();" accept-charset="UTF-8">
	<input type="hidden" id="idEvidence" name="idEvidence" value="<?php echo $idEvidence; ?>">
	<input type="hidden" id="method" name="method" value="curate">
	<input type="hidden" id="pageNumber" name="pageNumber" value="<?php echo $pageNumber; ?>">
	<input type="hidden" id="whatToSearch" name="whatToSearch" value="<?php echo $whatToSearch; ?>">
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
?>
<!-- <div class=\"slidingDiv\"> <a href=\"#\" class=\"show_hide\"><small>hide</small></a></div> -->
<section id="organism" class="cols">
	<h6>ORGANISM</h6>
<?php	
try{
	$selectSQL="select a.pubmed_id, a.text_evidence, a.curated, b.id_organism_ncbi, b.ncbi_organism_name, b.textmining_organism_name, c.strain,c.id_evidences_organisms from evidences as a, organisms as b, evidences_organisms as c where a.id_evidence='$idEvidence' and a.id_evidence=c.id_evidence and c.id_organism=b.id_organism";
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
		printOrganismsTable($organismsCounter,$textminingOrganismName,$id_organism_ncbi,"");
	}
	else{
		//Si que existen entradas para la tabla evidences_organisms así que recuperamos los valores
		$result2= mysql_query($selectSQL);
		$orgs="";
		while ($row2 = mysql_fetch_row($result2)){
			$existeEvidenciaOrganism=true;
			$idOrganismNCBI=$row2[3];
			$idOrganismNCBIname=$row2[4];
			$textminingOrganismName=$row2[5];
			$strain=$row2[6];
			$idEvidencesOrganisms=$row2[7];
			$orgs.="<OPTION value=\"$idEvidencesOrganisms\" >$textminingOrganismName";
			#print "<br>*****$idOrganismNCBI*****";
			#print "<br>*****$idOrganismNCBIname*****";
			#print "<br>$textminingOrganismName";
			#print "<br>*****$strain*****";
			//Mostramos los valores para el organismo en cuestion
		}
		printOrganismsTable($organismsCounter,$textminingOrganismName,$id_organism_ncbi,$orgs);
	}
}
catch(Exception $e){
print $e;
}
?>
</section><!-- End section#organism -->
<section id="compounds">
	<h6>COMPOUNDS</h6>

<?php 
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
	$strInputOutput=createStringInputOutput($inputOutput,$substrateScore,$productScore,$compoundsCounter);
	printCompoundsTable($compoundsCounter,$textminingCompoundName,$idChebi,$strInputOutput);	
	$compoundsCounter=$compoundsCounter+1;
}
?>
<?php
//Completamos los componentes con campos vacios que esconderemos para ir añadiendo compuestos.
printBlankCompounds($MAX_COMPOUNDS,$compoundsCounter);
echo "<input id=\"add_compound\" type=\"button\" onclick=\"addCompound();\" name=\"add_compound_$compoundsCounter\" value=\"Add More \" class=\"\">";
?>

</section><!-- End section#compounds -->

<section id="enzymes">
	<h6>ENZYMES</h6>

<?php
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
	$selectProteinList="SELECT proteins_list FROM evidences_enzymes AS t1, enzymes_proteins AS t2  WHERE t1.id_enzymes_proteins=t2.id_enzymes_proteins AND id_evidences_enzymes='$idEvidencesEnzymes'";
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
	$linksToProteins="";
	$optionsProteins="";
	if ($existeEnzymesProteins==true){
		foreach($arrayProteins as $protein){
			$selectProtein="select id_uniprot,protein_name from proteins where id_protein=$protein";
			#print "<br>$selectProtein";
			$resultSelectProtein= mysql_query($selectProtein);
			$rowSelectProtein=mysql_fetch_row($resultSelectProtein);
			$idUniprot=$rowSelectProtein[0];
			$linksToProteins.= "<a href=\"http://www.uniprot.org/uniprot/$idUniprot\" target=\"_blank\">$idUniprot</a>, ";
		}
		foreach($arrayProteins as $protein){
			#print "<br>$protein";
			//Tenemos que consultar los datos de la proteína en cuestión para mostrarla en el selectbox
			$selectProtein="select id_uniprot,protein_name from proteins where id_protein=$protein";
			$resultSelectProtein= mysql_query($selectProtein);
			$rowSelectProtein=mysql_fetch_row($resultSelectProtein);
			$idUniprot=$rowSelectProtein[0];
			$proteinName=$rowSelectProtein[1];
			$optionsProteins.= "<option value=\"$idUniprot\" selected>($idUniprot)-> $proteinName";
		}
	}
		
	printEnzymesTable($textminingEnzymeName,$enzymesCounter,$linksToProteins,$optionsProteins);
	$enzymesCounter=$enzymesCounter+1;
}

//Completamos las enzimas con campos vacios que esconderemos para ir añadiendo enzimas.
printBlankEnzymes($enzymesCounter,$MAX_ENZYMES);
?>

	<input id="add_enzyme" type="button" onclick="addEnzyme();" name="add_enzyme" value="Add More " class=""><br/>
</section><!-- End div#compounds -->

<section id="buttons">
	<input id="submitButton" class="button orange" type="submit" name="submitButton" value="curate">
</section>
</form>
</div><!-- End class.curate -->
</div><!-- End id=content_frame -->
</div> <!-- End id=frame -->