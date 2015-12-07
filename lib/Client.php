<?php

namespace Tamble\Bluedrone\Api;


use Guzzle\Http\Client as HttpClient;
use Guzzle\Http\ClientInterface as HttpClientInterface;
use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Exception\RequestException;
use Guzzle\Parser\UriTemplate\UriTemplate;
use Tamble\Bluedrone\Api\Token\Storage\StorageInterface;
use Tamble\Bluedrone\Api\Token\Token;

class Client
{
    const VERSION = "0.1.6";

    public static $baseUrl = "https://api.bluedrone.com";

    protected $uriTemplates = array(
        'channels.get' => '/channels{?offset,limit}',
        'channel.get' => '/channels/{id}',
        'products.get' => '/products{?sales_channel_id,unit_system,offset,limit}',
        'product.get' => '/products/{sku}{?unit_system}',
        'product.put' => '/products/{sku}',
        'product.delete' => '/products/{sku}',
        'orders.get' => '/channels/{sales_channel_id}/orders{?unit_system,offset,limit}',
        'order.get' => '/channels/{sales_channel_id}/orders/{external_order_id}{?unit_system}',
        'order.put' => '/channels/{sales_channel_id}/orders/{external_order_id}',
        'order.delete' => '/channels/{sales_channel_id}/orders/{external_order_id}{?unit_system}',
        'shipments.get' => '/channels/{sales_channel_id}/orders/{external_order_id}/shipments/{?unit_system,offset,limit}',
        'shipment.get' => '/channels/{sales_channel_id}/orders/{external_order_id}/shipments/{id}{?unit_system}',
        'hooks.get' => '/channels/{sales_channel_id}/hooks{?offset,limit}',
        'hook.get' => '/channels/{sales_channel_id}/hooks/{id}',
        'hook.post' => '/channels/{sales_channel_id}/hooks',
        'hook.put' => '/channels/{sales_channel_id}/hooks/{id}',
        'hook.delete' => '/channels/{sales_channel_id}/hooks/{id}',
        'token' => '/oauth2/token'
    );

    /**
     * @var StorageInterface
     */
    protected $tokenStorage;

    /**
     * @var string
     */
    protected $clientId;
    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var HttpClientInterface
     */
    protected $httpClient;

    /**
     * @param string           $clientId
     * @param string           $clientSecret
     * @param StorageInterface $tokenStorage
     */
    public function __construct($clientId, $clientSecret, StorageInterface $tokenStorage)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param null $offset
     * @param null $limit
     *
     * @return array|bool|float|int|string
     */
    public function getSalesChannels($offset = null, $limit = null)
    {
        return $this->request('get', 'channels', array('offset' => $offset, 'limit' => $limit));
    }

    public function getSalesChannel($id)
    {
        return $this->request('get', 'channel', array('id' => $id));
    }

    public function getProducts($salesChannelId = null, $unitSystem = null, $offset = null, $limit = null)
    {
        return $this->request(
            'get',
            'products',
            array(
                'sales_channel_id' => $salesChannelId,
                'unit_system' => $unitSystem,
                'offset' => $offset,
                'limit' => $limit
            )
        );
    }

    public function getProduct($sku, $unitSystem = null)
    {
        return $this->request('get', 'product', array('sku' => $sku, 'unit_system' => $unitSystem));
    }

    public function createOrUpdateProduct($sku, array $product)
    {
        return $this->request('put', 'product', array('sku' => $sku), $product);
    }

    public function removeProduct($sku)
    {
        return $this->request('delete', 'product', array('sku' => $sku));
    }

    public function getOrders($salesChannelId, $unitSystem = null, $offset = null, $limit = null)
    {
        return $this->request(
            'get',
            'orders',
            array(
                'sales_channel_id' => $salesChannelId,
                'unit_system' => $unitSystem,
                'offset' => $offset,
                'limit' => $limit
            )
        );
    }

    public function getOrder($salesChannelId, $externalOrderId, $unitSystem = null)
    {
        return $this->request(
            'get',
            'order',
            array(
                'sales_channel_id' => $salesChannelId,
                'external_order_id' => $externalOrderId,
                'unit_system' => $unitSystem
            )
        );
    }

    public function createOrUpdateOrder($salesChannelId, $externalOrderId, array $order)
    {
        return $this->request(
            'put',
            'order',
            array(
                'sales_channel_id' => $salesChannelId,
                'external_order_id' => $externalOrderId,
            ),
            $order
        );
    }

    public function removeOrder($salesChannelId, $externalOrderId)
    {
        return $this->request(
            'delete',
            'order',
            array(
                'sales_channel_id' => $salesChannelId,
                'external_order_id' => $externalOrderId,
            )
        );
    }

    public function getShipments($salesChannelId, $externalOrderId, $unitSystem = null, $offset = null, $limit = null)
    {
        return $this->request(
            'get',
            'shipments',
            array(
                'sales_channel_id' => $salesChannelId,
                'external_order_id' => $externalOrderId,
                'unit_system' => $unitSystem,
                'offset' => $offset,
                'limit' => $limit
            )
        );
    }

    public function getShipment($salesChannelId, $externalOrderId, $shipmentId, $unitSystem = null)
    {
        return $this->request(
            'get',
            'shipment',
            array(
                'sales_channel_id' => $salesChannelId,
                'external_order_id' => $externalOrderId,
                'id' => $shipmentId,
                'unit_system' => $unitSystem
            )
        );
    }

    public function getHooks($salesChannelId = null, $offset = null, $limit = null)
    {
        return $this->request(
            'get',
            'hooks',
            array(
                'sales_channel_id' => $salesChannelId,
                'offset' => $offset,
                'limit' => $limit
            )
        );
    }

