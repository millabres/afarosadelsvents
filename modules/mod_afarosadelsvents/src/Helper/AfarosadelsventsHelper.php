<?php

/**
 * @version     CVS: 1.0.0
 * @package     com_afarosadelsvents
 * @subpackage  mod_afarosadelsvents
 * @author      Miquel Llabrés <info@joowebs.com>
 * @copyright   2023 JooWebs.com
 * @license     Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 */

namespace Afarosadelsvents\Module\Afarosadelsvents\Site\Helper;

\defined('_JEXEC') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Language\Language;
use \Joomla\CMS\User\UserFactoryInterface;

/**
 * Helper for mod_afarosadelsvents
 *
 * @package     com_afarosadelsvents
 * @subpackage  mod_afarosadelsvents
 * @since       1.0.0
 */
Class AfarosadelsventsHelper
{
	/**
	 * Retrieve component items
	 *
	 * @param   Joomla\Registry\Registry &$params module parameters
	 *
	 * @return array Array with all the elements
	 *
	 * @throws Exception
	 */
	public static function getList(&$params)
	{
		$app   = Factory::getApplication();
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);

		$tableField = explode(':', $params->get('field'));
		$table_name = !empty($tableField[0]) ? $tableField[0] : '';

		/* @var $params Joomla\Registry\Registry */
		$query
			->select('*')
			->from($table_name)
			->where('state = 1');

		$db->setQuery($query, $app->input->getInt('offset', (int) $params->get('offset')), $app->input->getInt('limit', (int) $params->get('limit')));
		$rows = $db->loadObjectList();

		return $rows;
	}

	/**
	 * Retrieve component items
	 *
	 * @param   Joomla\Registry\Registry &$params module parameters
	 *
	 * @return mixed stdClass object if the item was found, null otherwise
	 */
	public static function getItem(&$params)
	{
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);

		/* @var $params Joomla\Registry\Registry */
		$query
			->select('*')
			->from($params->get('item_table'))
			->where('id = ' . intval($params->get('item_id')));

		$db->setQuery($query);
		$element = $db->loadObject();

		return $element;
	}

	/**
	 * Render element
	 *
	 * @param   Joomla\Registry\Registry $table_name  Table name
	 * @param   string                   $field_name  Field name
	 * @param   string                   $field_value Field value
	 *
	 * @return string
	 */
	public static function renderElement($table_name, $field_name, $field_value)
	{
		$result = '';
		
		if(strpos($field_name, ':'))
		{
			$tableField = explode(':', $field_name);
			$table_name = !empty($tableField[0]) ? $tableField[0] : '';
			$field_name = !empty($tableField[1]) ? $tableField[1] : '';
		}
		
		switch ($table_name)
		{
			
		case '#__afarosadelsvents_families':
		switch($field_name){
		case 'id':
		$result = $field_value;
		break;
		case 'created_by':
		$container = \Joomla\CMS\Factory::getContainer();
		$userFactory = $container->get(UserFactoryInterface::class);
		$user = $userFactory->loadUserById($field_value);
		$result = $user->name;
		break;
		case 'modified_by':
		$container = \Joomla\CMS\Factory::getContainer();
		$userFactory = $container->get(UserFactoryInterface::class);
		$user = $userFactory->loadUserById($field_value);
		$result = $user->name;
		break;
		case 'nom_nin':
		$result = $field_value;
		break;
		case 'comunitat_nin':
		$result = $field_value;
		break;
		case 'data_naixement':
		$result = $field_value;
		break;
		case 'comentaris':
		$result = $field_value;
		break;
		}
		break;
		case '#__afarosadelsvents_menjador':
		switch($field_name){
		case 'id':
		$result = $field_value;
		break;
		case 'created_by':
		$container = \Joomla\CMS\Factory::getContainer();
		$userFactory = $container->get(UserFactoryInterface::class);
		$user = $userFactory->loadUserById($field_value);
		$result = $user->name;
		break;
		case 'modified_by':
		$container = \Joomla\CMS\Factory::getContainer();
		$userFactory = $container->get(UserFactoryInterface::class);
		$user = $userFactory->loadUserById($field_value);
		$result = $user->name;
		break;
		case 'comunitat_nin_menjador':
		$result = $field_value;
		break;
		case 'id_nin_menjador':
		$result = $field_value;
		break;
		case 'nom_menjador':
		$result = self::loadValueFromExternalTable('#__afarosadelsvents_families', 'nom_nin', 'nom_nin', $field_value);
		break;
		case 'data_menjador':
		$result = $field_value;
		break;
		case 'opcio_menjador':
		$result = $field_value;
		break;
		}
		break;
		}

		return $result;
	}

	/**
	 * Returns the translatable name of the element
	 *
	 * @param   string .................. $table_name table name
	 * @param   string                   $field   Field name
	 *
	 * @return string Translatable name.
	 */
	public static function renderTranslatableHeader($table_name, $field)
	{
		return Text::_(
			'MOD_AFAROSADELSVENTS_HEADER_FIELD_' . str_replace('#__', '', strtoupper($table_name)) . '_' . strtoupper($field)
		);
	}

	/**
	 * Checks if an element should appear in the table/item view
	 *
	 * @param   string $field name of the field
	 *
	 * @return boolean True if it should appear, false otherwise
	 */
	public static function shouldAppear($field)
	{
		$noHeaderFields = array('checked_out_time', 'checked_out', 'ordering', 'state');

		return !in_array($field, $noHeaderFields);
	}

	

    /**
     * Method to get a value from a external table
     * @param string $source_table Source table name
     * @param string $key_field Source key field 
     * @param string $value_field Source value field
     * @param mixed  $key_value Value for the key field
     * @return mixed The value in the external table or null if it wasn't found
     */
    private static function loadValueFromExternalTable($source_table, $key_field, $value_field, $key_value) {
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);

        $query
                ->select($db->quoteName($value_field))
                ->from($source_table)
                ->where($db->quoteName($key_field) . ' = ' . $db->quote($key_value));


        $db->setQuery($query);
        return $db->loadResult();
    }
}
