<?php
/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 *
 * @package    MetaModels
 * @subpackage AttributeTranslatedTags
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

namespace MetaModels\DcGeneral\Events\Table\Attribute\TranslatedTags;

use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetPropertyOptionsEvent;

/**
 * Handle events for tl_metamodel_attribute.alias_fields.attr_id.
 */
class Subscriber extends \MetaModels\DcGeneral\Events\Table\Attribute\Tags\Subscriber
{
    /**
     * Boot the system in the backend.
     *
     * @return void
     */
    protected function registerEventsInDispatcher()
    {
        $this
            ->addListener(
                GetPropertyOptionsEvent::NAME,
                array($this, 'getLangColumnNames')
            )
            ->addListener(
                GetPropertyOptionsEvent::NAME,
                array($this, 'handleSrcTableNames')
            )
            ->addListener(
                GetPropertyOptionsEvent::NAME,
                array($this, 'getSourceColumnNames')
            );
    }

    /**
     * Retrieve all column names for the current selected table.
     *
     * @param GetPropertyOptionsEvent $event The event.
     *
     * @return void
     */
    public function getLangColumnNames(GetPropertyOptionsEvent $event)
    {
        if (($event->getEnvironment()->getDataDefinition()->getName() !== 'tl_metamodel_attribute')
            || ($event->getPropertyName() !== 'tag_langcolumn')
        ) {
            return;
        }

        $this->handleColumnNames($event, $event->getModel()->getProperty('tag_table'));
    }

    /**
     * Retrieve all database table names.
     *
     * @param GetPropertyOptionsEvent $event The event.
     *
     * @return void
     */
    public function handleSrcTableNames(GetPropertyOptionsEvent $event)
    {
        if (($event->getEnvironment()->getDataDefinition()->getName() !== 'tl_metamodel_attribute')
            || ($event->getPropertyName() !== 'tag_srctable')) {
            return;
        }

        $this->getTableNames($event);
    }

    /**
     * Retrieve all column names of type int for the current selected table.
     *
     * @param GetPropertyOptionsEvent $event The event.
     *
     * @return void
     */
    public function getSourceColumnNames(GetPropertyOptionsEvent $event)
    {
        if (($event->getEnvironment()->getDataDefinition()->getName() !== 'tl_metamodel_attribute')
            || ($event->getPropertyName() !== 'tag_srcsorting')) {
            return;
        }

        $model    = $event->getModel();
        $table    = $model->getProperty('select_srctable');
        $database = $this->getServiceContainer()->getDatabase();

        if (!$table || !$database->tableExists($table)) {
            return;
        }

        $result = array();

        foreach ($database->listFields($table) as $arrInfo) {
            if ($arrInfo['type'] != 'index') {
                $result[$arrInfo['name']] = $arrInfo['name'];
            }
        }

        $event->setOptions($result);
    }
}
