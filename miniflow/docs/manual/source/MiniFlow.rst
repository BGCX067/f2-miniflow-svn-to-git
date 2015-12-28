========================
 MiniFlow Documentation
========================

Contents
========

1. `Introduction to MiniFlow`_
	a. `What is MiniFlow?`_
	b. `Who should use MiniFlow?`_
	c. `Key Terminology`_
2. `How to use MiniFlow`_
	a. `Initial Setup`_
		i. `Installation`_
		ii. `Setting up ClassLoader`_
	b. `Creating custom Nodes and Links`_
		i. `Making a Node`_
		ii. `Making a Link`_
	c. `Creating a Workflow`_
		i. `Creating a Workflow Procedurally`_
		ii. `Using a Factory`_
		iii. `Serialization`_
	d. `Advanced Workflows`_
		i. `Sub-Workflows`_

Introduction to MiniFlow
========================

What is MiniFlow?
-----------------

MiniFlow is a very lightweight implementation of a `Workflow`_ Engine.
It is designed to provide you with the bare-bones requirements of a Workflow,
without overburdening the user with unnecessary complication.

As such, unlike other workflow engines, MiniFlow is not very friendly
to non-programmers. Everything in MiniFlow is handled by raw PHP code;
there is no Workflow Definition markup language, or GUI Workflow designer.
This means that there is a low barrier of entry for programmers, but a
high barrier for those who are not.

Note that, because by design there is no Workflow Definition, MiniFlows are inherently
Non-Interactive. That is, they cannot be stopped and resumed later on. A MiniFlow
Definition is the MiniFlow itself; there is no distinction between a Workflow Definition
and a Workflow Instance.

This does not mean it is impossible to get user input, as a Node may halt execution
to wait for input, if so programmed. However, this input is part *of* the workflow,
not an interruption. If pausing for input, the execution thread of the MiniFlow
must not exit, as there is no way to resume from the last execution pointer if it
does. This may cause difficulty in creating multi-page workflows via a web-based
interface. If there is a need for a multi-page workflow, it will need to be
broken into multiple workflows. (Though, it is theoretically possible to have Nodes
which connect to AJAX Sockets for information transfer, and emulate a multi-page
Workflow in this manner, using only the single execution thread. However, such
a setup, if desired, is left as an exercise to the reader.)

Who should use MiniFlow?
------------------------

MiniFlow is useful to anyone who finds that the majority of the work
for any given `Application`_ is in reorganizing existing functions
to perform a specific `Job`_.

MiniFlow is useful to anyone who needs a tool to help organize `Control Logic`_
of an application.

MiniFlow is useful to those who have a need for the programming concept of a `Workflow`_,
but do not want the overhead associated with traditional Workflow solutions.

Key Terminology
---------------

Throughout this documentation, I will be referring to a number of concepts and terms.
As such, I will layout their meaning here.

I will also be referring to standard concepts relating to programming and source control;
these may not be defined in this document, as I assume the reader has a working knowledge
of these topics.

_`Workflow`
	For our purposes, a Workflow is a discrete sequence of events (`Nodes`_) and transitions (`Links`_).
	This sequence is configured in such a way as to perform a designated `Job`_.
	
	Workflows may be referred to in this documentation generally as Flows, or specifically as MiniFlows.
	
_`Node`
	A Node is the base component of a `Workflow`_. Nodes are configured to perform a single, simple `Task`_.
	These Tasks are interwoven together by `Links`_ to create the Flow.
	
	A characteristic of a Node is that it should not preform any `Control Logic`_ internally.
	If you are considering putting Control Flow inside a Node, that should almost always be factored
	out into multiple Links and Nodes.
	
_`Link`
	A Link implements the `Control Logic`_ of a `Workflow`_. `Nodes`_ are connected to each other by Links.
	
	Links decide which Node should be executed next.
	
	Links should never perform any `Action`_. To enforce this, the standard Link `Interface`_ does not support
	returning data from it's execution.
	
