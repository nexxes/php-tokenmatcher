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

use \nexxes\tokenizer\Token;

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
	protected $tokenType;
	
	/**
	 * Status of the last matching process
	 * @var mixed
	 */
	protected $status = self::STATUS_VIRGIN;
	
	/**
	 * The token that matched in the last matching process
	 * @var \nexxes\tokenizer\Token
	 */
	protected $matched;
	
	
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
	 * 
	 * Allows a child to store a matcher in $matched instead of a token
	 */
	public function tokens() {
		if ($this->success()) {
			// Get tokens of contained matcher
			if ($this->matched instanceof MatcherInterface) {
				return $this->matched->tokens();
			}
			
			// Allow children to store arrays of matches here
			elseif(\is_array($this->matched)) {
				return $this->matched;
			}
			
			// Default behaviour on success
			elseif ($this->matched instanceof Token) {
				return [ $this->matched ];
			}
		}
		
		// Fallback
		return [];
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function __toString() {
		return (new \ReflectionClass(static::class))->getShortName()
			. ' for type "' . Token::typeName($this->tokenType) . '"'
			. ' has status "' . $this->status . '"';
	}
	
	
	/**
	 * Indent all elements in the supplied array.
	 * The array must contain either strings or objects that can be casted to strings.
	 * @param array $elements
	 */
	protected function indentArray(array $elements) {
		return self::INDENTATION . \str_replace(PHP_EOL, PHP_EOL . self::INDENTATION, \implode(PHP_EOL, $elements));
	}
}
