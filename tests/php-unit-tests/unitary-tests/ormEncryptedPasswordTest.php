<?php
/**
 * @copyright   Copyright (C) 2010-2024 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

namespace Combodo\iTop\Test\UnitTest\Core;

use Combodo\iTop\ItopAttributeEncryptedPassword\Helper\AttributeEncryptedPasswordException;
use Combodo\iTop\ItopAttributeEncryptedPassword\Model\ormEncryptedPassword;
use Combodo\iTop\Test\UnitTest\ItopDataTestCase;

class ormEncryptedPasswordTest extends ItopDataTestCase
{
	public static $DEBUG_UNIT_TEST = true;

	protected function setUp(): void
	{
		parent::setUp();
	}

	public function testCreatingEmptyPasswordRemainsEmpty()
	{
		$oPassword = new ormEncryptedPassword('');
		$this->assertEquals('', $oPassword->GetPassword(), 'Empty password should remain empty');
	}

	public function testCreatingPasswordWithNonEncryptedValueShouldFail()
	{
		$oPassword = new ormEncryptedPassword('toto');
		$this->expectException(AttributeEncryptedPasswordException::class);
		$oPassword->GetPassword();
	}

	public function testPasswordShouldBeDecrypted()
	{
		$oPassword = new ormEncryptedPassword('');
		$oPassword->SetPassword('toto');
		$this->assertNotEquals('toto', $oPassword->GetEncryptedPassword(), 'Password should be crypted');
		$this->assertEquals('toto', $oPassword->GetPassword(), 'Password should be decrypted');
	}

	public function testSettingEncryptedPasswordShouldWork()
	{
		$oPassword = new ormEncryptedPassword('');
		$oPassword->SetPassword('toto');
		$sEncryptedPassword = $oPassword->GetEncryptedPassword();

		$oNewPassword = new ormEncryptedPassword($sEncryptedPassword);
		$this->assertEquals('toto', $oNewPassword->GetPassword(), 'Password should be decrypted');
	}
}
