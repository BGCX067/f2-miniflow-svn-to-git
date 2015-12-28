<?php

namespace F2Dev\MiniFlow\Test;

use F2Dev\MiniFlow as MiniFlow;

class RandomLink extends MiniFlow\Bases\BaseLink
{
	/**
	 * Chooses a random Node from the children.
	 *
	 * @var Array arguments An associative array of arguments needed to run this Node.
	 * @return NodeInterface The Node which should be Executed next.
	 */
	public function determine(array $arguments = array())
	{
		if (count($this->getChildren()) > 0)
		{
			return $this->getChildren()[rand(0, count($this->getChildren())-1)];	
		}
	}
}