<?php
namespace Bitrix\Sale\Internals;

use Bitrix\Main;
use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class DiscountModuleTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> DISCOUNT_ID int mandatory
 * <li> MODULE_ID string(50) mandatory
 * </ul>
 *
 * @package Bitrix\Sale\Internals
 **/

class DiscountModuleTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	
	/**
	* <p>Метод возвращает название таблицы c модулями, необходимыми для работы правил корзины. Метод статический.</p> <p>Без параметров</p> <a name="example"></a>
	*
	*
	* @return string 
	*
	* @static
	* @link http://dev.1c-bitrix.ru/api_d7/bitrix/sale/internals/discountmoduletable/gettablename.php
	* @author Bitrix
	*/
	public static function getTableName()
	{
		return 'b_sale_discount_module';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	
	/**
	* <p>Метод возвращает список полей для таблицы с модулями, необходимыми для работы правил корзины. Метод статический.</p> <p>Без параметров</p> <a name="example"></a>
	*
	*
	* @return array 
	*
	* @static
	* @link http://dev.1c-bitrix.ru/api_d7/bitrix/sale/internals/discountmoduletable/getmap.php
	* @author Bitrix
	*/
	public static function getMap()
	{
		return array(
			'ID' => new Main\Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('DISCOUNT_MODULE_ENTITY_ID_FIELD')
			)),
			'DISCOUNT_ID' => new Main\Entity\IntegerField('DISCOUNT_ID', array(
				'required' => true,
				'title' => Loc::getMessage('DISCOUNT_MODULE_ENTITY_DISCOUNT_ID_FIELD')
			)),
			'MODULE_ID' => new Main\Entity\StringField('MODULE_ID', array(
				'required' => true,
				'validation' => array(__CLASS__, 'validateModuleId'),
				'title' => Loc::getMessage('DISCOUNT_MODULE_ENTITY_MODULE_ID_FIELD')
			)),
		);
	}
	/**
	 * Returns validators for MODULE_ID field.
	 *
	 * @return array
	 */
	
	/**
	* <p>Метод возвращает валидатор для поля <code>MODULE_ID</code> (идентификатор модуля). Метод статический.</p> <p>Без параметров</p> <a name="example"></a>
	*
	*
	* @return array 
	*
	* @static
	* @link http://dev.1c-bitrix.ru/api_d7/bitrix/sale/internals/discountmoduletable/validatemoduleid.php
	* @author Bitrix
	*/
	public static function validateModuleId()
	{
		return array(
			new Main\Entity\Validator\Length(null, 50),
		);
	}

	/**
	 * Delete modules by discount.
	 *
	 * @param int $discount				Discount id.
	 * @return void
	 */
	
	/**
	* <p>Метод удаляет модули, необходимые для работы правила с кодом <code>$discount</code>. Метод статический.</p>
	*
	*
	* @param integer $discount  Идентификатор правила.
	*
	* @return void 
	*
	* @static
	* @link http://dev.1c-bitrix.ru/api_d7/bitrix/sale/internals/discountmoduletable/deletebydiscount.php
	* @author Bitrix
	*/
	public static function deleteByDiscount($discount)
	{
		$discount = (int)$discount;
		if ($discount <= 0)
			return;
		$conn = Application::getConnection();
		$helper = $conn->getSqlHelper();
		$conn->queryExecute(
			'delete from '.$helper->quote(self::getTableName()).' where '.$helper->quote('DISCOUNT_ID').' = '.$discount
		);
	}

	/**
	 * Update module list by discount.
	 *
	 * @param int $discount			Discount id.
	 * @param array $moduleList		Modules list.
	 * @param bool $clear			Clear old values.
	 * @return bool
	 */
	
	/**
	* <p>Метод обновляет параметры списка модулей, необходимых для работы правила с кодом <code>$discount</code>. Метод статический.</p>
	*
	*
	* @param integer $discount  Идентификатор правила.
	*
	* @param array $moduleList  Массив идентификаторов модулей.
	*
	* @param boolean $clear  Если параметр принимает <i>true</i>, то будут очищены старые значения.
	*
	* @return boolean 
	*
	* @static
	* @link http://dev.1c-bitrix.ru/api_d7/bitrix/sale/internals/discountmoduletable/updatebydiscount.php
	* @author Bitrix
	*/
	public static function updateByDiscount($discount, $moduleList, $clear)
	{
		$discount = (int)$discount;
		if ($discount <= 0)
			return false;
		$clear = ($clear === true);
		if ($clear)
		{
			self::deleteByDiscount($discount);
		}
		if (!empty($moduleList) && is_array($moduleList))
		{
			foreach ($moduleList as &$module)
			{
				$fields = array(
					'DISCOUNT_ID' => $discount,
					'MODULE_ID' => $module
				);
				$result = self::add($fields);
			}
			unset($module);
		}
		return true;
	}

	/**
	 * Returns modules by discount list.
	 *
	 * @param array $discountList			Discount list.
	 * @param array $filter				Additional filter.
	 * @return array
	 */
	
	/**
	* <p>Метод возвращает список модулей, необходимых для работы перечисленных в массиве <code>$discountList</code> правил. Метод статический.</p>
	*
	*
	* @param array $discountList  Массив правил.
	*
	* @param array $filter = array() Дополнительный фильтр.
	*
	* @return array 
	*
	* @static
	* @link http://dev.1c-bitrix.ru/api_d7/bitrix/sale/internals/discountmoduletable/getbydiscount.php
	* @author Bitrix
	*/
	public static function getByDiscount($discountList, $filter = array())
	{
		$result = array();
		if (!empty($discountList) && is_array($discountList))
		{
			Main\Type\Collection::normalizeArrayValuesByInt($discountList);
			if (!empty($discountList))
			{
				if (!is_array($filter))
					$filter = array();

				$discountRows = array_chunk($discountList, 500);
				foreach ($discountRows as &$row)
				{
					$filter['@DISCOUNT_ID'] = $row;

					$moduleIterator = self::getList(array(
						'select' => array('DISCOUNT_ID', 'MODULE_ID'),
						'filter' => $filter
					));
					while ($module = $moduleIterator->fetch())
					{
						$module['DISCOUNT_ID'] = (int)$module['DISCOUNT_ID'];
						if (!isset($result[$module['DISCOUNT_ID']]))
							$result[$module['DISCOUNT_ID']] = array();
						$result[$module['DISCOUNT_ID']][] = $module['MODULE_ID'];
					}
					unset($module, $moduleIterator);
				}
				unset($row, $discountRows);
			}
		}
		return $result;
	}
}