_`Flow Data Environment Array`
	The Flow Data Environment Array, or FDEA for short, represents data that is global to the `Workflow`_.
	It is used by `Nodes`_ to execute their `Tasks`_, and by `Links`_ to determine which Node to execute.
	
	The FDEA takes the form of an associative array which is passed to Nodes as a parameter of execute(),
	and to Links as a parameter of determine().
	
	Nodes may make changes to the FDEA in order to pass data between Nodes, or for use by Links in order
	to modulate `Control Logic`_. Nodes must return the FDEA at the end of their execute() function.
	It will then be passed on to the next Link, and the next Node, et cetera.
	
	Note that Links have no means of modifying the FDEA outside of their local scope. Links may have read-only
	access to the FDEA for use in determining flow, but as Links are not allowed to perform `Actions`_,
	they cannot effect data changes.
	
	How the FDEA is actually used by the `Workflow`_ is an implementation detail for those creating
	Nodes and Links.

_`Control Logic`
	Program code comes in two types, Control Logic, and `Execution Code`_. Control Logic code examples
	are if, for, or while calls.
	
	Control Logic should rarely, if ever, be present in `Nodes`_. Control Logic in MiniFlow is handled
	by `Links`_ and the organization of the `Workflow`_.

_`Application`
	Application refers to the entire suite of code written to perform `Jobs`_ in a specific domain.
	For example, an Accounting Application.

_`Job`
	A Job is the main component of an `Application`_. A Job is a single unit of work for the user
	of the Application. That is, the user enters the Application in order to accomplish a specific
	goal. The steps required to reach this goal are considered a Job. For example, in an Accounting
	Application, a Job might be to send an invoice to a client via email.
	
	A `Workflow`_ is designed to complete a specific type of Job.
	
	A Job is comprised of multiple `Tasks`_.

_`Task`
	A Task is the intermediate entity in a `Job`_. A Task can be defined as the largest conglomeration
	of `Actions`_ possible without the need for `Control Logic`_. For example, if the Job was to send
	a client an invoice via email, a Task might be to retrieve the invoice from the database, or to
	send out the finalized email.
	
	A `Node`_ is constructed in order to perform a specific Task.
	
	A Task is comprised of multiple `Actions`_.

_`Action`
	An Action is the most basic entity in a `Job`_. An Action can be defined as a single line of
	procedural code, for example, a variable assignment, math operation, or a function call.
	
	`Control Logic`_ code, such as if, for, or while calls, are not considered Actions.
	
_`Interface`
	A Class Interface is a definition of a Class. It provides a template for class members and methods
	which must be implemented by descendent classes.

_`Base`
	A Class Base is an implementation of an `Interface`_ with common methods already implemented.
	
	In the case of MiniFlow, you will be extending `Node`_ and `Link` Base classes, which implement
	their respective Interfaces, with only one method not implemented.
	
	For Node Bases, the execute() method is not implemented.
	
	For Link Bases, the determine() method is not implemented.
	
	This allows you to easily define `Workflow`_ components without having to recode
	much of the overlapping functionality.

.. _Nodes: Node_
.. _Links: Link_
.. _FDEA: `Flow Data Environment Array`_
.. _Jobs: Job_
.. _Tasks: Task_
.. _Actions: Action_
.. _Execution Code: Action_

How to use MiniFlow
===================

Initial Setup
-------------

Installation
~~~~~~~~~~~~
First, extract the MiniFlow source, maintaining its directory structure, into your `Application`_'s
Vendors folder. If you don't have a Vendors folder, create a folder called Vendors in the root
directory of your source trunk.

MiniFlow does not require this specific location in order to function, but any examples given will
assume this location.

You should now have the following directory structure:

::

    trunk
      |
      -Vendors
          |
          -F2Dev
             |
             -MiniFlow


Congratulations! MiniFlow is installed.

Unfortunately, there is still more to be done for MiniFlow to be useful.

MiniFlow requires its namespace (F2Dev\\MiniFlow) to be added to the Autoloader.

MiniFlow comes packaged with the Symfony ClassLoader component, if you do not have
an autoloader currently. Read the section on `Setting up ClassLoader`_ if you plan to
use this.

If using your own Autoloader, make sure that the following call:

::

	use F2Dev\MiniFlow as MiniFlow;

Loads the file trunk/Vendors/F2Dev/MiniFlow/MiniFlow.php

Once that's done, you can skip ahead to `Creating custom Nodes and Links`_.

Setting up ClassLoader
~~~~~~~~~~~~~~~~~~~~~~

You will need initialize and register the ClassLoader in order to use MiniFlow.
This needs to be done for every thread that will use MiniFlow. This is usually
best accomplished by a routing system that calls all other functionality, but
that architecture decision is not within the scope of MiniFlow.

If execution flow for your `Application`_ does not go through a central point,
the next best solution would be to create a bootstrap file, that is executed
at the beginning of every file. It would be in this file that you register
the autoloader. However, again, this is an architecture decision that is not
within the scope of this documentation.

