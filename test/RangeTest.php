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
 * @covers \nexxes\tokenmatcher\Range
 */
class RangeTest extends \PHPUnit_Framework_TestCase {
	private function getTokens() {
		return [
			new Token(Token::WHITESPACE, 0, 0, ' '),
			new Token(Token::NEWLINE, 0, 0, ' '),	
			new Token(Token::WHITESPACE, 0, 0, ' '),
			new Token(Token::WHITESPACE, 0, 0, ' '),
			new Token(Token::NEWLINE, 0, 0, ' '),
			new Token(Token::WHITESPACE, 0, 0, ' '),
			new Token(Token::WHITESPACE, 0, 0, ' '),
			new Token(Token::WHITESPACE, 0, 0, ' '),
			new Token(Token::NEWLINE, 0, 0, ' '),
			new Token(Token::WHITESPACE, 0, 0, ' '),
			new Token(Token::WHITESPACE, 0, 0, ' '),
			new Token(Token::WHITESPACE, 0, 0, ' '),
			new Token(Token::WHITESPACE, 0, 0, ' '),
		];
	}
	
	/**
	 * @test
	 */
	public function testZeroOrOne() {
		$tokens = $this->getTokens();
		$simple = new Matches(Token::WHITESPACE);
		
		// Match 0 or 1 element
		$range = new Range($simple, 0, 1);
		$this->assertSame(1, $range->match($tokens));
		$this->assertSame(1, $range->match($tokens,0));
		$this->assertSame(0, $range->match($tokens,1));
		$this->assertSame(1, $range->match($tokens,2));
	}
	
	/**
	 * @test
	 */
	public function testZeroOrMore() {
		$tokens = $this->getTokens();
		$simple = new Matches(Token::WHITESPACE);
		
		// Match 0 or more elements
		$range = new Range($simple, 0);
		$this->assertSame(1, $range->match($tokens));
		$this->assertSame(1, $range->match($tokens,0));
		$this->assertSame(0, $range->match($tokens,1));
		$this->assertSame(2, $range->match($tokens,2));
		$this->assertSame(1, $range->match($tokens,3));
		$this->assertSame(0, $range->match($tokens,4));
		$this->assertSame(3, $range->match($tokens,5));
		$this->assertSame(2, $range->match($tokens,6));
		$this->assertSame(1, $range->match($tokens,7));
		$this->assertSame(0, $range->match($tokens,8));
		$this->assertSame(4, $range->match($tokens,9));
		$this->assertSame(3, $range->match($tokens,10));
		$this->assertSame(2, $range->match($tokens,11));
		$this->assertSame(1, $range->match($tokens,12));
		$this->assertSame(0, $range->match($tokens,13));
	}
	
	/**
	 * @test
	 */
	public function testOneOrMore() {
		$tokens = $this->getTokens();
		$simple = new Matches(Token::WHITESPACE);
		
		// Match 1 or more
		$range = new Range($simple, 1);
		$this->assertSame(1, $range->match($tokens));
		$this->assertSame(1, $range->match($tokens, 0));
		$this->assertFalse($range->match($tokens, 1));
		$this->assertSame(2, $range->match($tokens,2));
		$this->assertSame(1, $range->match($tokens,3));
		$this->assertFalse($range->match($tokens, 4));
		$this->assertSame(3, $range->match($tokens,5));
		$this->assertSame(2, $range->match($tokens,6));
		$this->assertSame(1, $range->match($tokens,7));
		$this->assertFalse($range->match($tokens, 8));
		$this->assertSame(4, $range->match($tokens,9));
		$this->assertSame(3, $range->match($tokens,10));
		$this->assertSame(2, $range->match($tokens,11));
		$this->assertSame(1, $range->match($tokens,12));
		$this->assertFalse($range->match($tokens, 13));
	}
	
	/**
	 * @test
	 */
	public function testTwoToThree() {
		$tokens = $this->getTokens();
		$simple = new Matches(Token::WHITESPACE);
		
		// Match 1 or more
		$range = new Range($simple, 2, 3);
		$this->assertFalse($range->match($tokens));
		$this->assertFalse($range->match($tokens, 0));
		$this->assertFalse($range->match($tokens, 1));
		$this->assertSame(2, $range->match($tokens,2));
		$this->assertFalse($range->match($tokens, 3));
		$this->assertFalse($range->match($tokens, 4));
		$this->assertSame(3, $range->match($tokens,5));
		$this->assertSame(2, $range->match($tokens,6));
		$this->assertFalse($range->match($tokens, 7));
		$this->assertFalse($range->match($tokens, 8));
		$this->assertSame(3, $range->match($tokens,9));
		$this->assertSame(3, $range->match($tokens,10));
		$this->assertSame(2, $range->match($tokens,11));
		$this->assertFalse($range->match($tokens, 12));
		$this->assertFalse($range->match($tokens, 13));
	}
	
	/**
	 * @test
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage The minimum must be
	 */
	public function testNegativeMin() {
		$simple = new Matches(Token::WHITESPACE);
		$range = new Range($simple, -1, 3);
	}
	
	/**
	 * @test
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage The maximum must be
	 */
	public function testMaxLowerMin() {
		$simple = new Matches(Token::WHITESPACE);
		$range = new Range($simple, 4, 3);
	}
}
