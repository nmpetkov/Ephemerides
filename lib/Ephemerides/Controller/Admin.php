<?php
/**
 * Zikula Application Framework
 * @copyright  (c) Zikula Development Team
 * @license    GNU/GPL
 * @category   Zikula_3rdParty_Modules
 * @package    Content_Management
 * @subpackage Ephemerides
*/

class Ephemerides_Controller_Admin extends Zikula_AbstractController
{
    /**
     * Ephemerides main administration function
     * @author The Zikula Development Team
     * @return string HTML string
     */
    public function main()
    {
        // security check
        if (!SecurityUtil::checkPermission($this->name.'::', '::', ACCESS_EDIT)) {
            return LogUtil::registerPermissionError();
        }

        $this->view->setCaching(false);        

        // return the output that has been generated by this function
        return $this->view->fetch('ephemerides_admin_main.tpl');
    }

    /**
     * Display form to create a new item
     * @author The Zikula Development Team
     * @return string HTML string
     */
    public function newitem()
    {
        // security check
        if (!SecurityUtil::checkPermission($this->name.'::', '::', ACCESS_ADD)) {
            return LogUtil::registerPermissionError();
        }

        // get all module vars
        $modvars = ModUtil::getVar($this->name);

        $this->view->setCaching(false);

        if ($modvars['enablecategorization']) {
            $catregistry = CategoryRegistryUtil::getRegisteredModuleCategories($this->name, 'ephem');
            $this->view->assign('catregistry', $catregistry);
        }
		$this->view->assign('language', ZLanguage::getLanguageCode());
		$this->view->assign('status', 1);
		$this->view->assign('type', 1);

        // assign the module vars
        $this->view->assign($modvars);

        return $this->view->fetch('ephemerides_admin_new.tpl');
    }

