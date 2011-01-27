<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2002, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: ephem.php 355 2011-01-27 13:10:50Z nikp $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Zikula_Value_Addons
 * @subpackage Ephemerids
 */

/**
 * initialise block
 */
function Ephemerids_ephemblock_init()
{
    // Security
    pnSecAddSchema('Ephemeridsblock::', 'Block title::');
}

/**
 * get information on block
 */
function Ephemerids_ephemblock_info()
{
    $dom = ZLanguage::getModuleDomain('Ephemerids');
    // Values
    return array('module' => 'Ephemerids',
                 'text_type' => __('Ephemerids', $dom),
                 'text_type_long' => __('Ephemerid', $dom),
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview'    => true,
                 'admin_tableless' => true);
}

function ephemerids_ephemblock_display($blockinfo)
{
    // Security check
    if (!SecurityUtil::checkPermission('Ephemeridsblock::', "$blockinfo[title]::", ACCESS_READ)) {
        return;
    }
    if (!pnModAvailable('Ephemerids')) {
        return;
    }
    // Get current content
    $vars = pnBlockVarsFromContent($blockinfo['content']);

	// Implementation cached content
	$enable_cache = true;
	$write_to_cache = false;	# flag
	$cache_time = 600; # seconds
	if (isset($vars['cache_time'])) $cache_time = $vars['cache_time'];
	$content = "";
	$title = $blockinfo['title'];
	if ($enable_cache and $cache_time>0) {
		$cachefilestem = 'ephem_' . $blockinfo['bid'];
	    $cachedir = pnConfigGetVar('temp');
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
	    $pnRender = & pnRender::getInstance('Ephemerids');
	    $items = pnModAPIFunc('Ephemerids', 'user', 'gettoday');
	    if (!$items) { 
	    	return '';	# do not display empty block, NP
	    }
	    $pnRender->assign('items', $items);
	    // Populate block info and pass to theme
	    $content = $pnRender->fetch('ephemerids_block_ephem.htm');
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
    return pnBlockThemeBlock($blockinfo);
}

/**
 * modify block settings
 * @author       The Zikula Development Team
 * @param        array       $blockinfo     a blockinfo structure
 * @return       output      the bock form
 */
function ephemerids_ephemblock_modify($blockinfo)
{
    // Get current content
    $vars = pnBlockVarsFromContent($blockinfo['content']);
    // Defaults
    if (!isset($vars['cache_time'])) {
        $vars['cache_time'] = 600;
    }
    if (!isset($vars['cache_dir'])) {
        $vars['cache_dir'] = 'any_cache';
    }
    // Create output object
    $pnRender = pnRender::getInstance('Ephemerids', false);
    // assign the vars
    $pnRender->assign($vars);
    // return the output
    return $pnRender->fetch('ephemerids_block_ephem_modify.htm');
}

/**
 * update block settings
 * @author       The Zikula Development Team
 * @param        array       $blockinfo     a blockinfo structure
 * @return       $blockinfo  the modified blockinfo structure
 */
function ephemerids_ephemblock_update($blockinfo)
{
    // Get current content
    $vars = pnBlockVarsFromContent($blockinfo['content']);
    // alter the corresponding variable
    $vars['cache_time'] = FormUtil::getPassedValue('cache_time');
    $vars['cache_dir'] = FormUtil::getPassedValue('cache_dir');
    // write back the new contents
    $blockinfo['content'] = pnBlockVarsToContent($vars);
    // clear the block cache
    $pnRender = pnRender::getInstance('Ephemerids');
    $pnRender->clear_cache('ephemerids_block_ephem.htm');
    return $blockinfo;
}
