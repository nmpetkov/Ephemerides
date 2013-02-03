<?php
/**
 * Zikula Application Framework
 * @copyright  (c) Zikula Development Team
 * @license    GNU/GPL
 * @category   Zikula_3rdParty_Modules
 * @package    Content_Management
 * @subpackage Ephemerides
 */

/**
 * Ephemerides Hooks Handlers.
 */
class Ephemerides_HookHandlers extends Zikula_Hook_AbstractHandler
{

    /**
     * Display hook for view.
     *
     * @param Zikula_Hook $hook The hook.
     *
     * @return void
     */
    public function uiView(Zikula_DisplayHook $hook)
    {
        // Input from the hook
        $callermodname = $hook->getCaller();
        $callerobjectid = $hook->getId();

        // Check permissions
        if (!SecurityUtil::checkPermission('Ephemerides::', "::", ACCESS_READ)) {
            return;
        }

        // Get items
        $items = ModUtil::apiFunc('Ephemerides', 'user', 'gettoday', $args);


        // create the output object
        $view = Zikula_View::getInstance('Ephemerides', false, null, true);
        $view->assign('areaid', $hook->getAreaId());
        $view->assign('items', $items);
        $template = 'ephemerides_user_display.tpl';

        // Add style
        PageUtil::addVar('stylesheet', 'modules/Ephemerides/style/style.css');

        $response = new Zikula_Response_DisplayHook('provider.ephemerides.ui_hooks.ephemeride', $view, $template);
        $hook->setResponse($response);
    }

}
