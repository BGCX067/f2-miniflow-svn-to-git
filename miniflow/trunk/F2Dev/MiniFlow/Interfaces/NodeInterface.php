<?php

namespace F2Dev\MiniFlow\Interfaces;

use F2Dev\MiniFlow\Interfaces\LinkInterface as LinkInterface;
use F2Dev\MiniFlow\Interfaces\ExecutableInterface as ExecutableInterface;

interface NodeInterface extends ExecutableInterface
{
	/**
	 * Sets up anything required by this Node at _instantiation_ (not execution).
	 * All Nodes in a Workflow are instantiated in linear order, regardless of when,
	 * or even if each Node will be executed. One thing that must be done is setting the parentLink member.
	 *
	 * @var LinkInterface parentLink Link object which leads to this Node.
	 * @var LinkInterface childLink An optional argument specifying the Child Link to which this Node leads. If present, it must be stored, if not, though, it can be safely ignored.
	 * @return NodeInterface The constructor should return the Node object upon exit.
	 */
	public function __construct(LinkInterface $parentLink = NULL, LinkInterface $childLink = NULL);
}