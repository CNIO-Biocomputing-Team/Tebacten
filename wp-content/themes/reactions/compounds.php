<?php get_header(); ?>
<section class="cols vis-break cntrtxt">
	<input id="tags" class="ui-autocomplete-input" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true" value="1-hydroxy-2-propanone" onclick="this.value=''"><a href="#" onClick="return searchEvidences('compound','search');" class="button orange" id="bigSearch">Search</a>
</section>
<section class="cols">
	<div id="evidences"</div>
</section>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
