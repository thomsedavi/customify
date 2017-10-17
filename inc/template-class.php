<?php

if ( ! function_exists( '_beacon_site_content_class' ) ) :
	/**
	 * Display the classes for the site content element.
	 *
	 * @since 0.0.1
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 */
	function _beacon_site_content_class( $class = '' ) {
		// Separates classes with a single space, collates classes for body element
		echo 'class="' . join( ' ', _beacon_get_site_content_class( $class ) ) . '"';
	}
endif;

if ( ! function_exists( '_beacon_get_site_content_class' ) ) :
	/**
	 * Retrieve the classes for the site content element as an array.
	 *
	 * @since 0.0.1
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 * @return array Array of classes.
	 */
	function _beacon_get_site_content_class( $class = '' ) {

		$classes = array();

		if ( ! empty( $class ) ) {
			if ( !is_array( $class ) )
				$class = preg_split( '#\s+#', $class );
			$classes = array_merge( $classes, $class );
		} else {
			// Ensure that we always coerce class to being an array.
			$class = array();
		}

		$classes = array_map( 'esc_attr', $classes );
		$classes = apply_filters( '_beacon_site_content_class', $classes, $class );

		return array_unique( $classes );
	}
endif;

if ( ! function_exists( '_beacon_sidebar_primary_class' ) ) :
	/**
	 * Display the classes for the primary sidebar element.
	 *
	 * @since 0.0.1
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 */
	function _beacon_sidebar_primary_class( $class = '' ) {
		// Separates classes with a single space, collates classes for body element
		echo 'class="' . join( ' ', _beacon_get_sidebar_primary_class( $class ) ) . '"';
	}
endif;

if ( ! function_exists( '_beacon_get_sidebar_primary_class' ) ) :
	/**
	 * Retrieve the classes for the primary sidebar element as an array.
	 *
	 * @since 0.0.1
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 * @return array Array of classes.
	 */
	function _beacon_get_sidebar_primary_class( $class = '' ) {

		$classes = array();

		if ( ! empty( $class ) ) {
			if ( !is_array( $class ) )
				$class = preg_split( '#\s+#', $class );
			$classes = array_merge( $classes, $class );
		} else {
			// Ensure that we always coerce class to being an array.
			$class = array();
		}

		$classes = array_map( 'esc_attr', $classes );
		$classes = apply_filters( '_beacon_sidebar_primary_class', $classes, $class );

		return array_unique( $classes );
	}
endif;

if ( ! function_exists( '_beacon_sidebar_secondary_class' ) ) :
	/**
	 * Display the classes for the secondary sidebar element.
	 *
	 * @since 0.0.1
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 */
	function _beacon_sidebar_secondary_class( $class = '' ) {
		// Separates classes with a single space, collates classes for body element
		echo 'class="' . join( ' ', _beacon_get_sidebar_secondary_class( $class ) ) . '"';
	}
endif;

if ( ! function_exists( '_beacon_get_sidebar_secondary_class' ) ) :
	/**
	 * Retrieve the classes for the secondary sidebar element as an array.
	 *
	 * @since 0.0.1
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 * @return array Array of classes.
	 */
	function _beacon_get_sidebar_secondary_class( $class = '' ) {

		$classes = array();

		if ( ! empty( $class ) ) {
			if ( !is_array( $class ) )
				$class = preg_split( '#\s+#', $class );
			$classes = array_merge( $classes, $class );
		} else {
			// Ensure that we always coerce class to being an array.
			$class = array();
		}

		$classes = array_map( 'esc_attr', $classes );
		$classes = apply_filters( '_beacon_sidebar_secondary_class', $classes, $class );

		return array_unique( $classes );
	}
endif;

if ( ! function_exists( '_beacon_main_content_class' ) ) :
	/**
	 * Display the classes for the main content element.
	 *
	 * @since 0.0.1
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 */
	function _beacon_main_content_class( $class = '' ) {
		// Separates classes with a single space, collates classes for body element
		echo 'class="' . join( ' ', _beacon_get_main_content_class( $class ) ) . '"';
	}
endif;

if ( ! function_exists( '_beacon_get_main_content_class' ) ) :
	/**
	 * Retrieve the classes for the main content element as an array.
	 *
	 * @since 0.0.1
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 * @return array Array of classes.
	 */
	function _beacon_get_main_content_class( $class = '' ) {

		$classes = array();

		if ( ! empty( $class ) ) {
			if ( !is_array( $class ) )
				$class = preg_split( '#\s+#', $class );
			$classes = array_merge( $classes, $class );
		} else {
			// Ensure that we always coerce class to being an array.
			$class = array();
		}

		$classes = array_map( 'esc_attr', $classes );
		$classes = apply_filters( '_beacon_main_content_class', $classes, $class );

		return array_unique( $classes );
	}
endif;
