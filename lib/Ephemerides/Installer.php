<?php
/**
 * Zikula Application Framework
 * @copyright  (c) Zikula Development Team
 * @license    GNU/GPL
 * @category   Zikula_3rdParty_Modules
 * @package    Content_Management
 * @subpackage Ephemerides
 */
 
class Ephemerides_Installer extends Zikula_AbstractInstaller
{
    /**
     * Init ephemerides module
     * @author The Zikula Development Team
     * @return true if init successful, false otherwise
     */
    public function install()
    {
        // create table
        if (!DBUtil::createTable('ephem')) {
            return false;
        }

        // set up module config variables
        $modvars = array(
                'itemsperpage' => 25,
                'enablecategorization' => true
        );

        // create our default category
        if (!$this->_createdefaultcategory()) {
            LogUtil::registerStatus($this->$this->__('Warning! Could not create the default Ephemerides category tree. If you want to use categorisation with Ephemerides, register at least one property for the module in the Category Registry.'));
            $modvars['enablecategorization'] = false;
        }

        // set up module variables
        ModUtil::setVars('Ephemerides', $modvars);

        // Register for hooks subscribing
        HookUtil::registerSubscriberBundles($this->version->getHookSubscriberBundles());

        // initialisation successful
        return true;
    }

    /**
     * Upgrade ephemerides module
     * @author The Zikula Development Team
     * @return true if init successful, false otherwise
     */
    public function upgrade($oldversion)
    {
        // upgrade dependent on old version number
        switch ($oldversion)
        {
			case '1.2':
				// version 1.2 shipped with postnuke .72x/.75
                ModUtil::setVar('Ephemerides', 'itemsperpage', 25);

            case '1.6':
				$this->ephemerides_upgrade_updateEphemeridesLanguages();

			case '1.7':
				// needs update of table, added status column

			case '1.8':
				// needs update of table, added type column

			case '1.9':
                $connection = Doctrine_Manager::getInstance()->getConnection('default');
                $sqlStatements = array();
				// drop table prefix
                $prefix = $this->serviceManager['prefix'];
                $sqlStatements[] = 'RENAME TABLE ' . $prefix . '_ephem' . " TO `ephem`";
                $sqlStatements[] = "ALTER TABLE `ephem` CHANGE `pn_eid` `eid` INT(11) NOT NULL AUTO_INCREMENT";
                $sqlStatements[] = "ALTER TABLE `ephem` CHANGE `pn_did` `did` TINYINT(4) NOT NULL DEFAULT '0'";
                $sqlStatements[] = "ALTER TABLE `ephem` CHANGE `pn_mid` `mid` TINYINT(4) NOT NULL DEFAULT '0'";
                $sqlStatements[] = "ALTER TABLE `ephem` CHANGE `pn_yid` `yid` SMALLINT(6) NOT NULL DEFAULT '0'";
                $sqlStatements[] = "ALTER TABLE `ephem` CHANGE `pn_content` `content` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";
                $sqlStatements[] = "ALTER TABLE `ephem` CHANGE `pn_language` `language` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";
                $sqlStatements[] = "ALTER TABLE `ephem` CHANGE `pn_obj_status` `obj_status` VARCHAR(1) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'A'";
                $sqlStatements[] = "ALTER TABLE `ephem` CHANGE `pn_cr_date` `cr_date` DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00'";
                $sqlStatements[] = "ALTER TABLE `ephem` CHANGE `pn_cr_uid` `cr_uid` INT(11) NOT NULL DEFAULT '0'";
                $sqlStatements[] = "ALTER TABLE `ephem` CHANGE `pn_lu_date` `lu_date` DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00'";
                $sqlStatements[] = "ALTER TABLE `ephem` CHANGE `pn_lu_uid` `lu_uid` INT(11) NOT NULL DEFAULT '0'";
                $sqlStatements[] = "ALTER TABLE `ephem` CHANGE `pn_status` `status` TINYINT(4) NULL DEFAULT '1'";
                $sqlStatements[] = "ALTER TABLE `ephem` CHANGE `pn_type` `type` TINYINT(4) NULL DEFAULT '1'";
                foreach ($sqlStatements as $sql) {
                    $stmt = $connection->prepare($sql);
                    try {
                        $stmt->execute();
                    } catch (Exception $e) {
                    }   
                }
				// update table structure according to table defenition
				if (!DBUtil::changeTable('ephem')) {
					return "1.9";
				}
				// enable categorisation this module
				ModUtil::setVar('Ephemerides', 'enablecategorization', true);
				// create our default category
				if (!$this->_createdefaultcategory()) {
					LogUtil::registerStatus($this->$this->__('Warning! Could not create the default Ephemerides category tree. If you want to use categorisation with Ephemerides, register at least one property for the module in the Category Registry.'));
					$modvars['enablecategorization'] = false;
				}
            case '3.0.0':
                $connection = Doctrine_Manager::getInstance()->getConnection('default');
                $sqlStatements = array();
                // change module name from Ephemerids to Ephemerides
                $sqlStatements[] = "UPDATE `modules` SET `name`='Ephemerides', `directory`='Ephemerides', `securityschema`='a:1:{s:13:\"Ephemerides::\";s:14:\"::Ephemerid ID\";}' WHERE `directory`='Ephemerids';";
                $sqlStatements[] = "UPDATE `module_vars` SET `modname`='Ephemerides' WHERE `modname`='Ephemerids';";
                foreach ($sqlStatements as $sql) {
                    $stmt = $connection->prepare($sql);
                    try {
                        $stmt->execute();
                    } catch (Exception $e) {
                    }   
                }
                // Register for hook subscribing
                HookUtil::registerSubscriberBundles($this->version->getHookSubscriberBundles());

            case '3.1.0':
				// future upgrade routines
        }

		// upgrade success
        return true;
    }

