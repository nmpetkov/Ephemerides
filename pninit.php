<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2002, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: pninit.php 420 2010-06-14 04:56:34Z drak $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Zikula_Value_Addons
 * @subpackage Ephemerids
 */

/**
 * Initialise ephemerids module
 * @author Xiaoyu Huang
 * @return bool true on success, false on failiure
 */
function Ephemerids_init()
{
    if (!DBUtil::createTable('ephem')) {
        return false;
    }

    // Set up config variables
    ModUtil::setVar('Ephemerids', 'itemsperpage', 25);

    // Initialisation successful
    return true;
}

/**
 * upgrade the module from an old version
 *
 * This function must consider all the released versions of the module!
 * If the upgrade fails at some point, it returns the last upgraded version.
 *
 * @author       Xiaoyu Huang
 * @param        string   $oldVersion   version number string to upgrade from
 * @return       mixed    true on success, last valid version string or false if fails
 */
function Ephemerids_upgrade($oldversion)
{
    if (!DBUtil::changeTable('ephem')) {
        return false;
    }

    // Upgrade dependent on old version number
    switch ($oldversion)
    {
        case '1.2':
            // version 1.2 shipped with postnuke .72x/.75
            ModUtil::setVar('Ephemerids', 'itemsperpage', 25);

        case '1.6':
            ephemerids_upgrade_updateEphemeridsLanguages();

        case '1.7':
            // needs update of table, added status column (see in front of this function)
            // DBUtil::changeTable('ephem')

        case '1.8':
            // future upgrade routines
    }

    // upgrade success
    return true;
}

/**
 * delete ephemerids module
 * @author Xiaoyu Huang
 * @return bool true on success, false on failiure
*/
function Ephemerids_delete()
{
    if (!DBUtil::dropTable('ephem')) {
        return false;
    }

    // Delete module variables
    ModUtil::delVar('Ephemerids');

    // Deletion successful
    return true;
}

function ephemerids_upgrade_updateEphemeridsLanguages()
{
    $obj = DBUtil::selectObjectArray('ephem');

    if (count($obj) == 0) {
        // nothing to do
        return;
    }

    foreach ($obj as $ephemerid) {
        // translate l3 -> l2
        if ($l2 = ZLanguage::translateLegacyCode($ephemerid['language'])) {
            $ephemerid['language'] = $l2;
        }
        DBUtil::updateObject($ephemerid, 'ephem', '', 'eid', true);
    }

    return true;
}
