<?php
/**
 * Smarty function to return first image src from given HTML content.
 *
 * Examples
 *  {getImage htmlcontent=$item.body}
 *  {getImage htmlcontent=$item.body putbaseurl=true}
 *  {getImage htmlcontent=$item.body putbaseurl=true assign='imagesrc'}
 *
 * @return string
 */
function smarty_function_getImage($params, Zikula_View $view)
{
    $result = $params['htmlcontent'];

    if (isset($params['htmlcontent']) && $params['htmlcontent']) {
        if (strpos($params['htmlcontent'], '<img ') === false) {
            // image is not found in content
        } else {
            // get image src
            $posstart = strpos($params['htmlcontent'], ' src="', $posstart) + 6;
            $posend = strpos($params['htmlcontent'], '"', $posstart);
            $result = substr($params['htmlcontent'], $posstart, $posend-$posstart);
            if (isset($params['putbaseurl']) && $params['putbaseurl']) {
                // put base url, if not
                if (substr($result, 0, 7) != 'http://' || substr($result, 0, 8) != 'https://') {
                    $result = System::getBaseUrl() . ltrim($result, DIRECTORY_SEPARATOR);
                }
            }
        }
    }

    if (isset($params['assign'])) {
        $view->assign ($params['assign'], $result);
    } else {    
        return $result;
    }
}
