<?php get_header(); ?>
<section class="cols">
	<article class="col first">
		<h6>TeBactEn (text mining for bacterial enzymes)</h6>
		<p>TeBactEn is a tool designed to facilitate the retrieval, extraction and annotation of bacterial enzymatic reactions and pathways from the literature. </p>
		<p>The system has been developed in the context of the <a href="http://microme.eu" target="_blank">Microme</a> project and contains three different data collections, namely (a) a compilation of articles derived from the Microme database, i.e. articles (abstracts and full text articles) that had been used for manual annotation of bacterial pathways (Microme set), (b) a set that covers abstracts from the entire PubMed database that are relevant to bacteria (PubMed set) and finally (c) a collection of abstracts and full text articles that are relevant for a list of bacteria of special interest to the Microme project, facilitating a more exhaustive extraction of enzymes particularly for these bacteria (species set). </p>
		<p>In case of all three TeBactEn data collections, an exhaustive recognition of mentions of all species and taxonomic entities was carried out.</p>
	</article>
	<article class="col side-nav">
		<h6>Main features</h6>
		<p>TeBactEn covers all the main steps relevant for the automatic extraction and ranking of metabolism relations from the literature and allows enhanced access and annotation of related information:</p>
		<ul>
			<li>Identification of metabolism relevant articles.</li>
			<li>Detection of the bio-entities involved in biochemical reactions:<br>enzyme, compounds and organisms.</li>
			<li>Extraction weighted (ranked) relationships between these<br>bio-entities.</li>
			<li>An interface to browse this information and to construct<br>a manually curated database of metabolism reactions.</li>
			<li>Host user-entered annotations.</li>
			<li>The option to normalize/ground bio-entity mentions<br>to other knowledgebases like UniProt and ChEBI.</li>
		</ul>
	</article>
</section>
<section class="cols">
	<h6>TeBactEn pipeline</h6>
</section>
<section class="vis-break brdr btmspc-dbl topspc">
	<img src="<?php bloginfo('template_url')?>/images/microme_flow-web.png" />
	<p><small>TeBactEn literature mining system flow chart. The system consists in two main components, the information retrieval pipeline and the information/relation extraction modules. The first component works at the level of articles while the second one is concerned on the identification of semantic labels and their relations at the level of individual sentences</small></p>
</section>
<section class="cols">
	<p>The figure illustrates the general flow chart followed in the TeBactEn pipeline. For each of the bacteria of interest, the species taxonomy identifier from the NCBI taxonomy was selected. Expansion by including the child nodes of the species <a href="http://www.ncbi.nlm.nih.gov/taxonomy" target="_blank">NCBI taxonomy</a> node corresponding to strains and sub-strains was performed. All the names, aliases and synonyms were derived from this resource and simple typographical variants were generated together with abbreviated genus names for cases were the resulting shortened species name was not ambiguous. As an alternative to this Boolean query, we explored originally the use of more sophisticated retrieval approaches. For instance to ascertain whether some extra keywords could be relevant for the retrieval step, a supervised document classifier tool (<a href="http://cbdm.mdc-berlin.de/tools/medlineranker/" target="_blank">MedlineRanker</a>) and a system based on text similarity and clustering (<a href="http://biocomp.o2i.it/bioCOMP/main.php?object=application_toMine&action=view" target="_blank">PubClust</a>) were tested. Moreover, we examined whether for some bacteria there existed species-specific journals in order to use them as an additional component for query expansion within the article selection step. </p>
	<p>Once the various document collections were assembled, we carried out document standardization and extraction of useful textual data. In case of PubMed records this consisted in selection of titles and abstracts sections, while in case of full text articles text conversion and preprocessing also had to deal with extraction of plain text data from PDF and HTML documents. The text conversion of PDF files was done using pdftotext and <a href="http://www.pdflib.com/" target="_blank">PDFlib</a>. The extraction of plain text from HTML files was carried out using an in house HTML parser tool optimized for handling scientific online articles. All documents were further processed using an in-house sentence boundary recognition script that worked reasonable well both on PubMed abstracts as well as full text articles.</p>
	<p>The next step of the TeBactEn pipeline consisted in the addition of semantic labels to the text, namely in detecting the mentions of relevant bio-entities for the extraction of bacterial metabolism and pathways. Within the TeBactEn article collections we tried to recognize three different types of entities, all of fundamental relevance for the annotation of bacteria metabolism. These entities consisted in (1) <a href="<?php bloginfo('home')?>/species">species and taxonomic names</a>, (2) <a href="<?php bloginfo('home')?>/enzymes">proteins and enzyme mentions</a>, and (3) mentions of <a href="<?php bloginfo('home')?>/compounds">chemical compounds and drugs</a>. </p>
</section>
<?php get_footer(); ?>