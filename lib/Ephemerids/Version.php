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
        $meta['version'] = '3.1.0';
        $meta['url'] = $this->__('ephem');
        $meta['core_min'] = '1.3.0'; // requires minimum 1.3.0 or later
        $meta['capabilities']   = array(HookUtil::SUBSCRIBER_CAPABLE => array('enabled' => true));
        $meta['securityschema'] = array('Ephemerids::' => '::Ephemerid ID');
        return $meta;
    }

    protected function setupHookBundles()
    {
        // Register hooks
        $bundle = new Zikula_HookManager_SubscriberBundle($this->name, 'subscriber.ephemerids.ui_hooks.items', 'ui_hooks', $this->__('Ephemerids Items Hooks'));
        $bundle->addEvent('display_view', 'ephemerids.ui_hooks.items.display_view');
        $bundle->addEvent('form_edit', 'ephemerids.ui_hooks.items.form_edit');
        $bundle->addEvent('form_delete', 'ephemerids.ui_hooks.items.form_delete');
        $this->registerHookSubscriberBundle($bundle);

        $bundle = new Zikula_HookManager_SubscriberBundle($this->name, 'subscriber.ephemerids.filter_hooks.items', 'filter_hooks', $this->__('Ephemerids Filter Hooks'));
        $bundle->addEvent('filter', 'ephemerids.filter_hooks.items.filter');
        $this->registerHookSubscriberBundle($bundle);
    }
}
