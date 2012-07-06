<?php
	/* Always have wp_footer() just before the closing </body>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to reference JavaScript files.
	 */
	//wp_footer();
	$template_url=get_bloginfo('template_url');
	$pagename = get_query_var('pagename');
	$source="$template_url/scripts/autocomplete.php?searchfor=$pagename";

?>
		</section><!-- #main -->
		<footer id="bottom" role="contentinfo">
			<nav>
				<ul>
					<li>Spanish National Cancer Research Centre. CNIO Structural Biology and BioComputing Programme</li>
					<li><a href="http://microme.eu" target="_blank"><img src="<?php echo $template_url; ?>/images/microme-off.png" border="0" class="rollover" /></a>
					<li><a href="http://cnio.es" target="_blank"><img src="<?php echo $template_url; ?>/images/cnio-off.png" border="0" class="rollover" /></a>
					<li><a href="http://inab.org" target="_blank"><img src="<?php echo $template_url; ?>/images/inb-off.png" border="0" class="rollover" /></a>
				</ul>
			</nav>
<?php
	/* A sidebar in the footer? Yep. You can can customize
	 * your footer with four columns of widgets.
	 */
	//get_sidebar( 'footer' );
?>			
		</footer><!-- footer -->
</div> <!-- End div page_wrap -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script><!--Load jQuery-->
<script>!window.jQuery && document.write(unescape('%3Cscript src="<?=$template_url?>/yaff/js/libs/jquery-1.5.2.min.js"%3E%3C/script%3E'))</script>
<script src="<?php echo $template_url; ?>/yaff/js/script.js"></script>
<script src="<?php echo $template_url; ?>/yaff/js/jquery.tipsy.js"></script>
<script src="<?php echo $template_url; ?>/yaff/js/jquery.reveal.js"></script>
<script src="<?php echo $template_url; ?>/yaff/js/jquery.orbit.min.js"></script>
<script src="<?php echo $template_url; ?>/yaff/js/jquery.bpopup-0.7.0.min.js"></script>
<script src="<?php echo $template_url; ?>/uniform/jquery.uniform.js"></script>
<script src="<?php echo $template_url; ?>/js/jquery-ui-1.8.21.js"></script>
<script src="<?php echo $template_url; ?>/js/reactions.js"></script>



<script type="text/javascript">
	 
$(document).ready(function(){
	$("img.rollover").hover( 
		function() { this.src = this.src.replace("-off", "-on"); 
		}, 
		function() { this.src = this.src.replace("-on", "-off"); 
	});
	$("input:text, input:checkbox, input:radio, input:file, .uniform-button").uniform();
	$("#bigSearch").trigger('click');
	 
    $(".slidingDiv").hide();
    $(".show_hide").show();
	 
    $('.show_hide').click(function(){
    $(".slidingDiv").slideToggle();
    });
	 
});



function annotate(url){
	$('#popup').bPopup({
          loadUrl: url,
          follow:[false,false],
          modalClose: true,
          position: ['auto',150]
    });
	
}
	 
</script>
<script type="text/javascript">
		
	/*function overlayTaxonomy(i){
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
	}*/
</script>	
<script>	
	$(function() {		
		
		$( "#tags" ).autocomplete({
			source: "<?php echo $source; ?>",
			minLength:3,
		});
	});
</script>
 	</body>
</html>