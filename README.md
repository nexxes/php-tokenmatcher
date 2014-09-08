php-tokenmatcher
================

PHP library to match tokenstreams (arrays of tokens) for later parsing.

The tokenmatcher library depends on the tokenizer package.
To use, you have to tokenize a string or construct a token stream yourself.

Match a token
-------------

``` php

// Produces two tokens: a Token::WHITESPACE token and a Token::MINUS token
$tokenizer = new Tokenizer("   ---");
$tokens = $tokenizer->getTokens();

$whitespaceMatcher = new Matches(Token::WHITESPACE);
// stores 1, the number of tokens matched
$matched = $whitespaceMatcher->match($tokens);
```

The `Matches` class is the simplest matcher available and matches just one token of the specified type.

Match a token of a choice of types
----------------------------------

The `Choice` type just works like the `Matches` type but accepts as many token types as you want:

``` PHP
$tokenizer = new Tokenizer('{{[[<<');
$tokens = $tokenizer->getTokens();

// Will match all types of brackets: (), [], {} and <>
$bracketMatcher = new Choice(Token::PARENTHESIS_LEFT, Token::PARENTHESIS_RIGHT, Token::SQUARE_BRACKET_LEFT, Token::SQUARE_BRACKET_RIGHT, Token::CURLY_BRACKET_LEFT, Token::CURLY_BRACKET_RIGHT, Token::ANGLE_BRACKET_LEFT, Token::ANGLE_BRACKET_RIGHT);
// will again be 1
$matched = $bracketMatcher->match($tokens);
```

Match several tokens
--------------------

The simple types `Matches` and `Choice` will not get us far, so we need types that can combine these basic types into more powerful pattern.

The `Range` works like curly braces in regular exceptions:

``` PHP
// Matches the same thing as /[()\[\]{}<>]{1,5}/
$bracketsMatcher = new Range($bracketMatcher, 1, 5);
// Will be 5 this time
$matched = $bracketMatcher->match($tokens);
```

The `Sequence` matcher is used to define a list of required matches:

``` PHP
$sequenceMatcher = new Sequence(
  new Matches(Token::CURLY_BRACKET_LEFT),
  new Matches(Token::CURLY_BRACKET_LEFT),
  new Matches(Token::SQUARE_BRACKET_LEFT)
);

// 3 tokens
$matched = $sequenceMatcher->match($tokens);
```

You may wonder why the sequence above contains two matchers for `Token::CURLY_BRACKET_LEFT`.
The tokenizer implementation has several token classes that match only one character.
These are parentheses, quotes and some other special chars.
Have a look at the `Tokenizer` implementation to see which characters are tokenized how.

Peeking into the token stream
-----------------------------

The `match()` method of a matcher is not bound to start matching at the beginning of the token stream.
You can give the method an offset to seek to a later position:

``` PHP
// 2 tokens: < and <
$matched = $bracketsMatcher->match($tokens, 4);
```

If you want to match the end of a token stream, use the `Tail` matcher:
``` PHP
$tailMatcher = new Tail();
// will be false
$matched = $tailMatcher->match($tokens, 5);

// will be 0 !== false, because there is "no end of stream" token
$matched = $tailMatcher->match($tokens, 6);

// will be false, behind the end
$matched = $tailMatcher->match($tokens, 7);
```

Length matching
---------------

Some matches apply only if the matched raw data fulfills a certain length constraint.
In Markdown, you may only use whitespace up to three spaces for indentation, otherwise you form a code block.

To match only a specific number of spaces, you can use the `Length` matcher.

``` PHP
$tokenizer = new Tokenizer('  This is just some text');
$tokens = $tokenizer->getTokens();

// Allows 3 or less space chars in the raw data of the matched whitespace token
$matchIndent = new Range($whitespaceMatcher, 3, '<=');

// 1 as the whitespace token is matched
$matched = $matchIndent->match($tokens);
```

Match the type of a previous match
----------------------------------

