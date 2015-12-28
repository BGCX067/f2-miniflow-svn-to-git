<?php

namespace F2Dev\MiniFlow\Bases;

use F2Dev\MiniFlow\Interfaces\LinkInterface as LinkInterface;
use F2Dev\MiniFlow\Interfaces\NodeInterface as NodeInterface;
use F2Dev\MiniFlow\Interfaces\ExecutableInterface as ExecutableInterface;

abstract class BaseLink implements LinkInterface
{
	/**
	 * Constructor for the Link.
	 * Should store the Parent Node, and any given Child Nodes.
	 *
	 * @var NodeInterface parentNode The Node Parent of this Link.
	 * @var Array childrenNodes An array of Child Nodes. If not present, store an empty array.
	 * @return LinkInterface Return the constructed Link Object.
	 */
	public function __construct(ExecutableInterface $parentNode, array $childrenNodes = array())
	{
		$this->parentNodes = array($parentNode);
		$parentNode->setChild($this);
		$this->childrenNodes = $childrenNodes;
		return $this;
	}
	
	/**
	 * If it is necessary to reassign this Link to another parent Node, this function provides
	 * that capability.
	 *
	 * @var NodeInterface parentNode The parent Node to add.
	 * @return Array Returns the array of Parent Nodes
	 */
	public function addParent(ExecutableInterface $newParentNode)
	{
		if (count($this->parentNodes)>0)
		{
			$this->parentNodes[] = $newParentNode;
		}
		else
		{
			$this->parentNodes = array($newParentNode);
		}
		return $this->parentNodes;
	}
	
	/**
	 * Searches through the Parent Nodes of this Link, searching for the given Node.
	 * If found, returns True, else, returns False.
	 *
	 * @var NodeInterface parentNode The Node which should be removed.
	 * @return Bool Returns the True if Node removed, False otherwise.
	 */
	public function removeParent(ExecutableInterface $parentNode)
	{
		foreach($this->parentNodes as $index => $child)
		{
			if ($parentNode === $child)
			{
				unset($this->parentNodes[$index]);
				return true;
			}
		}
		return false;
	}
	
	
	/**
	 * Returns the array of Parent Nodes for this Link.
	 *
	 * @return Array An array of Parent Nodes.
	 */
	public function getParents()
	{
		return $this->parentNodes;
	}
	
	/**
	 * Appends a given Child Node to the array of Children Nodes for this Link.
	 *
	 * @var NodeInterface newChildNode The Node to add.
	 * @return Array Returns the array of Children Nodes
	 */
	public function addChild(ExecutableInterface $newChildNode)
	{
		if (count($this->childrenNodes)>0)
		{
			$this->childrenNodes[] = $newChildNode;
		}
		else
		{
			$this->childrenNodes = array($newChildNode);
		}
		return $this->childrenNodes;
	}
	
	/**
	 * Searches through the Children Nodes of this Link, searching for the given Node.
	 * If found, returns True, else, returns False.
	 *
	 * @var NodeInterface childNode The Node which should be removed.
	 * @return Bool Returns the True if Node removed, False otherwise.
	 */
	public function removeChild(ExecutableInterface $childNode)
	{
		foreach($this->childrenNodes as $index => $child)
		{
			if ($childNode === $child)
			{
				unset($this->childrenNodes[$index]);
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Returns the array of Children Nodes for this Link.
	 *
	 * @return Array An array of Children Nodes.
	 */
	public function getChildren()
	{
		return $this->childrenNodes;
	}
}