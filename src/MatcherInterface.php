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
 * A token pattern is a definition a stream of tokens can be matched against
 * @author Dennis Birkholz <dennis.birkholz@nexxes.net>
 */
interface MatcherInterface {
	/**
	 * Try to match the pattern on the supplied token list.
	 * Returns FALSE if no match could be found or the number of tokens that are required to match this pattern.
	 * 
	 * @param array<\nexxes\tokenizer\Token> $tokens The token list to match against
	 * @param int $offset Seek into the array to this position
	 * @return int|boolean
	 */
	function match(array $tokens, $offset = 0);
}
