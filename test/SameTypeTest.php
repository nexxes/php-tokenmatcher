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
class SameTypeTest extends TestBase {
	public function testMatches() {
		$tokens = [
			new Token(Token::WHITESPACE, 0, 0, ' '),
			new Token(Token::NEWLINE, 0, 0, ' '),	
			new Token(Token::WHITESPACE, 0, 0, ' '),
			new Token(Token::NEWLINE, 0, 0, ' '),
		];
		
		// The reference matches the whitespace at the beginning
		$whitespace = new Matches(Token::WHITESPACE);
		$this->assertExecuteSuccess($whitespace, $tokens[0], $tokens);
		
		$matcher = new SameType($whitespace);
		
		$this->assertSame(MatcherInterface::STATUS_VIRGIN, $matcher->status());
		
		// Match 1st whitespace
		$this->assertExecuteSuccess($matcher, $tokens[0], $tokens);
		
		// Test later that it is unchanged
		$debug = $matcher->debug();
		$debugString = (string)$debug;
		
		// Match 1st whitespace (use explicit offset 0)
		$this->assertExecuteSuccess($matcher, $tokens[0], $tokens, 0);
		
		// Fail on 1st newline
		$this->assertExecuteFailure($matcher, $tokens, 1);
		
		// Match 2nd whitespace
		$this->assertExecuteSuccess($matcher, $tokens[2], $tokens, 2);
		
		// Fail on 2nd newline
		$this->assertExecuteFailure($matcher, $tokens, 3);
		
		// Nothing more to match
		$this->assertExecuteEmpty($matcher, $tokens, 4);
		
		// Verify $debug did not change
		$this->assertMatchSuccess($debug, $tokens[0]);
		$this->assertEquals($debugString, (string)$debug);
	}
	
	/**
	 * @test
	 */
	public function testRefNotExecuted() {
		$tokens = [
			new Token(Token::WHITESPACE, 0, 0, ' '),
			new Token(Token::NEWLINE, 0, 0, ' '),	
			new Token(Token::WHITESPACE, 0, 0, ' '),
			new Token(Token::NEWLINE, 0, 0, ' '),
		];
		
		// The reference without something matched
		$whitespace = new Matches(Token::WHITESPACE);
		
		$matcher = new SameType($whitespace);
		$this->assertSame(MatcherInterface::STATUS_VIRGIN, $matcher->status());
		
		for ($i=0; $i<25; ++$i) {
			$this->assertExecuteFailure($matcher, $tokens, $i);
		}
	}
	
	/**
	 * @test
	 */
	public function testRefNotMatched() {
		$tokens = [
			new Token(Token::WHITESPACE, 0, 0, ' '),
			new Token(Token::NEWLINE, 0, 0, ' '),	
			new Token(Token::WHITESPACE, 0, 0, ' '),
			new Token(Token::NEWLINE, 0, 0, ' '),
		];
		
		// The reference without something matched
		$whitespace = new Matches(Token::WHITESPACE);
		$this->assertExecuteFailure($whitespace, $tokens, 1);
		
		$matcher = new SameType($whitespace);
		$this->assertSame(MatcherInterface::STATUS_VIRGIN, $matcher->status());
		
		for ($i=0; $i<25; ++$i) {
			$this->assertExecuteFailure($matcher, $tokens, $i);
		}
	}
}