    /**
     * Delete ephemerides module
     * @author The Zikula Development Team
     * @return true if init successful, false otherwise
     */
    public function uninstall()
    {
        if (!DBUtil::dropTable('ephem')) {
            return false;
        }

        // delete module variables
        ModUtil::delVar('Ephemerides');

        // delete entries from category registry
        ModUtil::dbInfoLoad('Categories');
        DBUtil::deleteWhere('categories_registry', "crg_modname = 'Ephemerides'");
        DBUtil::deleteWhere('categories_mapobj', "cmo_modname = 'Ephemerides'");

        // unregister handlers
        HookUtil::unregisterSubscriberBundles($this->version->getHookSubscriberBundles());

        // deletion successful
        return true;
    }

    private function _createdefaultcategory($regpath = '/__SYSTEM__/Modules/Global')
    {
        // get the language
        $lang = ZLanguage::getLanguageCode();

        // get the category path for which we're going to insert our place holder category
        $rootcat = CategoryUtil::getCategoryByPath('/__SYSTEM__/Modules');
        $qCat    = CategoryUtil::getCategoryByPath('/__SYSTEM__/Modules/Ephemerides');

        if (!$qCat) {
            // create placeholder for all our migrated categories
            $cat = new Categories_DBObject_Category();
            $cat->setDataField('parent_id', $rootcat['id']);
            $cat->setDataField('name', 'Ephemerides');
            $cat->setDataField('display_name', array($lang => $this->__('Ephemerides')));
            $cat->setDataField('display_desc', array($lang => $this->__('Ephemerides')));
            if (!$cat->validate('admin')) {
                return false;
            }
            $cat->insert();
            $cat->update();
        }

        // get the category path for which we're going to insert our upgraded categories
        $rootcat = CategoryUtil::getCategoryByPath($regpath);
        if ($rootcat) {
            // create an entry in the categories registry
            $registry = new Categories_DBObject_Registry();
            $registry->setDataField('modname', 'Ephemerides');
            $registry->setDataField('table', 'ephem');
            $registry->setDataField('property', 'Main');
            $registry->setDataField('category_id', $rootcat['id']);
            $registry->insert();
        } else {
            return false;
        }

        return true;
    }

	private function ephemerides_upgrade_updateEphemeridesLanguages()
	{
		$obj = DBUtil::selectObjectArray('ephem');

		if (count($obj) == 0) {
			// nothing to do
			return;
		}

		foreach ($obj as $ephemerid) {
			// translate l3 -> l2
			if ($l2 = ZLanguage::translateLegacyCode($ephemerid['language'])) {
				$ephemerid['language'] = $l2;
			}
			DBUtil::updateObject($ephemerid, 'ephem', '', 'eid', true);
		}

		return true;
	}
}