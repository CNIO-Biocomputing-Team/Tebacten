<?php
/**
 *
Template Name: Species
 *
*/

get_header(); 

?>
<div id="frame">
	<div id="header_frame">
		Browse by <a href="<?php bloginfo('home') ?>/enzymes">ENZYMES</a> <a href="<?php bloginfo('home') ?>/compounds">COMPOUNDS</a> <a href="<?php bloginfo('home') ?>/species" class="active">SPECIES</a>
		<div id="logos">
			<a href="http://www.microme.eu/" target="_blank"><img src="<?php bloginfo('template_url') ?>/images/microme.png"></a><a href="http://www.cnio.es" target="_blank"></a><a href="http://www.inab.org/" target="_blank"><img src="<?php bloginfo('template_url') ?>/images/cnio.png"><img src="<?php bloginfo('template_url') ?>/images/inb.png"></a>
		</div>
	</div>
	<div id="content_frame">
		<div id="left_content_frame">
			<div id="search">				
				<input id="tags" class="ui-autocomplete-input" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true"><a href="#" onClick="return searchEvidences('organism','search');" class="searchLink">Search</a>
			</div>
			<div id="left_content">
				<?php //Recuperamos todas las especies diferentes:
					$conn = mysql_connect ("jabba.cnio.es", "tebacten", "tebacten");
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
		</div><!-- End id=left_content_frame  -->
		<div id="right_content_frame">
			<div id="text_evidences">Evidences</div>
			<div id="evidences">
				Select or search a specie to see its evidences. 
			</div><!-- End id=evidences  -->
		</div><!-- End id=right_content_frame  -->
	</div><!-- End id=content_frame  -->
	<div class="clear_both"></div>
	<div id="footer_frame">
	</div>
	


</div> <!-- End id=frame  -->


<?php get_sidebar(); ?>
<?php get_footer(); ?>