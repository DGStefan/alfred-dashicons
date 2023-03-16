<?php
require __DIR__ . '/vendor/autoload.php';

use Alfred\Workflows\ParamBuilder\Mod;
use Alfred\Workflows\Workflow;

$search = isset( $argv ) ? $argv[1] : null;
$icons = json_decode( file_get_contents( __DIR__ . '/dashicons.json' ), true );

$found = array_filter( $icons, function ( $v, $k ) use ( $search ) {
	return strpos( $v['hash'], $search ) > -1;
}, ARRAY_FILTER_USE_BOTH );

$workflow = new Workflow();

foreach ( $found as $item ) {
	$workflow->item()
	         ->arg( $item['id'] )
	         ->title( ucfirst( $item['hash'] ) )
	         //->subtitle( $item['code'] )
	         ->icon( 'icons/' . $item['id'] . '.svg.png' )
	         ->mod(
		         Mod::cmd()
		            ->subtitle( "Copy Glyph" )
		            ->arg( json_decode( '"' . '\u' . $item['code'] . '"', true, 512, JSON_THROW_ON_ERROR ) )
		            ->variable( 'action', 'copy' )
	         )
	         ->mod(
		         Mod::alt()
		            ->subtitle( "Copy HTML" )
		            ->arg( '<span class="dashicons dashicons-' . $item['id'] . '"></span>' )
		            ->variable( 'action', 'copy_html' )
	         )
	         ->mod(
		         Mod::ctrl()
		            ->subtitle( "Copy CSS" )
		            ->arg( 'content: "' . $item['code'] . '";' )
		            ->variable( 'action', 'copy_css' )
	         );
}

$workflow->output();
