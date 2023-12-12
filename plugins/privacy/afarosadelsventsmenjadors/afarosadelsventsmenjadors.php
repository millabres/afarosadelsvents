<?php
/**
 * @package    Com_Afarosadelsvents
 * @subpackage Privacy.menjador
 * @author     Miquel Llabrés <info@joowebs.com>
 * @copyright  2023 JooWebs.com
 * @license    Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\User\User;
use Joomla\Component\Privacy\Administrator\Plugin\PrivacyPlugin;
use Joomla\Component\Privacy\Administrator\Table\RequestTable;

/**
 * Privacy plugin managing Joomla user menjador data
 *
 * @since  1.0.0
 */
class PlgPrivacyAfarosadelsventsmenjadors extends PrivacyPlugin
{
	/**
	 * Processes an export request for menjador data
	 *
	 * This event will collect data for the menjador table
	 *
	 * @param   RequestTable  $request  The request record being processed
	 * @param   User          $user     The user account associated with this request if available
	 *
	 * @return  \Joomla\Component\Privacy\Administrator\Export\Domain[]
	 *
	 * @since   1.0.0
	 */
	public function onPrivacyExportRequest(RequestTable $request, User $user = null)
	{
		if (!$user)
		{
			return array();
		}

		$domains   = array();
		$domain    = $this->createDomain('user_menjador', 'joomla_user_menjador_data');
		$domains[] = $domain;

		$query = $this->db->getQuery(true)
			->select('*')
			->from($this->db->quoteName('#__afarosadelsvents_menjador'))
			->where($this->db->quoteName('created_by') . ' = ' . (int) $user->id);

		$items = $this->db->setQuery($query)->loadObjectList();

		foreach ($items as $item)
		{
			$domain->addItem($this->createItemFromArray((array) $item));
		}

		$domains[] = $this->createCustomFieldsDomain('com_afarosadelsvents.menjador', $items);

		return $domains;
	}

	/**
	 * Removes the data associated with a remove information request
	 *
	 * This event will pseudoanonymise the menjador
	 *
	 * @param   RequestTable  $request  The request record being processed
	 * @param   User          $user     The user account associated with this request if available
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function onPrivacyRemoveData(RequestTable $request, User $user = null)
	{
		// This plugin only processes data for registered user accounts
		if (!$user)
		{
			return;
		}

		$db = $this->db;

		$query = $db->getQuery(true);

		$query->clear()
			->delete($db->quoteName('#__afarosadelsvents_menjador'))
			->where($this->db->quoteName('created_by') . ' = ' . (int) $user->id);

		$db->setQuery($query)
			->execute();
	}
}
