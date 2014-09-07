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
 * @covers \nexxes\tokenmatcher\Tail
 */
class TailTest extends TestBase {
	public function testMatches() {
		$tokens = [
			new Token(Token::WHITESPACE, 0, 0, ' '),
			new Token(Token::NEWLINE, 0, 0, "\n"),
		];
		
		$matcher = new Tail();
		$this->assertSame(MatcherInterface::STATUS_VIRGIN, $matcher->status());
		
		// First two are no end
		$this->assertExecuteFailure($matcher, $tokens, 0);
		$this->assertExecuteFailure($matcher, $tokens, 1);
		
		// That is the end
		$this->assertExecuteSuccess($matcher, [], $tokens, 2);
		
		// Test later that it is unchanged
		$debug = $matcher->debug();
		$debugString = (string)$debug;
		
		// After the end comes only error
		for ($i=3; $i<10; ++$i) {
			$this->assertExecuteFailure($matcher, $tokens, $i);
		}
		
		// Verify $debug did not change
		$this->assertMatchSuccess($debug, []);
		$this->assertEquals($debugString, (string)$debug);
	}
}
