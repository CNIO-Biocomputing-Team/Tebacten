<?php 
	//Script que devuelve las evidencias y las carga de forma asíncrona en la parte de la derecha (div#evidences)
	// Retrieve data from Query String
	include("config.php");
	include 'functions.php';
	ini_set( "display_errors", 0);	
	$pathToPython="/opt/python/2.7/bin/python";
	$conn = mysql_connect ($database, $db_user, $db_password);
	mysql_select_db("tebacten", $conn);
	mysql_query("SET NAMES 'utf8'");	
			
	$targetpage		= "test";
	$adjacents 		= 3;
	$limit 			= 20;	
	$page 			= $_GET['page'];
	$start			= ($page)?($page - 1) * $limit:0;
	$type 			= $_GET['type'];
	$idCompuesto 	= trim(str_replace('e.g.','',$_GET['idCompuesto']));
	$textminingName = trim(str_replace('e.g.','',$_GET['textminingName']));
	$whatToSearch 	= $_GET['whatToSearch'];	
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
		
			$selectIdCompound = "select id_$whatToSearch from ".$whatToSearch."s where textmining_".$whatToSearch."_name=\"$textminingName\"";
			$result= mysql_query($selectIdCompound);
			if ($result){	
				$ids =  array();
				while($rowIdCompound = mysql_fetch_row($result)){
					array_push($ids,$rowIdCompound[0]);
				}
				if (count($ids)){ 
					$countQuery = "SELECT count(a.id_evidence) as num from evidences_".$whatToSearch."s a, evidences b where a.id_evidence=b.id_evidence and  id_".$whatToSearch." IN (".implode(',',$ids).")";
					
					
					$selectSQL = "select a.id_evidence,b.text_evidence,b.curated,b.pubmed_id from evidences_".$whatToSearch."s a, evidences b where a.id_evidence=b.id_evidence and  id_".$whatToSearch." IN (".implode(',',$ids).") LIMIT $start, $limit";

				}
			}
			break;	
	}

	$total_pages = mysql_fetch_array(mysql_query($countQuery));
	$total_pages = $total_pages[num];
	

	/* Setup page vars for display. */
	if ($page == 0) $page = 1;					//if no page var is given, default to 1.
	$prev = $page - 1;							//previous page is page - 1
	$next = $page + 1;							//next page is page + 1
	$lastpage = ceil($total_pages/$limit);		//lastpage is = total pages / items per page, rounded up.
	$lpm1 = $lastpage - 1;						//last page minus 1
	
	$result= ($selectSQL)?mysql_query($selectSQL):null;
	if (count($result)){
		while ($row = mysql_fetch_row($result)){
			$idEvidence=$row[0];
			$textEvidence=$row[1];
			$curated=$row[2];
			$pubmedId=$row[3];
			$tmpString.="<div class=\"evidence\">\n";
			//Colocamos el articleTitle en vez del pubmedId:
			$command = "$pathToPython returnPubmedInformation.py $pubmedId";
			$output=array();
			//exec($command,$output,$return);
			$titlePaper = $output[0];
			$tmpString .= "<h6>$titlePaper</h6>\n";
			$tmpString .= modificarTexto($idEvidence,$textEvidence);
			if ($curated == 0){
				$tmpString.= ' <small>[PMID: <a href="http://www.ncbi.nlm.nih.gov/pubmed/'.$pubmedId.'" target="_blank">'.$pubmedId.'</a> ] <a href="#" class="rgt-arw no-curated tooltip" onClick="javascript:annotate(\''.$home_url.'/curate-evidence?idEvidence='.$idEvidence.'&whatToSearch='.$whatToSearch.'&type='.$type.'\')">Curate</a></small>';
			}
			else{
				$tmpString.=' <small>[PMID: <a href="http://www.ncbi.nlm.nih.gov/pubmed/'.$pubmedId.'" target="_blank">'.$pubmedId.'</a> ]  <a href="#" class="rgt-arw curated"onClick="javascript:annotate(\''.$home_url.'/curate-evidence?idEvidence='.$idEvidence.'&whatToSearch='.$whatToSearch.'&type='.$type.'\')">Curate</a></small>';
			}
			$tmpString.="</div><!--  End div evidence -->\n";
		}
	}else
		$tmpString .= 'Sorry no evidences found for your query.';

	$tmpString .= printPagination($whatToSearch,$lastpage,$page,$adjacents,$prev,$next);
	
	echo $tmpString;
	
	
	
	
	
?>