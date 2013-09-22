<?php
/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 * @package    MetaModels
 * @subpackage AttributeTags
 * @author     Christian de la Haye <service@delahaye.de>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

namespace MetaModels\Dca;

use DcGeneral\DataContainerInterface;

/**
 * Supplementary class for handling DCA information for translated tag attributes.
 *
 * @package    MetaModels
 * @subpackage AttributeTranslatedTags
 * @author     Christian de la Haye <service@delahaye.de>
 */
class AttributeTranslatedTags extends AttributeTags
{
	/**
	 * @var AttributeTags
	 */
	protected static $objInstance = null;

	/**
	 * Get the static instance.
	 *
	 * @static
	 * @return AttributeTags
	 */
	public static function getInstance()
	{
		if (self::$objInstance == null) {
			self::$objInstance = new AttributeTranslatedTags();
		}
		return self::$objInstance;
	}

	public function getSourceColumnNames(DataContainerInterface $objDC)
	{
		$arrFields   = array();
		if (!($objDC->getEnvironment()->getCurrentModel()))
		{
			return $arrFields;
		}
		$strSrcTable = $objDC->getEnvironment()->getCurrentModel()->getProperty('tag_srctable');

		if (\Database::getInstance()->tableExists($strSrcTable))
		{
			foreach (\Database::getInstance()->listFields($strSrcTable) as $arrInfo)
			{
				if ($arrInfo['type'] != 'index')
				{
					$arrFields[$arrInfo['name']] = $arrInfo['name'];
				}
			}
		}

		return $arrFields;
	}
}
