<?php get_header(); ?>
<section class="cols vis-break ">
	<input id="tags" class="ui-autocomplete-input" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true"><a href="#" onClick="return searchEvidences('enzyme','search');" class="button orange">Search</a>
	<div id="left_content">
				<?php //Recuperamos todas las especies diferentes:
					include("scripts/config.php");
					$conn = mysql_connect ($database, $db_user, $db_password);
					mysql_select_db("tebacten", $conn);
					mysql_query("SET NAMES 'utf8'");
					$selectSQL="select distinct(a.id_organism),b.textmining_organism_name from evidences_organisms as a, organisms as b where a.id_organism=b.id_organism order by textmining_organism_name";
					$result= mysql_query($selectSQL);
					echo "<select id=\"species\" class=\"multiselect\" multiple=\"multiple\" name=\"species[]\">";
					$counter=0;
					while ($row = mysql_fetch_row($result)){
						$idOrganism=$row[0];
						$textMiningOrganismName=$row[1];
						echo "<option value=\"$textMiningOrganismName\" onclick=\"return showEvidences('$idOrganism','organism');\">$textMiningOrganismName</option>";
						#echo "<a id=\"compound_$idEnzyme\" href=\"#\" onclick=\"return showEvidences('$idEnzyme','enzyme');\">$textMiningEnzymeName</a></li>";
						$counter++;
					}
					echo "</select>";
				?>
	</div>
</section>
<section class="cols">
	<div id="evidences">Select or search an enzyme to see its evidences.</div>
</section>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
