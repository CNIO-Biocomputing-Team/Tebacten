<?php get_header(); ?>
<section class="cols vis-break brdr cntrtxt">
	<input type="text" id="tags" class="ui-autocomplete-input" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true" value="1,3-propanediol dehydrogenase"><a href="#" onClick="return searchEvidences('enzyme','search');" class="button orange" id="bigSearch">Search</a>
	<div id="left_content">
				<?php //Recuperamos todas las enzimas:
					/*
$conn = mysql_connect ("localhost", "root", "");
					mysql_select_db("tebacten", $conn);
					mysql_query("SET NAMES 'utf8'");
					$selectSQL="select distinct(a.id_enzyme),b.textmining_enzyme_name from evidences_enzymes as a, enzymes as b where a.id_enzyme=b.id_enzyme order by textmining_enzyme_name";
					$result= mysql_query($selectSQL);
					echo "<select id=\"enzymes\" class=\"multiselect\" multiple=\"multiple\" name=\"enzymes[]\">";
					$counter=0;
					while ($row = mysql_fetch_row($result)){
						$idEnzyme=$row[0];
						$textMiningEnzymeName=$row[1];
						echo "<option value=\"$textMiningEnzymeName\" onclick=\"return showEvidences('$idEnzyme','enzyme');\">$textMiningEnzymeName</option>";
						#echo "<a id=\"compound_$idEnzyme\" href=\"#\" onclick=\"return showEvidences('$idEnzyme','enzyme');\">$textMiningEnzymeName</a></li>";
						$counter++;
					}
					echo "</select>";
*/
				?>
	</div>
</section>
<section class="cols">
	<div id="evidences"></div>
</section>
<?php get_sidebar(); ?>
<?php get_footer(); ?>