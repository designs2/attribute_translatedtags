<?php
/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 * @package	   MetaModels
 * @subpackage AttributeTranslatedTags
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @copyright  CyberSpectrum, MEN AT WORK
 * @license    private
 * @filesource
 */
if (!defined('TL_ROOT'))
{
	die('You cannot access this file directly!');
}

/**
 * This is the MetaModelAttribute class for handling tag attributes.
 * 
 * @package	   MetaModels
 * @subpackage AttributeTags
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 */
class MetaModelAttributeTranslatedTags extends MetaModelAttributeTags implements IMetaModelAttributeTranslated 
{
	/**
	 * Get numbers of tag for the given ids.
	 */
	public function getTagCount($arrIds)
	{
		$objDB = Database::getInstance();
		$strTableName = $this->get('tag_table');
		$strColNameId = $this->get('tag_id');
		$arrReturn = array();
		
		if ($strTableName && $strColNameId)
		{
			$strMetaModelTableName = $this->getMetaModel()->getTableName();
			$strMetaModelTableNameId = $strMetaModelTableName.'_id';

			$objValue = $objDB->prepare(sprintf(
					'SELECT `item_id`, count(*) as count FROM `tl_metamodel_tag_relation` 
						WHERE att_id = ? AND item_id IN (%1$s) group BY `item_id`',
					implode(',', $arrIds) // 1
					))
			->execute($this->get('id'));

			while ($objValue->next())
			{

				if(!$arrReturn[$objValue->item_id])
				{
					$arrReturn[$objValue->item_id] = array();
				}
				$arrReturn[$objValue->item_id] = $objValue->count;
			}
		}
		return $arrReturn;
	}

	/////////////////////////////////////////////////////////////////
	// interface IMetaModelAttribute
	/////////////////////////////////////////////////////////////////

	public function getAttributeSettingNames()
	{
		return array_merge(parent::getAttributeSettingNames(), array(
			'tag_langcolumn'
		));
	}

	/////////////////////////////////////////////////////////////////
	// interface IMetaModelAttributeComplex
	/////////////////////////////////////////////////////////////////

	public function getDataFor($arrIds)
	{
		$strActiveLanguage = $this->getMetaModel()->getActiveLanguage();
		$strFallbackLanguage = $this->getMetaModel()->getFallbackLanguage();

		$arrReturn = $this->getTranslatedDataFor($arrIds, $strActiveLanguage);
		$arrTagCount = $this->getTagCount($arrIds);

		$arrFallbackIds = array();

		//check if we got all tags
		foreach ($arrReturn as $key => $results)
		{
			// remove matching tags
			if (count($results) == $arrTagCount[$key]) 
			{
				unset($arrTagCount[$key]);
			}
		}
		
		$arrFallbackIds = array_keys($arrTagCount);
		
		// second round, fetch fallback languages if not all items could be resolved.
		if ((count($arrFallbackIds) > 0) && ($strActiveLanguage != $strFallbackLanguage))
		{

			$arrFallbackData = $this->getTranslatedDataFor($arrFallbackIds, $strFallbackLanguage);

			// cannot use array_merge here as it would renumber the keys.
			foreach ($arrFallbackData as $intId => $arrTransValue)
			{
				foreach ($arrTransValue as $intTransID => $arrValue)
				{
					if (!$arrReturn[$intId][$intTransID])
					{
						$arrReturn[$intId][$intTransID] = $arrValue;
					}
				}
			}

		}
		return $arrReturn;
	}

	public function setDataFor($arrValues)
	{
		// TODO: store to database.
	}
	
	/////////////////////////////////////////////////////////////////
	// interface IMetaModelAttributeTranslated
	/////////////////////////////////////////////////////////////////

	public function setTranslatedDataFor($arrValues, $strLangCode)
	{
		// TODO: Save values.
	}
	
	/**
	 * Get values for the given items in a certain language.
	 */
	public function getTranslatedDataFor($arrIds, $strLangCode)
	{
		$objDB = Database::getInstance();
		$strTableName = $this->get('tag_table');
		$strColNameId = $this->get('tag_id');
		$strColNameLangCode = $this->get('tag_langcolumn');
		$arrReturn = array();
		
		if ($strTableName && $strColNameId && $strColNameLangCode)
		{
			$strMetaModelTableName = $this->getMetaModel()->getTableName();
			$strMetaModelTableNameId = $strMetaModelTableName.'_id';

			$objValue = $objDB->prepare(sprintf('
				SELECT %1$s.*, tl_metamodel_tag_relation.item_id AS %2$s
				FROM %1$s
				LEFT JOIN tl_metamodel_tag_relation ON (
					(tl_metamodel_tag_relation.att_id=?)
					AND (tl_metamodel_tag_relation.value_id=%1$s.%3$s)
					AND (%1$s.%5$s=?)
				)
				WHERE tl_metamodel_tag_relation.item_id IN (%4$s)',
				$strTableName, // 1
				$strMetaModelTableNameId, // 2
				$strColNameId, // 3
				implode(',', $arrIds), // 4
				$strColNameLangCode // 5
			))
			->execute($this->get('id'),$strLangCode);
			FB::log($objValue, '$objValue');
			while ($objValue->next())
			{

				if(!$arrReturn[$objValue->$strMetaModelTableNameId])
				{
					$arrReturn[$objValue->$strMetaModelTableNameId] = array();
				}
				$arrReturn[$objValue->$strMetaModelTableNameId][$objValue->$strColNameId] = $objValue->row();
			}
		}
		
		return $arrReturn;
	}

	/**
	 * Remove values for items in a certain lanugage.
	 */
	public function unsetValueFor($arrIds, $strLangCode)
	{
		
	}	
}

?>