    /**
     * Display full list of ephemerides
     * @author The Zikula Development Team
     * @return string HTML string
     */
    public function view($args)
    {
        // security check
        if (!(SecurityUtil::checkPermission($this->name.'::', '::', ACCESS_EDIT))) {
            return LogUtil::registerPermissionError();
        }
        // Get parameters from whatever input we need.
        $startnum = FormUtil::getPassedValue('startnum', isset($args['startnum']) ? $args['startnum'] : null, 'GET');
        $keyword  = FormUtil::getPassedValue('ephemerides_keyword', isset($args['ephemerides_keyword']) ? $args['ephemerides_keyword'] : '', 'POST');
        $keyword_GET  = FormUtil::getPassedValue('keyword', isset($args['keyword']) ? $args['keyword'] : '', 'GET');
		if ($keyword_GET) $keyword = $keyword_GET;
        $property = FormUtil::getPassedValue('ephemerides_property', isset($args['ephemerides_property']) ? $args['ephemerides_property'] : null, 'POST');
        $property_GET = FormUtil::getPassedValue("property", isset($args["property"]) ? $args["property"] : null, 'GET');
		if ($property_GET) $property = $property_GET;
        $category = FormUtil::getPassedValue("ephemerides_{$property}_category", isset($args["ephemerides_{$property}_category"]) ? $args["ephemerides_{$property}_category"] : null, 'POST');
        $category_GET = FormUtil::getPassedValue("category", isset($args["category"]) ? $args["category"] : null, 'GET');
		if ($category_GET) $category = $category_GET;
        $clear    = FormUtil::getPassedValue('clear', false, 'POST');
        if ($clear) {
            $property = $category = $keyword = null;
        }
        $sort = FormUtil::getPassedValue('sort', '', 'GET');
        $sortdir = FormUtil::getPassedValue('sortdir', '', 'GET');
        if ($sortdir != 'ASC' && $sortdir != 'DESC') {
                $sortdir = 'ASC';
        }

        // get all module vars
        $modvars = ModUtil::getVar($this->name);

        if ($modvars['enablecategorization']) {
            $catregistry  = CategoryRegistryUtil::getRegisteredModuleCategories($this->name, 'ephem');
            $properties = array_keys($catregistry);

            // validate and build the category filter - mateo
            if (!empty($property) && in_array($property, $properties) && !empty($category)) {
                $catFilter = array($property => $category);
            }

            // assign a default property - mateo
            if (empty($property) || !in_array($property, $properties)) {
                $property = $properties[0];
            }

            // plan ahead for ML features
            $propArray = array();
            foreach ($properties as $prop) {
                $propArray[$prop] = $prop;
            }
        }

		$filter = array('startnum' => $startnum, 'sort' => $sort, 'sortdir' => $sortdir,
                'numitems' => $modvars['itemsperpage'],
                'keyword'  => $keyword,
                'category' => isset($catFilter) ? $catFilter : null,
                'catregistry'  => isset($catregistry) ? $catregistry : null);
		
       // get the matching ephemerides
        $ephemerides = ModUtil::apiFunc($this->name, 'user', 'getall', $filter);

        $items = array();
        foreach ($ephemerides as $key => $item)
        {
            // options for the item
            $options = array();
            $ephemerides[$key]['options'][] = array('url'   => ModUtil::url($this->name, 'user', 'display', array('eid' => $item['eid'])),
                    'image' => 'demo.png',
                    'title' => $this->__('View'));
            if (SecurityUtil::checkPermission($this->name.'::', "::".$item['eid'], ACCESS_EDIT)) {
                $ephemerides[$key]['options'][] = array('url'   => ModUtil::url($this->name, 'admin', 'modify', array('eid' => $item['eid'])),
                        'image' => 'xedit.png',
                        'title' => $this->__('Edit'));

                if (SecurityUtil::checkPermission($this->name.'::', "::".$item['eid'], ACCESS_DELETE)) {
                    $ephemerides[$key]['options'][] = array('url'   => ModUtil::url($this->name, 'admin', 'delete', array('eid' => $item['eid'])),
                            'image' => '14_layer_deletelayer.png',
                            'title' => $this->__('Delete'));
                }
            }

			/*if ($item['yid'] < 1970) {
				$item['yid'] = 1970;
			}*/
			//$ephemerides[$key]['datetime'] = DateUtil::formatDatetime(mktime(0, 0, 0, $item['mid'], $item['did'], $item['yid']), 'datelong');
			// bug corrected, NP
			$ephemerides[$key]['datetime'] = DateUtil::formatDatetime(mktime(12, 0, 0, $item['mid'], $item['did'], $item['yid']), 'datelong');
 
			$items[] = $ephemerides[$key];
        }
		
        $this->view->setCaching(false);

        // assign the default language
        $this->view->assign('lang', ZLanguage::getLanguageCode());

        // assign the items and modvars to the template
        $this->view->assign('ephemerides', $items);
        $this->view->assign($modvars);

        // add the current filters
        $this->view->assign('ephemerides_keyword', $keyword);
        $this->view->assign('sort', $sort);
        $this->view->assign('sortdir', $sortdir);
		$this->view->assign('filter_active', (empty($keyword) && empty($category)) ? false : true);

        // assign the categories information if enabled
        if ($modvars['enablecategorization']) {
            $this->view->assign('catregistry', $catregistry);
            $this->view->assign('numproperties', count($propArray));
            $this->view->assign('properties', $propArray);
            $this->view->assign('property', $property);
            $this->view->assign("category", $category);
        } else {
            $this->view->assign('property', '');
            $this->view->assign("category", '');
        }

        // assign the values for the smarty plugin to produce a pager
        $this->view->assign('pager', array('itemsperpage' => $modvars['itemsperpage'],
                'numitems' => ModUtil::apiFunc($this->name, 'user', 'countitems',
                array('keyword'  => $keyword,
                'category' => isset($catFilter) ? $catFilter : null))));

        return $this->view->fetch('ephemerides_admin_view.tpl');
    }

