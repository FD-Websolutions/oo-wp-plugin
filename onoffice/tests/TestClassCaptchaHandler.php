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

use onOffice\WPlugin\Form\CaptchaHandler;

/**
 *
 * @url http://www.onoffice.de
 * @copyright 2003-2018, onOffice(R) GmbH
 *
 */

class TestClassCaptchaHandler
	extends WP_UnitTestCase
{
	/**
	 *
	 */

	public function testBuildFullUrl()
	{
		$pCaptchaHandler = new CaptchaHandler('asdfasdf2', 'abcdefghijklmn');
		$pClosure = function() {
			return $this->buildFullUrl();
		};

		$fullUrl = Closure::bind($pClosure, $pCaptchaHandler, CaptchaHandler::class)();
		$expected = 'https://www.google.com/recaptcha/api/siteverify?'
			.'secret=abcdefghijklmn&response=asdfasdf2';

		$this->assertEquals($expected, $fullUrl);
	}


	/**
	 *
	 */

	public function testGetResultBrokenJson()
	{
		$pCaptchaHandler = new CaptchaHandler('tokentokentoken', 'secretsecretsecret');
		$responseBroken = '<h1>Error 500!</h1> <p>{ I ain\'t valid json</p>';

		$this->assertFalse($pCaptchaHandler->getResult($responseBroken));
	}


	/**
	 *
	 */

	public function testGetResultValidJson()
	{
		$pCaptchaHandler = new CaptchaHandler('tokentokentoken', 'secretsecretsecret');
		$responseEmpty = '{}';
		$this->assertFalse($pCaptchaHandler->getResult($responseEmpty));

		$responseFalse = '{"success": false}';
		$this->assertFalse($pCaptchaHandler->getResult($responseFalse));

		$responseTrue = '{"success": true}';
		$this->assertTrue($pCaptchaHandler->getResult($responseTrue));
	}
}
