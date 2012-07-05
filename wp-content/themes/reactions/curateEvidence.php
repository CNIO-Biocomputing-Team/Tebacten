<?php
include("scripts/config.php");
include('scripts/functions.php');


$conn = mysql_connect ($database, $db_user, $db_password);
mysql_select_db("tebacten", $conn);
mysql_query("SET NAMES 'utf8'");

$MAX_ORGANISMS	=	10;
$MAX_COMPOUNDS	=	8;
$MAX_ENZYMES	=	8;

$idEvidence = $_GET['idEvidence'];
$idCompound = $_GET['idCompound'];
$type		= $_GET['type'];
$blogInfo	= get_bloginfo('home');


#$selectSQL="select a.pubmed_id, a.text_evidence, a.curated, b.id_organism_ncbi, b.ncbi_organism_name, b.textmining_organism_name, c.strain     from evidences as a, organisms as b, evidences_organisms as c where a.id_evidence='$idEvidence' and a.id_evidence=c.id_evidence and  c.id_organism=b.id_organism";
$selectSQL 		= "select pubmed_id,text_evidence,curated from evidences where id_evidence='$idEvidence'";
$result 		= mysql_query($selectSQL);
$row 			= mysql_fetch_row($result);
$pubmedId 		= $row[0];
$textEvidence	= $row[1];
$curated 		= $row[2];

#$pathToProgram="$blogInfo/scripts/returnPubmedInformation.py $pubmedId";
/*
$command	=	"$pathToPython scripts/returnPubmedInformation.py $pubmedId";
exec($command,$output,$return);
$titlePaper=$output[0];
if ($titlePaper==""){
	echo "Sorry but we are experiencing problems retrieving the Article Title from NCBI";
}
*/

//Incluimos el fichero functions.php que entre otras funciones contiene la funcion modificarTexto para el highlight de las enzimas y los compuestos
$textoModificado = modificarTexto($idEvidence,$textEvidence);
//$tmpString.="<div class=\"text_evidence\">".$textoModificado."</div>";

#Ocurre que al empezar no tenemos valores en la tabla organisms para los id_organism_ncbi, ncbi_organism_name y por eso los datos los obtenemos de dos queries diferentes.
#Primero cogemos pubmed_id, text_evidence y curated:
#Intentamos ver si existe una entrada de esa evidencia para ese organismo, una entrada en la tabla evidences_organisms. Si existe cogemos los datos para mostrarlos y si no existe la entrada lo que mostramos será un cuadro de selección para que el usuario elija el organismo que quiera

?>
<section id="fixed_header" class="cols">
	<div id="title_paper"><?php echo $titlePaper; ?></div>
	<div id="text_evidence"><?php echo $textoModificado; ?></div>
	<!-- <a href="#" class="show_hide"><small>View more sentences</small></a> -->
</section>

<section id="form" class="cols">
<form name="curateForm" method="post" id="curateForm" action=""  onsubmit="return validateForm();" accept-charset="UTF-8">
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

?>
<section id="organism" class="cols">
	<h6>ORGANISM</h6>
<?php
	try{
		$selectSQL	= "select a.pubmed_id, a.text_evidence, a.curated, b.id_organism_ncbi, b.ncbi_organism_name, b.textmining_organism_name, c.strain,c.id_evidences_organisms    from evidences as a, organisms as b, evidences_organisms as c where a.id_evidence='$idEvidence' and a.id_evidence=c.id_evidence and  c.id_organism=b.id_organism";
		//echo $selectSQL;
		$result	= mysql_query($selectSQL);
		$row 	= mysql_fetch_row($result);
		$organismsCounter	=	0;
		
		if (count($row)==0){
			//No existe entrada en evidencia_organism.Así que ponemos un único espacio para un orgnismo. Luego rellenaremos con espacios vacíos.
			$existeEvidenciaOrganism	= false;
			$id_organism_ncbi			= "0";
			$ncbi_organism_name			= "";
			$textminingOrganismName		= "";
			$strain="";
			printOrganismsTable($organismsCounter,$textminingOrganismName,"");
			$organismsCounter++;
		
		
		}else{
			//Si que existen entradas para la tabla evidences_organisms así que recuperamos los valores
			$result2= mysql_query($selectSQL);
			$orgOptions = "";

			while ($row2 = mysql_fetch_row($result2)){
				$existeEvidenciaOrganism	= true;
				$idOrganismNCBI				= $row2[3];
				$idOrganismNCBIname			= $row2[4];
				$textminingOrganismName		= $row2[5];
				$strain						= $row2[6];
				$idEvidencesOrganisms		= $row2[7];
				$orgOptions .= "<option value=\"$idEvidencesOrganisms\" >$textminingOrganismName</option>";
				$organismsCounter++;
			}
			printOrganismsTable($organismsCounter,$textminingOrganismName,$orgOptions);
			
		}
	}
	catch(Exception $e){
		print $e;
	} 
