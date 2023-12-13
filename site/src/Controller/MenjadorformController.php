<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Afarosadelsvents
 * @author     Miquel Llabrés <info@joowebs.com>
 * @copyright  2023 JooWebs.com
 * @license    Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 */

namespace Afarosadelsvents\Component\Afarosadelsvents\Site\Controller;
use Joomla\CMS\Factory as JFactory;


\defined('_JEXEC') or die;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * Menjador class.
 *
 * @since  1.0.0
 */
class MenjadorformController extends FormController
{
	/**
	 * Method to check out an item for editing and redirect to the edit form.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 *
	 * @throws  Exception
	 */
	public function edit($key = NULL, $urlVar = NULL)
	{
		// Get the previous edit id (if any) and the current edit id.
		$previousId = (int) $this->app->getUserState('com_afarosadelsvents.edit.menjador.id');
		$editId     = $this->input->getInt('id', 0);

		// Set the user id for the user to edit in the session.
		$this->app->setUserState('com_afarosadelsvents.edit.menjador.id', $editId);

		// Get the model.
		$model = $this->getModel('Menjadorform', 'Site');

		// Check out the item
		if ($editId)
		{
			$model->checkout($editId);
		}

		// Check in the previous user.
		if ($previousId)
		{
			$model->checkin($previousId);
		}

		// Redirect to the edit screen.
		$this->setRedirect(Route::_('index.php?option=com_afarosadelsvents&view=menjadorform&layout=edit', false));
	}

	/**
	 * Method to save data.
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   1.0.0
	 */
public function save($key = NULL, $urlVar = NULL)
{
    // Check for request forgeries.
    $this->checkToken();

    // Initialise variables.
    $model = $this->getModel('Menjadorform', 'Site');
    $data = $this->input->get('jform', array(), 'array');

    // Validate the posted data.
    $form = $model->getForm();
    if (!$form)
    {
        throw new \Exception($model->getError(), 500);
    }

    // Send an object which can be modified through the plugin event
    $objData = (object) $data;
    $this->app->triggerEvent(
        'onContentNormaliseRequestData',
        array($this->option . '.' . $this->context, $objData, $form)
    );
    $data = (array) $objData;

    // Extraer los datos del formulario
    $nombre = $data['nom_menjador'];
    $opcion = $data['opcio_menjador'];
    $fechas = explode(',', $data['data_menjador']); // Suponiendo que 'data_menjador' es una cadena separada por comas

    // Iniciar la variable de retorno
    $allSaved = true;

    // Procesar cada fecha
    foreach ($fechas as $fecha) {
        $db = JFactory::getDbo();

        // Obtener el ID del niño de la tabla de familias basado en el nombre
        $query = $db->getQuery(true);
        $query->select($db->quoteName('id'))
              ->from($db->quoteName('#__afarosadelsvents_families'))
              ->where($db->quoteName('nom_nin') . ' = ' . $db->quote($nombre));
        $db->setQuery($query);

        try {
            $idNinMenjador = $db->loadResult();
        } catch (RuntimeException $e) {
            JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            $allSaved = false;
            break;
        }

        if (!$idNinMenjador) {
            JFactory::getApplication()->enqueueMessage(Text::sprintf('No se encontró el ID para el niño %s', $nombre), 'error');
            $allSaved = false;
            continue; // Pasa a la siguiente fecha
        }

        // Verificar si ya existe un registro para ese niño en esa fecha
        $query = $db->getQuery(true);
        $query->select('COUNT(*)')
              ->from($db->quoteName('#__afarosadelsvents_menjador'))
              ->where($db->quoteName('id_nin_menjador') . ' = ' . (int) $idNinMenjador)
              ->where($db->quoteName('data_menjador') . ' = ' . $db->quote(JFactory::getDate($fecha)->format('Y-m-d')));
        $db->setQuery($query);

        try {
            $count = $db->loadResult();
            if ($count > 0) {
                JFactory::getApplication()->enqueueMessage(Text::sprintf('ja està apuntat el %s', $fecha), 'warning');
                $allSaved = false;
                continue; // Pasa a la siguiente fecha
            }
        } catch (RuntimeException $e) {
            JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            $allSaved = false;
            break;
        }

        

        // Obtener comunitat_nin de la tabla families
        $query = $db->getQuery(true);
        $query->select($db->quoteName('comunitat_nin'))
              ->from($db->quoteName('#__afarosadelsvents_families'))
              ->where($db->quoteName('nom_nin') . ' = ' . $db->quote($nombre));
        $db->setQuery($query);

        try {
            $comunitatNin = $db->loadResult();
        } catch (RuntimeException $e) {
            JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            $allSaved = false;
            break;
        }

        $registroIndividual = [
            'nom_menjador' => $nombre,
            'opcio_menjador' => $opcion,
            'data_menjador' => JFactory::getDate($fecha)->format('Y-m-d'),
            'comunitat_nin_menjador' => $comunitatNin,
            'id_nin_menjador' => $idNinMenjador // Guardar el ID del niño
        ];

        // Validar y guardar cada registro
        $registroValidado = $model->validate($form, $registroIndividual);
        if ($registroValidado === false)
        {
            $allSaved = false;
            break; // Detiene el procesamiento si hay un error
        }

        // Intentar guardar los datos.
        $return = $model->save($registroValidado);
        if ($return === false)
        {
            $allSaved = false;
            break; // Detiene el procesamiento si hay un error
        }
    }

    // Mensajes y redirecciones después de guardar todos los registros
    if ($allSaved)
    {
        $this->setMessage(Text::_('COM_AFAROSADELSVENTS_ITEM_SAVED_SUCCESSFULLY'));
    }
    else
    {
        $this->setMessage(Text::_('COM_AFAROSADELSVENTS_SAVE_FAILED'), 'warning');
    }

    $menu = Factory::getApplication()->getMenu();
    $item = $menu->getActive();
    $url = (empty($item->link) ? 'index.php?option=com_afarosadelsvents&view=menjadors' : $item->link);
    $this->setRedirect(Route::_($url, false));
    
    // Flush the data from the session.
    $this->app->setUserState('com_afarosadelsvents.edit.menjador.data', null);

    // Invoke the postSave method to allow for the child class to access the model.
    $this->postSaveHook($model, $data);
}


