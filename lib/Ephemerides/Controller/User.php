<?php
/**
 * Zikula Application Framework
 * @copyright  (c) Zikula Development Team
 * @license    GNU/GPL
 * @category   Zikula_3rdParty_Modules
 * @package    Content_Management
 * @subpackage Ephemerides
 */

class Ephemerides_Controller_User extends Zikula_AbstractController
{
    /**
     * the main user function
     *
     * @param array $args Arguments.
     *
     * @return string html string
     */
    public function main($args)
    {
        return $this->display($args);
    }

    /**
     * display items for a day
     *
     * @param $args array Arguments array.
     *
     * @return string html string
     */
    public function display($args)
    {
        $eid   = (int)FormUtil::getPassedValue('eid', isset($args['eid']) ? $args['eid'] : null, 'REQUEST');
        $objectid = (int)FormUtil::getPassedValue('objectid', isset($args['objectid']) ? $args['objectid'] : null, 'REQUEST');

        if (!empty($objectid)) {
            $eid = $objectid;
        }
        if (!isset($args['eid']) and !empty($eid)) {
            $args['eid'] = $eid;
        }

        // Chek permissions
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Ephemerides::', '::', ACCESS_READ), LogUtil::getErrorMsgPermission());

        // check if the contents are cached.
        $template = 'ephemerides_user_display.tpl';
        if ($this->view->is_cached($template)) {
            return $this->view->fetch($template);
        }

        // get items
        if (isset($args['eid']) and $args['eid']>0) {
            $items = ModUtil::apiFunc($this->name, 'user', 'getall', $args);
        } else {
            $items = ModUtil::apiFunc($this->name, 'user', 'gettoday', $args);
        }

        $this->view->assign('items', $items);

        return $this->view->fetch($template);
    }
}
