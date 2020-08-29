<?php
use PHPUnit\Framework\TestCase;
use Pod\Tools\Service\ToolsService;
use Pod\Base\Service\BaseInfo;
use Pod\Base\Service\Exception\ValidationException;
use Pod\Base\Service\Exception\PodException;

final class PayBillTest extends TestCase
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
        $this->scApiKey = $testData['scApiKey'];

        $baseInfo = new BaseInfo();
        $baseInfo->setToken($this->token);
		self::$ToolsService = new ToolsService($baseInfo);
    }

	public function testPayBillAllParameters()
	{
		$params = [
			## ================= *Required Parameters  =================
			'billId' => 1234,
			'paymentId' => 12,
			## ================= Optional Parameters  =================
			'token'     => '{Put Token}',
			//'scApiKey' => $this->scApiKey,
			//'scVoucherHash' => '['{Put Service Call Voucher Hashes}', ...]',
		];
		try {
			$result = $ToolsService->payBill($params);
			$this->assertFalse($result['error']);
			$this->assertEquals($result['code'], 200);
		} catch (ValidationException $e) {
			$this->fail('ValidationException: ' . $e->getErrorsAsString());
		} catch (PodException $e) {
			$error = $e->getResult();
			$this->fail('PodException: ' . $error['message']);
		}
	}

	public function testPayBillRequiredParameters()
	{
		$params = [
			## ================= *Required Parameters  =================
			'billId' => 1234,
			'paymentId' => 12,
		try {
			$result = $ToolsService->payBill($params);
			$this->assertFalse($result['error']);
			$this->assertEquals($result['code'], 200);
		} catch (ValidationException $e) {
			$this->fail('ValidationException: ' . $e->getErrorsAsString());
		} catch (PodException $e) {
			$error = $e->getResult();
			$this->fail('PodException: ' . $error['message']);
		}
	}

	public function testPayBillValidationError()
	{
		$paramsWithoutRequired = [];
		$paramsWrongValue = [
			## ======================= *Required Parameters  ==========================
			'billId' => 123,
			'paymentId' => 123,
			## ======================== Optional Parameters  ==========================
			'scVoucherHash' => '123',
			'scApiKey' => 123,
		];
		try {
			self::$ToolsService->payBill($paramsWithoutRequired);
		} catch (ValidationException $e) {
			$validation = $e->getErrorsAsArray();
			$this->assertNotEmpty($validation);

			$result = $e->getResult();

			$this->assertArrayHasKey('billId', $validation);
			$this->assertEquals('The property billId is required', $validation['billId'][0]);

			$this->assertArrayHasKey('paymentId', $validation);
			$this->assertEquals('The property paymentId is required', $validation['paymentId'][0]);


			$this->assertEquals(887, $result['code']);
		} catch (PodException $e) {
			$error = $e->getResult();
			$this->fail('PodException: ' . $error['message']);
		}
		try {
			self::$ToolsService->payBill($paramsWrongValue);
		} catch (ValidationException $e) {

			$validation = $e->getErrorsAsArray();
			$this->assertNotEmpty($validation);

			$result = $e->getResult();
			$this->assertArrayHasKey('billId', $validation);
			$this->assertEquals('Integer value found, but a string is required', $validation['billId'][1]);

			$this->assertArrayHasKey('scVoucherHash', $validation);
			$this->assertEquals('String value found, but an array is required', $validation['scVoucherHash'][0]);

			$this->assertArrayHasKey('scApiKey', $validation);
			$this->assertEquals('Integer value found, but a string is required', $validation['scApiKey'][0]);

			$this->assertArrayHasKey('paymentId', $validation);
			$this->assertEquals('Integer value found, but a string is required', $validation['paymentId'][1]);

			$this->assertEquals(887, $result['code']);
		} catch (PodException $e) {
			$error = $e->getResult();
			$this->fail('PodException: ' . $error['message']);
		}
	}

}