	/**
	 * Method to abort current operation
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function cancel($key = NULL)
	{

		// Get the current edit id.
		$editId = (int) $this->app->getUserState('com_afarosadelsvents.edit.menjador.id');

		// Get the model.
		$model = $this->getModel('Menjadorform', 'Site');

		// Check in the item
		if ($editId)
		{
			$model->checkin($editId);
		}

		$menu = Factory::getApplication()->getMenu();
		$item = $menu->getActive();
		$url  = (empty($item->link) ? 'index.php?option=com_afarosadelsvents&view=menjadors' : $item->link);
		$this->setRedirect(Route::_($url, false));
	}

	/**
	 * Method to remove data
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 *
	 * @since   1.0.0
	 */
	public function remove()
	{
		$model = $this->getModel('Menjadorform', 'Site');
		$pk    = $this->input->getInt('id');

		// Attempt to save the data
		try
		{
			// Check in before delete
			$return = $model->checkin($return);
			// Clear id from the session.
			$this->app->setUserState('com_afarosadelsvents.edit.menjador.id', null);

			$menu = $this->app->getMenu();
			$item = $menu->getActive();
			$url = (empty($item->link) ? 'index.php?option=com_afarosadelsvents&view=menjadors' : $item->link);

			if($return)
			{
				$model->delete($pk);
				$this->setMessage(Text::_('COM_AFAROSADELSVENTS_ITEM_DELETED_SUCCESSFULLY'));
			}
			else
			{
				$this->setMessage(Text::_('COM_AFAROSADELSVENTS_ITEM_DELETED_UNSUCCESSFULLY'), 'warning');
			}
			

			$this->setRedirect(Route::_($url, false));
			// Flush the data from the session.
			$this->app->setUserState('com_afarosadelsvents.edit.menjador.data', null);
		}
		catch (\Exception $e)
		{
			$errorType = ($e->getCode() == '404') ? 'error' : 'warning';
			$this->setMessage($e->getMessage(), $errorType);
			$this->setRedirect('index.php?option=com_afarosadelsvents&view=menjadors');
		}
	}

	/**
     * Function that allows child controller access to model data
     * after the data has been saved.
     *
     * @param   BaseDatabaseModel  $model      The data model object.
     * @param   array              $validData  The validated data.
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function postSaveHook(BaseDatabaseModel $model, $validData = array())
    {
    }
}
