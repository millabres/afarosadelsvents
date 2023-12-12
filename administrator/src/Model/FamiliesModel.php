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

use \Joomla\CMS\MVC\Model\ListModel;
use \Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Helper\TagsHelper;
use \Joomla\Database\ParameterType;
use \Joomla\Utilities\ArrayHelper;
use Afarosadelsvents\Component\Afarosadelsvents\Administrator\Helper\AfarosadelsventsHelper;

/**
 * Methods supporting a list of Families records.
 *
 * @since  1.0.0
 */
class FamiliesModel extends ListModel
{
	/**
	* Constructor.
	*
	* @param   array  $config  An optional associative array of configuration settings.
	*
	* @see        JController
	* @since      1.6
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
				'nom_nin', 'a.nom_nin',
				'comunitat_nin', 'a.comunitat_nin',
				'data_naixement', 'a.data_naixement',
		'data_naixement.from', 'data_naixement.to',
				'comentaris', 'a.comentaris',
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
            }catch(Exception $exc){
                return false;
            }
        }

	
        /**
         * This method revises if the $id of the item belongs to the current user
         * @param   integer     $id     The id of the item
         * @return  boolean             true if the user is the owner of the row, false if not.
         *
         */
        public function userIDItem($id){
            try{
                $user = Factory::getApplication()->getIdentity();                
                $db    = $this->getDbo();

                $query = $db->getQuery(true);
                $query->select("id")
                      ->from($db->quoteName('#__afarosadelsvents_families'))
                      ->where("id = " . $db->escape($id))
                      ->where("created_by = " . $user->id);

                $db->setQuery($query);

                $results = $db->loadObject();
                if ($results){
                    return true;
                }else{
                    return false;
                }
            }catch(\Exception $exc){
                return false;
            }
        }

	
        /**
         * This method to check if there are items created by the current user.
         * @return  boolean             true if the user created one of the item, false if not.
         *
         */
        public function isUserCreatedItem(){
            try{
                $user = Factory::getApplication()->getIdentity();                
                $db    = $this->getDbo();                
                $query = $db->getQuery(true);
                $query->select("id")
                      ->from($db->quoteName('#__afarosadelsvents_families'))
                      ->where("created_by = " . $user->id);

                $db->setQuery($query);

                $results = $db->loadObject();
                if ($results){
                    return true;
                }else{
                    return false;
                }
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
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information.
		parent::populateState('id', 'ASC');

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
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string A store id.
	 *
	 * @since   1.0.0
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');

		if(!$id || $this->userIDItem($id) || $this->isAdminOrSuperUser() || $this->isUserCreatedItem() || Factory::getUser()->authorise('core.manage', 'com_afarosadelsvents')){
		return parent::getStoreId($id);
		}else{
                               throw new \Exception(Text::_("JERROR_ALERTNOAUTHOR"), 401);
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
		$query->from('`#__afarosadelsvents_families` AS a');
		
		// Join over the users for the checked out user
		$query->select("uc.name AS uEditor");
		$query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");
		if(!$this->isAdminOrSuperUser()){
			$query->where("a.created_by = " . Factory::getUser()->get("id"));
		}

		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

		// Join over the user field 'modified_by'
		$query->select('`modified_by`.name AS `modified_by`');
		$query->join('LEFT', '#__users AS `modified_by` ON `modified_by`.id = a.`modified_by`');
		

		// Filter by published state
		$published = $this->getState('filter.state');

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif (empty($published))
		{
			$query->where('(a.state IN (0, 1))');
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
				$query->where('( a.nom_nin LIKE ' . $search . '  OR  a.comunitat_nin LIKE ' . $search . '  OR  a.data_naixement LIKE ' . $search . ' )');
			}
		}
		

		// Filtering comunitat_nin
		$filter_comunitat_nin = $this->state->get("filter.comunitat_nin");

		if ($filter_comunitat_nin !== null && (is_numeric($filter_comunitat_nin) || !empty($filter_comunitat_nin)))
		{
			$query->where("a.`comunitat_nin` = '".$db->escape($filter_comunitat_nin)."'");
		}

		// Filtering data_naixement
		$filter_data_naixement_from = $this->state->get("filter.data_naixement.from");

		if ($filter_data_naixement_from !== null && !empty($filter_data_naixement_from))
		{
			$query->where("a.`data_naixement` >= '".$db->escape($filter_data_naixement_from)."'");
		}
		$filter_data_naixement_to = $this->state->get("filter.data_naixement.to");

		if ($filter_data_naixement_to !== null  && !empty($filter_data_naixement_to))
		{
			$query->where("a.`data_naixement` <= '".$db->escape($filter_data_naixement_to)."'");
		}
		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'id');
		$orderDirn = $this->state->get('list.direction', 'ASC');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Get an array of data items
	 *
	 * @return mixed Array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();
		
		foreach ($items as $oneItem)
		{
					$oneItem->comunitat_nin = !empty($oneItem->comunitat_nin) ? Text::_('COM_AFAROSADELSVENTS_FAMILIES_COMUNITAT_NIN_OPTION_' . preg_replace('/[^A-Za-z0-9\_-]/', '',strtoupper(str_replace(' ', '_',$oneItem->comunitat_nin)))) : '';
		}

		return $items;
	}
}
