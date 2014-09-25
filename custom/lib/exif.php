<?php
/**
 *  EXIF script handler for PHP_JPEG_Metadata_Toolkit_1.11/EXIF.php
 *
 *  @ref http://www.kristarella.com/wp-content/uploads/exifphp.txt
 *
 *  @author Michael Cannon <mc@aihr.us>
 */

// === POST EXIF === //
function geo_frac2dec($str) {
	@list( $n, $d ) = explode( '/', $str );
	if ( !empty($d) )
		return $n / $d;
	return $str;
}


function geo_pretty_fracs2dec($fracs) {
	return	geo_frac2dec($fracs[0]) . '&deg; ' .
			geo_frac2dec($fracs[1]) . '&prime; ' .
			geo_frac2dec($fracs[2]) . '&Prime; ';
}


function geo_single_fracs2dec($fracs) {
	return	geo_frac2dec($fracs[0]) +
			geo_frac2dec($fracs[1]) / 60 +
			geo_frac2dec($fracs[2]) / 3600;
}


function display_exif( $image_id = false ) {
	if ( ! $image_id ) {
 		global $post;

 		$image_id					= $post->ID;
	}

	$image_id					= $post->ID;

	if ( ! is_attachment() || ! wp_attachment_is_image( $image_id ) )
		return;

	$image_meta					= wp_get_attachment_metadata( $image_id );

	if ( ! isset( $image_meta['image_meta'] ) )
		return;

	$image_meta					= $image_meta['image_meta'];
	$image_meta					= display_exif_remove_duplicates( $image_meta );

	echo '<h3>' . __( 'Attachment Details' , 'custom') . '</h3>';

	echo "<ul class='exif'>";

	foreach ( $image_meta as $key => $value ) {
		if ( '' == $value )
			continue;

		if ( is_object( $value ) || is_array( $value ) )
			$value				= object_to_unordered_list( $value );

		$value					= trim( $value );

		if ( 'iso' == $key )
			$key				= 'ISO';

		if ( 'aperture' == $key )
			$value				= 'f/' . $value;

		if ( 'ExifImageLength' == $key || 'ExifImageWidth' == $key )
			$value				.= ' ' . __( 'pixels' , 'custom');

		if ( 'FileSize' == $key )
			$value				= number_format( $value );

		if ( 'focal_length' == $key )
			$value				.= 'mm';

		if ( 'shutter_speed' == $key )
			$value				.= ' ' . __( 'seconds' , 'custom');

		if ( 'Version' == substr( $key, -7 )
			&& is_numeric( $value )
			&& 4 == strlen( $value )
		) {
			$value				= preg_replace( '#(\d\d)(\d\d)#', '\1.\2', $value );
			$value				= preg_replace( '#(^0|0$)#', '', $value );
		}

		if ( is_numeric( $value ) && 10 == strlen( $value ) )
			$value				= date( 'l F j, Y g:i:s a' , $value );

		$key					= cbMkReadableStr($key);
		// use parenthesis value
		$value					= preg_replace( '#-?\d+/\d+ \(([^\)]+)\)#', '\1', $value );

		echo '<li>' . $key . ': ' . $value . '</li>';
	}

	echo "</ul>";
}


