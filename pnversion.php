<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2002, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: pnversion.php 359 2009-11-22 13:23:49Z mateo $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Zikula_Value_Addons
 * @subpackage Ephemerids
 */

// get transation domainEphemerids
$dom = ZLanguage::getModuleDomain('Ephemerids');

$modversion['name']           = 'Ephemerids';
$modversion['displayname']    = __('Ephemerids', $dom);
$modversion['description']    = __('Provides a block displaying an information byte (historical event, thought for the day, etc.) linked to the day\'s date, with daily roll-over, and incorporates an interface for adding, editing and maintaining ephemerids.', $dom);
//! module name that appears in URL
$modversion['url']            = __('ephemerids', $dom);
$modversion['version']        = '1.8';

$modversion['credits']        = '';
$modversion['help']           = '';
$modversion['changelog']      = '';
$modversion['license']        = '';
$modversion['official']       = 1;
$modversion['author']         = 'Mark West';
$modversion['contact']        = 'http://www.markwest.me.uk';

$modversion['securityschema'] = array('Ephemerids::' => '::Ephemerid ID');
