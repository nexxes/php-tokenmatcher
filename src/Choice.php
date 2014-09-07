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
 * The Choice matcher tries to match the next token to one of the stored tokens.
 *
 * @author Dennis Birkholz <dennis.birkholz@nexxes.net>
 */
class Choice implements MatcherInterface {
	/**
	 * The token type to match the next token on
	 * @var array<string>
	 */
	private $tokenTypes;
	
	/**
	 * @param string $tokenType1
	 * @param string $tokenType2
	 */
	public function __construct($tokenType1, $tokenType2 = null) {
			$this->tokenTypes = \func_get_args();
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function match(array $tokens, $offset = 0) {
		// Next token must be set
		if (!isset($tokens[$offset])) { return false; }
		
		// Try to match
		return (\in_array($tokens[$offset]->type, $this->tokenTypes) ? 1 : false);
	}
}