Whatever method you use to initialize your environment, registering the ClassLoader
should be among the first `Actions`_ performed.

The example given will assume that the file performing environment setup is located
at the root of your trunk. If this is not the case, you will need to adjust the
paths in the following code accordingly.

In your initialization file, you need to execute the following code:

.. code-block:: php

	<?php
	
	require_once __dir__."/Vendors/F2Dev/MiniFlow/Vendors/ClassLoader/UniversalClassLoader.php";
	
	use Symfony\Component\ClassLoader\UniversalClassLoader;

	$loader = new UniversalClassLoader();
	$loader->registerNamespace('F2Dev\MiniFlow', __DIR__ . '/Vendors');
	$loader->register(true);
	
This will register the autoloader, as well as MiniFlow's namespace. This means that in any
thread where this code has been executed, the line:

::

	use F2Dev\MiniFlow as MiniFlow;

Can be used without an accompanying require or require_once call.

Creating custom Nodes and Links
-------------------------------

Making a Node
~~~~~~~~~~~~~

This example will walk you through creating a simple Hello World `Node`_.

Create a folder in your trunk (or elsewhere, but keep in mind any differences in directories) called Nodes.

In your Nodes folder, create a file called HelloNode.php.

Start by giving the file a namespace:

::

	<?php
	
	namespace MyApp\Nodes;
	

Then, we'll want to include the MiniFlow library, using the line:

::

	use F2Dev\MiniFlow as MiniFlow;
	
Now that we have access to the Node `Base`_, we can declare our class like so:

::

	class HelloNode extends MiniFlow\Bases\BaseNode
	{
	

The Base will implement all the common functionality needed for a Node, as specified by the `Interface`_.
There is no need for you as a user of MiniFlow to implement Node methods relating to `Workflow`_ operation.
The only exception is the execute() method, which will determine what this Node actually does on its execution.

So, let's define that now:

::
	
		public function execute(array $arguments = array())
		{
			echo "Hello " . $arguments['Message'] . "!\n";
			return $arguments;
		}
	}
	
Congratulations! You now have a finished Node! The final code should look like:


.. code-block:: php

	<?php
	
	namespace MyApp\Nodes;
	
	use F2Dev\MiniFlow as MiniFlow;
	
	class HelloNode extends MiniFlow\Bases\BaseNode
	{
		public function execute(array $arguments = array())
		{
			echo "Hello " . $arguments['Message'] . "!\n";
			return $arguments;
		}
	}


This Node will, upon its execution, check the `FDEA`_ for a 'Message' argument, and echo a simple message.

Though this is a simplistic example, Nodes can be made to perform any common `Tasks`_ that are needed for
your Application.
	
Making a Link
~~~~~~~~~~~~~

This example will walk you through creating a simple Random Choice `Link`_.

Create a folder in your trunk (or elsewhere, but keep in mind any differences in directories) called Links.

In your Links folder, create a file called RandomLink.php.

Start by giving the file a namespace:

::

	<?php
	
	namespace MyApp\Links;
	

Then, we'll want to include the MiniFlow library, using the line:

::

	use F2Dev\MiniFlow as MiniFlow;
	
Now that we have access to the Link `Base`_, we can declare our class like so:

::

	class RandomLink extends MiniFlow\Bases\BaseLink
	{
	

The Base will implement all the common functionality needed for a Link, as specified by the `Interface`_.
There is no need for you as a user of MiniFlow to implement Link methods relating to `Workflow`_ operation.
The only exception is the determine() method, which will determine which of the Link's children `Nodes`_ to
execute next.

So, let's define that now:

::
	
		public function determine(array $arguments = array())
		{
			return $this->getChildren()[rand(0, count($this->getChildren())-1)];
		}
	}
	
Congratulations! You now have a finished Link! The final code should look like:

.. code-block:: php

	<?php

	namespace MyApp\Links;
	
	use F2Dev\MiniFlow as MiniFlow;
	
	class RandomLink extends MiniFlow\Bases\BaseLink
	{
		public function determine(array $arguments = array())
		{
			return $this->getChildren()[rand(0, count($this->getChildren())-1)];
		}
	}


This Link will, upon its execution, chose a random child Node from it's list of children, gathered from
the call $this->getChildren().

$this->getChildren() will return an array of Node objects, from which you need to return one to be executed next.

