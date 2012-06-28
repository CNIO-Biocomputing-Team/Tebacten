<?php if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start(); ?>
<?php $page = basename($_SERVER['SCRIPT_NAME']); ?>
<!DOCTYPE html><!-- G5Framework -->

<!--[if lt IE 7 ]> <html lang="en" class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en" class="no-js"> <!--<![endif]-->

<head>
<meta charset="UTF-8"> 

<title>HTML5 Front End Development Framework | G5 Framework</title>

<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta http-equiv="imagetoolbar" content="no" />

<meta name="author" content="Greg Babula" /> 
<meta name="copyright" content="&copy; 2011 Greg Babula" /> 
<meta name="description" content="G5 Framework is a HTML5, CSS3, PHP &amp; jQuery Front-End Development framework created by Greg Babula." /> 
<meta name="robots" content="index,follow" /> 
<meta name="robots" content="noodp" />

<link rel="canonical" href="http://framework.gregbabula.info/<?php echo $page; ?>" />
<link rel="author" href="humans.txt" />
<link rel="shortcut icon" href="assets/favicon.ico" />
<link rel="apple-touch-icon" href="assets/favicon.png" />

<link rel="stylesheet" media="screen" href="css/base.css?v=2" /> <!--Load CSS-->
<link rel="stylesheet" media="handheld" href="css/handheld.css?v=2" /> <!-- Mobile -->

<script src="js/libs/modernizr-1.6.min.js"></script> <!-- Modernizr -->

