<?php

namespace F2Dev\MiniFlow\Bases;

use F2Dev\MiniFlow\Interfaces\NodeInterface as NodeInterface;
use F2Dev\MiniFlow\Interfaces\LinkInterface as LinkInterface;

abstract class BaseNode implements NodeInterface
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
	public function __construct(LinkInterface $parentLink = NULL, LinkInterface $childLink = NULL)
	{
		$this->parentLink = $parentLink;
		if ($parentLink != NULL)
		{
			$parentLink->addChild($this);
		}
		$this->childLink = $childLink;
		return $this;
	}
	
	/**
	 * If it is necessary to reassign this Node to another parent Link, this function provides
	 * that capability.
	 *
	 * @var LinkInterface parentLink The new parent Link.
	 * @return LinkInterface oldParentLink This function returns the old Link parent, if pop functionality is required.
	 */
	public function setParent(LinkInterface $parentLink)
	{
		$oldParent = $this->parentLink;
		$this->parentLink = $parentLink;
		$this->parentLink->addChild($this);
		return $oldParent;
	}
	
	/**
	 * Returns the Parent Link Object which leads to this Node.
	 *
	 * @return LinkInterface The Link object which is the Parent of this Node.
	 */
	public function getParent()
	{
		return $this->parentLink;
	}
	
	/**
	 * Sets the Link child to which this Node leads.
	 *
	 * @var LinkInterface childLink The child link which is to be set.
	 * @return Varies Returns NULL if no Child was previously set, otherwise returns the old Child.
	 */
	public function setChild(LinkInterface $childLink)
	{
		$oldChild = $this->childLink;
		$this->childLink = $childLink;
		$this->childLink->addParent($this);
		return $oldChild;
	}
	
	/**
	 * Returns the Link child of this Node.
	 *
	 * @return Mixed The child Link object, if it exists, False otherwise.
	 */
	public function getChild()
	{
		return $this->childLink;
	}
}

