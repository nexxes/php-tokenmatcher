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
		
		$this->assertSame(MatcherInterface::STATUS_VIRGIN, $matcher->status());
		
		// Match first whitespace
		$this->assertSame(1, $matcher->match($tokens));
		$this->assertTrue($matcher->success());
		$this->assertSame(MatcherInterface::STATUS_SUCCESS, $matcher->status());
		$this->assertCount(1, $matcher->tokens());
		$this->assertSame($tokens[0], $matcher->tokens()[0]);
		
		// Test later that it is unchanged
		$debug = $matcher->debug();
		
		// Match first whitespace (use explicit offset 0)
		$this->assertSame(1, $matcher->match($tokens, 0));
		$this->assertTrue($matcher->success());
		$this->assertSame(MatcherInterface::STATUS_SUCCESS, $matcher->status());
		$this->assertCount(1, $matcher->tokens());
		$this->assertSame($tokens[0], $matcher->tokens()[0]);
		
		// Fail to match the first newline
		$this->assertFalse($matcher->match($tokens, 1));
		$this->assertFalse($matcher->success());
		$this->assertSame(MatcherInterface::STATUS_FAILURE, $matcher->status());
		$this->assertCount(0, $matcher->tokens());
		
		// Match second whitespace
		$this->assertSame(1, $matcher->match($tokens, 2));
		$this->assertTrue($matcher->success());
		$this->assertSame(MatcherInterface::STATUS_SUCCESS, $matcher->status());
		$this->assertCount(1, $matcher->tokens());
		$this->assertSame($tokens[2], $matcher->tokens()[0]);
		
		// Fail to match the second newline
		$this->assertFalse($matcher->match($tokens, 3));
		$this->assertFalse($matcher->success());
		$this->assertSame(MatcherInterface::STATUS_FAILURE, $matcher->status());
		$this->assertCount(0, $matcher->tokens());
		
		// Nothing more to match
		$this->assertFalse($matcher->match($tokens, 4));
		$this->assertFalse($matcher->success());
		$this->assertSame(MatcherInterface::STATUS_EMPTY, $matcher->status());
		$this->assertCount(0, $matcher->tokens());
		
		// Verify $debug did not change
		$this->assertTrue($debug->success());
		$this->assertSame(MatcherInterface::STATUS_SUCCESS, $debug->status());
		$this->assertCount(1, $debug->tokens());
		$this->assertSame($tokens[0], $debug->tokens()[0]);
	}
}
