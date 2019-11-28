<?php

/**
 *
 *    Copyright (C) 2019 onOffice GmbH
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Affero General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU Affero General Public License for more details.
 *
 *    You should have received a copy of the GNU Affero General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

declare (strict_types=1);

namespace onOffice\WPlugin\Field\DefaultValue\ModelToOutputConverter;

use onOffice\WPlugin\Field\DefaultValue\DefaultValueCreate;
use onOffice\WPlugin\Field\DefaultValue\DefaultValueModelNumericRange;
use onOffice\WPlugin\Field\DefaultValue\DefaultValueModelSingleselect;
use onOffice\WPlugin\Field\DefaultValue\DefaultValueModelText;
use onOffice\WPlugin\Field\UnknownFieldException;
use onOffice\WPlugin\Language;
use onOffice\WPlugin\Record\RecordManagerInsertException;
use onOffice\WPlugin\Types\Field;
use onOffice\WPlugin\Types\FieldsCollection;
use onOffice\WPlugin\Types\FieldTypes;

/**
 *
 */

class DefaultValueRowSaver
{
	/** @var DefaultValueCreate */
	private $_pDefaultValueCreate;

	/** @var Language */
	private $_pLanguage;

	/**
	 * @param DefaultValueCreate $pDefaultValueCreate
	 * @param Language $pLanguage
	 */
	public function __construct(
		DefaultValueCreate $pDefaultValueCreate,
		Language $pLanguage)
	{
		$this->_pDefaultValueCreate = $pDefaultValueCreate;
		$this->_pLanguage = $pLanguage;
	}

	/**
	 * @param int $formId
	 * @param array $row
	 * @param FieldsCollection $pUsedFieldsCollection
	 * @throws RecordManagerInsertException
	 * @throws UnknownFieldException
	 */
	public function saveDefaultValues(int $formId, array $row, FieldsCollection $pUsedFieldsCollection)
	{
		foreach ($row as $field => $values) {
			$values = is_object($values) ? (array) $values : $values;
			if (!empty($values)) {
				$pField = $pUsedFieldsCollection->getFieldByKeyUnsafe($field);
				$this->saveForFoundType($formId, $pField, $values);
			}
		}
	}

	/**
	 * @param int $formId
	 * @param Field $pField
	 * @param mixed $values
	 * @throws RecordManagerInsertException
	 */
	private function saveForFoundType(int $formId, Field $pField, $values)
	{
		switch ($pField->getType()) {
			case FieldTypes::FIELD_TYPE_SINGLESELECT:
			case FieldTypes::FIELD_TYPE_MULTISELECT:
				$this->saveSingleSelect($formId, $pField, $values);
				break;
			case FieldTypes::FIELD_TYPE_TEXT:
			case FieldTypes::FIELD_TYPE_VARCHAR:
				$this->saveText($formId, $pField, $values);
				break;
			case FieldTypes::FIELD_TYPE_INTEGER:
			case FieldTypes::FIELD_TYPE_FLOAT:
				$this->saveNumericRange($formId, $pField, $values);
				break;
		}
	}

	/**
	 * @param int $formId
	 * @param Field $pField
	 * @param string $value
	 * @throws RecordManagerInsertException
	 */
	private function saveSingleSelect(int $formId, Field $pField, string $value)
	{
		$pModel = new DefaultValueModelSingleselect($formId, $pField);
		$pModel->setValue($value);
		$this->_pDefaultValueCreate->createForSingleselect($pModel);
	}

	/**
	 *
	 * @param int $formId
	 * @param Field $pField
	 * @param array $values
	 * @throws RecordManagerInsertException
	 */
	private function saveText(int $formId, Field $pField, array $values)
	{
		$pModel = new DefaultValueModelText($formId, $pField);

		foreach ($values as $locale => $value) {
			$this->addLocaleToModelForText($pModel, $locale, $value);
		}
		$this->_pDefaultValueCreate->createForText($pModel);
	}

	/**
	 * @param int $formId
	 * @param Field $pField
	 * @param array $values
	 * @throws RecordManagerInsertException
	 */
	private function saveNumericRange(int $formId, Field $pField, array $values)
	{
		$pModel = new DefaultValueModelNumericRange($formId, $pField);
		$pModel->setValueFrom((float)$values[0] ?? .0);
		$pModel->setValueTo((float)$values[1] ?? .0);
		$this->_pDefaultValueCreate->createForNumericRange($pModel);
	}

	/**
	 * @param DefaultValueModelText $pModel
	 * @param string $locale
	 * @param string $value
	 */
	private function addLocaleToModelForText(DefaultValueModelText $pModel, string $locale, string $value)
	{
		if ($locale === 'native') {
			$locale = $this->_pLanguage->getLocale();
		}
		$pModel->addValueByLocale($locale, $value);
	}
}