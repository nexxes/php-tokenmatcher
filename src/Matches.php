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
 * Matches tries to match the next token to the stored token type.
 * It can also be initialized with multiple token names, then one of the tokens must match.
 *
 * @author Dennis Birkholz <dennis.birkholz@nexxes.net>
 */
class Matches implements MatcherInterface {
	/**
	 * The token type to match the next token on
	 * @var string|array<string>
	 */
	private $tokenType;
	
	/**
	 * @param string $tokenType
	 */
	public function __construct($tokenType) {
		if (\func_num_args() > 1) {
			$this->tokenType = \func_get_args();
		} else {
			$this->tokenType = $tokenType;
		}
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function match(array $tokens, $offset = 0) {
		// Next token must be set
		if (!isset($tokens[$offset])) { return false; }
		
		// Try to match
		if (\is_array($this->tokenType)) {
			return (\in_array($tokens[$offset]->type, $this->tokenType) ? 1 : false);
		} else {
			return ($tokens[$offset]->type === $this->tokenType ? 1 : false);
		}
	}
}