Though this is a simplistic example, Links can be made to use the `FDEA`_ to make complex `Control Logic`_
decisions to control the flow of your `Workflow`_.

Creating a Workflow
-------------------

Creating a Workflow Procedurally
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

If you have an existing set of `Nodes`_ and `Links`_ already, this section will walk you through
using them to create a `Workflow`_.

You start, of course, with including the MiniFlow namespace:

::
	
	use F2Dev\MiniFlow as MiniFlow;

Keep in mind that a class loader system is required in order to use MiniFlow. Read the section on
`Setting up ClassLoader`_ if you need more information.

You will also need to include your library of Nodes and Links, like:

::

	use MyApp\Nodes as Nodes;
	use MyApp\Links as Links;
	
For this example, we'll be using the HelloNode and the RandomLink created in the section
`Creating custom Nodes and Links`_. This is to give you a basic idea on how to configure
a Workflow.

To start with, we need to define our Nodes and Links, one at a time.

::

	$startNode = new Nodes\HelloNode();
	
When definining Nodes or Links, the first argument given will be that Node or Link's parent entity.
If not given, no parent is set. The only reason not to supply a parent is if you are creating the
starting point of a workflow.

The second argument sets a child Link for a Node, or an array of children Nodes for a Link.
There is usually no need to supply a second argument, as supplying a Parent entity will
automatically add the Child entity to the Parent.

A Node can have 0-1 Links as a parent, and 0-1 Links as a child.

A Link can have 1+ Nodes as parents, and 1+ Nodes as children.

So, let us define a Link to be the child of the startNode.

::

	$linkS = new Links\RandomLink($startNode);
	
Now, let's make three more HelloNodes, as children of the firstLink.

::

	$nodeS1 = new Nodes\HelloNode($linkS);
	$nodeS2 = new Nodes\HelloNode($linkS);
	$nodeS3 = new Nodes\HelloNode($linkS);
	
Now, we have some options to play with. The current structure of the Workflow looks like:


::

		     startNode
		        |
		        |
		      linkS
		     /  |  \
		    /   |   \
		   /    |    \
		 nS1   nS2   nS3
		 

Let's let nodeS1's path end there.

Then, we'll create a new RandomLink as a child of nodeS2.

::

	$linkS2 = new Links\RandomLink($nodeS2);
	
And we'll make another HelloNode as a child of that link.

::

	$nodeS21 = new Nodes\HelloNode($linkS2);
	
The Workflow now looks like:

::

		     startNode
		        |
		        |
		      linkS
		     /  |  \
		    /   |   \
		   /    |    \
		 nS1   nS2   nS3
		        |
		       lS2
		        |
		       nS21


You may have noticed the naming notation I've used for keeping track of the position of Nodes and Links.
It is entirely optional, you can name these variables how ever you choose, but I find it helps me
keep track of where in a given Workflow an entity is.

Beginning with the start Node, which is indicated as an S, each subsequent Node in a given branch is
given a unique identifier, starting from 1. If 1 through 9 are used, then go on to lowercase letters.

For the next part of the Workflow, we're going to do something a bit tricky, and backtrack up the tree.

We take nodeS3, and explicitly set it's child link to a link which already exists, linkS.

::

	$nodeS3->setChild($linkS);
	
Now our workflow looks like:

::

		     startNode
		        |
		        | /<-\
		      linkS   \
		     /  |  \   \
		    /   |   \   \
		   /    |    \   \
		 nS1   nS2   nS3  \
		        |      \   |
		       lS2      \->/
		        |
		       nS21	

At each one of the Nodes, a Message will be printed to screen.

Now, all that's left is to initalize and execute our workflow.

::
	
	$testFlow = new MiniFlow\MiniFlow("Test Flow", $startNode)
	$testFlow->execute(array("Message" => "World"));



Now, we have a completed Workflow! It will display "Hello World!" a random number of times,
but always at least twice.

The final code should look like:

.. code-block:: php

	<?php
	
	use F2Dev\MiniFlow as MiniFlow;
	
	use MyApp\Nodes as Nodes;
	use MyApp\Links as Links;
	
	$startNode = new Nodes\HelloNode();

	$linkS = new Links\RandomLink($startNode);
	
	$nodeS1 = new Nodes\HelloNode($linkS);
	$nodeS2 = new Nodes\HelloNode($linkS);
	$nodeS3 = new Nodes\HelloNode($linkS);
	
	$linkS2 = new Links\RandomLink($nodeS2);
	
	$nodeS21 = new Nodes\HelloNode($linkS2);
	
	$nodeS3->setChild($linkS);
	
	$testFlow = new MiniFlow\MiniFlow("Test Flow", $startNode, array("Message" => "World"));
	$testFlow->execute(array("Message" => "World"));
	

