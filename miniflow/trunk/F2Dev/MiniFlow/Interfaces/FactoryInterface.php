<?php

namespace F2Dev\MiniFlow\Interfaces;

use F2Dev\MiniFlow as MiniFlow;

interface FactoryInterface
{
	/**
	 * A function wherein the user defines the workflow, if desired, and returns it.
	 *
	 * @return MiniFlow The instantiated MiniFlow.
	 **/
	static function getWorkflow();
	
	/**
	 * Unpack and prepare a serialized workflow to be returned.
	 *
	 * @var string $sWorkflow The serialized workflow to use.
	 * @return MiniFlow The instantiated MiniFlow.
	 */
	static function unserializeWorkflow($sWorkflow);
	
	/**
	 * Pack and prepare a workflow to be serialized.
	 *
	 * @var MiniFlow $oWorkflow The workflow to serialize.
	 * @return string The serialized MiniFlow.
	 */
	static function serializeWorkflow(MiniFlow\MiniFlow $oWorkflow);
}