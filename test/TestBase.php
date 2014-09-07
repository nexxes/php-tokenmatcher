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

/**
 * Description of MatcherTestBase
 *
 * @author Dennis Birkholz <dennis.birkholz@nexxes.net>
 */
abstract class TestBase extends \PHPUnit_Framework_TestCase {
	/**
	 * Assert that the matcher executes successfully with the supplied original list of tokens.
	 * Compare the number of returned tokens and confirm identity with reference tokens.
	 * 
	 * @param \nexxes\tokenmatcher\MatcherInterface $matcher
	 * @param \nexxes\tokenizer\Token|array<\nexxes\tokenizer\Token> $compareTo
	 * @param array<\nexxes\tokenizer\Token> $tokens
	 * @param int $offset
	 */
	protected function assertExecuteSuccess(MatcherInterface $matcher, $compareTo, array $tokens, $offset = null) {
		$matched = ($offset === null ? $matcher->match($tokens) : $matcher->match($tokens, $offset));
		$this->assertSame((\is_array($compareTo) ? \count($compareTo) : 1), $matched);
		$this->assertMatchSuccess($matcher, $compareTo);
	}	
	
	/**
	 * Assert that the matcher is in a state indicating that it executed successfully.
	 * Compare the number of returned tokens and confirm identity with reference tokens.
	 * 
	 * @param \nexxes\tokenmatcher\MatcherInterface $matcher
	 * @param \nexxes\tokenizer\Token|array<\nexxes\tokenizer\Token> $compareTo
	 */
	protected function assertMatchSuccess(MatcherInterface $matcher, $compareTo) {
		$expectedCount = (\is_array($compareTo) ? \count($compareTo) : 1);
		
		$this->assertTrue($matcher->success());
		$this->assertSame(MatcherInterface::STATUS_SUCCESS, $matcher->status());
		
		$tokens = $matcher->tokens();
		$this->assertCount($expectedCount, $tokens);
		
		if (\is_array($compareTo)) {
			for ($i=0; $i<$expectedCount; ++$i) {
				$this->assertSame($compareTo[$i], $tokens[$i]);
			}
		}
		
		else {
			$this->assertSame($compareTo, $tokens[0]);
		}
	}
	
	/**
	 * Assert that the matcher failes to execute successfully and matches the expected status;
	 * 
	 * @param \nexxes\tokenmatcher\MatcherInterface $matcher
	 * @param string $expectedStatus
	 * @param array<\nexxes\tokenizer\Token> $tokens
	 * @param int $offset
	 */
	protected function assertExecuteFailWithStatus(MatcherInterface $matcher, $expectedStatus, array $tokens, $offset = null) {
		$matched = ($offset === null ? $matcher->match($tokens) : $matcher->match($tokens, $offset));
		
		$this->assertFalse($matched);
		$this->assertFalse($matcher->success());
		$this->assertSame($expectedStatus, $matcher->status());
		$this->assertCount(0, $matcher->tokens());
	}
	
	/**
	 * Assert that the matcher failes to execute successfully and matches the FAILURE status
	 * 
	 * @param \nexxes\tokenmatcher\MatcherInterface $matcher
	 * @param array<\nexxes\tokenizer\Token> $tokens
	 * @param int $offset
	 */
	protected function assertExecuteFailure(MatcherInterface $matcher, array $tokens, $offset = null) {
		$this->assertExecuteFailWithStatus($matcher, MatcherInterface::STATUS_FAILURE, $tokens, $offset);
	}
	
	/**
	 * Assert that the matcher failes to execute successfully and matches the EMPTY status
	 * 
	 * @param \nexxes\tokenmatcher\MatcherInterface $matcher
	 * @param array<\nexxes\tokenizer\Token> $tokens
	 * @param int $offset
	 */
	protected function assertExecuteEmpty(MatcherInterface $matcher, array $tokens, $offset = null) {
		$this->assertExecuteFailWithStatus($matcher, MatcherInterface::STATUS_EMPTY, $tokens, $offset);
	}
}
