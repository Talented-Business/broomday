<?php

/**
 * ActionScheduler DateTime class.
 *
 * This is a custom extension to DateTime that
 */
class ActionScheduler_DateTime extends DateTime {
	protected $utc_offset = 0;

	/**
	 * Output an ISO 8601 date string in local (WordPress) timezone.
	 *
	 * @since  3.0.0
	 * @return string
	 */
	public function __toString() {
		return $this->format( DATE_ATOM );
	}

	/**
	 * Get the unix timestamp of the current object.
	 *
	 * Missing in PHP 5.2 so just here so it can be supported consistently.
	 *
	 * @return int
	 */
	public function getTimestamp() {
		
		return method_exists( 'DateTime', 'getTimestamp' ) ? parent::getTimestamp() : $this->format( 'U' );
	}
}
