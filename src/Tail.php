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
class Tail implements MatcherInterface {
	/**
	 * {@inheritdoc}
	 */
	public function match(array $tokens, $offset = 0) {
		if (\count($tokens) === $offset) {
			echo __CLASS__ . ' matched the end of the token stream' . PHP_EOL;
			return 0;
		} else {
			echo __CLASS__ . ' did not match' . PHP_EOL;
			return false;
		}
	}
}