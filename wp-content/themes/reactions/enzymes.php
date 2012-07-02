<?php get_header(); ?>
<section class="cols vis-break brdr cntrtxt">
	<input type="text" id="tags" class="ui-autocomplete-input" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true" value="1,3-propanediol dehydrogenase"><a href="#" onClick="return searchEvidences('enzyme','search');" class="button orange" id="bigSearch">Search</a>
</section>
<section class="cols">
	<div id="evidences"></div>
</section>
<?php get_sidebar(); ?>
<?php get_footer(); ?>