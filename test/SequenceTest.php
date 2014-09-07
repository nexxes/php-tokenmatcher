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
class SequenceTest extends TestBase {
	private function getTokens() {
		return [
			new Token(Token::WHITESPACE, 0, 0, ' '), // X1
			new Token(Token::NEWLINE, 0, 0, ' '),	   // X2
			new Token(Token::WHITESPACE, 0, 0, ' '), // X3
			new Token(Token::WHITESPACE, 0, 0, ' '), // X4
			new Token(Token::NEWLINE, 0, 0, ' '),    // X5
			new Token(Token::WHITESPACE, 0, 0, ' '), // X6
			new Token(Token::WHITESPACE, 0, 0, ' '), // X7
			new Token(Token::WHITESPACE, 0, 0, ' '), // X8
			new Token(Token::NEWLINE, 0, 0, ' '),    // X9
			new Token(Token::WHITESPACE, 0, 0, ' '), // X10
			new Token(Token::WHITESPACE, 0, 0, ' '), // X11
			new Token(Token::WHITESPACE, 0, 0, ' '), // X12
			new Token(Token::WHITESPACE, 0, 0, ' '), // X13
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
		$matcher = new Sequence($matchWhitespace, $matchNewline, $matchWhitespace2 = clone $matchWhitespace, $matchWhitespace3 = clone $matchWhitespace);
		
		// Match X1
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 0, 4), $tokens);
		
		// Test later that it is unchanged
		$debug = $matcher->debug();
		$debugString = (string)$debug;
		
		// Match X1 (use explicit offset 0)
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 0, 4), $tokens, 0);
		
		// 2 fails to X4
		$this->assertExecuteFailure($matcher, $tokens, 1);
		$this->assertExecuteFailure($matcher, $tokens, 2);
		
		// Match X4
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 3, 4), $tokens, 3);
		
		// 3 fails to X8
		$this->assertExecuteFailure($matcher, $tokens, 4);
		$this->assertExecuteFailure($matcher, $tokens, 5);
		$this->assertExecuteFailure($matcher, $tokens, 6);
		
		// Match X8
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 7, 4), $tokens, 7);
		
		// Only fails there after
		for ($i=8; $i<25; ++$i) {
			$this->assertExecuteFailure($matcher, $tokens, $i);
		}
		
		// Verify $debug did not change
		$this->assertMatchSuccess($debug, \array_slice($tokens, 0, 4));
		$this->assertEquals($debugString, (string)$debug);
	}
	
	/**
	 * @test
	 */
	public function testSequenceWithOptional() {
		$tokens = $this->getTokens();
		$matchWhitespace = new Matches(Token::WHITESPACE);
		$matchNewline = new Matches(Token::NEWLINE);
		$matchOptionalNewline = new Optional(clone $matchNewline);
		
		// Match a sequence of simple tokens
		$matcher = new Sequence($matchWhitespace, $matchOptionalNewline, clone $matchWhitespace, clone $matchWhitespace);
		
		// Match X1
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 0, 4), $tokens);
		
		// Test later that it is unchanged
		$debug = $matcher->debug();
		$debugString = (string)$debug;
		
		// Match X1 (use explicit offset 0)
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 0, 4), $tokens, 0);
		$this->assertExecuteFailure($matcher, $tokens, 1);
		$this->assertExecuteFailure($matcher, $tokens, 2);
		
		// Match X4
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 3, 4), $tokens, 3);
		$this->assertExecuteFailure($matcher, $tokens, 4);
		
		// Match X6 (no newline)
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 5, 3), $tokens, 5);
		$this->assertExecuteFailure($matcher, $tokens, 6);
		
		// Match X8
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 7, 4), $tokens, 7);
		$this->assertExecuteFailure($matcher, $tokens, 8);
		
		// Match X10 and X11
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 9, 3), $tokens, 9);
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 10, 4), $tokens, 10);
		
		// Only fails there after
		for ($i=11; $i<25; ++$i) {
			$this->assertExecuteFailure($matcher, $tokens, $i);
		}
		
		// Verify $debug did not change
		$this->assertMatchSuccess($debug, \array_slice($tokens, 0, 4));
		$this->assertEquals($debugString, (string)$debug);
	}
	
	/**
	 * @test
	 */
	public function testSequenceOptionalOnly() {
		$tokens = $this->getTokens();
		$matchNewline = new Matches(Token::NEWLINE);
		$matchOptionalNewline = new Optional($matchNewline);
		
		// Match a sequence of simple tokens
		$matcher = new Sequence($matchOptionalNewline, clone $matchOptionalNewline, clone $matchOptionalNewline, clone $matchOptionalNewline);
		
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 0, 0), $tokens);
		
		// Test later that it is unchanged
		$debug = $matcher->debug();
		$debugString = (string)$debug;
		
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 0, 0), $tokens, 0);
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 1, 1), $tokens, 1);
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 0, 0), $tokens, 2);
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 0, 0), $tokens, 3);
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 4, 1), $tokens, 4);
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 0, 0), $tokens, 5);
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 0, 0), $tokens, 6);
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 0, 0), $tokens, 7);
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 8, 1), $tokens, 8);
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 0, 0), $tokens, 9);
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 0, 0), $tokens, 10);
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 0, 0), $tokens, 11);
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 0, 0), $tokens, 12);
		
		// Only fails there after
		for ($i=13; $i<25; ++$i) {
			$this->assertExecuteSuccess($matcher, \array_slice($tokens, 0, 0), $tokens, $i);
		}
		
		$this->assertMatchSuccess($debug, \array_slice($tokens, 0, 0));
		$this->assertEquals($debugString, (string)$debug);
	}
}
