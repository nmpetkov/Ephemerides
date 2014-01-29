<?php
/**
 * Zikula Application Framework
 * @copyright  (c) Zikula Development Team
 * @license    GNU/GPL
 * @category   Zikula_3rdParty_Modules
 * @package    Content_Management
 * @subpackage Ephemerides
 */

class Ephemerides_Controller_Ajax extends Zikula_Controller_AbstractAjax
{
    /**
     * This function sets active/inactive status.
     *
     * @param eid
     *
     * @return mixed true or Ajax error
     */
    public function setstatus()
    {
        $this->checkAjaxToken();
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Ephemerides::', '::', ACCESS_ADMIN));

        $eid = $this->request->request->get('eid', 0);
        $status = $this->request->request->get('status', 0);
        $alert = '';
  
        if ($eid == 0) {
            $alert .= $this->__('No ID passed.');
        } else {
            $item = array('eid' => $eid, 'status' => $status);
            $res = DBUtil::updateObject($item, 'ephem', '', 'eid');
            if (!$res) {
                $alert .= $item['eid'].', '. $this->__f('Could not change item, ID %s.', DataUtil::formatForDisplay($eid));
                if ($item['status']) {
                    $item['status'] = 0;
                } else {
                    $item['status'] = 1;
                }
            }
        }
        // get current status to return
        $item = ModUtil::apiFunc($this->name, 'user', 'get', array('eid' => $eid));
        if (!$item) {
            $alert .= $this->__f('Could not get data, ID %s.', DataUtil::formatForDisplay($eid));
        }

        return new Zikula_Response_Ajax(array('eid' => $eid, 'status' => $item['status'], 'alert' => $alert));
    }
}
