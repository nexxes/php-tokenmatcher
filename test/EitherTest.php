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
class EitherTest extends TestBase {
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
		
		$matcher = new Either(new Matches(Token::WHITESPACE), new Matches(Token::NEWLINE));
		
		$this->assertSame(MatcherInterface::STATUS_VIRGIN, $matcher->status());
		
		// Match 1st whitespace
		$this->assertExecuteSuccess($matcher, $tokens[0], $tokens);
		
		// Test later that it is unchanged
		$debug = $matcher->debug();
		$debugString = (string)$debug;
		
		// Match 1st whitespace (use explicit offset 0)
		$this->assertExecuteSuccess($matcher, $tokens[0], $tokens, 0);
		
		// Match 1st newline
		$this->assertExecuteSuccess($matcher, $tokens[1], $tokens, 1);
		
		// Fail on 1st backtick
		$this->assertExecuteFailure($matcher, $tokens, 2);
		
		// Match 2nd newline
		$this->assertExecuteSuccess($matcher, $tokens[3], $tokens, 3);
		
		// Match 2nd whitespace
		$this->assertExecuteSuccess($matcher, $tokens[4], $tokens, 4);
		
		// Match 3nd newline
		$this->assertExecuteSuccess($matcher, $tokens[5], $tokens, 5);
		
		// Fail on 2nd backtick
		$this->assertExecuteFailure($matcher, $tokens, 6);
		
		// Nothing more to match
		$this->assertExecuteFailure($matcher, $tokens, 7);
		
		// Verify $debug did not change
		$this->assertMatchSuccess($debug, $tokens[0]);
		$this->assertEquals($debugString, (string)$debug);
	}
}
