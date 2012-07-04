<?php 
	//Script que devuelve las evidencias y las carga de forma asíncrona en la parte de la derecha (div#evidences)
	// Retrieve data from Query String
	include("config.php");
	include 'functions.php';
			
	$type 			= $_GET['type'];
	$idCompuesto 	= trim(str_replace('e.g.','',$_GET['idCompuesto']));
	$textminingName = trim(str_replace('e.g.','',$_GET['textminingName']));

	$whatToSearch 	= $_GET['whatToSearch'];	

	$pathToPython="/opt/python/2.7/bin/python";
	$conn = mysql_connect ($database, $db_user, $db_password);
	mysql_select_db("tebacten", $conn);
	mysql_query("SET NAMES 'utf8'");	
	
	$tmpString ='<h3>Evidences found for: '.$textminingName.'</h3>';
	$tmpString .= '<div class="btmspc-dbl"><small><em>Entity mentions are highlighted as follows: <mark class="compound">Compounds</mark>, <mark class="enzyme">Enzymes</mark> and <mark class="organism">Organisms</mark></em>. Curated evidences are indicated by: <a href="#" class="curated">&nbsp;</a></small></div>';

	
	switch($type){
		
		case 'enzyme':
			$selectSQL = "select a.id_evidence,b.text_evidence,b.curated,b.pubmed_id from evidences_enzymes a, evidences b where id_enzyme=$idCompuesto and a.id_evidence=b.id_evidence order by b.pubmed_id";
			break;
		case 'compound':
			$selectSQL = "select a.id_evidence,b.text_evidence,b.curated,b.pubmed_id from evidences_compounds a, evidences b where id_compound=$idCompuesto and a.id_evidence=b.id_evidence";
			break;
		case 'organism':
			$selectSQL = "select a.id_evidence,b.text_evidence,b.curated,b.pubmed_id from evidences_organisms a, evidences b where id_organism=$idCompuesto and a.id_evidence=b.id_evidence";
			break;	
		case 'search':
		
			$selectIdCompound="select id_$whatToSearch from ".$whatToSearch."s where textmining_".$whatToSearch."_name=\"$textminingName\"";
			$result= mysql_query($selectIdCompound);
			$rowIdCompound = mysql_fetch_row($result);
			$idCompuesto=$rowIdCompound[0];
			$selectSQL = "select a.id_evidence,b.text_evidence,b.curated,b.pubmed_id from evidences_".$whatToSearch."s a, evidences b where id_".$whatToSearch."=$idCompuesto and a.id_evidence=b.id_evidence";
			break;	
	}
	
	$result= mysql_query($selectSQL);
	while ($row = mysql_fetch_row($result)){
		$idEvidence=$row[0];
		$textEvidence=$row[1];
		$curated=$row[2];
		$pubmedId=$row[3];
		$tmpString.="<div class=\"evidence\">\n";
		//Colocamos el articleTitle en vez del pubmedId:
		$command = "$pathToPython returnPubmedInformation.py $pubmedId";
		$output=array();
		exec($command,$output,$return);
		$titlePaper = $output[0];
		$tmpString .= "<div class=\"title_paper\">$titlePaper</div>\n";
		$tmpString .= modificarTexto($idEvidence,$textEvidence);
		if ($curated == 0){
			$tmpString.= ' <small>[PMID: <a href="http://www.ncbi.nlm.nih.gov/pubmed/'.$pubmedId.'" target="_blank">'.$pubmedId.'</a> ] <a href="#" class="rgt-arw no-curated tooltip" onClick="javascript:annotate(\''.$home_url.'/curate-evidence?idEvidence='.$idEvidence.'&type='.$type.'&TB_iframe=true&height=600&width=1000\')">Curate</a></small>';
		}
		else{
			$tmpString.=' <small>[PMID: <a href="http://www.ncbi.nlm.nih.gov/pubmed/'.$pubmedId.'" target="_blank">'.$pubmedId.'</a> ]  <a href="'.$home_url.'/curate-evidence?idEvidence=$idEvidence&type=$type&TB_iframe=true&height=600&width=1000" class="rgt-arw curated">Curate</a></small>';
		}
		$tmpString.="</div><!--  End div evidence -->\n";
	}

	
	echo $tmpString;
	
	
?>