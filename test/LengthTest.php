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
 * @covers \nexxes\tokenmatcher\Length
 */
class LengthTest extends TestBase {
	/**
	 * @test
	 */
	public function testLowerThan() {
		$tokens = [
			new Token(Token::WHITESPACE, 0, 0, '   '),
			new Token(Token::WHITESPACE, 0, 0, '  '),
			new Token(Token::WHITESPACE, 0, 0, ' '),
			new Token(Token::NEWLINE, 0, 0, "\n"),
		];
		
		$whitespace = new Matches(Token::WHITESPACE);
		$range = new Range($whitespace, 1);
		
		$matcher = new Length($range, 4, '<');
		$this->assertSame(MatcherInterface::STATUS_VIRGIN, $matcher->status());
		
		$this->assertExecuteFailure($matcher, $tokens, 0);
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 1, 2), $tokens, 1);
		
		// Test later that it is unchanged
		$debug = $matcher->debug();
		$debugString = (string)$debug;
		
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 2, 1), $tokens, 2);
		$this->assertExecuteFailure($matcher, $tokens, 3);
		$this->assertExecuteFailure($matcher, $tokens, 4);
		
		// Verify $debug did not change
		$this->assertMatchSuccess($debug, \array_slice($tokens, 1, 2));
		$this->assertEquals($debugString, (string)$debug);
	}
	
	/**
	 * @test
	 */
	public function testLowerThanOrEqual() {
		$tokens = [
			new Token(Token::WHITESPACE, 0, 0, '   '),
			new Token(Token::WHITESPACE, 0, 0, '  '),
			new Token(Token::WHITESPACE, 0, 0, ' '),
			new Token(Token::NEWLINE, 0, 0, "\n"),
		];
		
		$whitespace = new Matches(Token::WHITESPACE);
		$range = new Range($whitespace, 1);
		
		$matcher = new Length($range, 3, '<=');
		$this->assertSame(MatcherInterface::STATUS_VIRGIN, $matcher->status());
		
		$this->assertExecuteFailure($matcher, $tokens, 0);
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 1, 2), $tokens, 1);
		
		// Test later that it is unchanged
		$debug = $matcher->debug();
		$debugString = (string)$debug;
		
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 2, 1), $tokens, 2);
		$this->assertExecuteFailure($matcher, $tokens, 3);
		$this->assertExecuteFailure($matcher, $tokens, 4);
		
		// Verify $debug did not change
		$this->assertMatchSuccess($debug, \array_slice($tokens, 1, 2));
		$this->assertEquals($debugString, (string)$debug);
	}
	
	/**
	 * @test
	 */
	public function testEqual() {
		$tokens = [
			new Token(Token::WHITESPACE, 0, 0, '   '),
			new Token(Token::WHITESPACE, 0, 0, '  '),
			new Token(Token::WHITESPACE, 0, 0, ' '),
			new Token(Token::NEWLINE, 0, 0, "\n"),
		];
		
		$whitespace = new Matches(Token::WHITESPACE);
		$range = new Range($whitespace, 1);
		
		$matcher = new Length($range, 3, '==');
		$this->assertSame(MatcherInterface::STATUS_VIRGIN, $matcher->status());
		
		$this->assertExecuteFailure($matcher, $tokens, 0);
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 1, 2), $tokens, 1);
		
		// Test later that it is unchanged
		$debug = $matcher->debug();
		$debugString = (string)$debug;
		
		$this->assertExecuteFailure($matcher, $tokens, 2);
		$this->assertExecuteFailure($matcher, $tokens, 3);
		$this->assertExecuteFailure($matcher, $tokens, 4);
		
		// Verify $debug did not change
		$this->assertMatchSuccess($debug, \array_slice($tokens, 1, 2));
		$this->assertEquals($debugString, (string)$debug);
	}
	
	/**
	 * @test
	 */
	public function testGreaterThan() {
		$tokens = [
			new Token(Token::WHITESPACE, 0, 0, '   '),
			new Token(Token::WHITESPACE, 0, 0, '  '),
			new Token(Token::WHITESPACE, 0, 0, ' '),
			new Token(Token::NEWLINE, 0, 0, "\n"),
		];
		
		$whitespace = new Matches(Token::WHITESPACE);
		$range = new Range($whitespace, 1);
		
		$matcher = new Length($range, 3, '>');
		$this->assertSame(MatcherInterface::STATUS_VIRGIN, $matcher->status());
		
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 0, 3), $tokens, 0);
		
		// Test later that it is unchanged
		$debug = $matcher->debug();
		$debugString = (string)$debug;
		
		$this->assertExecuteFailure($matcher, $tokens, 1);
		$this->assertExecuteFailure($matcher, $tokens, 2);
		$this->assertExecuteFailure($matcher, $tokens, 3);
		$this->assertExecuteFailure($matcher, $tokens, 4);
		
		// Verify $debug did not change
		$this->assertMatchSuccess($debug, \array_slice($tokens, 0, 3));
		$this->assertEquals($debugString, (string)$debug);
	}
	
	/**
	 * @test
	 */
	public function testGreaterThanOrEqual() {
		$tokens = [
			new Token(Token::WHITESPACE, 0, 0, '   '),
			new Token(Token::WHITESPACE, 0, 0, '  '),
			new Token(Token::WHITESPACE, 0, 0, ' '),
			new Token(Token::NEWLINE, 0, 0, "\n"),
		];
		
		$whitespace = new Matches(Token::WHITESPACE);
		$range = new Range($whitespace, 1);
		
		$matcher = new Length($range, 3, '>=');
		$this->assertSame(MatcherInterface::STATUS_VIRGIN, $matcher->status());
		
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 0, 3), $tokens, 0);
		
		// Test later that it is unchanged
		$debug = $matcher->debug();
		$debugString = (string)$debug;
		
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 1, 2), $tokens, 1);
		$this->assertExecuteFailure($matcher, $tokens, 2);
		$this->assertExecuteFailure($matcher, $tokens, 3);
		$this->assertExecuteFailure($matcher, $tokens, 4);
		
		// Verify $debug did not change
		$this->assertMatchSuccess($debug, \array_slice($tokens, 0, 3));
		$this->assertEquals($debugString, (string)$debug);
	}
	
	/**
	 * @test
	 */
	public function testIgnore() {
		$tokens = [
			new Token(Token::WHITESPACE, 0, 0, '   '),
			new Token(Token::WHITESPACE, 0, 0, '  '),
			new Token(Token::WHITESPACE, 0, 0, ' '),
			new Token(Token::NEWLINE, 0, 0, "\n"),
		];
		
		$whitespace = new Choice(Token::WHITESPACE, Token::NEWLINE);
		$range = new Range($whitespace, 1);
		
		$matcher = new Length($range, 3, '==', [Token::NEWLINE]);
		$this->assertSame(MatcherInterface::STATUS_VIRGIN, $matcher->status());
		
		$this->assertExecuteFailure($matcher, $tokens, 0);
		$this->assertExecuteSuccess($matcher, \array_slice($tokens, 1, 3), $tokens, 1);
		
		// Test later that it is unchanged
		$debug = $matcher->debug();
		$debugString = (string)$debug;
		
		$this->assertExecuteFailure($matcher, $tokens, 2);
		$this->assertExecuteFailure($matcher, $tokens, 3);
		$this->assertExecuteFailure($matcher, $tokens, 4);
		
		// Verify $debug did not change
		$this->assertMatchSuccess($debug, \array_slice($tokens, 1, 3));
		$this->assertEquals($debugString, (string)$debug);
	}
}
