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
	$template_url=get_bloginfo('template_url');
	$pagename = get_query_var('pagename');
	$source="$template_url/scripts/autocomplete.php?searchfor=$pagename";

?>
</div> <!-- End div page_wrap -->
<script type="text/javascript">
	 
	$(document).ready(function(){
	 
	        $(".slidingDiv").hide();
	        $(".show_hide").show();
	 
	    $('.show_hide').click(function(){
	    $(".slidingDiv").slideToggle();
	    });
	 
	});
	 
</script>
<script type="text/javascript">
		
	function overlayTaxonomy(i){
		$("#overlayTaxonomy_"+i).mask("Searching organism…");
		return false;
	}
	function overlayCompounds(i){
		$("#overlayCompounds_"+i).mask("Searching compound…");
		return false;
	}
	function overlayEnzymes(i){
		$("#overlayEnzymes_"+i).mask("Searching proteins…");
		return false;
	}
</script>	
<script>	
	$(function() {		
		$("select, input:checkbox, input:radio, input:file").uniform();
		$( "#tags" ).autocomplete({
			source: "<?php echo $source; ?>",
			minLength:3,
		});
	});
</script>
 	</body>
</html>