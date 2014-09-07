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

use \nexxes\tokenizer\Token;

/**
 * @author Dennis Birkholz <dennis.birkholz@nexxes.net>
 * @covers \nexxes\tokenmatcher\Matches
 */
class MatchesTest extends \PHPUnit_Framework_TestCase {
	public function testMatches() {
		$tokens = [
			new Token(Token::WHITESPACE, 0, 0, ' '),
			new Token(Token::NEWLINE, 0, 0, ' '),	
			new Token(Token::WHITESPACE, 0, 0, ' '),
			new Token(Token::NEWLINE, 0, 0, ' '),
		];
		
		$matcher = new Matches(Token::WHITESPACE);
		
		$this->assertSame(1, $matcher->match($tokens));
		$this->assertFalse($matcher->match($tokens, 1));
		$this->assertSame(1, $matcher->match($tokens, 2));
		$this->assertFalse($matcher->match($tokens, 3));
	}
}