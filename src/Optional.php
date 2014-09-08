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
class Optional extends Matches {
	/**
	 * @param \nexxes\tokenmatcher\MatcherInterface $matcher
	 */
	public function __construct(MatcherInterface $matcher) {
		$this->matched = $matcher; // Matcher is stored in $matched to reuse tokens() method of parent
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function match(array $tokens, $offset = 0) {
		$this->status = self::STATUS_SUCCESS; // Optional always matches
		
		if (false !== ($matched = $this->matched->match($tokens, $offset))) {
			return $matched;
		} else {
			return 0;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function __toString() {
		return (new \ReflectionClass(\get_class($this)))->getShortName()
			. ' has status "' . $this->status . '"' . PHP_EOL
			. $this->indentArray([$this->matched]);
	}
	
	/**
	 * Deep copy object
	 */
	public function __clone() {
		$this->matched = clone $this->matched;
	}
}
