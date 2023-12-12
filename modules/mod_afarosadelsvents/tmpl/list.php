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

$elements = AfarosadelsventsHelper::getList($params);

$tableField = explode(':', $params->get('field'));
$table_name = !empty($tableField[0]) ? $tableField[0] : '';
$field_name = !empty($tableField[1]) ? $tableField[1] : '';
?>

<?php if (!empty($elements)) : ?>
	<table class="jcc-table">
		<?php foreach ($elements as $element) : ?>
			<tr>
				<th><?php echo AfarosadelsventsHelper::renderTranslatableHeader($table_name, $field_name); ?></th>
				<td><?php echo AfarosadelsventsHelper::renderElement(
						$table_name, $params->get('field'), $element->{$field_name}
					); ?></td>
			</tr>
		<?php endforeach; ?>
	</table>
<?php endif;