?>
<section<!-- End div#organism -->



<section id="compounds" class="cols">
	<h6>COMPOUNDS</h6>             	
 	<?php           	
    //Ponemos los componentes implicados que pueden ser uno o varios O NINGUNO!!!! tanto de entrada como de salida
 	//Recuperamos todos los components que estén presentes en esa evidencia, para ello hacemos
 	$selectSQL2	= "SELECT id_evidences_compounds, ec.id_compound, input_output, id_chebi, textmining_compound_name, substrate_score, product_score FROM evidences_compounds AS ec, compounds AS c WHERE ec.id_compound = c.id_compound AND ec.id_evidence ='$idEvidence'";

 	$result2 	= mysql_query($selectSQL2);
 	$row2 		= mysql_fetch_row($result2);
 	$existeEvidencesCompounds = (count($row2)==0)?false:true;
	$compoundsCounter	= 0;
 	$result2= mysql_query($selectSQL2);
 	
 	while ($row2 = mysql_fetch_row($result2)){
		
		$idEvidencesCompounds 	= $row2[0];
		$idCompound				= $row2[1];
		$inputOutput			= $row2[2];
		$idChebi				= $row2[3];
		$textminingCompoundName	= $row2[4];
		$substrateScore			= $row2[5];
		$productScore			= $row2[6];
		$compoundsCounter		= $compoundsCounter+1;
		$strChebi				= "";
		$strInputOutput 		= "";
		
		if ($idChebi!=""){//Si tenemos idChebi lo mostramos normalmente
			$strChebi .= "<option value=\"$idChebi\" selected>$textminingCompoundName ($idChebi)</option>";
		
		}
		if(($inputOutput == "")){
			//No existe anotación para input/output entonces evaluamos los scores.
			if (($substrateScore == 0) && ($productScore == 0)){
				$inputOutput = "";
				$strInputOutput .= "<OPTION value=\"select\" selected>Select<OPTION value=\"input\">Substrate\n<OPTION value=\"output\" >Product\n";
			}
			elseif($substrateScore > $productScore){
				$inputOutput = "input";
				$strInputOutput .= "<OPTION value=\"select\">Select<OPTION value=\"input\" selected>Substrate\n<OPTION value=\"output\" >Product\n";
			}
			elseif($productScore > $substrateScore){
				$inputOutput="output";
				$strInputOutput .= "<OPTION value=\"select\">Select<OPTION value=\"input\">Substrate\n<OPTION value=\"output\" selected >Product\n";
			}
		}elseif ($inputOutput==0){
			//ya esta anotado como substrate(input)
			$inputOutput="input";
			$strInputOutput .= "<OPTION value=\"select\">Select<OPTION value=\"input\" selected>Substrate\n<OPTION value=\"output\" >Product\n";
		}
		elseif($inputOutput==1){
			//ya esta anotado como product(output)
			$inputOutput = "output";
			$strInputOutput .= "<OPTION value=\"select\">Select<OPTION value=\"input\">Substrate\n<OPTION value=\"output\" selected >Product\n";
		}
	
		printCompoundsTables($compoundsCounter,$textminingCompoundName,$strChebi,$strInputOutput,"display:block");
		$compoundsCounter++;		
	}	  
	//Completamos los componentes con campos vacios que esconderemos para ir añadiendo compuestos.
	for ($i=$compoundsCounter;$i < $MAX_COMPOUNDS;$i++){
		printCompoundsTables($i,"","","","display:none");		
	}
	?>
	<input id="add_compound" type="button" onclick="addCompound();" name="add_compound_$compoundsCounter" value="Add more">

