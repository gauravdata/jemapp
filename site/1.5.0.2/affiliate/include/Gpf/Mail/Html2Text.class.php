<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Viktor Zeman
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Html2Text.class.php 25323 2009-09-04 12:16:24Z vzeman $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_Mail_Html2Text extends Gpf_Object {

    /**
     *  function that does conversion from html to text.
     *	@param string $badStr Html string to be converted to text
     *  @return string
     */
    public static function convert($badStr)
    {
	    //remove PHP if it exists
	    while( substr_count( $badStr, '<'.'?' ) && substr_count( $badStr, '?'.'>' ) && strpos( $badStr, '?'.'>', strpos( $badStr, '<'.'?' ) ) > strpos( $badStr, '<'.'?' ) ) {
	        $badStr = substr( $badStr, 0, strpos( $badStr, '<'.'?' ) ) . substr( $badStr, strpos( $badStr, '?'.'>', strpos( $badStr, '<'.'?' ) ) + 2 ); }
	    //remove comments
	    while( substr_count( $badStr, '<!--' ) && substr_count( $badStr, '-->' ) && strpos( $badStr, '-->', strpos( $badStr, '<!--' ) ) > strpos( $badStr, '<!--' ) ) {
	        $badStr = substr( $badStr, 0, strpos( $badStr, '<!--' ) ) . substr( $badStr, strpos( $badStr, '-->', strpos( $badStr, '<!--' ) ) + 3 ); }
	    //now make sure all HTML tags are correctly written (> not in between quotes)


        $goodStr = $badStr;

	    //now that the page is valid (I hope) for strip_tags, strip all unwanted tags
	    $goodStr = strip_tags( $goodStr, '<title><hr><h1><h2><h3><h4><h5><h6><div><p><br><pre><sup><ul><ol><dl><dt><table><caption><tr><li><dd><th><td><a><area><img><form><input><textarea><button><select><option>' );
	    //strip extra whitespace except between <pre> and <textarea> tags
	    $badStr = preg_split( "/<\/?pre[^>]*>/i", $goodStr );
	    for( $x = 0; isset($badStr[$x]) && is_string( $badStr[$x] ); $x++ ) {
	        if( $x % 2 ) { $badStr[$x] = '<pre>'.$badStr[$x].'</pre>'; } else {
	            $goodStr = preg_split( "/<\/?textarea[^>]*>/i", $badStr[$x] );
	            for( $z = 0; isset($goodStr[$z]) && is_string( $goodStr[$z] ); $z++ ) {
	                if( $z % 2 ) { $goodStr[$z] = '<textarea>'.$goodStr[$z].'</textarea>'; } else {
	                    $goodStr[$z] = preg_replace( "/\s+/", ' ', $goodStr[$z] );
	            } }
	            $badStr[$x] = implode('',$goodStr);
	    } }
	    $goodStr = implode('',$badStr);

		$search = array(
		        "/\r/",                                  // Non-legal carriage return
		        "/[\n\t]+/",                             // Newlines and tabs
		        '/<br[^>]*>/i',                          // <br>
		        '/&nbsp;/i',
		        '/&quot;/i',
		        '/&gt;/i',
		        '/&lt;/i',
		        '/&amp;/i',
		        '/&copy;/i',
		        '/&trade;/i',
		        '/&#8220;/',
		        '/&#8221;/',
		        '/&#8211;/',
		        '/&#8217;/',
		        '/&#38;/',
		        '/&#169;/',
		        '/&#8482;/',
		        '/&#151;/',
		        '/&#147;/',
		        '/&#148;/',
		        '/&#149;/',
		        '/&reg;/i',
		        '/&bull;/i',
		        '/&[&;]+;/i'
		    );

			$replace = array(
		        '',                                     // Non-legal carriage return
		        ' ',                                    // Newlines and tabs
		        "\n",                                   // <br>
		        ' ',
		        '"',
		        '>',
		        '<',
		        '&',
		        '(c)',
		        '(tm)',
		        '"',
		        '"',
		        '-',
		        "'",
		        '&',
		        '(c)',
		        '(tm)',
		        '--',
		        '"',
		        '"',
		        '*',
		        '(R)',
		        '*',
		        ''
		    );


	    $goodStr = preg_replace( $search, $replace, $goodStr );

	    //remove all options from select inputs
	    $goodStr = preg_replace( "/<option[^>]*>[^<]*/i", '', $goodStr );
	    //replace all tags with their text equivalents
	    $goodStr = preg_replace( "/<(\/title|hr)[^>]*>/i", "\n          --------------------\n", $goodStr );
	    $goodStr = preg_replace( "/<(h|div|p)[^>]*>/i", "\n", $goodStr );
	    $goodStr = preg_replace( "/<sup[^>]*>/i", '^', $goodStr );
	    $goodStr = preg_replace( "/<(ul|ol|dl|dt|table|caption|\/textarea|tr[^>]*>\s*<(td|th))[^>]*>/i", "\n", $goodStr );
	    $goodStr = preg_replace( "/<li[^>]*>/i", "\n· ", $goodStr );
	    $goodStr = preg_replace( "/<dd[^>]*>/i", "\n\t", $goodStr );
	    $goodStr = preg_replace( "/<(th|td)[^>]*>/i", "\t", $goodStr );
	    $goodStr = preg_replace('/<br[^>]*>/i', "\n", $goodStr);
	    $goodStr = preg_replace( "/<a[^>]* href=(\"((?!\"|javascript:)[^\"]*)(\")|'((?!'|javascript:)[^']*)(')|((?!'|\"|>|javascript:)[^\"'> ]*))[^>]*>/i", "[LINK: $2$4$6] ", $goodStr );
	    $goodStr = preg_replace( "/<img[^>]* alt=(\"([^\"]+)\"|'([^']+)'|([^\"'> ]+))[^>]*>/i", "[IMAGE: $2$3$4] ", $goodStr );
	    $goodStr = preg_replace( "/<form[^>]* action=(\"([^\"]+)\"|'([^']+)'|([^\"'> ]+))[^>]*>/i", "\n[FORM: $2$3$4] ", $goodStr );
	    $goodStr = preg_replace( "/<(input|textarea|button|select)[^>]*>/i", "[INPUT] ", $goodStr );
	    //strip all remaining tags (mostly closing tags)
	    $goodStr = strip_tags( $goodStr );
	    //convert HTML entities
	    $goodStr = strtr( $goodStr, array_flip( get_html_translation_table( HTML_ENTITIES ) ) );
	    preg_replace( "/&#(\d+);/me", "chr('$1')", $goodStr );
	    //wordwrap
	    //$goodStr = wordwrap( $goodStr );
	    //make sure there are no more than 3 linebreaks in a row and trim whitespace
	    return preg_replace( "/^\n*|\n*$/", '', preg_replace( "/[ \t]+(\n|$)/", "$1", preg_replace( "/\n(\s*\n){2}/", "\n\n\n", preg_replace( "/\r\n?|\f/", "\n", $goodStr ) ) ) );

	    //this line was causing some japanese mails to be corrupted !!! in case it has to be removed from source, it should be done in any better way.
        //return preg_replace( "/^\n*|\n*$/", '', preg_replace( "/[ \t]+(\n|$)/", "$1", preg_replace( "/\n(\s*\n){2}/", "\n\n\n", preg_replace( "/\r\n?|\f/", "\n", str_replace( chr(160), ' ', $goodStr ) ) ) ) );
    }
}
?>
