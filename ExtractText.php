<?php


if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

$GLOBALS['wgExtensionCredits']['parserhook'][] = array(
	'path' => __FILE__,
	'name' => 'ExtractText',
	'version' => '0.2',
	'url' => 'https://github.com/SimilisTools/mediawiki-ExtractText',
	'author' => array( 'Toniher' ),
	'descriptionmsg' => 'extracttext-desc',
);

$GLOBALS['wgAutoloadClasses']['ExtExtractText'] = __DIR__.'/ExtractText_body.php';
$GLOBALS['wgMessagesDirs']['ExtractText'] = __DIR__ . '/i18n';
$GLOBALS['wgExtensionMessagesFiles']['ExtractText'] = __DIR__ . '/ExtractText.i18n.php';
$GLOBALS['wgExtensionMessagesFiles']['ExtractTextMagic'] = __DIR__ . '/ExtractText.i18n.magic.php';

$GLOBALS['wgExtensionFunctions'][] = 'wfSetupExtractText';

function wfSetupExtractText() {

	global $wgETHookStub, $wgHooks;

	$wgETHookStub = new ExtractText_HookStub;

	$wgHooks['ParserFirstCallInit'][] = array( &$wgETHookStub, 'registerParser' );
	$wgHooks['ParserClearState'][] = array( &$wgETHookStub, 'clearState' );

}

class ExtractText_HookStub {
	
	var $realObj;
	
		function registerParser( &$parser ) {
		$parser->setFunctionHook( 'extracttext', array( &$this, 'extracttext' ), SFH_OBJECT_ARGS );
		$parser->setFunctionHook( 'extractpagetext', array( &$this, 'extractpagetext' ), SFH_OBJECT_ARGS );
		return true;
	}
	
	/**
	 * Defer ParserClearState
	 */
	function clearState( &$parser ) {
		if ( !is_null( $this->realObj ) ) {
				$this->realObj->clearState( $parser );
		}
		return true;
	}

	/**
	 * Pass through function call
	 */
	function __call( $name, $args ) {
		if ( is_null( $this->realObj ) ) {
				$this->realObj = new ExtExtractText;
				$this->realObj->clearState( $args[0] );
		}
		return call_user_func_array( array( $this->realObj, $name ), $args );
	}

}

