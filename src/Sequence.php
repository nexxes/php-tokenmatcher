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
 * A Sequence tries to match a token stream against a sequence of token matchers
 * 
 * @author Dennis Birkholz <dennis.birkholz@nexxes.net>
 */
class Sequence implements MatcherInterface {
	/**
	 * @var array<\nexxes\tokenmatcher\MatcherInterface>
	 */
	private $sequence = [];
	
	
	
	
	/**
	 * Supply as many matchers as required to construct a sequence
	 * 
	 * @param \nexxes\tokenmatcher\MatcherInterface $firstPattern
	 * @param \nexxes\tokenmatcher\MatcherInterface $secondPattern
	 * @param \nexxes\tokenmatcher\MatcherInterface $thirdMatcher
	 */
	public function __construct(MatcherInterface $firstPattern, MatcherInterface $secondPattern = null, MatcherInterface $thirdMatcher = null) {
		$this->sequence = \func_get_args();
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function match(array $tokens, $offset = 0) {
		$eat = 0;
		
		for ($i=0; $i<\count($this->sequence); ++$i) {
			if (false !== ($matched = $this->sequence[$i]->match($tokens, $offset+$eat))) {
				$eat += $matched;
			} else {
				return false;
			}
		}
		
		return $eat;
	}
}