Some matches require a back reference to a previous match.
An example would be an inline code block:
match a \` (backtick) or a \~ (tilde), than text, than again \` or \~.

``` PHP
$tokenizer = new Tokenizer('`inlinecode~');
$tokens = $tokenizer->getTokens();

$inlineCodeMatcher = new Sequence(
  new Choice(Token::TILDE, Token::BACKTICK),
  new Matcher(Token::TEXT),
  new Choice(Token::TILDE, Token::BACKTICK)
);

// matched 3 tokens
$matched = $inlineCodeMatcher->match($tokens);
```

The above code will match the tokenized text but it is not a valid inline code block.
The inline code must be surrounded by either backticks or tildes, not a mix of both.

The `SameType` matcher provides help:

``` PHP
$inlineCodeMatcher = new Sequence(
  $codeFence = new Choice(Token::TILDE, Token::BACKTICK),
  new Matcher(Token::TEXT),
  new SameType($codeFence)
);

// false
$matched = $inlineCodeMatcher->match($tokens);

$tokenizer = new Tokenizer('`inlinecode`');
$tokens = $tokenizer->getTokens();

// 3 tokens: backtick, text, backtick
$matched = $inlineCodeMatcher->match($tokens);
```

A complex example
-----------------

The strength of the matchers is their ability to build complex patterns from simple building block.
The following matcher construct will match a horizontal rule in [commonmark](http://commonmark.org "CommonMark").
The pattern is in textual form:
* first optional whitespace indentation up to three spaces
* a star (\*), minus (-) or underscore (_) (marker)
* optional whitespace followed by the same token as marker was
* the previous at least so often that the line contains the of the marker characters
* finally optional whitespace and a newline or the end of the stream
(a detailed specification can be found in the [commonmark](http://jgm.github.io/stmd/spec.html#horizontal-rules "CommonMark spec").

The matcher to capture this pattern could be:
``` PHP
$horizontalRulerMatcher = new Length(
	new Sequence(
		new Length(new Optional(new Matches(Token::WHITESPACE)), 3, '<='),
		$char = new Choice(Token::MINUS, Token::STAR, Token::UNDERSCORE),

		new Range(new Sequence(
			new Matches(Token::WHITESPACE),
			new SameType($char)
		), 0),
		new Optional(new Matches(Token::WHITESPACE)),
		new Either(
			new Matches(Token::NEWLINE),
			new Tail()
		)
	), 3, '>=', [ Token::NEWLINE, Token::WHITESPACE ]
);
```
According to the compliance test of [commonmark] this even works!

The two Matchers `Optional` and `Either` have not been mentioned before.
`Optional` is quite obvious: it matches the contained matcher if it can or not. But it will always succeed.
The `Either` matcher would have been the `Or` matcher if `Or` wasn't a reserved work in PHP.
It succeeds as long as one matcher succeeds.

Debugging patterns
------------------

The tokenmatcher package was designed with easy debugging in mind.
If you want to know was the matcher actually did, just print it!

The above matcher executed on the tokenized text `***` would

``` PHP
$tokenizer = new Tokenizer("***\n");
$tokens = $tokenizer->getTokens();

$matched = $horizontalRulerMatcher->match($tokens);
echo $matched . PHP_EOL;

```

would output:

```
Length checked 3 >= 3 ignored NEWLINE, WHITESPACE has status "Token type matched."
  Sequence has status "Token type matched."
    Length checked 0 <= 3 has status "Token type matched."
      Optional has status "Token type matched."
        Matches for type "WHITESPACE" has status "Token type did not match."
    Choice for types (MINUS, STAR, UNDERSCORE) has status "Token type matched." matched type STAR
    Range for limits {0, 9223372036854775807} has status "Token type matched." had 0 successful matches
      Sequence has status "Token type did not match."
        Matches for type "WHITESPACE" has status "Token type did not match."
        SameType for type "UNKNOWN" has status "Not executed yet."
    Optional has status "Token type matched."
      Matches for type "WHITESPACE" has status "Token type did not match."
    Either has status "Token type matched." matches choice #1
      Matches for type "NEWLINE" has status "Token type matched."
      Tail has status "Not executed yet."
```

