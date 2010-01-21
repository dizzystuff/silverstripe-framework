<?php

/*
 * A hierarchy of data types, to 
 */
class AggregateTest_Foo extends DataObject implements TestOnly {
	static $db = array(
		"Foo" => "Int"
	);
	
	static $has_one = array('Bar' => 'AggregateTest_Bar');
	static $belongs_many_many = array('Bazi' => 'AggregateTest_Baz');
}

class AggregateTest_Fab extends AggregateTest_Foo {
	static $db = array(
		"Fab" => "Int"
	);
}

class AggregateTest_Fac extends AggregateTest_Fab {
	static $db = array(
		"Fac" => "Int"
	);
}

class AggregateTest_Bar extends DataObject implements TestOnly {
	static $db = array(
		"Bar" => "Int"
	);
	
	static $has_many = array(
		"Foos" => "AggregateTest_Foo"
	);
}

class AggregateTest_Baz extends DataObject implements TestOnly {
	static $db = array(
		"Baz" => "Int"
	);
	
	static $many_many = array(
		"Foos" => "AggregateTest_Foo"
	);
}

class AggregateTest extends SapphireTest {
	static $fixture_file = 'sapphire/tests/model/AggregateTest.yml';
	
	protected $extraDataObjects = array(
		'AggregateTest_Foo',
		'AggregateTest_Fab',
		'AggregateTest_Fac',
		'AggregateTest_Bar',
		'AggregateTest_Baz'
	);
	
	/**
	 * Test basic aggregation on a passed type
	 */
	function testTypeSpecifiedAggregate() {
		// Template style access
		$this->assertEquals(DataObject::Aggregate('AggregateTest_Foo')->XML_val('Max', array('Foo')), 9);
		$this->assertEquals(DataObject::Aggregate('AggregateTest_Fab')->XML_val('Max', array('Fab')), 3);

		// PHP style access
		$this->assertEquals(DataObject::Aggregate('AggregateTest_Foo')->Max('Foo'), 9);
		$this->assertEquals(DataObject::Aggregate('AggregateTest_Fab')->Max('Fab'), 3);
	}
	/* */
	
	/**
	 * Test basic aggregation on a given dataobject
	 * @return unknown_type
	 */
	function testAutoTypeAggregate() {
		$foo = $this->objFromFixture('AggregateTest_Foo', 'foo1');
		$fab = $this->objFromFixture('AggregateTest_Fab', 'fab1');
		
		// Template style access
		$this->assertEquals($foo->Aggregate()->XML_val('Max', array('Foo')), 9);
		$this->assertEquals($fab->Aggregate()->XML_val('Max', array('Fab')), 3);

		// PHP style access
		$this->assertEquals($foo->Aggregate()->Max('Foo'), 9);
		$this->assertEquals($fab->Aggregate()->Max('Fab'), 3);
	}
	/* */
	
	/**
	 * Test aggregation takes place on the passed type & it's children only
	 */
	function testChildAggregate() {
		
		// For base classes, aggregate is calculcated on it and all children classes
		$this->assertEquals(DataObject::Aggregate('AggregateTest_Foo')->Max('Foo'), 9);

		// For subclasses, aggregate is calculated for that subclass and it's children only
		$this->assertEquals(DataObject::Aggregate('AggregateTest_Fab')->Max('Foo'), 9);
		$this->assertEquals(DataObject::Aggregate('AggregateTest_Fac')->Max('Foo'), 6);
		
	}
	/* */

	/**
	 * Test aggregates are cached properly
	 */
	function testCache() {
		
	}
	/* */
	
	/**
	 * Test cache is correctly flushed on write
	 */
	function testCacheFlushing() {
		
		// For base classes, aggregate is calculcated on it and all children classes
		$this->assertEquals(DataObject::Aggregate('AggregateTest_Foo')->Max('Foo'), 9);

		// For subclasses, aggregate is calculated for that subclass and it's children only
		$this->assertEquals(DataObject::Aggregate('AggregateTest_Fab')->Max('Foo'), 9);
		$this->assertEquals(DataObject::Aggregate('AggregateTest_Fac')->Max('Foo'), 6);
		
		$foo = $this->objFromFixture('AggregateTest_Foo', 'foo1');
		$foo->Foo = 12;
		$foo->write();

		// For base classes, aggregate is calculcated on it and all children classes
		$this->assertEquals(DataObject::Aggregate('AggregateTest_Foo')->Max('Foo'), 12);

		// For subclasses, aggregate is calculated for that subclass and it's children only
		$this->assertEquals(DataObject::Aggregate('AggregateTest_Fab')->Max('Foo'), 9);
		$this->assertEquals(DataObject::Aggregate('AggregateTest_Fac')->Max('Foo'), 6);
				
		$fab = $this->objFromFixture('AggregateTest_Fab', 'fab1');
		$fab->Foo = 15;
		$fab->write();
		
		// For base classes, aggregate is calculcated on it and all children classes
		$this->assertEquals(DataObject::Aggregate('AggregateTest_Foo')->Max('Foo'), 15);

		// For subclasses, aggregate is calculated for that subclass and it's children only
		$this->assertEquals(DataObject::Aggregate('AggregateTest_Fab')->Max('Foo'), 15);
		$this->assertEquals(DataObject::Aggregate('AggregateTest_Fac')->Max('Foo'), 6);
	}
	/* */
	
	/**
	 * Test basic relationship aggregation
	 */
	function testRelationshipAggregate() {
		$bar1 = $this->objFromFixture('AggregateTest_Bar', 'bar1');
		$this->assertEquals($bar1->RelationshipAggregate('Foos')->Max('Foo'), 8);

		$baz1 = $this->objFromFixture('AggregateTest_Baz', 'baz1');
		$this->assertEquals($baz1->RelationshipAggregate('Foos')->Max('Foo'), 8);
	}
	/* */
	
}
