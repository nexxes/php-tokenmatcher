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
 * A Sequence tries to match a token stream against a sequence of token matchers.
 * 
 * @author Dennis Birkholz <dennis.birkholz@nexxes.net>
 */
class Sequence implements MatcherInterface {
	/**
	 * @var array<\nexxes\tokenmatcher\MatcherInterface>
	 */
	private $sequence = [];
	
	/**
	 * Status of the last matching process
	 * @var mixed
	 */
	private $status = self::STATUS_VIRGIN;
	
	
	/**
	 * Supply as many matchers as required to construct a sequence.
	 * If a matcher is supplied multiple times, each subsequent matcher is replaced by a copy to keep matched data clean
	 * 
	 * @param \nexxes\tokenmatcher\MatcherInterface $firstPattern
	 * @param \nexxes\tokenmatcher\MatcherInterface $secondPattern
	 * @param \nexxes\tokenmatcher\MatcherInterface $thirdMatcher
	 */
	public function __construct(MatcherInterface $firstPattern, MatcherInterface $secondPattern = null, MatcherInterface $thirdMatcher = null) {
		$args = \func_get_args();
		
		foreach ($args AS $matcher) {
			if (\in_array($matcher, $this->sequence, true)) {
				$this->sequence[] = clone $matcher;
			} else {
				$this->sequence[] = $matcher;
			}
		}
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function match(array $tokens, $offset = 0) {
		$total = 0;
		
		for ($i=0; $i<\count($this->sequence); ++$i) {
			if (false !== ($matched = $this->sequence[$i]->match($tokens, $offset+$total))) {
				$total += $matched;
			}
			
			else {
				$this->status = self::STATUS_FAILURE;
				return false;
			}
		}
		
		$this->status = self::STATUS_SUCCESS;
		return $total;
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
			foreach ($this->sequence AS $matcher) {
				$r = \array_merge($r, $matcher->tokens());
			}
			return $r;
		}
		
		else {
			return [];
		}
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function __toString() {
		return (new \ReflectionClass(__CLASS__))->getShortName()
			. ' with status "' . $this->status . '"'
			. PHP_EOL
			. self::INDENTATION . \str_replace(PHP_EOL, PHP_EOL . self::INDENTATION, \implode(PHP_EOL, $this->sequence));
	}
	
	/**
	 * Deep clone object
	 */
	public function __clone() {
		for ($i=0; $i<\count($this->sequence); ++$i) {
			$this->sequence[$i] = clone $this->sequence[$i];
		}
	}
}
