<?php

namespace Combodo\iTop\Test\UnitTest\Core;

use CMDBChangeOpSetAttributeEncryptedPassword;
use Combodo\iTop\ItopAttributeEncryptedPassword\Model\ormEncryptedPassword;
use Combodo\iTop\Test\UnitTest\ItopCustomDatamodelTestCase;
use MetaModel;
use RemoteiTopConnectionToken2;


class AttributeEncryptedPasswordTest extends ItopCustomDatamodelTestCase
{
	const CREATE_TEST_ORG = true;
	const USE_TRANSACTION = false;
	public static $DEBUG_UNIT_TEST = true;

	protected function setUp(): void
	{
		parent::setUp();
		$this->RequireOnceItopFile('env-php-unit-tests/itop-attribute-encrypted-password/vendor/autoload.php');
		$this->RequireOnceItopFile('env-php-unit-tests/itop-attribute-encrypted-password/src/Attribute/AttributeEncryptedPassword.php');
	}

	public function GetDatamodelDeltaAbsPath(): string
	{
		return __DIR__.'/resources/add-attribute-encrypted-pwd.xml';
	}

	public function testHasAValue_Default()
	{
		echo MetaModel::GetEnvironment();
		$oObject = MetaModel::NewObject(RemoteiTopConnectionToken2::class);

		// Test attribute without a value yet
		$this->assertEquals(false, $oObject->HasAValue('token'));
	}

	public function HasAValueProvider()
	{
		return [
			'non empty string' => ['gabuzomeu', true],
			'empty string' => ['', false],
		];
	}

	/**
	 * @dataProvider HasAValueProvider
	 */
	public function testHasAValue_hiddenUsablePassword($sValue, bool $bExpected)
	{
		$oObject = MetaModel::NewObject(RemoteiTopConnectionToken2::class);
		$oObject->Set('token', $sValue);
		$this->assertEquals($bExpected, $oObject->HasAValue('token'));
	}

	public function testSettingWrongPasswordTypeShouldFail()
	{
		$oChange = MetaModel::NewObject(CMDBChangeOpSetAttributeEncryptedPassword::class);
		$oObject = MetaModel::NewObject(RemoteiTopConnectionToken2::class);
		$this->expectException(\CoreException::class);
		$oObject->Set('token', $oChange);
		//$oToken = $oObject->Get('token');
		//$this->assertEquals($oChange, $oToken->GetPassword());
	}

	public function testHasAValue_hiddenUsablePassword2()
	{
		$oObject = MetaModel::NewObject(RemoteiTopConnectionToken2::class);
		$oPassword = new ormEncryptedPassword('');
		$oObject->Set('token', $oPassword);
		$this->assertEquals(false, $oObject->HasAValue('token'));
		$oPassword->SetPassword('gabuzomeu');
		$oObject->Set('token', $oPassword);
		$this->assertEquals(true, $oObject->HasAValue('token'));
	}

	private function CreateRemoteiTopConnectionToken2(): RemoteiTopConnectionToken2
	{
		//$oRemoteApplicationType = $this->createObject(\RemoteApplicationType::class, ['name'=> 'toto']);

		/** @var RemoteiTopConnectionToken2 $oRemoteiTopConnectionToken2 */
		$oRemoteiTopConnectionToken2 = $this->createObject(RemoteiTopConnectionToken2::class,
			[
				'token' => 'gabuzomeu',
				'name' => 'blabla.fr',
			]
		);

		return $oRemoteiTopConnectionToken2;
	}

	public function testObjectCreation()
	{
		$oRemoteiTopConnectionToken2 = $this->CreateRemoteiTopConnectionToken2();

		$oToken = $oRemoteiTopConnectionToken2->Get('token');
		$this->assertEquals(ormEncryptedPassword::class, get_class($oToken));

		$sMaskedPwd = '*****';
		$this->assertEquals($sMaskedPwd, $oToken->GetDisplayValue());
		$this->assertEquals($sMaskedPwd, $oToken->GetAsHTML());
		$this->assertEquals($sMaskedPwd, ''.$oToken);

		$this->assertEquals('gabuzomeu', $oToken->GetPassword());
	}

	public function testObjectUpdate()
	{
		$oRemoteiTopConnectionToken2 = $this->CreateRemoteiTopConnectionToken2();
		$oRemoteiTopConnectionToken2 = $this->updateObject(RemoteiTopConnectionToken2::class, $oRemoteiTopConnectionToken2->GetKey(),
			[
				'token' => 'gabuzomeu2',
			]
		);

		$oToken = $oRemoteiTopConnectionToken2->Get('token');
		$this->assertEquals('gabuzomeu2', $oToken->GetPassword());

		$oRemoteiTopConnectionToken2->Reload();
	}

	public function testObjectUpdate_StarsAreIgnored()
	{
		$oRemoteiTopConnectionToken2 = $this->CreateRemoteiTopConnectionToken2();
		$oRemoteiTopConnectionToken2 = $this->updateObject(RemoteiTopConnectionToken2::class, $oRemoteiTopConnectionToken2->GetKey(),
			[
				'token' => \AttributeEncryptedPassword::STARS,
			]
		);

		$oToken = $oRemoteiTopConnectionToken2->Get('token');
		$this->assertEquals('gabuzomeu', $oToken->GetPassword());

		$oRemoteiTopConnectionToken2->Reload();
	}
}