</head>
<body>

    <div id="wrapper">

        <header id="top">

                <h1 id="logo"><a href="<?php echo $page; ?>"><span class="hidden">G5 Framework - HTML5 Front End Development Framework</span></a></h1>
                
                <nav>
                	<ul>
                    	<li><a href="http://framework.gregbabula.info" class="<?php if ($page == 'index.php') { ?>active<?php } ?>">Home</a></li>
                        <li><a href="#" data-reveal-id="details-modal">Framework Details</a></li>
                        <li><a href="http://gregbabula.info/framework.php">Discussion &amp; Support</a></li>
                        <li><a href="#" class="button orange" data-reveal-id="download-modal">Download</a></li>
                    </ul>
                </nav>

        </header><!--end #top-->

        <section class="content">

            <div id="featured" class="btmspc">
                <img src="images/slides/1.png" />
                <img src="images/slides/2.png" alt="HTML Captions" data-caption="#caption-2" />

                <!--<span class="orbit-caption" id="caption-2">I'm A Badass Caption</span>-->
            </div><!--end #featured-->

            <section class="cols vis-break brdr cntrtxt btmspc-dbl">

                <h1>(X)HTML5, CSS3, PHP &amp; jQuery Front End Framework</h1>

                <img src="images/browser_logos-64.png" width="353" height="66" alt="Browsers" class="topspc tooltip" title="Cross Browser Compatible" />

            </section><!--end .cols-->

            <section class="cols cols4 btmspc-dbl">

                <article class="col col2 first">
                    <h6>Introduction</h6>

                    <p>G5 Framework started as a <a href="http://gregbabula.info" target="_blank">personal</a> project. In an attempt to speed up workflow, reuse the best coding practices &amp; similar coding techniques, the framework serves as a starter file for new websites.</p> 

                    <p>Being an active member of the web design community, this framework is a result of a ton of reading and research. For over a year the framework was constantly changed, simplified, and updated with current best practices. The framework is very lightweight and only includes what I believe are the essentials.</p>

                    <p>The framework is free for personal &amp; commercial use as long as <a href="humans.txt" target="_blank"><mark>humans.txt</mark></a> remains unchanged &amp; in the index head. Don't worry, you can add your team information in there also.</p>

                    <p><mark>All questions, comments, and suggestions regarding the framework can be directed <a href="http://gregbabula.info/framework.php" class="rgt-arw">here</a></mark></p>

                </article>

                <article class="col side-nav">
                    <h6>Packed With</h6>
                        <ul>
                            <li><a href="http://www.modernizr.com" target="_blank">Modernizr 1.6</a></li>
                            <li><a href="http://meyerweb.com/eric/thoughts/2007/05/01/reset-reloaded" target="_blank">Eric Meyer's Reset Reloaded</a></li>
                            <li><a href="http://docs.jquery.com/Downloading_jQuery" target="_blank">jQuery 1.5.2</a> with fallback</li>
                            <li><a href="http://www.css3pie.com" target="_blank">CSS3 PIE</a></li>
                            <li><a href="http://webdesignerwall.com/tutorials/css3-gradient-buttons" target="_blank">CSS3 Buttons</a></li>
                            <li><a href="http://cssglobe.com/post/9849/easy-percentage-grid-system-with-html5" target="_blank">Easy Grid</a></li>
                            <li>IE6 PNG Fix + <a href="http://ie6update.com" target="_blank">IE6 Update</a></li>
                            <li><a href="https://github.com/jaz303/tipsy" target="_blank">Tipsy Tool Tips</a></li>
                            <li><a href="http://www.zurb.com/playground/reveal-modal-plugin" target="_blank">Reveal Modals</a></li>
                            <li><a href="http://www.zurb.com/playground/orbit-jquery-image-slider" target="_blank">Orbit Image Slider</a></li>
                        </ul>
                </article>

                <aside class="col side-nav">
                    <h6>Framework Features</h6>
                        <ul>
                            <li>HTML5 baseline</li>
                            <li>Base CSS</li>
                            <li>PHP Active Class</li>
                            <li>Smooth Scroll to Top</li>
                            <li>HTML5 Placeholder Fallback</li>
                            <li>Base SEO</li>
                            <li>Based on a F Layout</li>
                            <li>Sticky Footer</li>
                            <li>Clean Organization</li>
                            <li><a href="#" data-reveal-id="details-modal">And More&hellip;</a></li>
                        </ul>
                </aside>

            </section><!--end .cols cols3--> 

            <section class="cols vis-break brdr cntrtxt btmspc-dbl topspc">

                <h3 class="topspc btmspc">Free for personal &amp; commercial use as long as <a href="humans.txt" target="_blank"><mark>humans.txt</mark></a><br />remains unchanged &amp; in the index head.</h3>

                <a href="http://framework.gregbabula.info/assets/g5framework.zip" class="button orange tooltip btmspc" title="Download Zip"><img src="images/save_64.png" width="64" height="64" alt="Download Zip" /></a>

            </section><!--end .cols-->


            <div id="grid-example">

                <section class="cols">

                    <article class="col first">
                        <h6>Column 1</h6>
                        <p>Vivamus nunc tortor, consequat in porta nec, suscipit lacinia dui. Nulla viverra bibendum nisi, 
                            sit amet ullamcorper nibh euismod at. Integer vehicula tempor metus eu dignissim.</p>
                    </article>
                    
                    <article class="col">
                        <h6>Column 2</h6>
                        <p>Vivamus nunc tortor, consequat in porta nec, suscipit lacinia dui. Nulla viverra bibendum nisi, 
                            sit amet ullamcorper nibh euismod at. Integer vehicula tempor metus eu dignissim.</p>
                    </article>
                
                </section><!--end .cols-->

                <section class="cols cols3">
                
                    <article class="col first">
                        <h6>Column 1</h6>
                        <p>Vivamus nunc tortor, consequat in porta nec, suscipit lacinia dui. Nulla viverra bibendum nisi, 
                            sit amet ullamcorper nibh euismod at. Integer vehicula tempor metus eu dignissim.</p>
                    </article>
                    
                    <article class="col">
                        <h6>Column 2</h6>
                        <p>Vivamus nunc tortor, consequat in porta nec, suscipit lacinia dui. Nulla viverra bibendum nisi, 
                            sit amet ullamcorper nibh euismod at. Integer vehicula tempor metus eu dignissim.</p>
                    </article>
                    
                    <article class="col">
                        <h6>Column 3</h6>
                        <p>Vivamus nunc tortor, consequat in porta nec, suscipit lacinia dui. Nulla viverra bibendum nisi, 
                            sit amet ullamcorper nibh euismod at. Integer vehicula tempor metus eu dignissim.</p>
                    </article>
                
                </section><!--end .cols cols3-->
                
                <section class="cols cols4">
                
                    <article class="col first">
                        <h6>Column 1</h6>
                        <p>Vivamus nunc tortor, consequat in porta nec, suscipit lacinia dui. Nulla viverra bibendum nisi, 
                            sit amet ullamcorper nibh euismod at. Integer vehicula tempor metus eu dignissim.</p>
                    </article>
                    
                    <article class="col">
                        <h6>Column 2</h6>
                        <p>Vivamus nunc tortor, consequat in porta nec, suscipit lacinia dui. Nulla viverra bibendum nisi, 
                            sit amet ullamcorper nibh euismod at. Integer vehicula tempor metus eu dignissim.</p>
                    </article>
                    
                    <article class="col">
                        <h6>Column 3</h6>
                        <p>Vivamus nunc tortor, consequat in porta nec, suscipit lacinia dui. Nulla viverra bibendum nisi, 
                            sit amet ullamcorper nibh euismod at. Integer vehicula tempor metus eu dignissim.</p>
                    </article>
                    
                    <article class="col">
                        <h6>Column 4</h6>
                        <p>Vivamus nunc tortor, consequat in porta nec, suscipit lacinia dui. Nulla viverra bibendum nisi, 
                            sit amet ullamcorper nibh euismod at. </p>
                    </article>
                
                </section><!--end .cols cols4-->

                <section class="cols cols5">
                
                    <article class="col first">
                        <h6>Column 1</h6>
                        <p>Vivamus nunc tortor, consequat in porta nec, suscipit lacinia dui. Nulla viverra bibendum nisi, 
                            sit amet ullamcorper nibh euismod at. Integer vehicula tempor metus eu dignissim.</p>
                    </article>
                    
                    <article class="col">
                        <h6>Column 2</h6>
                        <p>Vivamus nunc tortor, consequat in porta nec, suscipit lacinia dui. Nulla viverra bibendum nisi, 
                            sit amet ullamcorper nibh euismod at. Integer vehicula tempor metus eu dignissim.</p>
                    </article>
                    
                    <article class="col">
                        <h6>Column 3</h6>
                        <p>Vivamus nunc tortor, consequat in porta nec, suscipit lacinia dui. Nulla viverra bibendum nisi, 
                            sit amet ullamcorper nibh euismod at. Integer vehicula tempor metus eu dignissim.</p>
                    </article>
                    
                    <article class="col">
                        <h6>Column 4</h6>
                        <p>Vivamus nunc tortor, consequat in porta nec, suscipit lacinia dui. Nulla viverra bibendum nisi, 
                            sit amet ullamcorper nibh euismod at. </p>
                    </article>

                    <article class="col">
                        <h6>Column 5</h6>
                        <p>Vivamus nunc tortor, consequat in porta nec, suscipit lacinia dui. Nulla viverra bibendum nisi, 
                            sit amet ullamcorper nibh euismod at. </p>
                    </article>
                
                </section><!--end .cols cols5-->

                <section class="cols cols6">
                
                    <article class="col first">
                        <h6>Column 1</h6>
                        <p>Vivamus nunc tortor, consequat in porta nec, suscipit lacinia dui. Nulla viverra bibendum nisi, 
                            sit amet ullamcorper nibh euismod at. Integer vehicula tempor metus eu dignissim.</p>
                    </article>
                    
                    <article class="col">
                        <h6>Column 2</h6>
                        <p>Vivamus nunc tortor, consequat in porta nec, suscipit lacinia dui. Nulla viverra bibendum nisi, 
                            sit amet ullamcorper nibh euismod at. Integer vehicula tempor metus eu dignissim.</p>
                    </article>
                    
                    <article class="col">
                        <h6>Column 3</h6>
                        <p>Vivamus nunc tortor, consequat in porta nec, suscipit lacinia dui. Nulla viverra bibendum nisi, 
                            sit amet ullamcorper nibh euismod at. Integer vehicula tempor metus eu dignissim.</p>
                    </article>
                    
                    <article class="col">
                        <h6>Column 4</h6>
                        <p>Vivamus nunc tortor, consequat in porta nec, suscipit lacinia dui. Nulla viverra bibendum nisi, 
                            sit amet ullamcorper nibh euismod at. </p>
                    </article>

                    <article class="col">
                        <h6>Column 5</h6>
                        <p>Vivamus nunc tortor, consequat in porta nec, suscipit lacinia dui. Nulla viverra bibendum nisi, 
                            sit amet ullamcorper nibh euismod at. </p>
                    </article>

                    <article class="col">
                        <h6>Column 6</h6>
                        <p>Vivamus nunc tortor, consequat in porta nec, suscipit lacinia dui. Nulla viverra bibendum nisi, 
                            sit amet ullamcorper nibh euismod at. </p>
                    </article>
                
                </section><!--end .cols cols6-->

            </div><!--end #grid-example-->


                   <div id="download-modal" class="reveal-modal xlarge">
                        <section class="vis-break brdr cntrtxt">

                            <h3 class="topspc btmspc">Free for personal &amp; commercial use as long as <a href="humans.txt" target="_blank"><mark>humans.txt</mark></a><br />remains unchanged &amp; in the index head.</h3>

                            <a href="http://framework.gregbabula.info/assets/g5framework.zip" class="button orange tooltip btmspc" title="Download Zip"><img src="images/save_64.png" width="64" height="64" alt="Download Zip" /></a>

                        </section><!--end .cols-->

                        <a class="close-reveal-modal">&#215;</a>
                    </div><!--end #download-modal-->


                   <div id="details-modal" class="reveal-modal large">
                        <section class="vis-break brdr">

                        <h1 class="cntrtxt">G5 Framework Details</h1>

                        <h6 class="topspc-dbl">Search Engine Optimization</h6>
                            <ul>
                                <li>robots.txt / robots meta for the search engines</li>
                                <li>canonical tag + 301 via htaccess to ensure all content is indexed under one domain</li>
                                <li>hidden CSS class - the SEO friendly way (negative margin instead of display none)</li>
                                <li>GZip for faster page load (speed affects rankings)</li>
                            </ul>

                        <h6 class="topspc">CSS</h6>
                            <ul>
                                <li>Prevent CSS caching</li>
                                <li>Cross browser border radius, box shadow, and opacity classes</li>
                                <li>Shorthand CSS / some basic reusable classes</li>
                                <li>Sticky footer (footer always stays on bottom of page - read: section.content must have a bottom padding equal or greater than the height of the footer)</li>
                            </ul>

                        <h6 class="topspc">CSS // Via Reset</h6>
                            <ul>
                                <li>Eric Meyer's Reset Reloaded + HTML5 Baseline</li>
                                <li>Font normalisation</li>
                                <li>Webkit font smoothing</li>
                                <li>Force scrollbar</li>
                                <li>Formatting quoted code</li>
                                <li>Aligning Labels</li>
                                <li>Clickable inputs</li>
                                <li>Screenreader access</li>
                                <li>IE7 image resizing</li>
                                <li>Print styles</li>
                            </ul>
                            
                        <h6 class="topspc">X(HTML) / Content</h6>
                            <ul>
                                <li>Clean code structure</li>
                                <li>940px total content width</li>
                                <li>Initial design based on a F Layout</li>
                                <li>Remove image toolbar from old versions of IE</li>
                                <li>Always force latest IE rendering engine (even in intranet) &amp; Chrome Frame</li>
                            </ul>

                        <h6 class="topspc">jQuery</h6>
                            <ul>
                                <li>jQuery 1.5.2 with fall-back (1.4.4 also included if needed)</li>
                                <li>Smooth scroll to top</li>
                                <li>HTML5 placeholder fallback</li>
                                <li>Navigation - remove margin from last item</li>
                                <li>Remove bottom padding from last paragraph</li>
                            </ul>
                            
                        <h6 class="topspc">PHP</h6>
                            <ul>
                                <li>Add active class to navigation item based on page</li>
                                <li>GZip page (optional alternative to htaccess)</li>
                            </ul>

                        <h6 class="topspc">Packaged With</h6>
                            <ul>
                                <li><a href="http://www.modernizr.com" target="_blank">Modernizr 1.6</a></li>
                                <li><a href="http://www.css3pie.com" target="_blank">CSS3 PIE</a> (to support cross browser CSS3 effects)</li>
                                <li><a href="http://webdesignerwall.com/tutorials/css3-gradient-buttons" target="_blank">CSS3 Buttons</a> (awesome reusable buttons)</li>
                                <li><a href="http://cssglobe.com/post/9849/easy-percentage-grid-system-with-html5" target="_blank">Easy Grid</a> (2-6 columns)</li>
                                <li>IE6 PNG Fix (if you need IE6 compatibility)</li>
                                <li><a href="http://ie6update.com" target="_blank">IE6 Update</a> (if you want to show a update message to IE6 users)</li>
                                <li><a href="https://github.com/jaz303/tipsy" target="_blank">Tipsy Tool Tips</a> (Facebook like tool-tips)</li>
                                <li><a href="http://www.zurb.com/playground/reveal-modal-plugin" target="_blank">Reveal Modals</a> (great simple modals)</li>
                                <li><a href="http://www.zurb.com/playground/orbit-jquery-image-slider" target="_blank">Orbit Image Slider</a> (awesome image slider with 3 different animations, html captions, bullets, possibility for thumbnail bullets, etc...)</li>
                            </ul>

                        </section><!--end .cols-->

                        <a class="close-reveal-modal">&#215;</a>
                    </div><!--end #details-modal-->


        </section><!--end section.content-->

        <footer id="bottom">
            
            <nav>
                <ul>
                    <li><a href="#top" class="button gray return-top up-arw">Return To Top</a></li>
                    <li><a href="http://gregbabula.info/framework.php" class="button rosy rgt-arw">Discussion &amp; Support</a></li>
                    <li><a href="#" class="button orange dwn-arw" data-reveal-id="download-modal">Download</a></li>
                </ul>
            </nav>

            <p><a href="http://framework.gregbabula.info"><strong>G5 Framework</strong></a> &#47;&#47; Copyright &copy; <?php echo date("Y"); ?> <a href="http://gregbabula.info">Greg Babula</a></p>

        </footer><!--end #bottom-->

    </div><!--end #wrapper-->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script><!--Load jQuery-->
