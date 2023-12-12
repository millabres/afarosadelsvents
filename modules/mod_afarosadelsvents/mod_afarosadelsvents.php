<?php

/**
 * @version     CVS: 1.0.0
 * @package     com_afarosadelsvents
 * @subpackage  mod_afarosadelsvents
 * @author      Miquel Llabrés <info@joowebs.com>
 * @copyright   2023 JooWebs.com
 * @license     Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Afarosadelsvents\Module\Afarosadelsvents\Site\Helper\AfarosadelsventsHelper;

$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wr = $wa->getRegistry();
$wr->addRegistryFile('media/mod_afarosadelsvents/joomla.asset.json');
$wa->useStyle('mod_afarosadelsvents.style')
    ->useScript('mod_afarosadelsvents.script');

require ModuleHelper::getLayoutPath('mod_afarosadelsvents', $params->get('content_type', 'blank'));
