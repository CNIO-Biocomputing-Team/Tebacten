<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content
 * after.  Calls sidebar-footer.php for bottom widgets.
 *
 * @package WordPress
 * @subpackage Boilerplate
 * @since Boilerplate 1.0
 */
?>
		</section><!-- #main -->
		<footer role="contentinfo">
<?php
	/* A sidebar in the footer? Yep. You can can customize
	 * your footer with four columns of widgets.
	 */
	get_sidebar( 'footer' );
?>			
		</footer><!-- footer -->
<?php
	/* Always have wp_footer() just before the closing </body>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to reference JavaScript files.
	 */
	wp_footer();
?>
</div> <!-- End div page_wrap -->
<script>
<?php 
echo "$(function() {
			var availableTags = [";
$selectSQL="select distinct(a.id_enzyme),b.textmining_enzyme_name from evidences_enzymes as a, enzymes as b where a.id_enzyme=b.id_enzyme order by textmining_enzyme_name";
$result= mysql_query($selectSQL);
while ($row = mysql_fetch_row($result)){
	$idEnzyme=$row[0];
	$textMiningEnzymeName=$row[1];
	echo "{label: \"$textMiningEnzymeName\",value:\"$textMiningEnzymeName\"},";
}
echo "];";
?>				
	$( "#tags" ).autocomplete({
		source: availableTags
	});
});
</script>
 </body>
</html>