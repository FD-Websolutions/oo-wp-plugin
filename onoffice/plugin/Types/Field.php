<?php

/**
 *
 *    Copyright (C) 2018 onOffice GmbH
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

namespace onOffice\WPlugin\Types;

/**
 *
 */

class Field
{
	/** @var string */
	private $_name = '';

	/** @var string */
	private $_type = FieldTypes::FIELD_TYPE_VARCHAR;

	/** @var int */
	private $_length = 0;

	/** @var array */
	private $_permittedvalues = [];

	/** @var string */
	private $_default = null;

	/** @var string */
	private $_label = '';

	/** @var string */
	private $_category = '';


	/**
	 *
	 * @param string $name
	 * @param string $label
	 *
	 */

	public function __construct(string $name, string $label = '')
	{
		$this->_name = $name;
		$this->_label = $label;
	}


	/**
	 *
	 * @return string
	 *
	 */

	public function getType(): string
	{
		return $this->_type;
	}


	/**
	 *
	 * @return int
	 *
	 */

	public function getLength(): int
	{
		return $this->_length;
	}


	/**
	 *
	 * @return array
	 *
	 */

	public function getPermittedvalues(): array
	{
		return $this->_permittedvalues;
	}


	/**
	 *
	 * @return string
	 *
	 */

	public function getDefault()
	{
		return $this->_default;
	}


	/**
	 *
	 * @return string
	 *
	 */

	public function getLabel(): string
	{
		return $this->_label;
	}


	/**
	 *
	 * @param string $type
	 *
	 */

	public function setType(string $type)
	{
		$this->_type = $type;
	}


	/**
	 *
	 * @param int $length
	 *
	 */

	public function setLength(int $length)
	{
		$this->_length = $length;
	}


	/**
	 *
	 * @param array $permittedvalues
	 *
	 */

	public function setPermittedvalues(array $permittedvalues)
	{
		$this->_permittedvalues = $permittedvalues;
	}


	/**
	 *
	 * @param string $default
	 *
	 */

	public function setDefault($default)
	{
		$this->_default = $default;
	}


	/**
	 *
	 * @param string $label
	 *
	 */

	public function setLabel(string $label)
	{
		$this->_label = $label;
	}


	/**
	 *
	 * @return string
	 *
	 */

	public function getName(): string
	{
		return $this->_name;
	}


	/**
	 *
	 * @return string
	 *
	 */

	public function getCategory(): string
	{
		return $this->_category;
	}


	/**
	 *
	 * @param string  $category
	 *
	 */

	public function setCategory(string $category)
	{
		$this->_category = $category;
	}


	/**
	 *
	 * @return array
	 *
	 */

	public function getAsRow(): array
	{
		return [
			'label' => $this->_label,
			'default' => $this->_default,
			'length' => $this->_length === 0 ? null : $this->_length,
			'permittedvalues' => $this->_permittedvalues,
			'content' => $this->_category,
		];
	}
}
