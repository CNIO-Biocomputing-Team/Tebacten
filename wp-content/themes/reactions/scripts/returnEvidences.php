<?php 
	//Script que devuelve las evidencias y las carga de forma asíncrona en la parte de la derecha (div#evidences)
	// Retrieve data from Query String
	include("config.php");
	$tmpString="";
	$type= $_GET['type'];
	$conn = mysql_connect ($database, $db_user, $db_password);
	mysql_select_db("tebacten", $conn);
	mysql_query("SET NAMES 'utf8'");
	include 'functions.php';
	$pathToPython=" /opt/python/2.7/bin/python";
	if ($type=="enzyme"){
		$idCompuesto = $_GET['idCompuesto'];
		//Tenemos que devolver todas las evidencias que contienen esa id_enzyme
		//$selectSQL="select id_evidences_enzymes,id_evidence from evidences_enzymes where id_enzyme=$idCompuesto limit 1";
		//print $selectSQL;
		//echo $selectSQL;
		
		$selectSQL="select a.id_evidence,b.text_evidence,b.curated,b.pubmed_id from evidences_enzymes a, evidences b where id_enzyme=$idCompuesto and a.id_evidence=b.id_evidence";
		$result= mysql_query($selectSQL);
		while ($row = mysql_fetch_row($result)){
			$idEvidence=$row[0];
			$textEvidence=$row[1];
			$curated=$row[2];
			$pubmedId=$row[3];
			$tmpString.="<div class=\"evidence\">\n";
			//Colocamos el articleTitle en vez del pubmedId:
			$command="$pathToPython /home/tebacten/public_html/wp-content/themes/reactions/scripts/returnPubmedInformation.py $pubmedId";
			exec($command,$output,$return);
			$titlePaper=$output[0];			
			$tmpString.="<div class=\"title_paper\">$titlePaper</div>\n";
			$tmpString.=modificarTexto($idEvidence,$textEvidence);
			if ($curated==0){
				$tmpString.="<div class=\"curate_button\"><a href=\"http://tebacten.bioinfo.cnio.es/curate-evidence?idEvidence=$idEvidence&type=$type&TB_iframe=true&height=600&width=1000\" class=\"thickbox\">Annotate</a></div>";
			}
			else{
				$tmpString.="<div class=\"curate_button\"><a href=\"http://tebacten.bioinfo.cnio.es/curate-evidence?idEvidence=$idEvidence&type=$type&TB_iframe=true&height=600&width=1000\" class=\"thickbox\">Annotate</a><span style=\"color:#FF0000\">(Annotated)</span></div>";
			}
			$tmpString.="</div><!--  End div evidence -->\n";
		}
	}
	
	elseif($type=="compound"){
		$idCompuesto = $_GET['idCompuesto'];
		$selectSQL="select a.id_evidence,b.text_evidence,b.curated,b.pubmed_id from evidences_compounds a, evidences b where id_compound=$idCompuesto and a.id_evidence=b.id_evidence";
		$result= mysql_query($selectSQL);
		while ($row = mysql_fetch_row($result)){
			$idEvidence=$row[0];
			$textEvidence=$row[1];
			$curated=$row[2];
			$pubmedId=$row[3];
			$tmpString.="<div class=\"evidence\">\n";
			//Colocamos el articleTitle en vez del pubmedId:
			$pathToPython=" /opt/python/2.7/bin/python";
			$command="$pathToPython /home/tebacten/public_html/wp-content/themes/reactions/scripts/returnPubmedInformation.py $pubmedId";
			exec($command,$output,$return);
			$titlePaper=$output[0];			
			$tmpString.="<div class=\"title_paper\">$titlePaper</div>\n";
			$tmpString.=modificarTexto($idEvidence,$textEvidence);
			if ($curated==0){
				$tmpString.="<div class=\"curate_button\"><a href=\"http://tebacten.bioinfo.cnio.es/curate-evidence?idEvidence=$idEvidence&type=$type&TB_iframe=true&height=600&width=1000\" class=\"thickbox\">Annotate</a></div>";
			}
			else{
				$tmpString.="<div class=\"curate_button\"><a href=\"http://tebacten.bioinfo.cnio.es/curate-evidence?idEvidence=$idEvidence&type=$type&TB_iframe=true&height=600&width=1000\" class=\"thickbox\">Annotate</a><span style=\"color:#FF0000\">(Annotated)</span></div>";
			}
			$tmpString.="</div><!--  End div evidence -->\n";
		}
	}
	
	elseif($type=="organism"){
		$idCompuesto = $_GET['idCompuesto'];
		$selectSQL="select a.id_evidence,b.text_evidence,b.curated,b.pubmed_id from evidences_organisms a, evidences b where id_organism=$idCompuesto and a.id_evidence=b.id_evidence";
		//print "<br/>$selectSQL";
		$result= mysql_query($selectSQL);
		while ($row = mysql_fetch_row($result)){
			$idEvidence=$row[0];
			$textEvidence=$row[1];
			$curated=$row[2];
			$pubmedId=$row[3];
			$tmpString.="<div class=\"evidence\">\n";
			//Colocamos el articleTitle en vez del pubmedId:
			$pathToPython=" /opt/python/2.7/bin/python";
			$command="$pathToPython /home/tebacten/public_html/wp-content/themes/reactions/scripts/returnPubmedInformation.py $pubmedId";
			exec($command,$output,$return);
			$titlePaper=$output[0];	
			$tmpString.="<div class=\"title_paper\">$titlePaper</div>\n";
			$tmpString.=modificarTexto($idEvidence,$textEvidence);
			if ($curated==0){
				$tmpString.="<div class=\"curate_button\"><a href=\"http://tebacten.bioinfo.cnio.es/curate-evidence?idEvidence=$idEvidence&type=$type&TB_iframe=true&height=600&width=1000\" class=\"thickbox\">Annotate</a></div>";
			}
			else{
				$tmpString.="<div class=\"curate_button\"><a href=\"http://tebacten.bioinfo.cnio.es/curate-evidence?idEvidence=$idEvidence&type=$type&TB_iframe=true&height=600&width=1000\" class=\"thickbox\">Annotate</a><span style=\"color:#FF0000\">(Annotated)</span></div>";
			}
			$tmpString.="</div><!--  End div evidence -->\n";
		}
	}
	
	if ($type=="search"){
		$textminingName=$_GET['textminingName'];
		$whatToSearch=$_GET['whatToSearch'];
		#print "<br/>$textminingName";
		#print "<br/>$whatToSearch";
		//Generamos un selectSQL que nos sirva tanto para enzymes como para compounds como para species
		$selectIdCompound="select id_$whatToSearch from ".$whatToSearch."s where textmining_".$whatToSearch."_name=\"$textminingName\"";
		//print "<br/>$selectIdCompound";
		$result= mysql_query($selectIdCompound);
		$rowIdCompound = mysql_fetch_row($result);
		$idCompuesto=$rowIdCompound[0];
		//Tenemos que devolver todas las evidencias que contienen esa id_enzyme
		//$selectSQL="select id_evidences_enzymes,id_evidence from evidences_enzymes where id_enzyme=$idCompuesto limit 1";
		//print $selectSQL;
		//echo $selectSQL;
		$selectSQL="select a.id_evidence,b.text_evidence,b.curated,b.pubmed_id from evidences_".$whatToSearch."s a, evidences b where id_".$whatToSearch."=$idCompuesto and a.id_evidence=b.id_evidence";
		$result= mysql_query($selectSQL);
		while ($row = mysql_fetch_row($result)){
			$idEvidence=$row[0];
			$textEvidence=$row[1];
			$curated=$row[2];
			$pubmedId=$row[3];
			$tmpString.="<div class=\"evidence\">\n";
			//Colocamos el articleTitle en vez del pubmedId:
			$pathToPython=" /opt/python/2.7/bin/python";
			$command="$pathToPython /home/tebacten/public_html/wp-content/themes/reactions/scripts/returnPubmedInformation.py $pubmedId";
			exec($command,$output,$return);
			$titlePaper=$output[0];			
			$tmpString.="<div class=\"title_paper\">$titlePaper</div>\n";
			$tmpString.=modificarTexto($idEvidence,$textEvidence);
			if ($curated==0){
				$tmpString.="<div class=\"curate_button\"><a href=\"http://tebacten.bioinfo.cnio.es/curate-evidence?idEvidence=$idEvidence&type=$type&TB_iframe=true&height=600&width=1000\" class=\"thickbox\">Annotate</a></div>";
			}
			else{
				$tmpString.="<div class=\"curate_button\"><a href=\"http://tebacten.bioinfo.cnio.es/curate-evidence?idEvidence=$idEvidence&type=$type&TB_iframe=true&height=600&width=1000\" class=\"thickbox\">Annotate</a><span style=\"color:#FF0000\">(Annotated)</span></div>";
			}
			$tmpString.="</div><!--  End div evidence -->\n";
		}
	}
	
	echo $tmpString;
	
	
?>