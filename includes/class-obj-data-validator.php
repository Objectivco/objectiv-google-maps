<?php
defined( 'WPINC' ) || header( 'HTTP/1.1 403' ) & exit; // Prevent direct access

class Obj_Gmaps_DataValidator {
	private static $var_filters = null;
	private static $valid_url_protocols = array('http', 'https', 'ftp', 'ftps', 'mailto', 'news', 
		'irc', 'gopher', 'nntp', 'feed', 'telnet', 'mms', 'rtsp', 'svn');
	
	function __construct () {
		if( !isset(self::$var_filters) )
			self::$var_filters = filter_list();
	}
	
	private function filter_exists ($filter_name) {
		return !empty(self::$var_filters[$filter_name]);
	}
	
	public function validate_integer ($numeric_string) {
		return preg_match('/^(\d+)$/', $numeric_string);
	}
	
	public function validate_date ($date_string) {
		return preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date_string);
	}

	public function validate_time ($time_string) {
		return preg_match('/^(\d{2}):(\d{2})$/', $time_string);
	}
	
	public function validate_decimal ($numeric_string) {
		return preg_match('/^(\d+)(\.\d+)?$/', $numeric_string);
	}
	
	public function validate_file_ext ($filename, $file_exts) {
		if( !is_array($file_exts) )
			$file_exts = array($file_exts);
		
		list($name, $ext) = explode('.', $filename, 2);
		$ext = strtolower($ext);
		if( '' == $ext 
			|| !in_array($ext, $file_exts))
			return false;
		return true;
	}
	
	public function sanitize_filename ($filename) {
		return preg_replace('#[^a-zA-Z0-9.\-_]#', '', $filename);
	}
	
	public function sanitize_url ($url, $protocols=false) {
		$original_url = $url;
		
		if( '' == $url )
			return false;
		
		if( empty($protocols) 
			|| !is_array($protocols) )
			$protocols = self::$valid_url_protocols;
		
		//Remove invalid characters from url
		if( $this->filter_exists('url') ) {
			$url = filter_var($url, FILTER_SANITIZE_URL);
		} else {
			$url = preg_replace( '|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url );
			$url = $this->deep_replace( array('%0d', '%0a', '%0D', '%0A'), $url );
		}
		if( empty($url) )
			return false;
		
		//Fix common mistakes
		$url = str_replace(';//', '://', $url);
		
		//Append http:// if protocol is missing and http is allowed
		if( false === strpos($url, ':') ) {
			if( in_array('http', $protocols) )
				return 'http://' . $url;
			return false;
		}
		
		//Verify protocol is valid
		foreach( $protocols as $protocol ) {
			if( 0 == strcasecmp( $protocol, substr($url, 0, strlen($protocol)) ) ) 
				return $url;
		}
		
		return false;
	}
	
	public function validate_email ($email_string) {
		if( $this->filter_exists('validate_email') )
			return filter_var($email_string, FILTER_VALIDATE_EMAIL);
		
		//Emails must be at least 3 characters and @ must appear somewhere after the first character
		if( 3 > strlen($email_string)
			|| false === strpos( $email_string, '@', 1 ) )
			return false;
		
		list( $local, $domain ) = explode( '@', $email_string, 2 );
		
		//Check for invalid characters in local
		if( !preg_match( '/^[a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~\.-]+$/', $local ) )
			return false;
		
		//Check for valid domain period syntax
		if( preg_match( '/\.{2,}/', $domain )
			|| $domain != trim( $domain, " \t\n\r\0\x0B." ) )
			return false;
		
		$namespaces = explode( '.', $domain );
		
		//Domain must have at least 2 namespaces
		if( 2 > count( $namespaces ) )
			return false;
		
		foreach( $namespaces as $namespace ) {
			//Check for invalid characters in namespace
			if( $namespace !== trim( $namespace, " \t\n\r\0\x0B-" )
				|| !preg_match('/^[a-z0-9-]+$/i', $namespace) )
				return false;
		}
		
		return true;
	}
	
	private function deep_replace ($search, $subject) {
		$subject = (string) $subject;
	
		$count = 1;
		while ( $count ) {
			$subject = str_replace( $search, '', $subject, $count );
		}
	
		return $subject;
	}
}
	