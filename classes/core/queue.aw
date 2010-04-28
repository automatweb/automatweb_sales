<?php

namespace automatweb;

class queue
{
	/** Queue constructor. Resets internal arrays and counters.

		@attrib name=queue params=pos api=1

		@comment
			Class that implements the FIFO data structure also known as queue
		@examples
		$q = get_instance('core/queue');
		$q->push('foo');
		$q->push('bar');
		$q->count(); // prints '2'
		echo $q->get(); // prints 'foo'
		var_dump($q->has_more()); // prints 'bool(true)'
		echo $q->count(); // prints '1'
		echo $q->get(); // prints 'bar'
		var_dump($q->has_more()); // prints 'bool(false)'
	**/
	function queue()
	{
		$this->q = array();
		$this->index = array();
		$this->items = 0;
	}


	/** Adds new item into the queue
		@attrib name=push params=pos api=1

		@param item required type=string
			Item to add in the queue

		@examples
			#core/queue::queue

	**/
	function push($item)
	{
		$this->q[] = $item;
		$this->index[$item] = 1;
		$this->items++;
	}

	/** Gets the item from the queue which waas added first
		@attrib name=get params=pos api=1

		@returns
			The first added item from the queue
		@examples
			#core/queue::queue
	**/
	function get()
	{
		$this->items--;
		$ret = array_shift($this->q);
		unset($this->index[$ret]);
		return $ret;
	}

	/** Checks if there is items in the queue or not
		@attrib name=has_more params=pos api=1

		@returns
			Boolean true, the queue is not empty, false othervise
		@examples
			#core/queue::queue
	**/
	function has_more()
	{
		return $this->items > 0;
	}

	/** Get the array with all the items in the queue
		@attrib name=get_all params=pos api=1

		@returns
			Array with all items in the queue
		@examples
			#core/queue::queue
	**/
	function get_all()
	{
		return $this->q;
	}

	/** Get the count of items in the queue
		@attrib name=count params=pos api=1

		@returns
			The count of the items in the queue
		@examples
			#core/queue::queue
	**/
	function count()
	{
		return $this->items;
	}

	/** Fill the queue with values of an array
		@attrib name=set_all params=pos api=1

		@param a required type=array
			An array with the items to put in the queue
		@comment
			Previous items in the queue will be removed
		@examples
			$q = get_instance('core/queue');
			$q->set_all(array('foo', 'bar', 'asd', 'blah'));
	**/
	function set_all($a)
	{
		$this->q = array_values(safe_array($a));
		$this->index = array_flip($this->q);
		$this->items = count($this->q);
	}

	/** Checks if a value exists in the queue or not
		@attrib name=contains params=pos api=1

		@param val required type=string
			Item to check if it exists in the queue or not
		@returns
			Boolean true if value exists, false othervise
		@examples
			$q = get_instance('core/queue');
			$q->set_all(array('foo', 'bar', 'asd', 'blah'));
			var_dump($q->contains('foo')); // prints 'bool(true)'
			var_dump($q->contains('fafa')); // prints 'bool(false)'
	**/
	function contains($val)
	{
		return isset($this->index[$val]);
	}
}

?>
