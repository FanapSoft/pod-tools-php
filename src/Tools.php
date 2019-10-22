<?php
/**
 * Created by PhpStorm.
 * User :  keshtgar
 * Date :  6/19/19
 * Time : 12:29 PM
 *
 * $baseInfo BaseInfo
 */

namespace Pod\Tools;

use Pod\Base\Service\BaseService;
use Pod\Base\Service\ApiRequestHandler;

class Tools extends BaseService
{
    private static $serviceProductId;
    private $header;
    const BASE_URI = 'PLATFORM-ADDRESS';
    const SUB_URI = 'nzh/doServiceCall';
    const METHOD = 'POST';

    public function __construct($baseInfo)
    {
        parent::__construct();
        self::$jsonSchema = json_decode(file_get_contents(__DIR__ . '/../config/validationSchema.json'), true);
        self::$serviceProductId = require __DIR__ . '/../config/serviceProductId.php';
        $this->header = [
            '_token_issuer_'    =>  $baseInfo->getTokenIssuer(),
            '_token_'           => $baseInfo->getToken(),
        ];
    }

    public function payBill($params) {
        $apiName = 'payBill';
        $header = $this->header;
        array_walk_recursive($params, 'self::prepareData');

        $paramKey = self::METHOD == 'GET' ? 'query' : 'form_params';

        // set tokenIssuer in header
        if (isset($params['tokenIssuer'])) {
            $header['_token_issuer_'] = $params['tokenIssuer'];
            unset($params['tokenIssuer']);
        }

        // set token in header
        if (isset($params['token'])) {
            $header['_token_'] = $params['token'];
            unset($params['token']);
        }

        $option = [
            'headers' => $header,
            $paramKey => $params,
        ];

        self::validateOption($apiName, $option, $paramKey);

        // prepare params to send
        $option[$paramKey]['scProductId'] = self::$serviceProductId[$apiName];
        return  ApiRequestHandler::Request(
            self::$config[self::$serverType][self::BASE_URI],
            self::METHOD,
            self::SUB_URI,
            $option
        );
    }

    public function payedBillList($params) {
        $apiName = 'payedBillList';
        $header = $this->header;

        array_walk_recursive($params, 'self::prepareData');
        $paramKey = self::METHOD == 'GET' ? 'query' : 'form_params';
        // set tokenIssuer in header
        if (isset($params['tokenIssuer'])) {
            $header['_token_issuer_'] = $params['tokenIssuer'];
            unset($params['tokenIssuer']);
        }

        // set token in header
        if (isset($params['token'])) {
            $header['_token_'] = $params['token'];
            unset($params['token']);
        }

        $option = [
            'headers' => $header,
            $paramKey => $params,
        ];

        self::validateOption($apiName, $option, $paramKey);

        // prepare params to send
        $option[$paramKey]['scProductId'] = self::$serviceProductId[$apiName];
        return  ApiRequestHandler::Request(
            self::$config[self::$serverType][self::BASE_URI],
            self::METHOD,
            self::SUB_URI,
            $option
        );
    }
}