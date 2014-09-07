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
 * Tries to match a repeated occurence of a matcher.
 * Similar to the {min, max} regular expression.
 * The minimum and maximum limit how often the contained matcher can (successfully) be executed and not the number of tokens that can be consumed.
 * 
 * A range is gready and tries to matches as many elements as possible.
 * If the matched element consumes zero elements, the matching is terminated with either success, if enough matches have been reached or failure otherwise.
 * 
 * The minimum must be greater or equal to zero.
 * The maximum must be greater than (and not equal to) the minimum. It can be omitted to match infinite many occurrences.
 * 
 * @author Dennis Birkholz <dennis.birkholz@nexxes.net>
 */
class Range implements MatcherInterface {
	/**
	 * @var \nexxes\tokenmatcher\MatcherInterface
	 */
	private $matcher;
	
	/**
	 * @var int
	 */
	private $min;
	
	/**
	 * @var int
	 */
	private $max;
	
	/**
	 * Status of the last matching process, null means not matched yet
	 * @var mixed
	 */
	private $status = self::STATUS_VIRGIN;
	
	/**
	 * List of the matcher objects executed during the last run
	 * @var array<\nexxes\tokenmatcher\MatcherInterface>
	 */
	private $executedMatcher = [];
	
	
	/**
	 * @param \nexxes\tokenmatcher\MatcherInterface $matcher
	 * @param int $min In the range from 0...PHP_INT_MAX
	 * @param int $max (optional) In the range from 1...PHP_INT_MAX
	 */
	public function __construct(MatcherInterface $matcher, $min, $max = null) {
		if (!is_int($min) || ($min < 0)) {
			throw new \InvalidArgumentException('The minimum must be an integer in the range of 0 to ' . PHP_INT_MAX);
		}
		$this->min = $min;
		
		if (($max !== null) && (($max === 0) || ($max < $this->min))) {
			throw new \InvalidArgumentException('The maximum must be an integer greater zero and greater than or equal to the minimum');
		}
		$this->max = ($max === null ? PHP_INT_MAX : $max);
		
		$this->matcher = $matcher;
	}

	/**
	 * {@inheritdoc}
	 */
	public function match(array $tokens, $offset = 0) {
		$this->executedMatcher = []; // Clean old debug info
		
		$consumed = 0; // Number of tokens consumed
		
		while (\count($this->executedMatcher) < $this->max) {
			$matched = $this->matcher->match($tokens, $offset+$consumed);
			$this->executedMatcher[] = $this->matcher->debug(); // Safe debug information
			
			// End of matching process
			if (false === $matched) { break; }
			
			// Do not repeat matchers that are ok but read no tokens (e.g. like an optional matcher)
			if ($matched === 0) { break; }
			
			$consumed += $matched;
		}
		
		
		if ((0 === $this->min) // We did not need to match anything
			|| (// Verify matcher has executed at least $min times and the $minth matcher was successful
				(\count($this->executedMatcher) >= $this->min)
				&& ($this->executedMatcher[$this->min - 1]->success())
			)
		) {
			$this->status = self::STATUS_SUCCESS;
			return $consumed;
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
			$r = [];
			
			foreach ($this->executedMatcher AS $matcher) {
				$r = \array_merge($r, $matcher->tokens());
			}
			
			return $r;
		} else {
			return [];
		}
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function __toString() {
		return (new \ReflectionClass(__CLASS__))->getShortName()
			. ' for limits {' . $this->min . ', ' . $this->max . '}'
			. ' with status "' . $this->status . '"'
			. (\count($this->executedMatcher) ? ' had ' . (\count($this->executedMatcher) - ($this->executedMatcher[\count($this->executedMatcher)-1]->success() ? 0 : 1)) . ' successful matches' : '')
			. PHP_EOL
			. self::INDENTATION . \str_replace(PHP_EOL, PHP_EOL . self::INDENTATION, \implode(PHP_EOL, $this->executedMatcher));
	}
}