<script>!window.jQuery && document.write(unescape('%3Cscript src="js/libs/jquery-1.5.2.min.js"%3E%3C/script%3E'))</script>
<script src="js/script.js"></script>
<script src="js/jquery.tipsy.js"></script>
<script src="js/jquery.reveal.js"></script>
<script src="js/jquery.orbit.min.js"></script>

<script>
   $(window).load(function() {
            $('#featured').orbit({
                 animation: 'vertical-slide',           // fade, horizontal-slide, vertical-slide, horizontal-push
                 animationSpeed: 1000,                
                 timer: true,            
                 advanceSpeed: 4000,                    // if timer is enabled, time between transitions 
                 pauseOnHover: false,                   // if you hover pauses the slider
                 startClockOnMouseOut: false,           // if clock should start on MouseOut
                 startClockOnMouseOutAfter: 1000,       // how long after MouseOut should the timer start again
                 directionalNav: false,                  // manual advancing directional navs
                 captions: true,                        // do you want captions?
                 captionAnimation: 'fade',               // fade, slideOpen, none
                 captionAnimationSpeed: 800,            // if so how quickly should they animate in
                 bullets: false,                        // true or false to activate the bullet navigation
                 bulletThumbs: false,                   // thumbnails for the bullets
                 bulletThumbLocation: '',               // location from this file where thumbs will be
                 afterSlideChange: function(){}         // empty function 
            });
     });
</script>

<!--[if lt IE 7 ]>
<script src="js/libs/dd_belatedpng.js"></script>
<script> DD_belatedPNG.fix('img, .png_bg');</script>
<![endif]-->

<!--[if IE 6]>
    <script type="text/javascript"> 
        /*Load jQuery if not already loaded*/ if(typeof jQuery == 'undefined'){ document.write("<script type=\"text/javascript\"   src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js\"></"+"script>"); var __noconflict = true; } 
            var IE6UPDATE_OPTIONS = {
                icons_path: "js/ie6update/images/"
            }
     </script>
     <script type="text/javascript" src="js/ie6update/ie6update.js"></script>
<![endif]-->

<!--Google Analytics-->
<script>
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-11669243-11']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
<!--Google Analytics-->

</body>
</html>