</section><!-- End div#compounds -->
    
<section id="enzymes" class="cols"> 
	<h6>ENZYMES</h6>            	
<?php
             	
 	//Ponemos las enzimas implicadas que pueden ser una o varias
 	//Recuperamos todas las enzimas que estén presentes en esa evidencia, para ello hacemos
 	//$selectSQL3="SELECT id_evidences_enzymes, t1.id_enzyme, textmining_enzyme_name, proteins_list FROM evidences_enzymes AS t1, enzymes AS t2, enzymes_proteins AS t3 WHERE t1.id_enzyme = t2.id_enzyme AND t1.id_enzymes_proteins=t3.id_enzymes_proteins AND id_evidence ='$idEvidence'";
 	$selectSQL3		= "SELECT id_evidences_enzymes, ee.id_enzyme, e.textmining_enzyme_name FROM evidences_enzymes as ee, enzymes as e where ee.id_enzyme = e.id_enzyme AND ee.id_evidence ='$idEvidence'";
 	//echo $selectSQL3; 
 	$result3		= mysql_query($selectSQL3);
 	$enzymesCounter	= 0;
 	while ($row3 = mysql_fetch_row($result3)){
 		
 		#Tenemos una serie de enzimas y tenemos que ver si tienen o no tienen un listado de proteínas asociadas.
		$strProteins 			= "";
		$strSelectProteins 		= "";
		$idEvidencesEnzymes		= $row3[0];
		$idEnzyme				= $row3[1];
		$textminingEnzymeName	= $row3[2];
		$selectProteinList		= "SELECT proteins_list FROM evidences_enzymes AS t1, enzymes_proteins AS t2 WHERE t1.id_enzymes_proteins=t2.id_enzymes_proteins AND id_evidence ='$idEvidence'";
		$resultProteinList		= mysql_query($selectProteinList);
		$rowProteinsList		= mysql_fetch_row($resultProteinList);
		$proteinsList			= $rowProteinsList[0];
		
		if ($proteinsList==""){
			$existeEnzymesProteins	= false;
		}else{
			$existeEnzymesProteins	= true;
			$arrayProteins	= explode(",",$proteinsList);
		}
		
		//colocamos un div con los links a las proteínas del selectbox para poder consultar antes de anotar.
		if ($existeEnzymesProteins == true){
			foreach($arrayProteins as $protein){
				$selectProtein="select id_uniprot,protein_name from proteins where id_protein=$protein";
				$resultSelectProtein= mysql_query($selectProtein);
				$rowSelectProtein=mysql_fetch_row($resultSelectProtein);
				$idUniprot=$rowSelectProtein[0];
				$proteinName=$rowSelectProtein[1];
				$strProteins .= "<a href=\"http://www.uniprot.org/uniprot/$idUniprot\" target=\"_blank\">$idUniprot</a>, ";
				$strSelectProteins .= "<option value=\"$idUniprot\" selected>($idUniprot)-> $proteinName";
			}
		}
		
		printEnzymeTables($enzymesCounter,$textminingEnzymeName,$strProteins,$strSelectProteins,"display:block");
		$enzymesCounter++;
			
	}   
	//Completamos las enzimas con campos vacios que esconderemos para ir añadiendo enzimas.
	for ($i=$enzymesCounter;$i<$MAX_ENZYMES;$i++){
		printEnzymeTables($i,"","","","display:none");
	}

?>   
	<input id="add_enzyme" type="button" onclick="addEnzyme();" name="add_enzyme" value="Add more">  
</section>        	
 	
<section id="buttons" class="cols">
		<input id="submitButton" class="button orange" type="submit" name="submitButton" value="Curate">
</section>
</section>				
			
</form>