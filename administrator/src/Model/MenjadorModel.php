<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Afarosadelsvents
 * @author     Miquel Llabrés <info@joowebs.com>
 * @copyright  2023 JooWebs.com
 * @license    Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 */

namespace Afarosadelsvents\Component\Afarosadelsvents\Administrator\Model;
// No direct access.
defined('_JEXEC') or die;

use \Joomla\CMS\Table\Table;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Plugin\PluginHelper;
use \Joomla\CMS\MVC\Model\AdminModel;
use \Joomla\CMS\Helper\TagsHelper;
use \Joomla\CMS\Filter\OutputFilter;

/**
 * Menjador model.
 *
 * @since  1.0.0
 */
class MenjadorModel extends AdminModel
{
	/**
	 * @var    string  The prefix to use with controller messages.
	 *
	 * @since  1.0.0
	 */
	protected $text_prefix = 'COM_AFAROSADELSVENTS';

	/**
	 * @var    string  Alias to manage history control
	 *
	 * @since  1.0.0
	 */
	public $typeAlias = 'com_afarosadelsvents.menjador';

	/**
	 * @var    null  Item data
	 *
	 * @since  1.0.0
	 */
	protected $item = null;

	/**
	 * Checks whether or not a user is manager or super user
	 *
	 * @return bool
	 */
	public function isAdminOrSuperUser()
	{
		try
		{
			$user = Factory::getApplication()->getIdentity();
			return in_array("8", $user->groups) || in_array("7", $user->groups);
		}
		catch (\Exception $exc)
		{
			return false;
		}
	}
	/**
	 * This method revises if the $id of the item belongs to the current user
	 * @param   integer     $id     The id of the item
	 * @return  boolean             true if the user is the owner of the row, false if not.
	 */
	public function userIDItem($id)
	{
		try
		{
			$user  = Factory::getApplication()->getIdentity();
			$db    = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select("id")
						->from($db->quoteName('#__afarosadelsvents_menjador'))
						->where("id = " . $db->escape($id))
						->where("created_by = " . $user->id);
			$db->setQuery($query);
			$results = $db->loadObject();
			if ($results)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		catch(\Exception $exc)
		{
			return false;
		}
	}


	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  Table    A database object
	 *
	 * @since   1.0.0
	 */
	public function getTable($type = 'Menjador', $prefix = 'Administrator', $config = array())
	{
		return parent::getTable($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  \JForm|boolean  A \JForm object on success, false on failure
	 *
	 * @since   1.0.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app = Factory::getApplication();

		// Get the form.
		$form = $this->loadForm(
								'com_afarosadelsvents.menjador', 
								'menjador',
								array(
									'control' => 'jform',
									'load_data' => $loadData 
								)
							);

		

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.0.0
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_afarosadelsvents.edit.menjador.data', array());

		if (empty($data))
		{
			if ($this->item === null)
			{
				$this->item = $this->getItem();
			}

			$data = $this->item;
			

			// Support for multiple or not foreign key field: opcio_menjador
			$array = array();

			foreach ((array) $data->opcio_menjador as $value)
			{
				if (!is_array($value))
				{
					$array[] = $value;
				}
			}
			if(!empty($array)){

			$data->opcio_menjador = $array;
			}
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since   1.0.0
	 */
	public function getItem($pk = null)
	{
		if(!$pk || $this->userIDItem($pk) || $this->isAdminOrSuperUser())
		{

			if ($item = parent::getItem($pk))
			{
				if (isset($item->params))
				{
					$item->params = json_encode($item->params);
				}
				
				// Do any procesing on fields here if needed
			}

			return $item;
		}
		else
		{
			throw new \Exception(Text::_("JERROR_ALERTNOAUTHOR"), 401);
		}

	}

	/**
	 * Method to duplicate an Menjador
	 *
	 * @param   array  &$pks  An array of primary key IDs.
	 *
	 * @return  boolean  True if successful.
	 *
	 * @throws  Exception
	 */
	public function duplicate(&$pks)
	{
		$app = Factory::getApplication();
		$user = $app->getIdentity();

		// Access checks.
		if (!$user->authorise('core.create', 'com_afarosadelsvents'))
		{
			throw new \Exception(Text::_('JERROR_CORE_CREATE_NOT_PERMITTED'));
		}

		$context    = $this->option . '.' . $this->name;

		// Include the plugins for the save events.
		PluginHelper::importPlugin($this->events_map['save']);

		$table = $this->getTable();

		foreach ($pks as $pk)
		{
			if(!$pk || $this->userIDItem($pk) || $this->isAdminOrSuperUser())
			{

				if ($table->load($pk, true))
				{
					// Reset the id to create a new record.
					$table->id = 0;

					if (!$table->check())
					{
						throw new \Exception($table->getError());
					}
					
				if (!empty($table->nom_menjador))
				{
					if (is_array($table->nom_menjador))
					{
						$table->nom_menjador = implode(',', $table->nom_menjador);
					}
				}
				else
				{
					$table->nom_menjador = '';
				}


					// Trigger the before save event.
					$result = $app->triggerEvent($this->event_before_save, array($context, &$table, true, $table));

					if (in_array(false, $result, true) || !$table->store())
					{
						throw new \Exception($table->getError());
					}

					// Trigger the after save event.
					$app->triggerEvent($this->event_after_save, array($context, &$table, true));
				}
				else
				{
					throw new \Exception($table->getError());
				}
			}
			else
			{
				throw new \Exception(Text::_("JERROR_ALERTNOAUTHOR"), 401);
			}

		}

		// Clean cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   Table  $table  Table Object
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');

		if (empty($table->id))
		{
			// Set ordering to the last item if not set
			if (@$table->ordering === '')
			{
				$db = $this->getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__afarosadelsvents_menjador');
				$max             = $db->loadResult();
				$table->ordering = $max + 1;
			}
		}
	}
}
