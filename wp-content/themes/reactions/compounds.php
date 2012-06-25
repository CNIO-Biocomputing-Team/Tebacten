<?php
/**
 *
Template Name: Compounds
 *
*/

get_header(); 

?>
<div id="frame">
	<div id="header_frame">
		Browse by <a href="<?php bloginfo('home') ?>/enzymes">ENZYMES</a> <a href="<?php bloginfo('home') ?>/compounds" class="active">COMPOUNDS</a> <a href="<?php bloginfo('home') ?>/species">SPECIES</a>
		<div id="logos">
			<a href="http://www.microme.eu/" target="_blank"><img src="<?php bloginfo('template_url') ?>/images/microme.png"></a><a href="http://www.cnio.es" target="_blank"></a><a href="http://www.inab.org/" target="_blank"><img src="<?php bloginfo('template_url') ?>/images/cnio.png"><img src="<?php bloginfo('template_url') ?>/images/inb.png"></a>
		</div>
	</div>
	<div id="content_frame">
		<div id="left_content_frame">
			<div id="search">				
				<input id="tags" class="ui-autocomplete-input" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true"><a href="#" onClick="return searchEvidences('compound','search');" class="searchLink">Search</a>
			</div>
			<div id="left_content">
				<?php //Recuperamos todos los compuestos:
					$conn = mysql_connect ("jabba.cnio.es", "tebacten", "tebacten");
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
		</div><!-- End id=left_content_frame  -->
		<div id="right_content_frame">
			<div id="text_evidences">Evidences</div>
			<div id="evidences">
				Select or search a compound to see its evidences. 
			</div><!-- End id=evidences  -->
		</div><!-- End id=right_content_frame  -->
	</div><!-- End id=content_frame  -->
	<div class="clear_both"></div>
	<div id="footer_frame">
	</div>
	


</div> <!-- End id=frame  -->


<?php get_sidebar(); ?>
<?php get_footer(); ?>