<?php

/*
 * This file is part of the nexxes/tokenmatcher package.
 * It is licenced under the terms of the LGPL v3 or later.
 * 
 * Copyright 2014 Dennis Birkholz <dennis.birkholz@nexxes.net>.
 * 
 * More information can be found in the LICENSE file.
 */

namespace nexxes\tokenmatcher;

/**
 * A token pattern is a definition a stream of tokens can be matched against
 * 
 * If debug mode is enabled, each matcher should append at least one debug entry to the $debug array.
 * 
 * @author Dennis Birkholz <dennis.birkholz@nexxes.net>
 */
interface MatcherInterface {
	const STATUS_VIRGIN = 'Not executed yet.';
	const STATUS_EMPTY = 'No token left to match.';
	const STATUS_SUCCESS = 'Token type matched.';
	const STATUS_FAILURE = 'Token type did not match.';
	
	/**
	 * Try to match the pattern on the supplied token list.
	 * Returns FALSE if no match could be found or the number of tokens that are required to match this pattern.
	 * 
	 * @param array<\nexxes\tokenizer\Token> $tokens The token list to match against
	 * @param int $offset Seek into the array to this position
	 * @param array $debug Write debug information into the supplied array
	 * @return int|boolean
	 */
	function match(array $tokens, $offset = 0);
	
	/**
	 * Return a DebugInterface instances that represents the event of the last matching
	 *  or a list of DebugInterface instances for multiple events
	 * @return \nexxes\tokenizer\DebugInterface|array<\nexxes\tokenizer\DebugInterface>
	 */
	function debug();
	
	/**
	 * Indicates if the last matching process was a success or a failure
	 * @return boolean
	 */
	function success();
	
	/**
	 * Get the status of the last matching process.
	 * One of the STATUS_* interface constants.
	 * @return mixed
	 */
	function status();
	
	/**
	 * Get the tokens that matched a matcher in the last matching process
	 * @return array<\nexxes\tokenizer\Token>
	 */
	function tokens();
	
	/**
	 * Create a string representation of the matcher for debug purposes
	 * @return string
	 */
	function __toString();
}
