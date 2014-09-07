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
 * Matches the same token type as a reference matcher did.
 * Reference matcher must have been executed successfully and must at least match one token.
 * The first token type returned by the reference is used, all other tokens are ignored.
 *
 * @author Dennis Birkholz <dennis.birkholz@nexxes.net>
 */
class SameType extends Matches {
	/**
	 * Matcher that contains the type to look after
	 * @var \nexxes\tokenmatcher\MatcherInterface
	 */
	protected $reference;
	
	
	/**
	 * @param \nexxes\tokenmatcher\MatcherInterface $reference
	 */
	public function __construct(MatcherInterface $reference) {
			$this->reference = $reference;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function match(array $tokens, $offset = 0) {
		$this->matched = null;
		
		// Can not match if reference matched nothing
		if (!$this->reference->success() || (\count($this->reference->tokens()) === 0)) {
			$this->status = self::STATUS_FAILURE;
			return false;
		}
		
		$this->tokenType = $this->reference->tokens()[0]->type;
		return parent::match($tokens, $offset);
	}
}
