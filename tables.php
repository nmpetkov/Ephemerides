<?php
/**
 * Zikula Application Framework
 * @copyright  (c) Zikula Development Team
 * @license    GNU/GPL
 *
 * Get Ephemerides table array
 * @return       array with table information.
*/
function Ephemerides_tables()
{
	// initialise table array
	$tables = array();

	// full table definition
	$tables['ephem'] = 'ephem';
	$tables['ephem_column'] = array ('eid'       => 'eid',
                                      'did'       => 'did',
                                      'mid'       => 'mid',
                                      'yid'       => 'yid',
                                      'content'   => 'content',
                                      'language'  => 'language',
                                      'status'    => 'status',
                                      'type'      => 'type');
	$tables['ephem_column_def'] = array('eid'      => 'I NOTNULL AUTO PRIMARY',
									 'did'      => "I1 NOTNULL DEFAULT '0'",
									 'mid'      => "I1 NOTNULL DEFAULT '0'",
									 'yid'      => "I2 NOTNULL DEFAULT '0'",
									 'content'  => 'X NOTNULL',
									 'language' => "C(30) NOTNULL DEFAULT ''",
									 'status'   => "I1 DEFAULT '1'",
									 'type'     => "I1 DEFAULT '1'");

	// enable categorization services
	$tables['ephem_db_extra_enable_categorization'] = ModUtil::getVar('Ephemerides', 'enablecategorization', true);
	$tables['ephem_primary_key_column'] = 'eid';

	// add standard data fields
	ObjectUtil::addStandardFieldsToTableDefinition($tables['ephem_column'], '');
	ObjectUtil::addStandardFieldsToTableDataDefinition($tables['ephem_column_def']);

	// return table information
	return $tables;
}