function display_exif_remove_duplicates( $image_meta ) {
	if ( isset( $image_meta['Aperture F Number'] ) && isset( $image_meta['aperture'] ) ) {
		unset( $image_meta['aperture'] );
		unset( $image_meta['COMPUTED'] );
		unset( $image_meta['FNumber'] );
	}

	if ( isset( $image_meta['APEX Exposure Bias Value (Exposure Compensation)'] ) && isset( $image_meta['ExposureBiasValue'] ) )
		unset( $image_meta['ExposureBiasValue'] );

	if ( isset( $image_meta['APEX Maximum Aperture Value'] ) && isset( $image_meta['MaxApertureValue'] ) )
		unset( $image_meta['MaxApertureValue'] );

	if ( isset( $image_meta['Colour Space'] ) && isset( $image_meta['ColorSpace'] ) )
		unset( $image_meta['ColorSpace'] );

	if ( isset( $image_meta['Compressed Bits Per Pixel'] ) && isset( $image_meta['CompressedBitsPerPixel'] ) )
		unset( $image_meta['CompressedBitsPerPixel'] );

	if ( isset( $image_meta['Date and Time'] ) && isset( $image_meta['DateTime'] ) )
		unset( $image_meta['DateTime'] );

	if ( isset( $image_meta['Date and Time of Original'] ) && isset( $image_meta['DateTimeOriginal'] ) ) {
		unset( $image_meta['DateTimeOriginal'] );
		unset( $image_meta['created_timestamp'] );
	}

	if ( isset( $image_meta['Date and Time when Digitized'] ) && isset( $image_meta['DateTimeDigitized'] ) )
		unset( $image_meta['DateTimeDigitized'] );

	if ( isset( $image_meta['Digital Zoom Ratio'] ) && isset( $image_meta['DigitalZoomRatio'] ) )
		unset( $image_meta['DigitalZoomRatio'] );

	if ( isset( $image_meta['Equivalent Focal Length In 35mm Film'] ) && isset( $image_meta['FocalLengthIn35mmFilm'] ) )
		unset( $image_meta['FocalLengthIn35mmFilm'] );

	if ( isset( $image_meta['Exif Version'] ) && isset( $image_meta['ExifVersion'] ) )
		unset( $image_meta['ExifVersion'] );

	if ( isset( $image_meta['Exposure Mode'] ) && isset( $image_meta['ExposureMode'] ) )
		unset( $image_meta['ExposureMode'] );

	if ( isset( $image_meta['Exposure Program'] ) && isset( $image_meta['ExposureProgram'] ) )
		unset( $image_meta['ExposureProgram'] );

	if ( isset( $image_meta['Exposure Time'] ) && isset( $image_meta['shutter_speed'] ) ) {
		unset( $image_meta['ExposureTime'] );
		unset( $image_meta['shutter_speed'] );
	}

	if ( isset( $image_meta['MimeType'] ) && isset( $image_meta['FileType'] ) )
		unset( $image_meta['FileType'] );

	if ( isset( $image_meta['FlashPix Version'] ) && isset( $image_meta['FlashPixVersion'] ) )
		unset( $image_meta['FlashPixVersion'] );

	if ( isset( $image_meta['FocalLength'] ) && isset( $image_meta['focal_length'] ) )
		unset( $image_meta['focal_length'] );

	if ( isset( $image_meta['GPS_IFD_Pointer'] ) && isset( $image_meta['GPSLatitude'] ) ) {
		$latitude				= $image_meta['GPSLatitude'];
		$lat_ref				= $image_meta['GPSLatitudeRef'];

		$longitude				= $image_meta['GPSLongitude'];
		$lng_ref				= $image_meta['GPSLongitudeRef'];

		$image_meta['Latitude']		= geo_pretty_fracs2dec($latitude). $lat_ref;
		$image_meta['Longitude']	= geo_pretty_fracs2dec($longitude) . $lng_ref;

		unset( $image_meta['GPSLatitude'] );
		unset( $image_meta['GPSLatitudeRef'] );
		unset( $image_meta['GPSLongitude'] );
		unset( $image_meta['GPSLongitudeRef'] );
		unset( $image_meta['East or West Longitude'] );
		unset( $image_meta['North or South Latitude'] );
	}

	if ( isset( $image_meta['ISO Speed Ratings'] ) && isset( $image_meta['iso'] ) ) {
		unset( $image_meta['iso'] );
		unset( $image_meta['ISOSpeedRatings'] );
	}

	if ( isset( $image_meta['Light Source'] ) && isset( $image_meta['LightSource'] ) )
		unset( $image_meta['LightSource'] );

	if ( isset( $image_meta['Metering Mode'] ) && isset( $image_meta['MeteringMode'] ) )
		unset( $image_meta['MeteringMode'] );

	if ( isset( $image_meta['Make (Manufacturer)'] ) && isset( $image_meta['Make'] ) )
		unset( $image_meta['Make'] );

	if ( isset( $image_meta['Resolution Unit'] ) && isset( $image_meta['ResolutionUnit'] ) )
		unset( $image_meta['ResolutionUnit'] );

	if ( isset( $image_meta['Scene Capture Type'] ) && isset( $image_meta['SceneCaptureType'] ) )
		unset( $image_meta['SceneCaptureType'] );

	if ( isset( $image_meta['Software or Firmware'] ) && isset( $image_meta['Software'] ) )
		unset( $image_meta['Software'] );

	if ( isset( $image_meta['Special Processing (Custom Rendered)'] ) && isset( $image_meta['CustomRendered'] ) )
		unset( $image_meta['CustomRendered'] );

	if ( isset( $image_meta['Subject Distance Range'] ) && isset( $image_meta['SubjectDistanceRange'] ) )
		unset( $image_meta['SubjectDistanceRange'] );

	if ( isset( $image_meta['White Balance'] ) && isset( $image_meta['WhiteBalance'] ) )
		unset( $image_meta['WhiteBalance'] );

	if ( isset( $image_meta['X Resolution'] ) && isset( $image_meta['XResolution'] ) )
		unset( $image_meta['XResolution'] );

	if ( isset( $image_meta['Y Resolution'] ) && isset( $image_meta['YResolution'] ) )
		unset( $image_meta['YResolution'] );

	if ( isset( $image_meta['shutter_speed'] ) && $image_meta['shutter_speed'] ) {
		if ( ( 1 / $image_meta['shutter_speed'] ) > 1 ) {
			if ( ( number_format( ( 1 / $image_meta['shutter_speed'] ), 1 ) ) == 1.3
				|| number_format( ( 1 / $image_meta['shutter_speed'] ), 1 ) == 1.5
				|| number_format( ( 1 / $image_meta['shutter_speed'] ), 1 ) == 1.6
				|| number_format( ( 1 / $image_meta['shutter_speed'] ), 1 ) == 2.5) {
					$image_meta['shutter_speed']	= number_format( (1 / $image_meta['shutter_speed'] ), 1, '.', '');
				} else{
					$image_meta['shutter_speed']	= number_format( (1 / $image_meta['shutter_speed'] ), 0, '.', '');
				}
		} else{
			$image_meta['shutter_speed']	= $image_meta['shutter_speed'];
		}
	}

	return $image_meta;
}


