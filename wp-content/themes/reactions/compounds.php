<?php get_header(); ?>
<section class="cols vis-break ">
	<input id="tags" class="ui-autocomplete-input" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true"><a href="#" onClick="return searchEvidences('enzyme','search');" class="button orange">Search</a>
	<div id="left_content">
	<?php //Recuperamos todos los compuestos:
		include("scripts/config.php");
		$conn = mysql_connect ($database, $db_user, $db_password);
		mysql_select_db("tebacten", $conn);
		mysql_query("SET NAMES 'utf8'");
		$selectSQL="select distinct(a.id_compound),b.textmining_compound_name from evidences_compounds as a, compounds as b where a.id_compound=b.id_compound order by textmining_compound_name";
		$result= mysql_query($selectSQL);
		echo "<select id=\"compounds\" class=\"multiselect\" multiple=\"multiple\" name=\"enzymes[]\">";
		$counter=0;
		while ($row = mysql_fetch_row($result)){
			$idCompound=$row[0];
			$textMiningCompoundName=$row[1];
			echo "<option value=\"$textMiningCompoundName\" onclick=\"return showEvidences('$idCompound','compound');\">$textMiningCompoundName</option>";
			#echo "<a id=\"compound_$idEnzyme\" href=\"#\" onclick=\"return showEvidences('$idEnzyme','enzyme');\">$textMiningEnzymeName</a></li>";
			$counter++;
		}
		echo "</select>";
	?>
	</div>
</section>
<section class="cols">
	<div id="evidences">Select or search a compound to see its evidences.</div>
</section>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