Using a Factory
~~~~~~~~~~~~~~~

`Creating a Workflow Procedurally`_ is all well and good, but what, I hear you ask, if you want to
include `Workflows`_ defined in another scope, or reuse an existing Workflow in multiple places?
For that, we have Factories.

Creating a Workflow Factory is not much different from creating a Workflow procedurally, only with
a few extra steps. Let's create a very simple workflow (just a single Node), as a Factory.

To start with, create a file with the name of the workflow you're creating. Let's use:

::

	MyApp\Workflows\TestFlow.php
	
Now, in this file, we want to declare a namespace.

::

	<?php
	
	namespace MyApp\Workflows\TestFlow;
	

Then, use the namespaces we need.

::

	use F2Dev\MiniFlow as MiniFlow;
	
	use MyApp\Nodes as Nodes;
	use MyApp\Links as Links;
	
Now, we'll vary somewhat from the procedural paradigm, and declare a class. The class should be
named the same as the file name, and needs to extend MiniFlow\Bases\BaseFactory.

::

	class TestFlow extends MiniFlow\Bases\BaseFactory
	{
	

We only need to define one function for this class, which is getWorkflow(). This function is static, takes no
parameters, and needs to return an instance of a MiniFlow. We do that in the same manner as
we did for procedural declaration.

::
		
		static function getWorkflow()
		{
			$startNode = new Nodes\HelloNode();
			
			$testFlow = new MiniFlow\MiniFlow("Test Flow", $startNode);
			
			return($testFlow);
		}
	}

That's it! You now have a fully functional MiniFlow Factory.

The final code should look like:

.. code-block:: php

	<?php
	
	namespace MyApp\Workflows\TestFlow;
	
	use F2Dev\MiniFlow as MiniFlow;
	
	use MyApp\Nodes as Nodes;
	use MyApp\Links as Links;

	class TestFlow extends MiniFlow\Bases\BaseFactory
	{
		static function getWorkflow()
		{
			$startNode = new Nodes\HelloNode();
			
			$testFlow = new MiniFlow\MiniFlow("Test Flow", $startNode);
			
			return($testFlow);
		}
	}

Now, in order to use this Workflow from another file or context, in the other file you just need
to call:

::

	use MyApp\Workflows as Workflows;
	
	$testFlow = Workflows\TestFlow::getWorkflow();
	$testFlow->execute(array("Message" => "World"));
	
Easy as that!

Serialization
~~~~~~~~~~~~~

`Using a Factory`_ is certainly fancy, I hear you say, but what if I want to share a workflow
to another computer, or store all our workflows in a central location?

There are a lot of reasons to want to load Workflows from a database, or another server.
It allows you to seperate concerns, having one team create and modify workflows, while
another maintains the code that executes them.

Because there is no definition language for MiniFlows, (or, rather, because the definition language
is PHP itself), it might seem burdensome to store and retreive MiniFlows. However, because a MiniFlow
instance is it's definition, it's possible to serialize a MiniFlow instance into a string for
database or file storage, posting in forums or emails, or whatever other needs you might have.

While it is technically feasible at the current iteration to use the built-in PHP functions
serialize() and unserialize() to handle this yourself, a pair of static serialization functions
is provided by the Factory class set in order to provide future-proof serialization functionality.

The functions work like so, assuming $flowToStore is a MiniFlow instance:

.. code-block:: php

	<?php
	
	use F2Dev\MiniFlow as MiniFlow;
	
	$serializedFlow = MiniFlow\Bases\BaseFactory::serializeWorkflow($flowToStore);
	//$serializedFlow now contains a string representation of the MiniFlow.
	
	$retrievedFlow = MiniFlow\Bases\BaseFactory::unserializeWorkflow($serializedFlow);
	//$retrievedFlow is now an instance of the stored MiniFlow.
	
Note that the serialize functions are also present for any user defined Factories,
if required for whatever reason.

Advanced Workflows
------------------

This section will cover some advanced topics regarding Workflow definition and use.

It's recommended that you test and familiarize yourself with basic MiniFlow use
before trying out the following functionality.