// Call the EXIF data in your theme with display_exif()

// try to save all exif data file available than normal
// @ref http://wordpress.org/extend/plugins/thesography/
// Thank you kristarella for the kickstarter ideas
// from wp-admin/includes/image.php
// apply_filters( 'wp_read_image_metadata', $meta, $file, $sourceImageType );
// to use, do...
// add_filter('wp_read_image_metadata', 'read_all_image_metadata', '', 3);
function read_all_image_metadata( $meta, $file, $sourceImageType ) {
	if ( ! is_callable('exif_read_data') || ! in_array( $sourceImageType, apply_filters('wp_read_image_metadata_types', array(IMAGETYPE_JPEG, IMAGETYPE_TIFF_II, IMAGETYPE_TIFF_MM) ) ) )
		return $meta;

	$exif						= exif_read_data( $file );
	$latitude					= false;
	$longitude					= false;
	$location					= false;
	$keywords					= false;

	if ( isset( $exif['GPSLatitude'] ) ) {
		$latitude				= $exif['GPSLatitude'];
		$lat					= geo_single_fracs2dec($latitude);
		$lat_ref				= $exif['GPSLatitudeRef'];
		$neg_lat				= ($lat_ref == 'S') ? '-' : '';

		$longitude				= $exif['GPSLongitude'];
		$lng					= geo_single_fracs2dec($longitude);
		$lng_ref				= $exif['GPSLongitudeRef'];
		$neg_lng				= ($lng_ref == 'W') ? '-' : '';

		$exif['location']		= '<a href="http://maps.google.com/maps?q=' . $neg_lat . number_format($lat,6) . '+' . $neg_lng . number_format($lng, 6) . '&z=11">' . geo_pretty_fracs2dec($latitude). $lat_ref . ' ' . geo_pretty_fracs2dec($longitude) . $lng_ref . '</a>';
	}

	// Pull IPTC data for title & keywords
	$image_info					= getimagesize( $file, $info );
	if ( isset( $info['APP13'] ) ) {
		$iptc					= iptcparse( $info['APP13'] );

		$title					= isset( $iptc['2#005'] ) ? $iptc['2#005'] : false;
		if ( $title )
			$exif[ 'title' ]	= $title[0];

		$keywords				= isset( $iptc['2#025'] ) ? $iptc['2#025'] : false;
		if ( $keywords ) {
			$keywords			= implode( ', ', $keywords );
			$exif[ 'keywords' ]	= trim( $keywords );
		}
	}

	if ( is_readable( $file ) ) {
		// some how $file gets overwritten when requiring EXIF.php
		$filename				= $file;
		require_once( 'PHP_JPEG_Metadata_Toolkit_1.11/EXIF.php' );

		$exif_jpeg				= get_EXIF_JPEG( $filename );
		$exif_jpeg				= $exif_jpeg[ 0 ];
		$exif_new				= array();

		if ( ! is_array( $exif_jpeg ) )
			$exif_jpeg			= array( $exif_jpeg );

		foreach ( $exif_jpeg as $key => $value ) {
			$exif_data			= process_exif_jpeg( $key, $value, $exif );
			if ( count( $exif_data ) ) {
				foreach ( $exif_data as $entry ) {
					$value		= $entry['value'];

					if ( ! is_array( $value ) ) {
						$key				= $entry['key'];
						$value				= trim( $value );
						$exif_new[ $key ]	= $value;
					} else {
						foreach ( $value as $entry2 ) {
							$key2				= $entry2[0]['key'];
							$value2				= $entry2[0]['value'];
							$value2				= trim( $value2 );
							$exif_new[ $key2 ]	= $value2;
						}
					}
				}
			}
		}

		// $exif_new key values will overwrite $exif
		$exif					= array_merge( $exif, $exif_new );
	}

	if ( isset( $exif['Make'] ) && isset( $exif['Model'] ) )
		$exif['camera']			= $exif['Make'] . ' ' . $exif['Model'];

	$meta					= array_merge( $meta, $exif );
	ksort( $meta );

	return $meta;
}


function process_exif_jpeg( $index, $contents, & $exif ) {
	$data						= array();

	if ( is_int( $index )
		&& isset( $contents['Tag Name'] )
		&& ! stristr( $contents['Tag Name'], 'Unknown' )
		&& isset( $contents['Text Value'] )
	) {
		$text_value				= $contents['Text Value'];
		$tag_name				= $contents['Tag Name'];

		$process_further		= array(
			'EXIF Image File Directory (IFD)',
			'GPS Info Image File Directory (IFD)'
		);

		if ( ! in_array( $tag_name, $process_further ) ) {
			$data[]				= array(
				'key'			=> $tag_name,
				'value'			=> $text_value
			);
		} else {
			foreach ( $contents['Data'][0] as $key => $value ) {
				$exif_data		= process_exif_jpeg( $key, $value, $exif );

				if ( count( $exif_data ) ) {
					$data[$tag_name]['value'][]	= $exif_data;
				}
			}
		}
	}

	return $data;
}
?>