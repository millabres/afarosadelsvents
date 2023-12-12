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

use Afarosadelsvents\Module\Afarosadelsvents\Site\Helper\AfarosadelsventsHelper;

$element = AfarosadelsventsHelper::getItem($params);
?>

<?php if (!empty($element)) : ?>
	<div>
		<?php $fields = get_object_vars($element); ?>
		<?php foreach ($fields as $field_name => $field_value) : ?>
			<?php if (AfarosadelsventsHelper::shouldAppear($field_name)): ?>
				<div class="row">
					<div class="span4">
						<strong><?php echo AfarosadelsventsHelper::renderTranslatableHeader($params->get('item_table'), $field_name); ?></strong>
					</div>
					<div
						class="span8"><?php echo AfarosadelsventsHelper::renderElement($params->get('item_table'), $field_name, $field_value); ?></div>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
<?php endif;
