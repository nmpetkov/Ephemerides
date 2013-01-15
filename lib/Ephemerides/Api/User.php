<?php
/**
 * Zikula Application Framework
 * @copyright  (c) Zikula Development Team
 * @license    GNU/GPL
 * @category   Zikula_3rdParty_Modules
 * @package    Content_Management
 * @subpackage Ephemerides
 */

class Ephemerides_Api_User extends Zikula_AbstractApi
{
    /**
     * process user input and form a WHERE clause
     * @return string SQL where clause
     */
    private function _process_args(&$args)
    {
        // optional arguments.
        if (!isset($args['startnum']) || !is_numeric($args['startnum'])) {
            $args['startnum'] = -1;
        }
        if (!isset($args['numitems']) || !is_numeric($args['numitems'])) {
            $args['numitems'] = -1;
        }
        if (!isset($args['keyword'])) {
            $args['keyword'] = null;
        }
        if (!isset($args['category'])) {
            $args['category'] = null;
        }
        if (!isset($args['catFilter']) || !is_numeric($args['catFilter'])) {
            $args['catFilter'] = array();
        }
        if (!isset($args['rootCat'])) {
            $args['rootCat'] = 0;
        }
		// ignor multi-language
		if (!isset($args['ignoreml']) || !is_bool($args['ignoreml'])) {
			$args['ignoreml'] = false;
		}
		if (!isset($args['language'])) {
			$args['language'] = null;
		}

        // build the where clause
        $wheres = array();
		if (isset($args['eid'])) {
			$wheres[] = "eid = ".DataUtil::formatForStore($args['eid']);
		}
		if (isset($args['status'])) {
			$wheres[] = "status = '".DataUtil::formatForStore($args['status'])."'";
		}
		if (isset($args['type'])) {
			$wheres[] = "type = '".DataUtil::formatForStore($args['type'])."'";
		}
		// for ML filtering
		if (System::getVar('multilingual') == 1 && !$args['ignoreml'] && isset($args['language'])) {
			$wheres[] = "(language='" . DataUtil::formatForStore($args['language']) . "' OR language='')";
		}

        if ($args['category']){
            if (is_array($args['category'])) {
                $args['catFilter'] = $args['category'];
            } else {
                $args['catFilter'][] = $args['category'];
            }
            $args['catFilter']['__META__'] = array('module' => 'Ephemerides');
        }

        if ($args['keyword']) {
            $wheres[] = "content LIKE '%".DataUtil::formatForStore($args['keyword'])."%'";
        }

        $args['where'] = implode(' AND ', $wheres);

        return $args['where'];
    }

    /**
     * Get all Ephemerides
     * @author The Zikula Development Team
     * @return array array containing item id
     */
    public function getall($args)
    {
        // security check
        if (!SecurityUtil::checkPermission('Ephemerides::', '::', ACCESS_READ)) {
            return array();
        }

		$where = $this->_process_args($args);
        $sort = isset($args['sort']) && $args['sort'] ? $args['sort'] : '';
        $sortdir = isset($args['sortdir']) && $args['sortdir'] ? $args['sortdir'] : 'ASC';
        if ($sort) {
			if ($sort=='mid') $sort .= ' '.$sortdir.', did '.$sortdir.', yid '.$sortdir.', eid '.$sortdir;
			else if ($sort=='did') $sort .= ' '.$sortdir.', mid '.$sortdir.', yid '.$sortdir.', eid '.$sortdir;
			else if ($sort=='yid') $sort .= ' '.$sortdir.', mid '.$sortdir.', did '.$sortdir.', eid '.$sortdir;
			else if ($sort=='eid') $sort .= ' '.$sortdir;
			else $sort .= ' '.$sortdir.', eid '.$sortdir;
        } else {
            $sort = 'mid, did, yid, eid'; # appropriate order: @nikp
        }

        // define the permissions filter to use
        $permFilter = array();
        $permFilter[] = array('realm' => 0,
                'component_left'   => 'Ephemerides',
                'component_middle' => '',
                'component_right'  => '',
                'instance_left'    => 'author',
                'instance_middle'  => '',
                'instance_right'   => 'eid',
                'level'            => ACCESS_READ);

        $args['catFilter'] = array();
        if (isset($args['category']) && !empty($args['category'])){
            if (is_array($args['category'])) {
                $args['catFilter'] = $args['category'];
            } elseif (isset($args['property'])) {
                $property = $args['property'];
                $args['catFilter'][$property] = $args['category'];
            }
            $args['catFilter']['__META__'] = array('module' => 'Ephemerides');
        }
	
        // get the object array from the db
        $objArray = DBUtil::selectObjectArray('ephem', $where, $sort, $args['startnum']-1, $args['numitems'], '', $permFilter, $args['catFilter']);
        
        // check for an error with the database code, and if so set an appropriate
        // error message and return
        if ($objArray === false) {
            return LogUtil::registerError($this->__('Error! Could not load the ephemerides.'));
        }

        // need to do this here as the category expansion code can't know the
        // root category which we need to build the relative path component
        if ($objArray && isset($args['catregistry']) && $args['catregistry']) {
            ObjectUtil::postProcessExpandedObjectArrayCategories($objArray, $args['catregistry']);
        }

        // return the items
        return $objArray;
    }

