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
	 * Status of the last matching process, null means not matched yet
	 * @var mixed
	 */
	private $status = self::STATUS_VIRGIN;
	
	/**
	 * The token that matched in the last matching process
	 * @var \nexxes\tokenizer\Tokenizer
	 */
	private $matched;
	
	
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
	public function debug() {
		return clone $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function status() {
		return $this->status;
	}

	/**
	 * {@inheritdoc}
	 */
	public function success() {
		return ($this->status === self::STATUS_SUCCESS);
	}

	/**
	 * {@inheritdoc}
	 */
	public function tokens() {
		if ($this->status === self::STATUS_SUCCESS) {
			return [ $this->matched ];
		} else {
			return [];
		}
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function __toString() {
		return (new \ReflectionClass(__CLASS__))->getShortName() . ' for types (' . \implode(', ', $this->tokenTypes) . ') with status "' . $this->status . '"' . ($this->status === self::STATUS_SUCCESS ? ' matched type ' . $this->matched->type : '');
	}
}
