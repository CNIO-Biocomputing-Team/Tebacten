<?php get_header(); ?>
<section class="cols vis-break cntrtxt">
	<input id="tags" class="ui-autocomplete-input" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true" value="1-hydroxy-2-propanone"><a href="#" onClick="return searchEvidences('enzyme','search');" class="button orange" id="bigSearch">Search</a>
</section>
<section class="cols">
	<div id="evidences">Select or search a compound to see its evidences.</div>
</section>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
