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
 * An OR matcher: matches if one of the matchers matches.
 * Matchers are tested from left to right.
 *
 * @author Dennis Birkholz <dennis.birkholz@nexxes.net>
 */
class Either implements MatcherInterface {
	/**
	 * @var array<\nexxes\tokenmatcher\MatcherInterface>
	 */
	private $choices = [];
	
	/**
	 * Status of the last matching process, null means not matched yet
	 * @var mixed
	 */
	private $status = self::STATUS_VIRGIN;
	
	/**
	 * The matcher that matched successfully
	 * @var \nexxes\tokenmatcher\MatcherInterface
	 */
	private $match;
	
	
	/**
	 * Supply as many matchers as required to construct a sequence
	 * 
	 * @param \nexxes\tokenmatcher\MatcherInterface $firstPattern
	 * @param \nexxes\tokenmatcher\MatcherInterface $secondPattern
	 * @param \nexxes\tokenmatcher\MatcherInterface $thirdMatcher
	 */
	public function __construct(MatcherInterface $firstPattern, MatcherInterface $secondPattern = null, MatcherInterface $thirdMatcher = null) {
		$this->choices = \func_get_args();
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function match(array $tokens, $offset = 0) {
		for ($i=0; $i<\count($this->choices); ++$i) {
			if (false !== ($matched = $this->choices[$i]->match($tokens, $offset))) {
				$this->match = $this->choices[$i];
				$this->status = self::STATUS_SUCCESS;
				return $matched;
			}
		}
		
		$this->match = null; // Cleanup old run
		$this->status = self::STATUS_FAILURE;
		return false;
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
		return ($this->success() ? $this->match->tokens() : []);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function __toString() {
		return (new \ReflectionClass(__CLASS__))->getShortName()
			. ' with status "' . $this->status . '"'
			. ($this->success() ? ' match choice #' . (\array_search($this->match, $this->choices, true)+1) : '')
			. PHP_EOL
			. self::INDENTATION . \str_replace(PHP_EOL, PHP_EOL . self::INDENTATION, \implode(PHP_EOL, $this->choices));
	}
	
	/**
	 * Deep clone object
	 * Also keep matched object in sync with cloned choices list
	 */
	public function __clone() {
		for ($i=0; $i<\count($this->choices); ++$i) {
			$choice = $this->choices[$i];
			$this->choices[$i] = clone $choice;
			
			if ($this->match === $choice) {
				$this->match = $this->choices[$i];
			}
		}
	}
}
