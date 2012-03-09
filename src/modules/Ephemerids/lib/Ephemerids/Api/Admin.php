<?php
/**
 * Zikula Application Framework
 * @copyright  (c) Zikula Development Team
 * @license    GNU/GPL
 * @category   Zikula_3rdParty_Modules
 * @package    Content_Management
 * @subpackage Ephemerides
 */
 
class Ephemerids_Api_Admin extends Zikula_AbstractApi
{
	/**
	 * Create a new Ephemeride item
	 * @author The Zikula Development Team
	 * @param 'status' item status
	 * @return id of item if success, false otherwise
	 */
    public function create($ephemeride)
    {
		// Argument check
		if ((!isset($ephemeride['did'])) ||
			(!isset($ephemeride['mid'])) ||
			(!isset($ephemeride['yid'])) ||
			(!isset($ephemeride['content'])) ||
			(!isset($ephemeride['language']))) {
			return LogUtil::registerArgsError();
		}
		if (!isset($ephemeride['status'])) $ephemeride['status'] = 1;
		if (!isset($ephemeride['type'])) $ephemeride['type'] = 1;

        // security check
        if (!SecurityUtil::checkPermission('Ephemerids::', '::', ACCESS_ADD)) {
            return LogUtil::registerPermissionError();
        }

        // insert the object and check the return value for error
        $res = DBUtil::insertObject($ephemeride, 'ephem', 'eid');
        if (!$res) {
            return LogUtil::registerError($this->__('Error! Ephemeride creation failed.'));
        }

        // return the id of the newly created item to the calling process
        return $ephemeride['eid'];
    }

    /**
     * Delete Ephemeride
     * @author The Zikula Development Team
     * @param 'eid' the id of the ephemerid
     * @return true if success, false otherwise
     */
    public function delete($args)
    {
        // argument check
        if (!isset($args['eid']) || !is_numeric($args['eid'])) {
            return LogUtil::registerArgsError();
        }

        // get the existing item
        $item = ModUtil::apiFunc('Ephemerids', 'user', 'get', array('eid' => $args['eid']));
        if (!$item) {
            return LogUtil::registerError($this->__('No such Ephemeride found.'));
        }

        // delete the item and check the return value for error
        $res = DBUtil::deleteObjectByID('ephem', $args['eid'], 'eid');
        if (!$res) {
            return LogUtil::registerError($this->__('Error! Ephemeride deletion failed.'));
        }

        // delete any object category mappings for this item
        ObjectUtil::deleteObjectCategories($item, 'ephem', 'eid');

        return true;
    }

    /**
     * Update Ephemeride
     * @author The Zikula Development Team
     * @param 'args['eid']' item ID
     * @return true if success, false otherwise
     */
    public function update($args)
    {
		// Argument check
		if ((!isset($args['eid'])) ||
			(!isset($args['did'])) ||
			(!isset($args['mid'])) ||
			(!isset($args['yid'])) ||
			(!isset($args['content'])) ||
			(!isset($args['language']))) {
			return LogUtil::registerArgsError();
		}
		if (!isset($args['status'])) $args['status'] = 1;
		if (!isset($args['type'])) $args['type'] = 1;

        // get the existing args
        $item = ModUtil::apiFunc('Ephemerids', 'user', 'get', array('eid' => $args['eid']));
        if (!$item) {
            return LogUtil::registerError($this->__('No such Ephemeride found.'));
        }

        // security check(s)
        // check permissions for both the original and modified ephemerids
		if (!SecurityUtil::checkPermission('Ephemerids::', "::$args[eid]", ACCESS_EDIT)) {
			return LogUtil::registerPermissionError();
		}

        // update the args and check return value for error
        $res = DBUtil::updateObject($args, 'ephem', '', 'eid');
        if (!$res) {
            return LogUtil::registerError($this->__('Error! Ephemeride update failed.'));
        }

        return true;
    }

    /**
     * get available admin panel links
     *
     * @return array array of admin links
     */
    public function getlinks()
    {
        $links = array();

        if (SecurityUtil::checkPermission('Ephemerids::', '::', ACCESS_EDIT)) {
            $links[] = array('url' => ModUtil::url('Ephemerids', 'admin', 'view'), 'text' => $this->__('Ephemerides list'), 'class' => 'z-icon-es-view');
        }
        if (SecurityUtil::checkPermission('Ephemerids::', '::', ACCESS_ADD)) {
            $links[] = array('url' => ModUtil::url('Ephemerids', 'admin', 'newitem'), 'text' => $this->__('Create ephemeride'), 'class' => 'z-icon-es-new');
        }
        if (SecurityUtil::checkPermission('Ephemerids::', '::', ACCESS_ADMIN)) {
            $links[] = array('url' => ModUtil::url('Ephemerids', 'admin', 'modifyconfig'), 'text' => $this->__('Settings'), 'class' => 'z-icon-es-config');
        }

        return $links;
    }
}
