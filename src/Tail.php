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
 * Matches the end of the token stream
 *
 * @author Dennis Birkholz <dennis.birkholz@nexxes.net>
 */
class Tail extends Matches {
	/**
	 * Remove required argument from parent
	 */
	public function __construct() {
	}
	
	
	/**
	 * {@inheritdoc}
	 */
	public function match(array $tokens, $offset = 0) {
		if (\count($tokens) === $offset) {
			$this->status = self::STATUS_SUCCESS;
			return 0;
		} else {
			$this->status = self::STATUS_FAILURE;
			return false;
		}
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function tokens() {
		return [];
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function __toString() {
		return (new \ReflectionClass(\get_class($this)))->getShortName()
			. ' has status "' . $this->status . '"'
			. PHP_EOL;
	}
}