    public function getHook($salesChannelId, $hookId)
    {
        return $this->request(
            'get',
            'hook',
            array(
                'sales_channel_id' => $salesChannelId,
                'id' => $hookId
            )
        );
    }

    public function createHook($salesChannelId, array $hook)
    {
        return $this->request(
            'post',
            'hook',
            array(
                'sales_channel_id' => $salesChannelId
            ),
            $hook
        );
    }

    public function updateHook($salesChannelId, $hookId, array $hook)
    {
        return $this->request(
            'put',
            'hook',
            array(
                'sales_channel_id' => $salesChannelId,
                'id' => $hookId
            ),
            $hook
        );
    }

    /**
     * @param $salesChannelId
     * @param $hookId
     *
     * @return array|bool|float|int|string
     */
    public function removeHook($salesChannelId, $hookId)
    {
        return $this->request(
            'delete',
            'hook',
            array(
                'sales_channel_id' => $salesChannelId,
                'id' => $hookId
            )
        );
    }

    /**
     * @param HttpClientInterface $httpClient
     */
    public function setHttpClient(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @return HttpClientInterface
     */
    public function getHttpClient()
    {
        if ($this->httpClient === null) {
            $this->httpClient = new HttpClient();
        }
        return $this->httpClient;
    }

    /**
     * @return string
     */
    public static function getBaseUrl()
    {
        return static::$baseUrl;
    }

    /**
     * @param string $baseUrl
     */
    public static function setBaseUrl($baseUrl)
    {
        static::$baseUrl = $baseUrl;
    }


    /**
     * @param string $httpVerb
     * @param string $urlTemplate
     * @param array  $urlVariables
     * @param array  $payload
     *
     * @return array|bool|float|int|string
     * @throws BluedroneException
     */
    protected function request($httpVerb, $urlTemplate, array $urlVariables = array(), array $payload = array())
    {
        try {
            $token = $this->tokenStorage->fetchToken();
            if (!$token || $token->isExpired()) {
                $token = $this->requestToken();
                $this->tokenStorage->storeToken($token);
            }

            $url = $this->getResourceUrl($urlTemplate . '.' . $httpVerb, $urlVariables);
            $request = $this->getHttpClient()->createRequest(strtoupper($httpVerb), $url);
            $request->setHeader('Authorization', 'Bearer ' . $token);
            $request->addHeaders($this->getUserAgentHeaders());

            if (count($payload)) {
                $request->setBody(json_encode($payload), 'application/json');
            }

            $response = $request->send();

            if ($response->getStatusCode() == 204) {
                return true;
            }

            $jsonData = $response->json();
        } catch (\Exception $e) {
            throw $this->getBluedroneException($e);
        }

        return $jsonData;
    }

    /**
     * Requests a new access token from the oauth2 token access point.
     *
     * @return Token
     */
    protected function requestToken()
    {
        $request = $this->getHttpClient()->createRequest('POST', $this->getResourceUrl('token'));
        $request->setAuth($this->clientId, $this->clientSecret);
        $request->setPostField('grant_type', 'client_credentials');
        $request->addHeaders($this->getUserAgentHeaders());

        $response = $request->send();
        $tokenData = $response->json();

        return new Token($tokenData['access_token'], time() + $tokenData['expires_in']);
    }

    /**
     * Expands a URL template with the $variables and returns the result.
     *
     * @param string $resourceName
     * @param array  $variables
     *
     * @return mixed|string
     */
    protected function getResourceUrl($resourceName, array $variables = array())
    {
        $template = $this->uriTemplates[$resourceName];
        $uriTemplate = new UriTemplate();
        $path = $uriTemplate->expand($template, $variables);
        $url = rtrim(static::$baseUrl, '/') . '/' . ltrim($path, '/');
        return $url;
    }

    /**
     * It converts all exceptions to Bluedrone Api Exceptions which are
     * in fact Problem objects. The Problem object offers
     * more details about the error that occurred.
     *
     * @param \Exception $e
     *
     * @return BluedroneException
     */
    protected function getBluedroneException(\Exception $e)
    {
        if ($e instanceof BadResponseException) {
            // if it's a 4xx or 5xx error
            $statusCode = $e->getResponse()->getStatusCode();
            $body = $e->getResponse()->getBody(true);
            $jsonBody = json_decode($body, true);

            // check to see if the server responded well behaved (with a json object)
            if ($jsonBody !== null && count($jsonBody) != 0) {
                $bluedroneException = BluedroneException::fromArray($jsonBody);
            } else {
                // if not, it means we're talking about an unrecoverable server error
                $bluedroneException = new BluedroneException(
                    'Unrecoverable Server Error',
                    0,
                    "The server responded with code '$statusCode' but did not provide more details about the error."
                );
            }
        } elseif ($e instanceof RequestException) {
            // if it's a connection error
            $bluedroneException = new BluedroneException('Connection Error', 0, $e->getMessage());
        } else {
            $bluedroneException = new BluedroneException('Exception', 0, $e->getMessage());
        }

        return $bluedroneException;
    }

    /**
     * Composes some debug headers to be sent with each request.
     * It helps us identify some details about the type of library
     * that hits our API.
     *
     * @return array
     */
    protected function getUserAgentHeaders()
    {
        $langVersion = phpversion();
        $uname = php_uname();
        $userAgent = array(
            'bindings_version' => static::VERSION,
            'lang' => 'php',
            'lang_version' => $langVersion,
            'publisher' => 'bluedrone',
            'uname' => $uname
        );
        $headers = array(
            'X-BlueDrone-Client-User-Agent' => json_encode($userAgent),
            'User-Agent' => 'Bluedrone/v1 PhpClient/' . static::VERSION
        );

        return $headers;
    }
}