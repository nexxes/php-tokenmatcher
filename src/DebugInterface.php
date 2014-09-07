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
 * @author Dennis Birkholz <dennis.birkholz@nexxes.net>
 */
interface DebugInterface {
	/**
	 * Get the matcher for the debug event
	 * @return \nexxes\tokenmatcher\MatcherInterface
	 */
	function getMatcher();
}