    /**
     * Get Ephemeride
     * @author The Zikula Development Team
     * @param 'args['eid']' item id
     * @return array item array
     */
    public function get($args)
    {
        // argument check
        if (!isset($args['eid']) || !is_numeric($args['eid'])) {
            return LogUtil::registerArgsError();
        }

        // define the permissions filter to use
        $permFilter = array();
        $permFilter[] = array('realm' => 0,
                'component_left'   => 'Ephemerides',
                'component_middle' => '',
                'component_right'  => '',
                'instance_middle'  => '',
                'instance_right'   => 'eid',
                'level'            => ACCESS_READ);

        // get the item
        $item = DBUtil::selectObjectByID('ephem', $args['eid'], 'eid', null, $permFilter);

        // return the fetched object or false
        return ($item ? $item : false);
    }

    /**
     * Count Ephemerides
     * @author The Zikula Development Team
     * @return int count of items
     */
    public function countitems($args)
    {
        // optional arguments.
        if (isset($args['category']) && !empty($args['category'])){
            if (is_array($args['category'])) {
                $args['catFilter'] = $args['category'];
            } elseif (isset($args['property'])) {
                $property = $args['property'];
                $args['catFilter'][$property] = $args['category'];
            }
        }

        if (!isset($args['catFilter'])) {
            $args['catFilter'] = array();
        }

        $where = $this->_process_args($args);

        return DBUtil::selectObjectCount('ephem', $where, 'eid', false, $args['catFilter']);
    }

	/**
	 * get all items for today
	 * @return mixed array of items, or false on failure
     * @param 'args['catFilter']' if exist category filter
	 */
	function gettoday($args)
	{
		$items = array();

		if (!SecurityUtil::checkPermission('Ephemerides::', '::', ACCESS_READ)) {
			return $items;
		}

		// get todays date
		$today = getdate();
        if (isset($args['eday'])) {
            $eday = $args['eday'];
        } else {
            $eday = $today['mday'];
        }
        if (isset($args['emonth'])) {
            $emonth = $args['emonth'];
        } else {
            $emonth = $today['mon'];
        }

		// init where clause vars
		$whereargs = array();

		// filter by language?
		if (System::getVar('multilingual') == 1) {
			$whereargs[] = "(language='' OR language='" . DataUtil::formatForStore(ZLanguage::getLanguageCode()) . "')";
		}
		$whereargs[] = "did='" . DataUtil::formatForStore($eday) . "'";
		$whereargs[] = "mid='" . DataUtil::formatForStore($emonth) . "'";
		$whereargs[] = "status='1'";

		$where = 'WHERE ' . implode(' AND ', $whereargs);

		// define the permission filter to apply
		$permFilter = array(array('component_left' => 'Ephemerides',
								  'instance_right' => 'eid',
								  'level'          => ACCESS_READ));

		$args['catFilter'] = array();
        if (isset($args['category']) && !empty($args['category'])){
            if (is_array($args['category'])) {
                $args['catFilter'] = $args['category'];
            } elseif (isset($args['property'])) {
                $property = $args['property'];
                $args['catFilter'][$property] = $args['category'];
            }
            $args['catFilter']['__META__'] = array('module' => 'Ephemerides');
        }

		// get the objects from the db
		$items = DBUtil::selectObjectArray('ephem', $where, 'eid', -1, -1, '', $permFilter, $args['catFilter']);
		if ($items === false) {
			return LogUtil::registerError(__('Error! Could not load any ephemerides.'));
		}

		// Return the items
		return $items;
	}
}