    /**
     * Edit Ephemeride
     * @author The Zikula Development Team
     * @param 'eid' Ephemeride id to delete
     * @param 'confirm' Delete confirmation
     * @return mixed HTML string if confirm is null, true otherwise
     */
    public function modify($args)
    {
        // get parameters from whatever input we need.
        $eid = FormUtil::getPassedValue('eid', isset($args['eid']) ? $args['eid'] : null, 'GET');
        $objectid = FormUtil::getPassedValue('objectid', isset($args['objectid']) ? $args['objectid'] : null, 'GET');
        $delcache = FormUtil::getPassedValue('delcache', isset($args['delcache']) ? $args['delcache'] : null, 'GET');

        // check to see if we have been passed $objectid, the generic item identifier.
        if (!empty($objectid)) {
            $eid = $objectid;
        }

        // get the item
        $item = ModUtil::apiFunc($this->name, 'user', 'get', array('eid' => $eid));
        if (!$item) {
            return DataUtil::formatForDisplayHTML($this->__('No such Ephemeride found.'));
        }
		// calulate date for use in template
		$item['date'] = $item['yid'] . '-' . $item['mid'] . '-' . $item['did'];

        // security check
        if (!SecurityUtil::checkPermission($this->name.'::', "::$eid", ACCESS_EDIT)) {
            return LogUtil::registerPermissionError();
        }

        // get all module vars
        $modvars = ModUtil::getVar($this->name);

        if ($modvars['enablecategorization']) {
            // load the category registry util
            $catregistry = CategoryRegistryUtil::getRegisteredModuleCategories($this->name, 'ephem');

            $this->view->assign('catregistry', $catregistry);
        }

        // assign the item and module vars
        $this->view->assign($item);
        $this->view->assign($modvars);
        $this->view->assign('delcache', $delcache);

        // return the output that has been generated by this function
        return $this->view->fetch('ephemerides_admin_modify.tpl');
    }

    /**
     * Delete selected item
     * @author The Zikula Development Team
     * @param 'eid' Ephemeride id to delete
     * @param 'confirm' Delete confirmation
     * @return mixed HTML string if confirm is null, true otherwise
     */
    public function delete($args)
    {
        // get parameters from whatever input we need.
        $eid = FormUtil::getPassedValue('eid', isset($args['eid']) ? $args['eid'] : null, 'GETPOST');
        $objectid = FormUtil::getPassedValue('objectid', isset($args['objectid']) ? $args['objectid'] : null, 'POST');
        $confirmation = FormUtil::getPassedValue('confirmation', null, 'POST');
        if (!empty($objectid)) {
            $eid = $objectid;
        }

        // get the item
        $item = ModUtil::apiFunc($this->name, 'user', 'get', array('eid' => $eid));
        if ($item == false) {
            return LogUtil::registerError ($this->__('No such Ephemeride found.'));
        }

        // security check
        if (!SecurityUtil::checkPermission($this->name.'::', '::'.$eid, ACCESS_DELETE)) {
            return LogUtil::registerPermissionError();
        }

        // check for confirmation.
        if (empty($confirmation)) {
            // no confirmation yet - display a suitable form to obtain confirmation
            // of this action from the user

            $this->view->setCaching(false);

            // item id
            $this->view->assign('eid', $eid);

            // return the output that has been generated by this function
            return $this->view->fetch('ephemerides_admin_delete.tpl');
        }

        // if we get here it means that the user has confirmed the action

        // Confirm the forms authorisation key
        $this->checkCsrfToken();

        // delete the item
        if (ModUtil::apiFunc($this->name, 'admin', 'delete', array('eid' => $eid))) {
            LogUtil::registerStatus($this->__('Done! Ephemeride deleted.'));
        }

        // This function generated no output, and so now it is complete we redirect
        // the user to an appropriate page for them to carry on their work
        return System::redirect(ModUtil::url($this->name, 'admin', 'view'));
    }

