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
 * @covers \nexxes\tokenmatcher\Choice
 */
class ChoiceTest extends \PHPUnit_Framework_TestCase {
	public function testMatches() {
		$tokens = [
			new Token(Token::WHITESPACE, 0, 0, ' '),
			new Token(Token::NEWLINE, 0, 0, "\n"),
			new Token(Token::BACKTICK, 0, 0, '`'),
			new Token(Token::NEWLINE, 0, 0, "\n"),
			new Token(Token::WHITESPACE, 0, 0, ' '),
			new Token(Token::NEWLINE, 0, 0, "\n"),
			new Token(Token::BACKTICK, 0, 0, '`'),
		];
		
		$matcher = new Choice(Token::WHITESPACE, Token::NEWLINE);
		
		$this->assertSame(1, $matcher->match($tokens));
		$this->assertSame(1, $matcher->match($tokens, 0));
		$this->assertSame(1, $matcher->match($tokens, 1));
		$this->assertFalse($matcher->match($tokens, 2));
		$this->assertSame(1, $matcher->match($tokens, 3));
		$this->assertSame(1, $matcher->match($tokens, 4));
		$this->assertSame(1, $matcher->match($tokens, 5));
		$this->assertFalse($matcher->match($tokens, 6));
	}
}
