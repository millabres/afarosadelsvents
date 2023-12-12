<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Afarosadelsvents
 * @author     Miquel Llabrés <info@joowebs.com>
 * @copyright  2023 JooWebs.com
 * @license    Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 */

namespace Afarosadelsvents\Component\Afarosadelsvents\Site\Service;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Factory;
use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Categories\CategoryInterface;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Menu\AbstractMenu;

/**
 * Class AfarosadelsventsRouter
 *
 */
class Router extends RouterView
{
	private $noIDs;
	/**
	 * The category factory
	 *
	 * @var    CategoryFactoryInterface
	 *
	 * @since  1.0.0
	 */
	private $categoryFactory;

	/**
	 * The category cache
	 *
	 * @var    array
	 *
	 * @since  1.0.0
	 */
	private $categoryCache = [];

	public function __construct(SiteApplication $app, AbstractMenu $menu, CategoryFactoryInterface $categoryFactory, DatabaseInterface $db)
	{
		$params = Factory::getApplication()->getParams('com_afarosadelsvents');
		$this->noIDs = (bool) $params->get('sef_ids');
		$this->categoryFactory = $categoryFactory;
		
		
			$families = new RouterViewConfiguration('families');
			$this->registerView($families);
			$familyform = new RouterViewConfiguration('familyform');
			$familyform->setKey('id');
			$this->registerView($familyform);
			$menjadors = new RouterViewConfiguration('menjadors');
			$this->registerView($menjadors);
			$menjadorform = new RouterViewConfiguration('menjadorform');
			$menjadorform->setKey('id');
			$this->registerView($menjadorform);
		$llistamenjadors = new RouterViewConfiguration('llistamenjadors');
		$this->registerView($llistamenjadors);
		$resums = new RouterViewConfiguration('resums');
		$this->registerView($resums);

		parent::__construct($app, $menu);

		$this->attachRule(new MenuRules($this));
		$this->attachRule(new StandardRules($this));
		$this->attachRule(new NomenuRules($this));
	}


	
		/**
		 * Method to get the segment(s) for an family
		 *
		 * @param   string  $id     ID of the family to retrieve the segments for
		 * @param   array   $query  The request that is built right now
		 *
		 * @return  array|string  The segments of this item
		 */
		public function getFamilySegment($id, $query)
		{
			return array((int) $id => $id);
		}
			/**
			 * Method to get the segment(s) for an familyform
			 *
			 * @param   string  $id     ID of the familyform to retrieve the segments for
			 * @param   array   $query  The request that is built right now
			 *
			 * @return  array|string  The segments of this item
			 */
			public function getFamilyformSegment($id, $query)
			{
				return $this->getFamilySegment($id, $query);
			}
		/**
		 * Method to get the segment(s) for an menjador
		 *
		 * @param   string  $id     ID of the menjador to retrieve the segments for
		 * @param   array   $query  The request that is built right now
		 *
		 * @return  array|string  The segments of this item
		 */
		public function getMenjadorSegment($id, $query)
		{
			return array((int) $id => $id);
		}
			/**
			 * Method to get the segment(s) for an menjadorform
			 *
			 * @param   string  $id     ID of the menjadorform to retrieve the segments for
			 * @param   array   $query  The request that is built right now
			 *
			 * @return  array|string  The segments of this item
			 */
			public function getMenjadorformSegment($id, $query)
			{
				return $this->getMenjadorSegment($id, $query);
			}

	
		/**
		 * Method to get the segment(s) for an family
		 *
		 * @param   string  $segment  Segment of the family to retrieve the ID for
		 * @param   array   $query    The request that is parsed right now
		 *
		 * @return  mixed   The id of this item or false
		 */
		public function getFamilyId($segment, $query)
		{
			return (int) $segment;
		}
			/**
			 * Method to get the segment(s) for an familyform
			 *
			 * @param   string  $segment  Segment of the familyform to retrieve the ID for
			 * @param   array   $query    The request that is parsed right now
			 *
			 * @return  mixed   The id of this item or false
			 */
			public function getFamilyformId($segment, $query)
			{
				return $this->getFamilyId($segment, $query);
			}
		/**
		 * Method to get the segment(s) for an menjador
		 *
		 * @param   string  $segment  Segment of the menjador to retrieve the ID for
		 * @param   array   $query    The request that is parsed right now
		 *
		 * @return  mixed   The id of this item or false
		 */
		public function getMenjadorId($segment, $query)
		{
			return (int) $segment;
		}
			/**
			 * Method to get the segment(s) for an menjadorform
			 *
			 * @param   string  $segment  Segment of the menjadorform to retrieve the ID for
			 * @param   array   $query    The request that is parsed right now
			 *
			 * @return  mixed   The id of this item or false
			 */
			public function getMenjadorformId($segment, $query)
			{
				return $this->getMenjadorId($segment, $query);
			}

	/**
	 * Method to get categories from cache
	 *
	 * @param   array  $options   The options for retrieving categories
	 *
	 * @return  CategoryInterface  The object containing categories
	 *
	 * @since   1.0.0
	 */
	private function getCategories(array $options = []): CategoryInterface
	{
		$key = serialize($options);

		if (!isset($this->categoryCache[$key]))
		{
			$this->categoryCache[$key] = $this->categoryFactory->createCategory($options);
		}

		return $this->categoryCache[$key];
	}
}