    /**
     * Search by keyword - unfinished obviously.
     * @author The Zikula Development Team
     * @return string HTML string
     */
    public function modifyconfig()
    {
        // security check
        if (!SecurityUtil::checkPermission($this->name.'::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError();
        }

        $this->view->setCaching(false);
        // module variables
        $this->view->assign(ModUtil::getVar($this->name));

        // return the output that has been generated by this function
        return $this->view->fetch('ephemerides_admin_modifyconfig.tpl');
    }

    /**
     * Create a new item
     * @author The Zikula Development Team
	 * @param 'Date_Day' the day of the emphererid
	 * @param 'Date_Month' the month of the emphererid
	 * @param 'Date_Year' the year of the emphererid
	 * @param 'content' the ephmerid description
	 * @param 'language' the language of the ephemerid
	 * @param 'status' the status of the ephemerid
	 * @param 'type' the type of the ephemerid
     * @return bool true if create success, false otherwise
     */
    public function create($args)
    {
        // Confirm the forms authorisation key
        $this->checkCsrfToken();

		// get parameters from whatever input we need.
        $ephemerid = FormUtil::getPassedValue('ephemerid', isset($args['ephemerid']) ? $args['ephemerid'] : null, 'POST');
		// add date names as in table
		$ephemerid['did'] = $ephemerid['Date_Day'];
		$ephemerid['mid'] = $ephemerid['Date_Month'];
		$ephemerid['yid'] = $ephemerid['Date_Year'];
		
        // notable by its absence there is no security check here.
        // create the item
        $eid = ModUtil::apiFunc($this->name, 'admin', 'create', $ephemerid);
        if ($eid != false) {
            // success
            LogUtil::registerStatus($this->__('Done! Ephemeride created.'));
        }

        return System::redirect(ModUtil::url($this->name, 'admin', 'view'));
    }

    /**
     * Update item
     *
     * Takes info from edit form and passes to API
     * @author The Zikula Development Team
     * @param 'eid' Ephemeride id to delete
     * @param 'qauther' Author of item to delete
     * @param 'confirm' Delete confirmation
     * @return mixed HTML string if confirm is null, true otherwise
     */
    public function update($args)
    {
        // Confirm the forms authorisation key
        $this->checkCsrfToken();

        // get parameters from whatever input we need.
		$ephemerid = FormUtil::getPassedValue('ephemerid', isset($args['ephemerid']) ? $args['ephemerid'] : null, 'POST');
		$delcache = FormUtil::getPassedValue('delcache', isset($args['delcache']) ? $args['delcache'] : null, 'POST');

        // check to see if we have been passed $objectid, the generic item identifier.
        if (!empty($ephemerid['objectid'])) {
            $ephemerid['eid'] = $ephemerid['objectid'];
        }
		// add date names as in table
		$ephemerid['did'] = $ephemerid['Date_Day'];
		$ephemerid['mid'] = $ephemerid['Date_Month'];
		$ephemerid['yid'] = $ephemerid['Date_Year'];

        // notable by its absence there is no security check here.
        // update the ephemerid
        if (ModUtil::apiFunc($this->name, 'admin', 'update', $ephemerid)) {
            // success
            LogUtil::registerStatus($this->__('Done! Ephemeride updated.'));
            
            if ($delcache) {
                // delete respective block cache
                $cachedir = System::getVar('temp');
                if (StringUtil::right($cachedir, 1)<>'/') {
                    $cachedir .= '/';
                }
                $cachedir .= ModUtil::getVar($this->name, 'cache_dir', 'any_cache');
                // delete all files matching a pattern
                array_map('unlink', glob($cachedir . '/ephem_*'));
            }
        }

        // this function generated no output, and so now it is complete we redirect
        // the user to an appropriate page for them to carry on their work
        return System::redirect(ModUtil::url($this->name, 'admin', 'view'));
    }

    /**
     * Update Ephemerides Config
     * @author The Zikula Development Team
     */
    public function updateconfig()
    {
        // Confirm the forms authorisation key
        $this->checkCsrfToken();
        // Security check
		 $this->throwForbiddenUnless(SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN));

        // update module variables.
        $modvars = array();
        $modvars['itemsperpage'] = FormUtil::getPassedValue('itemsperpage', 25, 'POST');
        $modvars['enablecategorization'] = (bool)FormUtil::getPassedValue('enablecategorization', false, 'POST');
        $modvars['enablefacebookshare'] = (bool)FormUtil::getPassedValue('enablefacebookshare', false, 'POST');
		$this->setVars($modvars);

        // the module configuration has been updated successfuly
        LogUtil::registerStatus($this->__('Done! Module configuration updated.'));

        // this function generated no output, and so now it is complete we redirect
        // the user to an appropriate page for them to carry on their work
        return System::redirect(ModUtil::url($this->name, 'admin', 'view'));
    }
}