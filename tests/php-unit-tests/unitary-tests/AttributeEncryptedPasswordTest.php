<?php

namespace Combodo\iTop\Test\UnitTest\Core;

use AttributeDefinition;
use CMDBChangeOpSetAttributeEncryptedPassword;
use Combodo\iTop\ItopAttributeEncryptedPassword\Model\ormEncryptedPassword;
use Combodo\iTop\Test\UnitTest\ItopCustomDatamodelTestCase;
use MetaModel;
use RemoteiTopConnectionToken;


class AttributeEncryptedPasswordTest extends ItopCustomDatamodelTestCase
{
	const CREATE_TEST_ORG = true;
	const USE_TRANSACTION = false;
	public static $DEBUG_UNIT_TEST = true;

	protected function setUp(): void
	{
		parent::setUp();
		$this->RequireOnceItopFile('env-production/itop-attribute-encrypted-password/vendor/autoload.php');
		$this->RequireOnceItopFile('env-production/itop-attribute-encrypted-password/src/Attribute/AttributeEncryptedPassword.php');
	}

	public function GetDatamodelDeltaAbsPath(): string
	{
		return __DIR__.'/resources/add-attribute-encrypted-pwd.xml';
	}

	public function testHasAValue_Default()
	{
		echo MetaModel::GetEnvironment();
		$oObject = MetaModel::NewObject(RemoteiTopConnectionToken::class);

		// Test attribute without a value yet
		$this->assertEquals(false, $oObject->HasAValue('token'));

		$oAttributeDefinition = MetaModel::GetAttributeDef(RemoteiTopConnectionToken::class, 'token');
		$this->assertEquals(true, $oAttributeDefinition->IsNull($oObject->Get('token')));
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
		$oObject = MetaModel::NewObject(RemoteiTopConnectionToken::class);
		$oObject->Set('token', $sValue);
		$this->assertEquals($bExpected, $oObject->HasAValue('token'));
	}

	public function testSettingWrongPasswordTypeShouldFail()
	{
		$oChange = MetaModel::NewObject(CMDBChangeOpSetAttributeEncryptedPassword::class);
		$oObject = MetaModel::NewObject(RemoteiTopConnectionToken::class);
		$this->expectException(\CoreException::class);
		$oObject->Set('token', $oChange);
	}

	public function testHasAValue_hiddenUsablePassword2()
	{
		$oObject = MetaModel::NewObject(RemoteiTopConnectionToken::class);
		$oPassword = new ormEncryptedPassword('');
		$oObject->Set('token', $oPassword);
		$this->assertEquals(false, $oObject->HasAValue('token'));
		$oPassword->SetPassword('gabuzomeu');
		$oObject->Set('token', $oPassword);
		$this->assertEquals(true, $oObject->HasAValue('token'));
	}

	private function CreateRemoteiTopConnectionToken2(): RemoteiTopConnectionToken
	{
		//$oRemoteApplicationType = $this->createObject(\RemoteApplicationType::class, ['name'=> 'toto']);

		/** @var RemoteiTopConnectionToken $oRemoteiTopConnectionToken2 */
		$oRemoteiTopConnectionToken2 = $this->createObject(RemoteiTopConnectionToken::class,
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

		$oAttributeDefinition = MetaModel::GetAttributeDef(RemoteiTopConnectionToken::class, 'token');
		$oOrmEncryptedPwd = $oRemoteiTopConnectionToken2->Get('token');
		$this->assertEquals(ormEncryptedPassword::class, get_class($oOrmEncryptedPwd));

		$this->assertEquals('*****', $oOrmEncryptedPwd->GetDisplayValue());
		$this->assertEquals('*****', $oAttributeDefinition->GetForJSON($oOrmEncryptedPwd));
		$this->assertEquals('*****', $oAttributeDefinition->Fingerprint($oOrmEncryptedPwd)); // used to compare linksets
		$this->assertEquals('*****', $oRemoteiTopConnectionToken2->GetEditValue('token'));
		$this->assertEquals('*****', $oRemoteiTopConnectionToken2->GetAsHTML('token'));
		$this->assertEquals('', $oRemoteiTopConnectionToken2->GetAsCSV('token'));
		$this->assertEquals('', $oRemoteiTopConnectionToken2->GetAsXML('token'));
		$this->assertEquals('*****', $oRemoteiTopConnectionToken2->GetForTemplate('token'));
		$this->assertEquals('*****', $oRemoteiTopConnectionToken2->GetForTemplate('html(token)'));
		$this->assertEquals('*****', $oRemoteiTopConnectionToken2->GetForTemplate('text(token)'));
		$this->assertEquals('*****', (string)$oOrmEncryptedPwd);

		$this->assertEquals('gabuzomeu', $oOrmEncryptedPwd->GetPassword());
	}

	public function testObjectUpdate()
	{
		$oRemoteiTopConnectionToken2 = $this->CreateRemoteiTopConnectionToken2();
		$oRemoteiTopConnectionToken2 = $this->updateObject(RemoteiTopConnectionToken::class, $oRemoteiTopConnectionToken2->GetKey(),
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
		$oRemoteiTopConnectionToken2 = $this->updateObject(RemoteiTopConnectionToken::class, $oRemoteiTopConnectionToken2->GetKey(),
			[
				'token' => ormEncryptedPassword::STARS,
			]
		);

		$oToken = $oRemoteiTopConnectionToken2->Get('token');
		$this->assertEquals('gabuzomeu', $oToken->GetPassword());

		$oRemoteiTopConnectionToken2->Reload();
	}

	public function testEquals()
	{
		$oAttributeDefinition = MetaModel::GetAttributeDef(RemoteiTopConnectionToken::class, 'token');
		$oOrmEncryptedPwd = new ormEncryptedPassword('gabuzomeu');
		$oOrmEncryptedPwd2 = new ormEncryptedPassword('gabuzomeu');
		$this->assertTrue( $oAttributeDefinition->Equals($oOrmEncryptedPwd, $oOrmEncryptedPwd2));
		$oOrmEncryptedPwd2->SetPassword('gabuzomeu2');
		$this->assertFalse($oAttributeDefinition->Equals($oOrmEncryptedPwd, $oOrmEncryptedPwd2));
	}

	public function testIsSearchable()
	{
		$oAttributeDefinition = MetaModel::GetAttributeDef(RemoteiTopConnectionToken::class, 'token');
		$this->assertFalse($oAttributeDefinition->IsSearchable());

		$this->assertEquals(AttributeDefinition::SEARCH_WIDGET_TYPE_RAW, $oAttributeDefinition->GetSearchType());
	}
}
