<?php

namespace F2Dev\MiniFlow;

use F2Dev\MiniFlow\Interfaces as Interfaces;

class MiniFlow implements Interfaces\ExecutableInterface
{
	public function __construct($sFlowName, Interfaces\NodeInterface $nStartNode)
	{
		$this->sFlowName = $sFlowName;
		$this->nStartNode = $nStartNode;
		$this->nCurrentNode = $nStartNode;
		$this->childLink = NULL;
		$this->parentLink = NULL;
	}
	
	public function execute(array $arguments = array())
	{
		if ($arguments != array())
		{
			$aFlowEnv = $arguments;
		}
		else
		{
			$aFlowEnv = $this->aFlowEnv;
		}
		
		if ($this->nCurrentNode instanceof Interfaces\ExecutableInterface)
		{
			//Execute the current Node, passing through the Flow Data Environment Array.
			$aFlowEnv = $this->nCurrentNode->execute($aFlowEnv);
			//Check if this Node has a Child Link.
			if ($this->nCurrentNode->getChild() != False)
			{
				//If so, run its determinator using the FDEA, and set a new current Node.
				$this->nCurrentNode = $this->nCurrentNode->getChild()->determine($aFlowEnv);
				//Finally, recurse through Execute again, passing back up the end return containing...
				return $this->execute($aFlowEnv);
			}
		}
		//...The FDEA, for analysis, logging, or whatever.
		return $aFlowEnv;
	}
	
	public function setParent(Interfaces\LinkInterface $parentLink)
	{
		$oldParent = $this->parentLink;
		$this->parentLink = $parentLink;
		$this->parentLink->addChild($this);
		return $oldParent;
	}

	public function getParent()
	{
		return $this->parentLink;
	}
	
	public function setChild(Interfaces\LinkInterface $childLink)
	{
		$oldChild = $this->childLink;
		$this->childLink = $childLink;
		$this->childLink->addParent($this);
		return $oldChild;
	}

	public function getChild()
	{
		return $this->childLink;
	}
}