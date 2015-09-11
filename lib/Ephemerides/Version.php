<?php
/**
 * Zikula Application Framework
 * @copyright  (c) Zikula Development Team
 * @license    GNU/GPL
 * @category   Zikula_3rdParty_Modules
 * @package    Content_Management
 * @subpackage Ephemerides
 */
class Ephemerides_Version extends Zikula_AbstractVersion
{
    public function getMetaData() {
        $meta = array();
        $meta['displayname'] = $this->__('Ephemerides publisher');
        $meta['description'] = $this->__('Manage and display information (historical event, thought for the day, etc.) linked to the day\'s date.');
        $meta['version'] = '3.2.0';
        $meta['url'] = $this->__('ephem');
        $meta['core_min'] = '1.3.0'; // requires minimum 1.3.0 or later
        $meta['capabilities']   = array(HookUtil::SUBSCRIBER_CAPABLE => array('enabled' => true),
                                        HookUtil::PROVIDER_CAPABLE => array('enabled' => true));
        $meta['securityschema'] = array('Ephemerides::' => '::Ephemerid ID');
        return $meta;
    }

    protected function setupHookBundles()
    {
        // Register hooks
        $bundle = new Zikula_HookManager_SubscriberBundle($this->name, 'subscriber.ephemerides.ui_hooks.items', 'ui_hooks', $this->__('Ephemerides Items Hooks'));
        $bundle->addEvent('display_view', 'ephemerides.ui_hooks.items.display_view');
        $bundle->addEvent('form_edit', 'ephemerides.ui_hooks.items.form_edit');
        $this->registerHookSubscriberBundle($bundle);

        $bundle = new Zikula_HookManager_ProviderBundle($this->name, 'provider.ephemerides.ui_hooks.ephemeride', 'ui_hooks', $this->__('Ephemerides Item'));
        $bundle->addServiceHandler('display_view', 'Ephemerides_HookHandlers', 'uiView', 'ephemerides.ephemeride');
        $this->registerHookProviderBundle($bundle);
    }
}
