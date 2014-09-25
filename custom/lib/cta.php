<?php
/**
 *  WordPress Thesis theme admin customizations
 *
 *  @author Michael Cannon <mc@aihr.us>
 */

// add_filter( 'the_content', 'custom_call_to_action', 29 );


function custom_call_to_action( $content ) {
	if ( ! is_single() && ! is_page() && ! is_feed() )
		return $content;

	$cta						= <<<EOD
<hr />
<p><strong><a title="Lifestylus - healthy, content, financially independent" href="http://lifestyl.us/">Lifestylus</a></strong> is about <a title="About Peichi Liu" href="http://lifestyl.us/about-peichi/">Peichi</a> and <a title="About Michael Cannon" href="http://lifestyl.us/about-michael/">Michael</a> sharing their journey to getting healthier, being more content and wisely achieving financial independence.</p>
<p>Want to learn more, meet or write a guest post? <a title="Contact Lifestylus" href="http://lifestyl.us/contact-lifestylus/#EmailUs">Contact us now</a>.</p>
<ul class="cta">
<li><a class="btn orangeBtn" title="Start your own business with Vemma" href="http://www.vemmaeurope.com/signup.html?type=member&amp;cou=GB&amp;lang=en&amp;referrer=165088406&amp;source=lifestyl.us">Start A Business!</a></li>
<li><a class="btn greenBtn buyNowBtn" title="Try Vemma, Verve! & Thirst" href="http://www.vemmaeurope.com/signup.html?type=customer&amp;cou=GB&amp;lang=en&amp;referrer=165088406&amp;source=lifestyl.us">Try Out Vemma</a></li>
</ul>
EOD;

	return $content . $cta;
}
?>
