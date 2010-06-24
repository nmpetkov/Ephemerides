<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2002, Zikula Development Team
 * @link http://www.zikula.org
 * @version $Id: ephem.php 420 2010-06-14 04:56:34Z drak $
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
    SecurityUtil::registerPermissionSchema('Ephemeridsblock::', 'Block title::');
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
                 'show_preview' => true);
}

function Ephemerids_ephemblock_display($blockinfo)
{
    // Security check
    if (!SecurityUtil::checkPermission('Ephemeridsblock::', "$blockinfo[title]::", ACCESS_READ)) {
        return;
    }

    if (!ModUtil::available('Ephemerids')) {
        return;
    }

    // Create output object
    $Renderer = & Renderer::getInstance('Ephemerids');

    $items = ModUtil::apiFunc('Ephemerids', 'user', 'gettoday');
    if (!$items) { 
    	return '';	# do not display empty block, NP
    }
    $Renderer->assign('items', $items);

    // Populate block info and pass to theme
    $blockinfo['content'] = $Renderer->fetch('ephemerids_block_ephem.htm');

    return BlockUtil::themeBlock($blockinfo);
}
