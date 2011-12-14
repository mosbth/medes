<?php
/**
 * Setting up definitions for Medes.
 *
 * @package MedesCore
 */

/**
 * Enable auto-load of class declarations.
 */
function autoload($aClassName) {
	$file1 = MEDES_INSTALL_PATH . "/src/{$aClassName}/{$aClassName}.php";
	$file2 = MEDES_INSTALL_PATH . "/site/src/{$aClassName}/{$aClassName}.php";
	if(is_file($file1)) {
		require_once($file1);
	} elseif(is_file($file2)) {
		require_once($file2);
	}
}
spl_autoload_register('autoload');

/**
 * Translation.
 */
function t($key, $arg=null) {
	$t = $key;
	if(isset($arg)) {
		foreach($arg as $key => $val) {
			$t = preg_replace('/' . $key . '/', $val, $t);		
		}
	}
	return $t;
}

/**
 * Sanitizing text to be able to display it in a html-page.
 * @param string text The text to be sanitized.
 * @returns string The sanitized html.
 */
function sanitizeHTML($text) {	
	return htmlspecialchars($text, ENT_NOQUOTES, 'UTF-8', false);
}

/**
 * BBCode formatting converting to HTML.
 * @param string text The text to be converted.
 */
function bbcode2html($text) {
	function phpsyntax($text) {
		return "<blockquote class='code'>".highlight_string(str_replace('#DOLLAR#', '$', trim($text)), true).'</blockquote>';
	};
	function code($text) {
		return "<blockquote class='code'>".nl2br(sanitizeHTML(trim($text)), true).'</blockquote>';
	};
	return preg_replace(
		array(
			'/\\[url[\\:\\=]((\\"([\\W]*javascript\:[^\\"]*)?([^\\"]*)\\")|'.
					'(([\\W]*javascript\:[^\\]]*)?([^\\]]*)))\\]/ie', '/\\[\\/url\\]/i',
			'/\\[b\\]/i', '/\\[\/b\\]/i',
			'/\\[i\\]/i', '/\\[\/i\\]/i',
			'/\\[quote\\]/i', '/\\[\/quote\\]/i',
			'/\[code\](.*?)\[\/code\]/ies',
			'/\[php\](.*?)\[\/php\]/ies',
			'/\[youtube\](.*?)\[\/youtube\]/is',
		),
		array(
			'\'<a href="\'.(\'$4\'?\'$4\':\'$7\').\'">\'', '</a>',
			'<b>', '</b>',
			'<i>', '</i>',
			"<blockquote class='quote'>", '</blockquote>',
			'code("\\1")',
			'phpsyntax("\\1")',
			'<object width="425" height="350">
			 <param name="movie" value="http://www.youtube.com/v/$1"></param>
			 <param name="wmode" value="transparent"></param>
			 <embed src="http://www.youtube.com/v/$1" type="application/x-shockwave-flash" wmode="transparent" width="425" height="350"></embed>
			 </object>
			',
		),
		$text
		//str_replace('$', '#DOLLAR#', $text)
	);
}
