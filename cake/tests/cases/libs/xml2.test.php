<?php
App::import('Core', 'Xml');

class Xml2Test extends CakeTestCase {

	function setUp() {
		$manager =& new XmlManager();
		$manager->namespaces = array();
	}

	function testToArray2() {
		
		$sXml = 
		'<t1>
		 	<t2>A</t2>
      		<t2><t3>AAA</t3>B</t2>
	  		<t2>C</t2>
		</t1>';
		$xml = new Xml($sXml);
		$result = $xml->toArray();
		$expected = array(
			'T1' => array(
				'T2' => array(
					'A',
					array('t3' => 'AAA', 'value' => 'B'),
					'C'
					)
				)
			);
		$this->assertIdentical($result, $expected);
		$result = $xml->toArray(false);
		$expected = array(
			't1' => array(
				't2' => array(
					'A',
					array('t3' => 'AAA', 'value' => 'B'),
					'C'
					)
				)
			);
		$this->assertIdentical($result, $expected);
		
		$sXml = 
		'<t1>
		 	<t2>A</t2>
	  		<t2>B</t2>
      		<t2>
	         	<t3>CCC</t3>
	      	</t2>
		</t1>';
		$xml = new Xml($sXml);
		$result = $xml->toArray();
		$expected = array(
			'T1' => array(
				'T2' => array(
					'A',
					'B',
					array('t3' => 'CCC'),
					)
				)
			);
		$this->assertIdentical($result, $expected);
		$result = $xml->toArray(false);
		$expected = array(
			't1' => array(
				't2' => array(
					'A',
					'B',
					array('t3' => 'CCC'),
					)
				)
			);
		$this->assertIdentical($result, $expected);
		
		$sXml = 
		'<t1>
		 <t2>A</t2>
		 <t2></t2>
		 <t2>C</t2>
		</t1>';
		$xml = new Xml($sXml);
		$result = $xml->toArray();
		$expected = array(
			'T1' => array(
				'T2' => array(
					'A',
					array(),
					'C'
					)
				)
			);
		$this->assertIdentical($result, $expected);

		$result = $xml->toArray(false);
		$expected = array(
			't1' => array(
				't2' => array(
					'A',
					array(),
					'C'
					)
				)
			);
		$this->assertIdentical($result, $expected);

		$sXml = 
		'<t1>A<t2>B</t2></t1>';
		$xml = new Xml($sXml);
		$result = $xml->toArray();
		$expected = array(
			'T1' => array(
				'value' => 'A',
				't2' => 'B',
			)
		);
		$this->assertIdentical($result, $expected);

		$result = $xml->toArray(false);
		$expected = array(
			't1' => array(
				'value' => 'A',
				't2' => 'B',
			)
		);
		$this->assertIdentical($result, $expected);
		
		$sXml = 
		'<stuff>
    <foo name="abc-16" profile-id="Default" />
    <foo name="abc-17" profile-id="Default" >
        <bar id="HelloWorld" />
    </foo>
    <foo name="abc-asdf" profile-id="Default" />
    <foo name="cba-1A" profile-id="Default">
        <bar id="Baz" />
    </foo>
    <foo name="cba-2A" profile-id="Default">
        <bar id="Baz" />
    </foo>
    <foo name="qa" profile-id="Default" />
</stuff>';
		$xml = new Xml($sXml);
		$result = $xml->toArray();
		$expected = array(
			'Stuff' => array(
				'Foo' => array(
					array('name' => 'abc-16', 'profile-id' => 'Default'),
					array('name' => 'abc-17', 'profile-id' => 'Default', 
						'Bar' => array('id' => 'HelloWorld')),
					array('name' => 'abc-asdf', 'profile-id' => 'Default'),
					array('name' => 'cba-1A', 'profile-id' => 'Default', 
						'Bar' => array('id' => 'Baz')),
					array('name' => 'cba-2A', 'profile-id' => 'Default', 
						'Bar' => array('id' => 'Baz')),
					array('name' => 'qa', 'profile-id' => 'Default'),
					)
				)
			);
		$this->assertIdentical($result, $expected);
		$result = $xml->toArray(false);
		$expected = array(
			'stuff' => array(
				'foo' => array(
					array('name' => 'abc-16', 'profile-id' => 'Default'),
					array('name' => 'abc-17', 'profile-id' => 'Default', 
						'bar' => array('id' => 'HelloWorld')),
					array('name' => 'abc-asdf', 'profile-id' => 'Default'),
					array('name' => 'cba-1A', 'profile-id' => 'Default', 
						'bar' => array('id' => 'Baz')),
					array('name' => 'cba-2A', 'profile-id' => 'Default', 
						'bar' => array('id' => 'Baz')),
					array('name' => 'qa', 'profile-id' => 'Default'),
					)
				)
			);
		$this->assertIdentical($result, $expected);
		
		
		$sXml = 
		'<root>
  <node name="first" />
  <node name="second"><subnode name="first sub" /><subnode name="second sub" /></node>
  <node name="third" />
</root>';
		$xml = new Xml($sXml);
		$result = $xml->toArray();
		$expected = array(
			'Root' => array(
				'Node' => array(
					array('name' => 'first'),
					array('name' => 'second', 
						'Subnode' => array(
							array('name' => 'first sub'), 
							array('name' => 'second sub'))),
					array('name' => 'third'),
					)
				)
			);
		$this->assertIdentical($result, $expected);
		
		$result = $xml->toArray(false);
		$expected = array(
			'root' => array(
				'node' => array(
					array('name' => 'first'),
					array('name' => 'second', 
						'subnode' => array(
							array('name' => 'first sub'), 
							array('name' => 'second sub'))),
					array('name' => 'third'),
					)
				)
			);
		$this->assertIdentical($result, $expected);
		
	
	}
	
}
