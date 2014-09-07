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
	 * @param \nexxes\tokenmatcher\MatcherInterface $matcher
	 */
	public function __construct(MatcherInterface $matcher) {
		$this->matcher = $matcher;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function match(array $tokens, $offset = 0) {
		return (false !== ($matched = $this->matcher->match($tokens, $offset)) ? $matched : 0);
	}
}
