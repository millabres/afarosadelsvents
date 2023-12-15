<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Afarosadelsvents
 * @author     Miquel Llabrés <info@joowebs.com>
 * @copyright  2023 JooWebs.com
 * @license    Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 */

namespace Afarosadelsvents\Component\Afarosadelsvents\Site\Model;
// No direct access.
defined('_JEXEC') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\MVC\Model\ListModel;
use \Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use \Joomla\CMS\Helper\TagsHelper;
use \Joomla\CMS\Layout\FileLayout;
use \Joomla\Database\ParameterType;
use \Joomla\Utilities\ArrayHelper;
use \Afarosadelsvents\Component\Afarosadelsvents\Site\Helper\AfarosadelsventsHelper;






/**
 * Methods supporting a list of Afarosadelsvents records.
 *
 * @since  1.0.0
 */
class MenjadorsModel extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see    JController
	 * @since  1.0.0
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'state', 'a.state',
				'ordering', 'a.ordering',
				'created_by', 'a.created_by',
				'modified_by', 'a.modified_by',
				'comunitat_nin_menjador', 'a.comunitat_nin_menjador',
				'id_nin_menjador', 'a.id_nin_menjador',
				'nom_menjador', 'a.nom_menjador',
				'data_menjador', 'a.data_menjador',
				'opcio_menjador', 'a.opcio_menjador',
			);
		}

		parent::__construct($config);
	}

	
       /**
        * Checks whether or not a user is manager or super user
        *
        * @return bool
        */
        public function isAdminOrSuperUser()
        {
            try{
                $user = Factory::getApplication()->getIdentity();
                return in_array("8", $user->groups) || in_array("7", $user->groups);
            }catch(\Exception $exc){
                return false;
            }
        }

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 *
	 * @since   1.0.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information.
		parent::populateState('a.id', 'ASC');

		$app = Factory::getApplication();
		$list = $app->getUserState($this->context . '.list');

		$value = $app->getUserState($this->context . '.list.limit', $app->get('list_limit', 25));
		$list['limit'] = $value;
		
		$this->setState('list.limit', $value);

		$value = $app->input->get('limitstart', 0, 'uint');
		$this->setState('list.start', $value);

		$ordering  = $this->getUserStateFromRequest($this->context .'.filter_order', 'filter_order', 'a.id');
		$direction = strtoupper($this->getUserStateFromRequest($this->context .'.filter_order_Dir', 'filter_order_Dir', 'ASC'));
		
		if(!empty($ordering) || !empty($direction))
		{
			$list['fullordering'] = $ordering . ' ' . $direction;
		}

		$app->setUserState($this->context . '.list', $list);

		

		$context = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $context);

		// Split context into component and optional section
		if (!empty($context))
		{
			$parts = FieldsHelper::extract($context);

			if ($parts)
			{
				$this->setState('filter.component', $parts[0]);
				$this->setState('filter.section', $parts[1]);
			}
		}
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  DatabaseQuery
	 *
	 * @since   1.0.0
	 */
	protected function getListQuery()
	{
			// Create a new query object.
			$db    = $this->getDbo();
			$query = $db->getQuery(true);

			// Select the required fields from the table.
			$query->select(
						$this->getState(
								'list.select', 'DISTINCT a.*'
						)
				);

			$query->from('`#__afarosadelsvents_menjador` AS a');
			
		// Join over the users for the checked out user.
		$query->select('uc.name AS uEditor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the created by field 'created_by'
		$query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');

		// Join over the created by field 'modified_by'
		$query->join('LEFT', '#__users AS modified_by ON modified_by.id = a.modified_by');
		// Join over the foreign key 'nom_menjador'
		$query->select('`#__afarosadelsvents_families_3990295`.`nom_nin` AS families_fk_value_3990295');
		$query->join('LEFT', '#__afarosadelsvents_families AS #__afarosadelsvents_families_3990295 ON #__afarosadelsvents_families_3990295.`nom_nin` = a.`nom_menjador`');
		if(!$this->isAdminOrSuperUser()){
			$query->where("a.created_by = " . Factory::getApplication()->getIdentity()->get("id"));
		}
			
		if (!Factory::getApplication()->getIdentity()->authorise('core.edit', 'com_afarosadelsvents'))
		{
			$query->where('a.state = 1');
		}
		else
		{
			$query->where('(a.state IN (0, 1))');
		}

		
		

		
		// Establecer la zona horaria de Madrid
    date_default_timezone_set('Europe/Madrid');
    $currentDateTime = new \DateTime('now');
    $currentDate = $currentDateTime->format('Y-m-d');
    $currentTime = $currentDateTime->format('H:i:s');
		
		


    // Resto de tu código...

    // Comprobar si la hora actual es posterior a las 22:10
    if ($currentTime > '22:22') {
        // Si es posterior a las 22:10, usa la fecha de mañana
        $tomorrow = $currentDateTime->modify('+1 day')->format('Y-m-d');
        $query->where('a.data_menjador >= ' . $db->quote($tomorrow));
    } else {
        // Si es anterior a las 22:10, usa la fecha de hoy
        $query->where('a.data_menjador >= ' . $db->quote($currentDate));
    }
		
			// Filter by search in title
			$search = $this->getState('filter.search');

			if (!empty($search))
			{
				if (stripos($search, 'id:') === 0)
				{
					$query->where('a.id = ' . (int) substr($search, 3));
				}
				else
				{
					$search = $db->Quote('%' . $db->escape($search, true) . '%');
					$query->where('( a.comunitat_nin_menjador LIKE ' . $search . '  OR #__afarosadelsvents_families_3990295.nom_nin LIKE ' . $search . ' )');
				}
			}
			

		// Filtering comunitat_nin_menjador

		// Filtering nom_menjador
		$filter_nom_menjador = $this->state->get("filter.nom_menjador");

		if ($filter_nom_menjador)
		{
			$query->where("a.`nom_menjador` = '".$db->escape($filter_nom_menjador)."'");
		}

		// Filtering data_menjador

		// Filtering opcio_menjador
		$filter_opcio_menjador = $this->state->get("filter.opcio_menjador");
		if ($filter_opcio_menjador != '') {
			$query->where("a.`opcio_menjador` = '".$db->escape($filter_opcio_menjador)."'");
		}

			
			
			// Add the list ordering clause.
			$orderCol  = $this->state->get('list.ordering', 'a.id');
			$orderDirn = $this->state->get('list.direction', 'ASC');

			if ($orderCol && $orderDirn)
			{
				$query->order($db->escape($orderCol . ' ' . $orderDirn));
			}

			return $query;
	}

	/**
	 * Method to get an array of data items
	 *
	 * @return  mixed An array of data on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();
		
		foreach ($items as $item)
		{

			if (isset($item->nom_menjador))
			{

				$values    = explode(',', $item->nom_menjador);
				$textValue = array();

				foreach ($values as $value)
				{
					$db    = $this->getDbo();
					$query = $db->getQuery(true);
					$query
						->select('`#__afarosadelsvents_families_3990295`.`nom_nin`')
						->from($db->quoteName('#__afarosadelsvents_families', '#__afarosadelsvents_families_3990295'))
						->where($db->quoteName('#__afarosadelsvents_families_3990295.nom_nin') . ' = '. $db->quote($db->escape($value)));

					$db->setQuery($query);
					$results = $db->loadObject();

					if ($results)
					{
						$textValue[] = $results->nom_nin;
					}
				}

				$item->nom_menjador = !empty($textValue) ? implode(', ', $textValue) : $item->nom_menjador;
			}


				if (!empty($item->opcio_menjador))
					{
						$item->opcio_menjador = Text::_('COM_AFAROSADELSVENTS_MENJADORS_OPCIO_MENJADOR_OPTION_' . preg_replace('/[^A-Za-z0-9\_-]/', '',strtoupper(str_replace(' ', '_',$item->opcio_menjador))));
					}
		}

		return $items;
	}

	/**
	 * Overrides the default function to check Date fields format, identified by
	 * "_dateformat" suffix, and erases the field if it's not correct.
	 *
	 * @return void
	 */
	protected function loadFormData()
	{
		$app              = Factory::getApplication();
		$filters          = $app->getUserState($this->context . '.filter', array());
		$error_dateformat = false;

		foreach ($filters as $key => $value)
		{
			if (strpos($key, '_dateformat') && !empty($value) && $this->isValidDate($value) == null)
			{
				$filters[$key]    = '';
				$error_dateformat = true;
			}
		}

		if ($error_dateformat)
		{
			$app->enqueueMessage(Text::_("COM_AFAROSADELSVENTS_SEARCH_FILTER_DATE_FORMAT"), "warning");
			$app->setUserState($this->context . '.filter', $filters);
		}

		return parent::loadFormData();
	}

	/**
	 * Checks if a given date is valid and in a specified format (YYYY-MM-DD)
	 *
	 * @param   string  $date  Date to be checked
	 *
	 * @return bool
	 */
	private function isValidDate($date)
	{
		$date = str_replace('/', '-', $date);
		return (date_create($date)) ? Factory::getDate($date)->format("Y-m-d") : null;
	}
}
