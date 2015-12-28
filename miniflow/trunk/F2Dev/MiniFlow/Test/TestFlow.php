<?php

namespace F2Dev\MiniFlow\Test;

use F2Dev\MiniFlow as MiniFlow;
use F2Dev\MiniFlow\Test as Test;
use F2Dev\MiniFlow\Bases\BaseFactory as BaseFactory;

class TestFlow extends BaseFactory
{
	static function getWorkflow()
	{
		$startNode = new Test\HelloNode();

		$linkS = new Test\RandomLink($startNode);
		
		$nodeS1 = new Test\HelloNode($linkS);
		$nodeS2 = new Test\HelloNode($linkS);
		$nodeS3 = new Test\HelloNode($linkS);
		
		$linkS2 = new Test\RandomLink($nodeS2);
		
		$nodeS21 = new Test\HelloNode($linkS2);
		
		$nodeS3->setChild($linkS);
		
		
		$testFlow = new MiniFlow\MiniFlow("Test Flow", $startNode);
		
		return ($testFlow);
	}
}


