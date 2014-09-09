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

$GLOBALS['METAMODELS']['attributes']['translatedtags']['class'] = 'MetaModels\Attribute\TranslatedTags\TranslatedTags';
$GLOBALS['METAMODELS']['attributes']['translatedtags']['image'] =
    'system/modules/metamodelsattribute_translatedtags/html/tags.png';

$GLOBALS['TL_EVENTS'][\ContaoCommunityAlliance\Contao\EventDispatcher\Event\CreateEventDispatcherEvent::NAME][] =
    'MetaModels\DcGeneral\Events\Table\Attribute\Translated\Tags\Subscriber::registerEvents';
