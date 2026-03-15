<?php
/**
 * Warns when debug-only utility functions are called outside of utility-functions.php.
 *
 * @package CoreWP
 */

namespace CoreWP\Sniffs\Debug;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Flags calls to core_wp_print_pre() and core_wp_theme_error_log() in any file
 * other than utility-functions.php, where they are defined.
 */
class NoDebugFunctionsSniff implements Sniff {

	/**
	 * Debug-only functions that must not be called outside utility-functions.php.
	 *
	 * @var string[]
	 */
	private const DEBUG_FUNCTIONS = array(
		'core_wp_print_pre',
		'core_wp_theme_error_log',
	);

	/**
	 * Register the token types this sniff wants to process.
	 *
	 * @return int[]
	 */
	public function register(): array {
		return array( T_STRING );
	}

	/**
	 * Process a T_STRING token and warn if it is a call to a debug function.
	 *
	 * @param File $phpcs_file The file being scanned.
	 * @param int  $stack_ptr  Position of the token in the token stack.
	 * @return void
	 */
	public function process( File $phpcs_file, $stack_ptr ): void {
		// Allow the definition file itself.
		if ( str_ends_with( $phpcs_file->getFilename(), 'utility-functions.php' ) ) {
			return;
		}

		$tokens = $phpcs_file->getTokens();
		$token  = $tokens[ $stack_ptr ];

		if ( ! in_array( $token['content'], self::DEBUG_FUNCTIONS, true ) ) {
			return;
		}

		// Skip the function definition — token preceded by the `function` keyword.
		$prev_ptr = $phpcs_file->findPrevious( T_WHITESPACE, $stack_ptr - 1, null, true );
		if ( false !== $prev_ptr && T_FUNCTION === $tokens[ $prev_ptr ]['code'] ) {
			return;
		}

		// Only flag actual calls — token must be followed by an opening parenthesis.
		$next_ptr = $phpcs_file->findNext( T_WHITESPACE, $stack_ptr + 1, null, true );
		if ( false === $next_ptr || T_OPEN_PARENTHESIS !== $tokens[ $next_ptr ]['code'] ) {
			return;
		}

		$phpcs_file->addWarning(
			'Debug function %s() should not be called outside of utility-functions.php.',
			$stack_ptr,
			'Found',
			array( $token['content'] )
		);
	}
}
