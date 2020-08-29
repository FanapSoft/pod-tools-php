<?php
use PHPUnit\Framework\TestCase;
use Pod\Tools\Service\ToolsService;
use Pod\Base\Service\BaseInfo;
use Pod\Base\Service\Exception\ValidationException;
use Pod\Base\Service\Exception\PodException;

final class PayedBillListTest extends TestCase
{
    public static $ToolsService;
    private $token;
    public function setUp(): void
   {
        parent::setUp();
        # set serverType to SandBox or Production
        BaseInfo::initServerType(BaseInfo::SANDBOX_SERVER);
        $testData =  require __DIR__ . '/testData.php';
        $this->token = $testData['token'];

        $baseInfo = new BaseInfo();
        $baseInfo->setToken($this->token);
		self::$ToolsService = new ToolsService($baseInfo);
    }

	public function testPayedBillListAllParameters()
	{
		$params = [
			## ================= *Required Parameters  =================
			'offset' => 0,
			'size' => 10,
			## ================= Optional Parameters  =================
			'id' => 123,
			'referenceNumber' => '124',
			'fromDate' => '2019-12-01',
			'toDate' => '2019-12-29',
			'billId' => 432,
			'paymentId' => 654,
			'token'     => '{Put Token}',
			//'scApiKey' => '{Put Service Call Api Key}',
			//'scVoucherHash' => '['{Put Service Call Voucher Hashes}', ...]',
		];
		try {
			$result = $ToolsService->payedBillList($params);
			$this->assertFalse($result['error']);
			$this->assertEquals($result['code'], 200);
		} catch (ValidationException $e) {
			$this->fail('ValidationException: ' . $e->getErrorsAsString());
		} catch (PodException $e) {
			$error = $e->getResult();
			$this->fail('PodException: ' . $error['message']);
		}
	}

	public function testPayedBillListRequiredParameters()
	{
		$params = [
			## ================= *Required Parameters  =================
			'offset' => 0,
			'size' => 10,
		try {
			$result = $ToolsService->payedBillList($params);
			$this->assertFalse($result['error']);
			$this->assertEquals($result['code'], 200);
		} catch (ValidationException $e) {
			$this->fail('ValidationException: ' . $e->getErrorsAsString());
		} catch (PodException $e) {
			$error = $e->getResult();
			$this->fail('PodException: ' . $error['message']);
		}
	}

	public function testPayedBillListValidationError()
	{
		$paramsWithoutRequired = [];
		$paramsWrongValue = [
			## ======================= *Required Parameters  ==========================
			'offset' => '123',
			'size' => '123',
			## ======================== Optional Parameters  ==========================
			'id' => '123',
			'referenceNumber' => 123,
			'fromDate' => 123,
			'toDate' => 123,
			'billId' => 123,
			'paymentId' => 123,
			'scVoucherHash' => '123',
			'scApiKey' => 123,
		];
		try {
			self::$ToolsService->payedBillList($paramsWithoutRequired);
		} catch (ValidationException $e) {
			$validation = $e->getErrorsAsArray();
			$this->assertNotEmpty($validation);

			$result = $e->getResult();

			$this->assertArrayHasKey('offset', $validation);
			$this->assertEquals('The property offset is required', $validation['offset'][0]);

			$this->assertArrayHasKey('size', $validation);
			$this->assertEquals('The property size is required', $validation['size'][0]);


			$this->assertEquals(887, $result['code']);
		} catch (PodException $e) {
			$error = $e->getResult();
			$this->fail('PodException: ' . $error['message']);
		}
		try {
			self::$ToolsService->payedBillList($paramsWrongValue);
		} catch (ValidationException $e) {

			$validation = $e->getErrorsAsArray();
			$this->assertNotEmpty($validation);

			$result = $e->getResult();
			$this->assertArrayHasKey('id', $validation);
			$this->assertEquals('String value found, but an integer is required', $validation['id'][0]);

			$this->assertArrayHasKey('referenceNumber', $validation);
			$this->assertEquals('Integer value found, but a string is required', $validation['referenceNumber'][0]);

			$this->assertArrayHasKey('billId', $validation);
			$this->assertEquals('Integer value found, but a string is required', $validation['billId'][0]);

			$this->assertArrayHasKey('paymentId', $validation);
			$this->assertEquals('Integer value found, but a string is required', $validation['paymentId'][0]);

			$this->assertArrayHasKey('offset', $validation);
			$this->assertEquals('String value found, but an integer is required', $validation['offset'][1]);

			$this->assertArrayHasKey('size', $validation);
			$this->assertEquals('String value found, but a number is required', $validation['size'][1]);

			$this->assertArrayHasKey('scVoucherHash', $validation);
			$this->assertEquals('String value found, but an array is required', $validation['scVoucherHash'][0]);

			$this->assertArrayHasKey('scApiKey', $validation);
			$this->assertEquals('Integer value found, but a string is required', $validation['scApiKey'][0]);

			$this->assertEquals(887, $result['code']);
		} catch (PodException $e) {
			$error = $e->getResult();
			$this->fail('PodException: ' . $error['message']);
		}
	}

}