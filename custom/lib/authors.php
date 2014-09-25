<?php
/**
 *  WordPress Thesis theme author customizations
 *
 *  @author Michael Cannon <mc@aihr.us>
 */


// copy and uncomment as needed to custom_functions.php
// add_action( 'thesis_hook_before_content', 'author_info' );
// add_filter( 'thesis_archive_intro','archive_intro_content_author' );
// add_filter( 'thesis_archive_intro_headline','archive_intro_headline_author' );

// add_filter( 'gettext', 'gettext_mbr' );
// add_filter( 'ngettext', 'gettext_mbr' );


function gettext_mbr( $translated ) {
	static $do_it;

	if ( is_null( $do_it ) ) {
		// why is this so dificult to figure what post_type we're currently on?
		$post_type = isset( $_GET['post_type'] ) ? esc_attr( $_GET['post_type'] ) : false;
		if ( ! $post_type ) {
			global $post;
			if ( ! is_null( $post ) )
				$post_type = get_the_ID() ? get_post_type( get_the_ID() ) : false;
		}

		if ( $post_type )
			$do_it     = in_array( $post_type, array( 'video', 'document' ) );
	}

	if ( ! $do_it )
		return $translated;

	// $producers					= __( 'Cataloguer', 'custom' );
	$producers					= 'Cataloguer';
	// $translated					= str_replace( 'Post Authors', $producers, $translated );
	// $translated					= str_replace( 'Authors', $producers, $translated );
	$translated					= str_replace( 'Author', $producers, $translated );

	return $translated;
}


function archive_intro_headline_author( $text ) {
	if ( ! is_author() )
		return $text;

	$by							= __( 'Articles by' , 'custom');

	return $by . ' ' . $text;
}


function archive_intro_content_author( $header ) {
	if ( ! is_author() )
		return $header;

	if ( get_query_var('author_name') ) {
		$curauth				= get_userdatabylogin(get_query_var('author_name'));
	} else {
		$curauth				= get_userdata(get_query_var('author'));
	}

	// I get bored reading the same thing over and over again, so why not mix it up
	$writtenArr					= array(
		__('written', 'custom'),
		__('authored', 'custom'),
		__('completed', 'custom'),
		__('composed', 'custom'),
		__('indited', 'custom'),
		__('inscribed', 'custom'),
		__('jotted down', 'custom'),
		__('penned', 'custom'),
		__('posted', 'custom'),
		__('published', 'custom')
	);
	// comment out the following line to not randomize the writtenArr
	shuffle( $writtenArr );

	$awesomeArr					= array(
		__('awesome', 'custom'),
		__('admirable', 'custom'),
		__('awe-inspiring', 'custom'),
		__('excellent', 'custom'),
		__('extremely good', 'custom'),
		__('extremely impressive', 'custom'),
		__('fabulous', 'custom'),
		__('great', 'custom'),
		__('impressive', 'custom'),
		__('terrific', 'custom'),
		__('wonderous', 'custom')
	);
	// comment out the following line to not randomize the awesomeArr
	shuffle( $awesomeArr );

	$bio						= '';
	$bio						.= '<div class="format_text">';
	$bio						.= '<p>';
	$bio						.= get_avatar( $curauth->ID , 120 );
	$bio						.= $curauth->first_name;
	$bio						.= ' ';
	$bio						.= __('has', 'custom');
	$bio						.= ' ';
	$bio						.= $writtenArr[0];
	$bio						.= ' ';
	$bio						.= number_format( get_the_author_posts() );
	$bio						.= ' ';
	$bio						.= $awesomeArr[0];
	$bio						.= ' ';
	$bio						.= __('articles at', 'custom');
	$bio						.= ' <a href="';
	$bio						.= __('http://typovagabond.com', 'custom');
	$bio						.= '">';
	$bio						.= __('TYPO3 Vagabond', 'custom');
	$bio						.= '</a>.';
	$bio						.= '<p>' . $curauth->description .'</p>';
	$bio						.= '</div>';
	$headerEnd					= '</div>';

	$content					= preg_replace( "#{$headerEnd}#", $bio . $headerEnd, $header );

	return $content;
}

?>
