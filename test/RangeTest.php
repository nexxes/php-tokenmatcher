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
class RangeTest extends TestBase {
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
			new Token(Token::NEWLINE, 0, 0, ' '),
		];
	}
	
	/**
	 * @test
	 */
	public function testZeroOrOne() {
		$tokens = $this->getTokens();
		$simple = new Matches(Token::WHITESPACE);
		
		// Match 0 or 1 element
		$matcher = new Range($simple, 0, 1);
		
		// Match 1st whitespace
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 0, 1), $tokens);
		
		// Test later that it is unchanged
		$debug = $matcher->debug();
		$debugString = (string)$debug;
		
		// Match 1st (use explicit offset 0)
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 0, 1), $tokens, 0);
		
		// Match nothing at 1st newline
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 0, 0), $tokens, 1);
		
		// Match 2nd and 3rd whitespace
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 2, 1), $tokens, 2);
		
		// Verify $debug did not change
		$this->assertMatchSuccess($debug, \array_slice($tokens, 0, 1));
		$this->assertEquals($debugString, (string)$debug);
	}
	
	/**
	 * @test
	 */
	public function testZeroOrMore() {
		$tokens = $this->getTokens();
		$simple = new Matches(Token::WHITESPACE);
		
		// Match 0 or more elements
		$matcher = new Range($simple, 0);
		
		// Match 1st whitespace
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 0, 1), $tokens);
		
		// Test later that it is unchanged
		$debug = $matcher->debug();
		$debugString = (string)$debug;
		
		// Match 1st (use explicit offset 0)
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 0, 1), $tokens, 0);
		
		// Match nothing at 1st newline
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 0, 0), $tokens, 1);
		
		// Match 2nd and 3rd whitespace
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 2, 2), $tokens, 2);
		
		// Match 3rd whitespace
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 3, 1), $tokens, 3);
		
		// Match nothing at 2st newline
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 0, 0), $tokens, 4);
		
		// Match 4th to 6th whitespace
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 5, 3), $tokens, 5);
		
		// Match 5th and 6th whitespace
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 6, 2), $tokens, 6);
		
		// Match 6th whitespace
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 7, 1), $tokens, 7);
		
		// Match nothing at 3st newline
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 0, 0), $tokens, 8);
		
		// Match 7th to 10th whitespace
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 9, 4), $tokens, 9);
		
		// Match 8th to 10th whitespace
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 10, 3), $tokens, 10);
		
		// Match 9th and 10th whitespace
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 11, 2), $tokens, 11);
		
		// Match 10th whitespace
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 12, 1), $tokens, 12);
		
		// Match nothing at 4st newline
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 0, 0), $tokens, 13);
		
		// Match nothing after end of tokenstream
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 0, 0), $tokens, 20);
		
		// Verify $debug did not change
		$this->assertMatchSuccess($debug, \array_slice($tokens, 0, 1));
		$this->assertEquals($debugString, (string)$debug);
	}
	
	/**
	 * @test
	 */
	public function testOneOrMore() {
		$tokens = $this->getTokens();
		$simple = new Matches(Token::WHITESPACE);
		$matcher = new Range($simple, 1);
		
		// Match 1st whitespace
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 0, 1), $tokens);
		
		// Test later that it is unchanged
		$debug = $matcher->debug();
		$debugString = (string)$debug;
		
		// Match 1st whitespace (use explicit offset 0)
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 0, 1), $tokens, 0);
		
		// Fail to match the 1st newline
		$this->assertExecuteFailure($matcher, $tokens, 1);
		
		// Match 2nd and 3rd whitespace
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 2, 2), $tokens, 2);
		
		// Match 3rd whitespace
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 3, 1), $tokens, 3);
		
		// Fail to match the 2nd newline
		$this->assertExecuteFailure($matcher, $tokens, 4);
		
		// Match 4th to 6th whitespace
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 5, 3), $tokens, 5);
		
		// Match 5th and 6th whitespace
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 6, 2), $tokens, 6);
		
		// Match 6th whitespace
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 7, 1), $tokens, 7);
		
		// Fail to match the 3nd newline
		$this->assertExecuteFailure($matcher, $tokens, 8);
		
		// Match 7th to 10th whitespace
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 9, 4), $tokens, 9);
		
		// Match 8th to 10th whitespace
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 10, 3), $tokens, 10);
		
		// Match 9th and 10th whitespace
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 11, 2), $tokens, 11);
		
		// Match 10th whitespace
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 12, 1), $tokens, 12);
		
		// Fail to match the 4nd newline
		$this->assertExecuteFailure($matcher, $tokens, 13);
		
		// Verify $debug did not change
		$this->assertMatchSuccess($debug, \array_slice($tokens, 0, 1));
		$this->assertEquals($debugString, (string)$debug);
	}
	
	/**
	 * @test
	 */
	public function testTwoToThree() {
		$tokens = $this->getTokens();
		$simple = new Matches(Token::WHITESPACE);
		
		$matcher = new Range($simple, 2, 3);
		
		// Only 1st whitespace
		$this->assertExecuteFailure($matcher, $tokens);
		
		// Only 1st whitespace
		$this->assertExecuteFailure($matcher, $tokens, 0);
		
		// Fail to match the 1st newline
		$this->assertExecuteFailure($matcher, $tokens, 1);
		
		// Match 2nd and 3rd whitespace
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 2, 2), $tokens, 2);
		
		// Test later that it is unchanged
		$debug = $matcher->debug();
		$debugString = (string)$debug;
		
		// Only 3rd whitespace
		$this->assertExecuteFailure($matcher, $tokens, 3);
		
		// Fail to match the 2nd newline
		$this->assertExecuteFailure($matcher, $tokens, 4);
		
		// Match 4th to 6th whitespace
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 5, 3), $tokens, 5);
		
		// Match 5th and 6th whitespace
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 6, 2), $tokens, 6);
		
		// Only 6th whitespace
		$this->assertExecuteFailure($matcher, $tokens, 7);
		
		// Fail to match the 3nd newline
		$this->assertExecuteFailure($matcher, $tokens, 8);
		
		// Match 7th to 9th whitespace
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 9, 3), $tokens, 9);
		
		// Match 8th to 10th whitespace
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 10, 3), $tokens, 10);
		
		// Match 9th and 10th whitespace
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 11, 2), $tokens, 11);
		
		// Only 10th whitespace
		$this->assertExecuteFailure($matcher, $tokens, 7);
		
		// Fail to match the 4nd newline
		$this->assertExecuteFailure($matcher, $tokens, 13);
		
		// Verify $debug did not change
		$this->assertMatchSuccess($debug, \array_slice($tokens, 2, 2));
		$this->assertEquals($debugString, (string)$debug);
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
