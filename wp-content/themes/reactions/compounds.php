<?php get_header(); ?>
<section class="cols vis-break cntrtxt">
	<input id="tags" class="ui-autocomplete-input" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true" value="e.g. aristolochic acid" onclick="this.value=''"><a href="#" onClick="return searchEvidences('compound','search');" class="button orange" id="bigSearch">Search</a>
</section>
<section class="cols">
	<div id="evidences"><img src="<?php bloginfo('template_url')?>/images/chemical-example.jpg"></div>
</section>
<?php get_footer(); ?>
