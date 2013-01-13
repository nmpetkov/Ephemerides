<?php
/**
 * Zikula Application Framework
 * @copyright  (c) Zikula Development Team
 * @license    GNU/GPL
 * @category   Zikula_3rdParty_Modules
 * @package    Content_Management
 * @subpackage Ephemerides
 */
class Ephemerids_Version extends Zikula_AbstractVersion
{
    public function getMetaData() {
        $meta = array();
        $meta['displayname'] = $this->__('Ephemerides publisher');
        $meta['description'] = $this->__('Provides a block displaying an information (historical event, thought for the day, etc.) linked to the day\'s date, with daily roll-over, and incorporates an interface for adding, editing and maintaining ephemerides.');
        $meta['version'] = '3.0.0';
        $meta['url'] = $this->__('ephem');
        $meta['core_min'] = '1.3.0'; // requires minimum 1.3.0 or later
        $meta['securityschema'] = array('Ephemerids::' => '::Ephemerid ID');
        return $meta;
    }
}
