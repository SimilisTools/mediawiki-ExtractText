<?php

class ExtExtractText {

	/**
	* The rendering object (skin)
	*/
	private $display=NULL;


	/**
	 * @param $parser Parser
	 * @return bool
	 */
	function clearState(&$parser) {
		$parser->pf_ifexist_breakdown = array();
		return true;
	}

	/**
	 * @param $parser Parser
	 * @param $frame PPFrame
	 * @param $args array
	 * @return string
	 */
	public static function extracttext( &$parser, $frame, $args ) {

		$parser->disableCache();
		
		$num = 50;
		$extra = "";
		
		if ( isset($args[1]) ) {
			$num = $frame->expand( $args[1]);
		}
		
		if ( isset($args[2]) ) {
			$extra = $frame->expand( $args[2]);
		}
		
		return isset( $args[0] ) ? self::dewikify( $frame->expand( $args[0] ), $num, $extra ) : '';

	}
	
	
	public static function extractpagetext ( &$parser, $frame, $args  ) {
		
		$parser->disableCache();
		
		$text = "";
		$num = 50;
		$extra = "";
		
		
		//Page
		if ( isset($args[0]) ) {
	    		
			$titleObject = Title::newFromText(trim(strip_tags($frame->expand($args[0]))));
			if ( $titleObject->exists() ) {
				$wikipage = WikiPage::factory( $titleObject );
				$text = $wikipage->getText();
			}
		}
		
		if ( isset($args[1]) ) {
			$num = $frame->expand( $args[1]);
		}
		
		if ( isset($args[2]) ) {
			$extra = $frame->expand( $args[2]);
		}
		
		return isset( $args[0] ) ? self::dewikify( $text, $num, $extra ) : '';
		
	}
	

	private static function dewikify($text, $num=50, $end="") {
		// first get rid of HTML tags
		$text = strip_tags($text);

		// remove large blocks (treat as tags)
		$text = preg_replace('/\{\{([^\}]+)?\}\}/', '', $text);

		$text = preg_replace("/(<![^>]+>)/", '', $text);
		$text = preg_replace('/\{\{\s?/', '', $text);
		$text = str_replace('}}', '', $text);

		$text = str_replace('<! />', '', $text);

		// more wiki formatting
		//$text = preg_replace("/'{2,6}/", '', $text);

		// Remove between ==
		$text = preg_replace("/\=.*\=/", '', $text);


		// drop page link text
		$text = preg_replace('/\[\[([^:\|\]]+)\|([^:\]]+)\]\]/', "$2", $text);


		// or keep it with preg_replace('/\[\[([^:\|\]]+)\|([^:\]]+)\]\]/', "$1 ($2)", $text);

		$text = preg_replace('/\(\[[^\]]+\]\)/', '', $text);
		$text = preg_replace('/\*?\s?\[\[([^\|]]+)\]\]/', '', $text);
		$text = preg_replace('/\*\s?\[([^\s]+)\s([^\]]+)\]/', "$2", $text);
		$text = preg_replace('/\[\[([^\]]+)\|([^\]]+)\]\]/', "$2", $text);

		$text = preg_replace('/\[\[([^\]]+)\]\]/', "$1", $text);

		$text = preg_replace('/\n(\*+\s?)/', '', $text);
		$text = preg_replace('/\n{3,}/', "\n\n", $text);
		$text = preg_replace('/<ref[^>]?>[^>]+>/', '', $text);
		$text = preg_replace('/<cite[^>]?>[^>]+>/', '', $text);

		$text = preg_replace('/\s*={2,}\s*/', "\r\n", $text);
		$text = preg_replace('/{?class="[^"]+"/', "", $text);
		$text = preg_replace('/!?\s?width="[^"]+"/', "", $text);
		$text = preg_replace('/!?\s?height="[^"]+"/', "", $text);
		$text = preg_replace('/!?\s?style="[^"]+"/', "", $text);
		$text = preg_replace('/!?\s?rowspan="[^"]+"/', "", $text);
		$text = preg_replace('/!?\s?bgcolor="[^"]+"/', "", $text);

		$text = preg_replace('/\[([^\]]+)\s+([^\]]+)\]/', " $2", $text);


		$text = trim($text);

		//$text = preg_replace('/\n\n/', "<br />\n<br />\n", $text);
		//$text = preg_replace('/\r\n\r\n/', "<br />\r\n<br />\r\n", $text);

		$text = str_replace(" ,", ',', $text);
		$text = str_replace(", ", ',', $text);
		$text = str_replace(",", ', ', $text);
		$text = str_replace("(, ", '(', $text);
		$text = str_replace(";,", ',', $text);

		// em and strong
		$text = str_replace("'''''", '', $text);
		$text = str_replace("'''", '', $text);
		$text = str_replace("''", '', $text);
		
		// lets keep it plain plain plain
		$text = strip_tags($text);

		if (isset($num) && is_numeric($num)) {
	
		$extra = "";
		
		if (isset($end)) {
			$extra = $end;
		}
		
		$text = self::get_snippet($text, $num);
		$text = $text.$extra;

	}

	return($text);
}

	private static function  get_snippet( $str, $wordCount = 50 ) {
	
		return implode( 
		'', 
		array_slice( 
		preg_split(
		'/([\s,\.;\?\!]+)/', 
		$str, 
		$wordCount*2+1, 
		PREG_SPLIT_DELIM_CAPTURE
		),
		0,
		$wordCount*2-1
		)
		);
	}

}
?>
