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
class Choice extends Matches {
	/**
	 * The token type to match the next token on
	 * @var array<string>
	 */
	protected $tokenTypes;
	
	
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
		$this->matched = null;
		
		// Next token must be set
		if (!isset($tokens[$offset])) {
			$this->status = self::STATUS_EMPTY;
			return false;
		}
		
		// Try to match
		if (\in_array($tokens[$offset]->type, $this->tokenTypes)) {
			$this->matched = $tokens[$offset];
			$this->status = self::STATUS_SUCCESS;
			return 1;
		} else {
			$this->status = self::STATUS_FAILURE;
			return false;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function __toString() {
		return (new \ReflectionClass(static::class))->getShortName()
			. ' for types (' . \implode(', ', $this->tokenTypes) . ')'
			. ' has status "' . $this->status . '"'
			. ($this->success() ? ' matched type ' . $this->matched->type : '');
	}
}