Sub-Workflows
~~~~~~~~~~~~~

It's possible to have a `Workflow`_ called by another Workflow. It is a fairly straightforward
process syntactically, but can easily lead to complex and chaotic Workflow structure if not
used with care and planning. However, when used responsibly, Sub-Workflows are a very powerful
tool. This capability allows you to make simple Workflows for basic `Jobs`_, then string those
Workflows together, creating a high level business logic Workflow.

It's a good idea to use a Sub-Workflow for a `Task`_ or `Job`_ when you have a set of `Nodes`_
and `Links`_ that perform a Task or Job that can be encapsulated from the current context.
For example, say you are creating a Workflow to calculate something from given data,
then e-mail the results to someone based on a name (not an e-mail address). There is nothing
wrong with defining all of this in a single workflow. However, you might often need to e-mail
someone based only on name. So, it makes sense to take the part of the Workflow that handles
the e-mail Task, and move it into another Workflow. You can then call this e-mail by name
Workflow from the original Workflow, or any other place you need it, reducing duplication
of effort.

Nothing special is required to *create* a Sub-Workflow. Sub-Workflows are Workflows in their
own right, and are created in the same manner. They can also be used in the same manner,
if desired. When using a Workflow as a Sub-Workflow, it is treated as you would treat a Node.
The only difference is in it's construct signature, Workflows do not accept parents or children
as instantiation parameters. For this example, we'll assume that a `Factory`_ is created at
MyApp\Workflows\SubWorkflow.

First, we'll start creating the top-level workflow, using example Links and Nodes.

::

	use F2Dev\MiniFlow;
	
	use MyApp\Nodes as Nodes;
	use MyApp\Links as Links;
	use MyApp\Workflows as Workflows;
	
	$startNode = new Nodes\HelloNode();
	
	$linkS = new Links\RandomLink($startNode);
	
	$topWorkflow = MiniFlow\MiniFlow("Top Workflow", $startNode);
	
	
Then, we get the sub-workflow from the factory.

::

	$flowS1 = Workflows\SubWorkflow::getWorkflow();
	
Now, we just need to treat it as any other node, and add it to the existing tree.
Because Workflows cannot be instantiated with a parent or child, we need to explicitly
declare such.

::

	$flowS1->setParent($linkS);
	
	$linkS1 = new Links\RandomLink($flowS1);
	
	$nodeS11 = new Nodes\HelloNode($linkS1);
	
With this, we put the sub-workflow into the execution tree, and continue on afterwards
with another node. The described workflow, when executed, will execute a HelloNode,
then the SubWorkflow, then another HelloNode.

So let's execute it now.

::

	$topWorkflow->execute(array("Message" => "World"));
	
If we assume SubWorkflow is comprised of a chain of 2 HelloNodes, then "Hello World!"
will be printed a total of 4 times.

The final code should look like:

.. code-block:: php

	<?php

	use F2Dev\MiniFlow;
	
	use MyApp\Nodes as Nodes;
	use MyApp\Links as Links;
	use MyApp\Workflows as Workflows;
	
	$startNode = new Nodes\HelloNode();
	
	$linkS = new Links\RandomLink($startNode);
	
	$topWorkflow = MiniFlow\MiniFlow("Top Workflow", $startNode);
	
	$flowS1 = Workflows\SubWorkflow::getWorkflow();
	
	$flowS1->setParent($linkS);
	
	$linkS1 = new Links\RandomLink($flowS1);
	
	$nodeS11 = new Nodes\HelloNode($linkS1);
	
	$topWorkflow->execute(array("Message" => "World"));
	
There are a couple of things to keep in mind when using sub-workflows, though.

Firstly, you may have noticed that the order in which Workflows are declared doesn't really matter.
So long as you, in your definition of the workflow, do not reference an object which you have not
declared, the order of Node, Link, and Workflow creation does not matter.

This is because nothing is parsed until the workflow is executed. So long as the definition
is finished before the execute() call, it will work without issue.

Secondly, and more importantly, any workflows called by another workflow will share the same
`FDEA`_. This makes it possible to share data between workflows, but also may run the risk
of data collisions if you are not careful. For example, if you have two workflows that both
use the variable Foo, but for different purposes. If workflow A calls workflow B, and workflow
B changes the variable Foo, this may cause unexpected behavior in A once B returns.

For this reason you should create a variable naming scheme to prevent such data collisions.


.. _Factory: `Creating a Factory`_