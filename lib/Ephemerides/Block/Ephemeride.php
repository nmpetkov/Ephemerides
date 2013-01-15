<?php
/**
 * Zikula Application Framework
 * @copyright  (c) Zikula Development Team
 * @license    GNU/GPL
 * @category   Zikula_3rdParty_Modules
 * @package    Content_Management
 * @subpackage Ephemerides
*/

class Ephemerides_Block_Ephemeride extends Zikula_Controller_AbstractBlock
{
    /**
     * initialise block
     */
    public function init()
    {
        // Security
        SecurityUtil::registerPermissionSchema('Ephemerides:Ephemerideblock:', 'Block ID::');
    }

    /**
     * @return       array with block information
     * @author       The Zikula Development Team
     */
    public function info()
    {
        return array('module' => $this->name,
                     'text_type' => $this->__('Ephemeride'),
                     'text_type_long' => $this->__('Ephemeride block'),
                     'allow_multiple' => true,
                     'form_content' => false,
                     'form_refresh' => false,
                     'show_preview'    => true,
                     'admin_tableless' => true);
    }

    /**
     * display block
     * @author       The Zikula Development Team
     */
    public function display($blockinfo)
    {
        // security check
        if (!SecurityUtil::checkPermission('Ephemerides:Ephemerideblock:', $blockinfo['bid'].'::', ACCESS_READ)) {
            return;
        }
        // Get current content
        $vars = BlockUtil::varsFromContent($blockinfo['content']);
        if (!isset($vars['category'])) {
            $vars['category'] = null;
        }
        
        // Implementation cached content: @nikp
        $enable_cache = true;
        $write_to_cache = false;	# flag
        $cache_time = 3600; # seconds
        if (isset($vars['cache_time'])) $cache_time = $vars['cache_time'];
        $content = "";
        $title = $blockinfo['title'];
        if ($enable_cache and $cache_time>0) {
            $cachefilestem = 'ephem_' . $blockinfo['bid'];
            $cachedir = System::getVar('temp');
            if (StringUtil::right($cachedir, 1)<>'/') $cachedir .= '/';
            if (isset($vars['cache_dir']) and !empty($vars['cache_dir'])) $cachedir .= $vars['cache_dir'];
            else $cachedir .= 'any_cache';
            $cachefile = $cachedir .'/'. $cachefilestem;
            $cachefile_title = $cachedir .'/'. $cachefilestem . '_t';
           // attempt to load from cache
            if (file_exists($cachefile)) {
                $file_time = filectime($cachefile);
                $now = time();
                $diff = ($now - $file_time);
                if ($diff <= $cache_time) {
                    $content = file_get_contents($cachefile);
                }
                if (file_exists($cachefile_title)) {
                    $title = file_get_contents($cachefile_title);
                }
            }
            if (empty($content)) $write_to_cache = true; # not loaded, flag to write to cache later
        }
        if (empty($content)) {
            // Create output object

            $this->view->setCaching(false); // we implement caching other way

            $apiargs = array();
            $apiargs['status'] = 1;
            // Make a category filter only if categorization is enabled
            $enablecategorization = ModUtil::getVar($this->name, 'enablecategorization');
            if ($enablecategorization) {
                // load the categories system
                if (!Loader::loadClass('CategoryRegistryUtil')) {
                    return LogUtil::registerError(__f('Error! Could not load [%s] class.'), 'CategoryRegistryUtil');
                }
                // Get the registrered categories for the module
                $catregistry  = CategoryRegistryUtil::getRegisteredModuleCategories($this->name, 'ephem');
                $this->view->assign('catregistry', $catregistry);
                $apiargs['catregistry'] = $catregistry;
                $apiargs['category'] = $vars['category'];
            }
            $this->view->assign('enablecategorization', $enablecategorization);
            $this->view->assign($vars); // assign the block vars
            if (!is_array($vars['category'])) $vars['category'] = array();
            $this->view->assign('category', $vars['category']);
            // get items
            $items = ModUtil::apiFunc($this->name, 'user', 'gettoday', $apiargs);
            if (!$items) { 
                return '';	# do not display empty block: @nikp
            }
            $this->view->assign('items', $items);
            // Populate block info and pass to theme
            $content = $this->view->fetch('ephemerides_block_ephemeride.tpl');

            // loop to see if we have items of type=1 (events), if not - not to put title to the block (holidays only)
            $have_events = false;
            foreach ($items as $item) {
                if ($item['type']==1) {
                    $have_events = true;
                    break;
                }
            }
            if (!$have_events) $title = "";
        }
        if ($write_to_cache and !empty($content)) {
           // attempt to write to cache if not loaded before
            if (!file_exists($cachedir)) {
                mkdir($cachedir, 0777); # attempt to make the dir
            }
            if (file_put_contents($cachefile, $content)) {
                file_put_contents($cachefile_title, $title);
            } else {
                //echo "<br />Could not save data to cache. Please make sure your cache directory exists and is writable.<br />";
            }
        }
        $blockinfo['content'] = $content;
        $blockinfo['title'] = $title;
        
        // return the rendered block
        return BlockUtil::themeBlock($blockinfo);
    }

    /**
     * modify block settings
     *
     * @author       The Zikula Development Team
     * @param        array       $blockinfo     a blockinfo structure
     * @return       output      the bock form
     */
    public function modify($blockinfo)
    {
        // Get current content
        $vars = BlockUtil::varsFromContent($blockinfo['content']);
        // Defaults
        if (!isset($vars['cache_time'])) {
            $vars['cache_time'] = 600;
        }
        if (!isset($vars['cache_dir'])) {
            $vars['cache_dir'] = 'any_cache';
        }
        // Create output object
        $this->view->caching = false; # Admin output changes often, we do not want caching
        // Select categories only if enabled for the module
        $enablecategorization = ModUtil::getVar($this->name, 'enablecategorization');
        if ($enablecategorization) {
            // load the categories system
            if (!Loader::loadClass('CategoryRegistryUtil')) {
                return LogUtil::registerError(__f('Error! Could not load [%s] class.'), 'CategoryRegistryUtil');
            }
            // Get the registrered categories for the module
            $catregistry  = CategoryRegistryUtil::getRegisteredModuleCategories($this->name, 'ephem');
            $this->view->assign('catregistry', $catregistry);
        }
        $this->view->assign('enablecategorization', $enablecategorization);
        $this->view->assign($vars); // assign the block vars
        if (!is_array($vars['category'])) $vars['category'] = array();
        $this->view->assign('category', $vars['category']);
        // return the output
        return $this->view->fetch('ephemerides_block_ephemeride_modify.tpl');
    }

    /**
     * update block settings
     *
     * @author       The Zikula Development Team
     * @param        array       $blockinfo     a blockinfo structure
     * @return       $blockinfo  the modified blockinfo structure
     */
    public function update($blockinfo)
    {
        // Get current content
        $vars = BlockUtil::varsFromContent($blockinfo['content']);

        // alter the corresponding variable
        $vars['cache_time'] = FormUtil::getPassedValue('cache_time');
        $vars['cache_dir'] = FormUtil::getPassedValue('cache_dir');
        $vars['category'] = FormUtil::getPassedValue('category', null);

        // write back the new contents
        $blockinfo['content'] = BlockUtil::varsToContent($vars);

        // clear the block cache
        $this->view->clear_cache('ephemerides_block_ephemeride.tpl');

        return $blockinfo;
    }
}