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
 * @covers \nexxes\tokenmatcher\Sequence
 */
class SequenceTest extends \PHPUnit_Framework_TestCase {
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
	public function testSimpleSequence() {
		$tokens = $this->getTokens();
		$matchWhitespace = new Matches(Token::WHITESPACE);
		$matchNewline = new Matches(Token::NEWLINE);
		
		// Match a sequence of simple tokens
		$sequenceMatcher = new Sequence($matchWhitespace, $matchNewline, $matchWhitespace, $matchWhitespace);
		$this->assertSame(4, $sequenceMatcher->match($tokens));
		$this->assertSame(4, $sequenceMatcher->match($tokens,  0));
		$this->assertFalse(  $sequenceMatcher->match($tokens,  1));
		$this->assertFalse(  $sequenceMatcher->match($tokens,  2));
		$this->assertSame(4, $sequenceMatcher->match($tokens,  3));
		$this->assertFalse(  $sequenceMatcher->match($tokens,  4));
		$this->assertFalse(  $sequenceMatcher->match($tokens,  5));
		$this->assertFalse(  $sequenceMatcher->match($tokens,  6));
		$this->assertSame(4, $sequenceMatcher->match($tokens,  7));
		
		for ($i=8; $i<25; ++$i) {
			$this->assertFalse($sequenceMatcher->match($tokens, $i));
		}
	}
	
	/**
	 * @test
	 */
	public function testSequenceWithOptional() {
		$tokens = $this->getTokens();
		$matchWhitespace = new Matches(Token::WHITESPACE);
		$matchNewline = new Matches(Token::NEWLINE);
		$matchOptionalNewline = new Optional($matchNewline);
		
		// Match a sequence of simple tokens
		$sequenceMatcher = new Sequence($matchWhitespace, $matchOptionalNewline, $matchWhitespace, $matchWhitespace);
		$this->assertSame(4, $sequenceMatcher->match($tokens));
		$this->assertSame(4, $sequenceMatcher->match($tokens,  0));
		$this->assertFalse(  $sequenceMatcher->match($tokens,  1));
		$this->assertFalse(  $sequenceMatcher->match($tokens,  2));
		$this->assertSame(4, $sequenceMatcher->match($tokens,  3));
		$this->assertFalse(  $sequenceMatcher->match($tokens,  4));
		$this->assertSame(3, $sequenceMatcher->match($tokens,  5));
		$this->assertFalse(  $sequenceMatcher->match($tokens,  6));
		$this->assertSame(4, $sequenceMatcher->match($tokens,  7));
		$this->assertFalse(  $sequenceMatcher->match($tokens,  8));
		$this->assertSame(3, $sequenceMatcher->match($tokens,  9));
		$this->assertSame(3, $sequenceMatcher->match($tokens, 10));
		$this->assertFalse(  $sequenceMatcher->match($tokens, 11));
		$this->assertFalse(  $sequenceMatcher->match($tokens, 12));
		$this->assertFalse(  $sequenceMatcher->match($tokens, 13));
	}
	
	/**
	 * @test
	 */
	public function testSequenceOptionalOnly() {
		$tokens = $this->getTokens();
		$matchNewline = new Matches(Token::NEWLINE);
		$matchOptionalNewline = new Optional($matchNewline);
		
		// Match a sequence of simple tokens
		$sequenceMatcher = new Sequence($matchOptionalNewline, $matchOptionalNewline, $matchOptionalNewline, $matchOptionalNewline);
		$this->assertSame(0, $sequenceMatcher->match($tokens));
		$this->assertSame(0, $sequenceMatcher->match($tokens,  0));
		$this->assertSame(1, $sequenceMatcher->match($tokens,  1));
		$this->assertSame(0, $sequenceMatcher->match($tokens,  2));
		$this->assertSame(0, $sequenceMatcher->match($tokens,  3));
		$this->assertSame(1, $sequenceMatcher->match($tokens,  4));
		$this->assertSame(0, $sequenceMatcher->match($tokens,  5));
		$this->assertSame(0, $sequenceMatcher->match($tokens,  6));
		$this->assertSame(0, $sequenceMatcher->match($tokens,  7));
		$this->assertSame(1, $sequenceMatcher->match($tokens,  8));
		$this->assertSame(0, $sequenceMatcher->match($tokens,  9));
		$this->assertSame(0, $sequenceMatcher->match($tokens, 10));
		$this->assertSame(0, $sequenceMatcher->match($tokens, 11));
		$this->assertSame(0, $sequenceMatcher->match($tokens, 12));
		$this->assertSame(0, $sequenceMatcher->match($tokens, 13));
		$this->assertSame(0, $sequenceMatcher->match($tokens, 14));
		$this->assertSame(0, $sequenceMatcher->match($tokens, 15));
	}
}
