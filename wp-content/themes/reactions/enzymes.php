<?php
/**
 *
Template Name: Enzymes
 *
*/

get_header('enzymes'); 

?>
<div id="frame">
	<div id="header_frame">
		Browse by <a href="<?php bloginfo('home') ?>/enzymes" class="active">ENZYMES</a> <a href="<?php bloginfo('home') ?>/compounds">COMPOUNDS</a> <a href="<?php bloginfo('home') ?>/species">SPECIES</a>
		<div id="logos">
			<a href="http://www.microme.eu/" target="_blank"><img src="<?php bloginfo('template_url') ?>/images/microme.png"></a><a href="http://www.cnio.es" target="_blank"></a><a href="http://www.inab.org/" target="_blank"><img src="<?php bloginfo('template_url') ?>/images/cnio.png"><img src="<?php bloginfo('template_url') ?>/images/inb.png"></a>
		</div>
	</div>
	<div id="content_frame">
		<div id="left_content_frame">
			<div id="search">				
				<input id="tags" class="ui-autocomplete-input" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true"><a href="#" onClick="return searchEvidences('enzyme','search');" class="searchLink">Search</a>
			</div>
			<div id="left_content">
				<?php //Recuperamos todas las enzimas:
					$conn = mysql_connect ("jabba.cnio.es", "tebacten", "tebacten");
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
				?>
			</div>
		</div><!-- End id=left_content_frame  -->
		<div id="right_content_frame">
			<div id="text_evidences">Evidences</div>
			<div id="evidences">
				Select or search an enzyme to see its evidences.
			</div><!-- End id=evidences  -->
		</div><!-- End id=right_content_frame  -->
	</div><!-- End id=content_frame  -->
	<div class="clear_both"></div>
	<div id="footer_frame">
	</div>
<div class="demo">
</div><!-- End demo -->



<div style="display: none;" class="demo-description">
<p>The Autocomplete widgets provides suggestions while you type into the field. Here the suggestions are tags for programming languages, give "ja" (for Java or JavaScript) a try.</p>
<p>The datasource is a simple JavaScript array, provided to the widget using the source-option.</p>
</div>


</div> <!-- End id=frame  -->


<?php get_sidebar(); ?>
<?php get_footer('enzymes'); ?>