<?php

require_once 'PHPUnit/Framework.php';

require_once dirname(__FILE__) . '/../../source/query.class.php';

/**
 * Test class for query.
 * Generated by PHPUnit on 2011-01-26 at 19:39:13.
 */
class queryTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var query
	 */
	protected $q;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->q = new query;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
		$this->q = null;
	}
	/**
	 * Test adding a single table to the query
	 */
	public function testAddTable(){
		$this->q->table('test');
		$this->assertEquals(array('table'=>'test', 'alias'=>null), $this->q->tables[0]);
	}
	public function testAddColumn(){
		$this->q->column('test');
		$expected = array(
			'column'=>'test',
			'alias'=>null
		);
		$this->assertEquals($expected, $this->q->columns[0]);
	}
	public function testNULLColumn(){
		$this->q->column(null);
		$expected = array(
			'column'=>'NULL',
			'alias'=>null
		);
		$this->assertEquals($expected, $this->q->columns[0]);
	}
	public function testFALSEColumn(){
		$this->q->column(false);
		$expected = array(
			'column'=>'FALSE',
			'alias'=>null
		);
		$this->assertEquals($expected, $this->q->columns[0]);
	}
	public function testTRUEColumn(){
		$this->q->column(true);
		$expected = array(
			'column'=>'TRUE',
			'alias'=>null
		);
		$this->assertEquals($expected, $this->q->columns[0]);
	}
	public function testMathColumn(){
		$this->q->column('1 = 1');
		$expected = array(
			'column'=>'1 = 1',
			'alias'=>null
		);
		$this->assertEquals($expected, $this->q->columns[0]);
	}
	public function testFunctionColumn(){
		$this->q->column('COUNT(*)');
		$expected = array(
			'column'=>'COUNT(*)',
			'alias'=>null
		);
		$this->assertEquals($expected, $this->q->columns[0]);
	}
	/**
	 * Test chaining of functions
	 */
	public function testChaining(){
		$this->q->table('test')
				->column('test_2', 'test')
				->table('test_3', 'test_2');
		
		$expected_tables = array(
			array(
				'table'=>'test',
				'alias'=>null
			),
			array(
				'table'=>'test_3',
				'alias'=>'test_2'
			)
		);
		$expected_columns = array(
			array(
				'column'=>'test_2',
				'alias'=>'test'
			)
		);
		$this->assertEquals($expected_tables, $this->q->tables);
		$this->assertEquals($expected_columns, $this->q->columns);
	}
	public function testFirstWhere(){
		$this->q->where('1', '1');
		$expected = array(
			'column'=>'1',
			'value'=>'1',
			'comparison'=>'=',
			'type'=>null,
			'escape'=>true,
		);
		$this->assertEquals($expected, $this->q->wheres[0]);
	}
	public function testClearWhere(){
		$this->q->where('1', '1')->where('2', '2');
		$expected = array(
			'column'=>'2',
			'value'=>'2',
			'comparison'=>'=',
			'type'=>null,
			'escape'=>true,
		);
		$this->assertEquals($expected, $this->q->wheres[0]);
	}
	public function testAndWhere(){
		$this->q->where('1', '1')->and_where('2', '2');
		$expected =  array(
			array(
				'column'=>'1',
				'value'=>'1',
				'comparison'=>'=',
				'type'=>null,
				'escape'=>true,
			),
			array(
				'column'=>'2',
				'value'=>'2',
				'comparison'=>'=',
				'type'=>'AND',
				'escape'=>true,
			)
		);
		$this->assertEquals($expected, $this->q->wheres);
	}
	public function testOrWhere(){
		$this->q->where('1', '1')->or_where('2', '2');
		$expected =  array(
			array(
				'column'=>'1',
				'value'=>'1',
				'comparison'=>'=',
				'type'=>null,
				'escape'=>true,
			),
			array(
				'column'=>'2',
				'value'=>'2',
				'comparison'=>'=',
				'type'=>'OR',
				'escape'=>true,
			)
		);
		$this->assertEquals($expected, $this->q->wheres);
	}
	public function testAndWhereOrWhere(){
		$this->q->where('1', '1')
				->and_where(true, true)
				->and_where(null, null, 'iS')
				->or_where('2', '2', '=', false);
		$expected =  array(
			array(
				'column'=>'1',
				'value'=>'1',
				'comparison'=>'=',
				'type'=>null,
				'escape'=>true,
			),
			array(
				'column'=>'TRUE',
				'value'=>'TRUE',
				'comparison'=>'=',
				'type'=>'AND',
				'escape'=>false,
			),
			array(
				'column'=>'NULL',
				'value'=>'NULL',
				'comparison'=>'IS',
				'type'=>'AND',
				'escape'=>false,
			),
			array(
				'column'=>'2',
				'value'=>'2',
				'comparison'=>'=',
				'type'=>'OR',
				'escape'=>false,
			)
		);
		$this->assertEquals($expected, $this->q->wheres);
	}
	public function testBrackets(){
		$this->q->where('1', '1')
				->begin_and()
				->and_where(true, true)
				->begin_or()
				->and_where(null, null, 'iS')
				->end_or()
				->end_and()
				->or_where('2', '2', '=', false);
		$expected =  array(
			array(
				'column'=>'1',
				'value'=>'1',
				'comparison'=>'=',
				'type'=>null,
				'escape'=>true,
			),
			array(
				'bracket'=>'OPEN',
				'type'=>'AND'
			),
			array(
				'column'=>'TRUE',
				'value'=>'TRUE',
				'comparison'=>'=',
				'type'=>'AND',
				'escape'=>false,
			),
			array(
				'bracket'=>'OPEN',
				'type'=>'OR'
			),
			array(
				'column'=>'NULL',
				'value'=>'NULL',
				'comparison'=>'IS',
				'type'=>'AND',
				'escape'=>false,
			),
			array(
				'bracket'=>'CLOSE',
			),
			array(
				'bracket'=>'CLOSE',
			),
			array(
				'column'=>'2',
				'value'=>'2',
				'comparison'=>'=',
				'type'=>'OR',
				'escape'=>false,
			)
		);
		$this->assertEquals($expected, $this->q->wheres);
	}
	public function testBuildSelect(){
		$this->q->column('column')
				->column('column')
				->table('table')
				->where(true, true);
		$expected = 'SELECT column, column FROM table WHERE TRUE = TRUE';
		$this->assertEquals($expected,$this->q->build_select());
	}
	public function testBuildSelectWithTableAlias(){
		$this->q->column('column', 'col')
				->column('column', 'pink')
				->table('table', 'Table')
				->where(true, 1);
		$expected = "SELECT column AS col, column AS pink FROM table AS Table WHERE TRUE = '1'";
		$this->assertEquals($expected,$this->q->build_select());
	}
	public function testSelectStar(){
		$this->q->table('table', 'Table')
				->where(true, 'true');
		$expected = "SELECT * FROM table AS Table WHERE TRUE = 'true'";
		$this->assertEquals($expected,$this->q->build_select());
	}
	public function testWhereGroup(){
		$this->q->table('table', 'Table')
				->begin_and()
				->and_where(true, 'true')
				->end_and();
		$expected = "SELECT * FROM table AS Table WHERE ( TRUE = 'true' )";
		$this->assertEquals($expected,$this->q->build_select());
	}
	public function testMultiWhereGroup(){
		$this->q->table('table', 'Table')
				->begin_and()
				->and_where(true, 'true')
				->or_where('2', false)
				->end_and();
		$expected = "SELECT * FROM table AS Table WHERE ( TRUE = 'true' OR 2 = FALSE )";
		$this->assertEquals($expected,$this->q->build_select());
	}

	public function testMultiWhere(){
		$this->q->table('table')
				->begin_and()
				->and_where('col_1', 1)
				->or_where('col_2', 2)
				->end_and()
				->or_where('col_3', 3, '!=');
		$expected = "SELECT * FROM table WHERE ( col_1 = '1' OR col_2 = '2' ) OR col_3 != '3'";
		$this->assertEquals($expected,$this->q->build_select());
	}

	public function testMultiWhereGrouping(){
		$this->q->table('table')
				->begin_and()
				->begin_and()
				->and_where('col_1', 1)
				->or_where('col_2', 2)
				->end_and()
				->end_and()
				->or_where('col_3', 3, '!=');
		$expected = "SELECT * FROM table WHERE ( ( col_1 = '1' OR col_2 = '2' ) ) OR col_3 != '3'";
		$this->assertEquals($expected,$this->q->build_select());
	}

	public function testJoin(){
		$this->q->table('table')
				->join('table_2', 'table.id = table_2.id');
		$expected = array(
			'table'=>'table_2',
			'conditions'=>'table.id = table_2.id',
			'type'=>'JOIN',
		);
		$this->assertEquals($expected, $this->q->joins[0]);
	}

	public function testRightJoin(){
		$this->q->table('table')
				->right_join('table_2', 'table.id = table_2.id');
		$expected = array(
			'table'=>'table_2',
			'conditions'=>'table.id = table_2.id',
			'type'=>'RIGHT JOIN',
		);
		$this->assertEquals($expected, $this->q->joins[0]);
	}

	public function testLeftJoin(){
		$this->q->table('table')
				->left_join('table_2', 'table.id = table_2.id');
		$expected = array(
			'table'=>'table_2',
			'conditions'=>'table.id = table_2.id',
			'type'=>'LEFT JOIN',
		);
		$this->assertEquals($expected, $this->q->joins[0]);
	}

	public function testStraifghtJoin(){
		$this->q->table('table')
				->straight_join('table_2', 'table.id = table_2.id');
		$expected = array(
			'table'=>'table_2',
			'conditions'=>'table.id = table_2.id',
			'type'=>'STRAIGHT JOIN',
		);
		$this->assertEquals($expected, $this->q->joins[0]);
	}

	public function testInnerJoin(){
		$this->q->table('table')
				->inner_join('table_2', 'table.id = table_2.id');
		$expected = array(
			'table'=>'table_2',
			'conditions'=>'table.id = table_2.id',
			'type'=>'INNER JOIN',
		);
		$this->assertEquals($expected, $this->q->joins[0]);
	}

	public function testCrossJoin(){
		$this->q->table('table')
				->cross_join('table_2', 'table.id = table_2.id');
		$expected = array(
			'table'=>'table_2',
			'conditions'=>'table.id = table_2.id',
			'type'=>'CROSS JOIN',
		);
		$this->assertEquals($expected, $this->q->joins[0]);
	}

	public function testMultipleJoins(){
		$this->q->table('table')
				->join('join_table', 'id')
				->right_join('right_table', 'id')
				->left_join('left_table', 'id')
				->inner_join('inner_table', 'id')
				->straight_join('straight_table', 'id')
				->cross_join('cross_table', 'id');

		//make sure there are 6 joins
		$this->assertEquals(6, count($this->q->joins));

		//make sure they are in the right order
		$this->assertEquals('JOIN', $this->q->joins[0]['type']);
		$this->assertEquals('RIGHT JOIN', $this->q->joins[1]['type']);
		$this->assertEquals('LEFT JOIN', $this->q->joins[2]['type']);
		$this->assertEquals('INNER JOIN', $this->q->joins[3]['type']);
		$this->assertEquals('STRAIGHT JOIN', $this->q->joins[4]['type']);
		$this->assertEquals('CROSS JOIN', $this->q->joins[5]['type']);
	}
}