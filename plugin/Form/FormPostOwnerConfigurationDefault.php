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

declare (strict_types=1);

namespace onOffice\WPlugin\Form;

use onOffice\SDK\onOfficeSDK;
use onOffice\WPlugin\Controller\InputVariableReader;
use onOffice\WPlugin\SDKWrapper;

/**
 *
 */

class FormPostOwnerConfigurationDefault
	implements FormPostOwnerConfiguration
{
	/** @var SDKWrapper */
	private $_pSDKWrapper;

	/** @var FormAddressCreator */
	private $_pFormAddressCreator;


	/**
	 *
	 * @param SDKWrapper $pSDKWrapper
	 * @param FormAddressCreator $pFormAddressCreator
	 *
	 */

	public function __construct(SDKWrapper $pSDKWrapper, FormAddressCreator $pFormAddressCreator)
	{
		$this->_pSDKWrapper = $pSDKWrapper;
		$this->_pFormAddressCreator = $pFormAddressCreator;
	}


	/**
	 *
	 * @return InputVariableReader
	 *
	 */

	public function getEstateListInputVariableReader(): InputVariableReader
	{
		return new InputVariableReader(onOfficeSDK::MODULE_ESTATE);
	}


	/**
	 *
	 * @return string
	 *
	 */

	public function getReferrer(): string
	{
		return filter_input(INPUT_SERVER, 'REQUEST_URI') ?? '';
	}


	/**
	 *
	 * @return SDKWrapper
	 *
	 */

	public function getSDKWrapper(): SDKWrapper
	{
		return $this->_pSDKWrapper;
	}


	/**
	 *
	 * @return FormAddressCreator
	 *
	 */

	public function getFormAddressCreator(): FormAddressCreator
	{
		return $this->_pFormAddressCreator;
	}
}
