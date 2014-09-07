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
	 * @var string
	 */
	private $tokenType;
	
	/**
	 * Status of the last matching process, null means not matched yet
	 * @var mixed
	 */
	private $status = self::STATUS_VIRGIN;
	
	/**
	 * The token that matched in the last matching process
	 * @var \nexxes\tokenizer\Token
	 */
	private $matched;
	
	
	/**
	 * @param string $tokenType
	 */
	public function __construct($tokenType) {
		$this->tokenType = $tokenType;
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
		if ($tokens[$offset]->type === $this->tokenType) {
			$this->matched = $tokens[$offset];
			$this->status = self::STATUS_SUCCESS;
			return 1;
		}
		
		else {
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
		return (new \ReflectionClass(__CLASS__))->getShortName() . ' for type "' . $this->tokenType . '" with status "' . $this->status . '"';
	}
}
