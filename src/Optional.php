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
 * Matches zero or one occurrence of the contained matcher
 *
 * @author Dennis Birkholz <dennis.birkholz@nexxes.net>
 */
class Optional implements MatcherInterface {
	/**
	 * @var \nexxes\tokenmatcher\MatcherInterface
	 */
	private $matcher;
	
	/**
	 * Status of the last matching process, null means not matched yet
	 * @var mixed
	 */
	private $status = self::STATUS_VIRGIN;
	
	
	/**
	 * @param \nexxes\tokenmatcher\MatcherInterface $matcher
	 */
	public function __construct(MatcherInterface $matcher) {
		$this->matcher = $matcher;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function match(array $tokens, $offset = 0) {
		$this->status = self::STATUS_SUCCESS; // Optional always matches
		
		if (false !== ($matched = $this->matcher->match($tokens, $offset))) {
			return $matched;
		} else {
			return 0;
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
		if ($this->matcher->status() === self::STATUS_SUCCESS) {
			return $this->matcher->tokens();
		} else {
			return [];
		}
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function __toString() {
		return (new \ReflectionClass(__CLASS__))->getShortName() . ' with status "' . $this->status . '"' . PHP_EOL
			. self::INDENTATION . \implode(PHP_EOL . self::INDENTATION, \explode(PHP_EOL, (string)$this->matcher));
	}
	
	public function __clone() {
		$this->matcher = $this->matcher->debug();
	}
}
