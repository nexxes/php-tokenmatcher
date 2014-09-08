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
 * Matches the length of the raw text that was matched, e.g. on the total raw length of a Sequence.
 * Raw length can be compared to the reference length with on of <, <=, ==, >=, >,
 * A list of token classes to ignore can also be supplied.
 * 
 * The Length matcher does not alter number or list of matched tokens, it only enforces a constraint.
 * 
 * @author Dennis Birkholz <dennis.birkholz@nexxes.net>
 */
class Length extends Matches {
	/**
	 * @var int
	 */
	protected $length;
	
	/**
	 * @var <|<=|==|=>|>
	 */
	protected $compare;
	
	/**
	 * @var array<\nexxes\tokenizer\Token>
	 */
	protected $ignore;
	
	/**
	 * @var int
	 */
	protected $lastLength = 0;
	
	
	
	/**
	 * @param \nexxes\tokenmatcher\MatcherInterface
	 * @param int $length
	 * @param <|<=|==|=>|> $compare
	 */
	public function __construct(MatcherInterface $matcher, $length, $compare = '==', $ignore = []) {
		$this->matched = $matcher;
		$this->length = $length;
		$this->compare = $compare;
		$this->ignore = $ignore;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function match(array $tokens, $offset = 0) {
		// Can only enforce constaint on successful match
		if (false === ($matched = $this->matched->match($tokens, $offset))) {
			$this->status = self::STATUS_FAILURE;
			return false;
		}
		
		$tokens = $this->matched->tokens();
		$this->lastLength = 0;
		
		// Filter out unwanted tokens
		foreach ($tokens AS $token) {
			if (!\in_array($token->type, $this->ignore, true)) {
				$this->lastLength += $token->length;
			}
		}
		
		if ('<' === $this->compare) {
			$status = ($this->lastLength < $this->length);
		} elseif ('<=' === $this->compare) {
			$status = ($this->lastLength <= $this->length);
		} elseif ('==' === $this->compare) {
			$status = ($this->lastLength == $this->length);
		} elseif ('>=' === $this->compare) {
			$status = ($this->lastLength >= $this->length);
		} elseif ('>' === $this->compare) {
			$status = ($this->lastLength > $this->length);
		} else {
			$status = false;
		}
		
		if ($status) {
			$this->status = self::STATUS_SUCCESS;
			return $matched;
		}
		
		else {
			$this->status = self::STATUS_FAILURE;
			return false;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function __toString() {
		return (new \ReflectionClass(\get_class($this)))->getShortName()
			. ' checked ' . $this->lastLength . ' ' . $this->compare . ' ' . $this->length
			. (\count($this->ignore) ? ' ignored ' . \implode(', ', \array_map([Token::class, 'typeName'], $this->ignore)) : '')
			. ' has status "' . $this->status . '"'
			. PHP_EOL . $this->indentArray( [$this->matched] );
	}
	
	/**
	 * Deep clone object
	 */
	public function __clone() {
		$this->matched = clone $this->matched;
	}
}
