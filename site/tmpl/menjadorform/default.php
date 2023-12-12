<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Afarosadelsvents
 * @author     Miquel Llabrés <info@joowebs.com>
 * @copyright  2023 JooWebs.com
 * @license    Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Language\Text;
use \Afarosadelsvents\Component\Afarosadelsvents\Site\Helper\AfarosadelsventsHelper;

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');
HTMLHelper::_('bootstrap.tooltip');

// Load admin language file
$lang = Factory::getLanguage();
$lang->load('com_afarosadelsvents', JPATH_SITE);

$user    = Factory::getApplication()->getIdentity();
$canEdit = AfarosadelsventsHelper::canUserEdit($this->item, $user);
?>

<div class="menjador-edit front-end-edit">
    <?php if (!$canEdit) : ?>
        <h3>
        <?php throw new \Exception(Text::_('COM_AFAROSADELSVENTS_ERROR_MESSAGE_NOT_AUTHORISED'), 403); ?>
        </h3>
    <?php else : ?>
        <?php if (!empty($this->item->id)): ?>
            <h1><?php echo Text::sprintf('COM_AFAROSADELSVENTS_EDIT_ITEM_TITLE', $this->item->id); ?></h1>
        <?php else: ?>
            <h1><?php echo Text::_('COM_AFAROSADELSVENTS_ADD_ITEM_TITLE'); ?></h1>
        <?php endif; ?>

        <form id="form-menjador"
              action="<?php echo Route::_('index.php?option=com_afarosadelsvents&task=menjadorform.save'); ?>"
              method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
            
            <input type="hidden" name="jform[id]" value="<?php echo isset($this->item->id) ? $this->item->id : ''; ?>" />
            <input type="hidden" name="jform[state]" value="<?php echo isset($this->item->state) ? $this->item->state : ''; ?>" />
            <input type="hidden" name="jform[ordering]" value="<?php echo isset($this->item->ordering) ? $this->item->ordering : ''; ?>" />
            <input type="hidden" name="jform[checked_out]" value="<?php echo isset($this->item->checked_out) ? $this->item->checked_out : ''; ?>" />
            <input type="hidden" name="jform[checked_out_time]" value="<?php echo isset($this->item->checked_out_time) ? $this->item->checked_out_time : ''; ?>" />

            <?php echo $this->form->getInput('created_by'); ?>
            <?php echo $this->form->getInput('modified_by'); ?>
            <input type="hidden" name="jform[comunitat_nin_menjador]" value="<?php echo isset($this->item->comunitat_nin_menjador) ? $this->item->comunitat_nin_menjador : ''; ?>" />
            <input type="hidden" name="jform[id_nin_menjador]" value="<?php echo isset($this->item->id_nin_menjador) ? $this->item->id_nin_menjador : ''; ?>" />

            <!-- Aquí eliminamos las pestañas y mostramos los campos directamente -->
            <?php echo $this->form->renderField('nom_menjador'); ?>
            <?php echo $this->form->renderField('data_menjador'); ?>
            <?php echo $this->form->renderField('opcio_menjador'); ?>

            <div class="control-group">
                <div class="controls">
                    <?php if ($this->canSave): ?>
                        <button type="submit" class="validate btn btn-primary">
                            <span class="fas fa-check" aria-hidden="true"></span>
                            <?php echo Text::_('JSUBMIT'); ?>
                        </button>
                    <?php endif; ?>
                    <a class="btn btn-danger"
                       href="<?php echo Route::_('index.php?option=com_afarosadelsvents&task=menjadorform.cancel'); ?>"
                       title="<?php echo Text::_('JCANCEL'); ?>">
                       <span class="fas fa-times" aria-hidden="true"></span>
                        <?php echo Text::_('JCANCEL'); ?>
                    </a>
                </div>
            </div>

            <input type="hidden" name="option" value="com_afarosadelsvents"/>
            <input type="hidden" name="task" value="menjadorform.save"/>
            <?php echo HTMLHelper::_('form.token'); ?>
        </form>
    <?php endif; ?>
</div>
