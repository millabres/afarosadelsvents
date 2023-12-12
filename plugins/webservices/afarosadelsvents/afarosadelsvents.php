<?php
/**
 * @package    Com_Afarosadelsvents
 * @author     Miquel Llabrés <info@joowebs.com>
 * @copyright  2023 JooWebs.com
 * @license    Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Router\ApiRouter;

/**
 * Web Services adapter for afarosadelsvents.
 *
 * @since  1.0.0
 */
class PlgWebservicesAfarosadelsvents extends CMSPlugin
{
	public function onBeforeApiRoute(&$router)
	{
		
		$router->createCRUDRoutes('v1/afarosadelsvents/families', 'families', ['component' => 'com_afarosadelsvents']);
		$router->createCRUDRoutes('v1/afarosadelsvents/menjadors', 'menjadors', ['component' => 'com_afarosadelsvents']);
	}
}
