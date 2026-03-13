<?php
namespace Integra\Whatsapp\Controller\Adminhtml\Config;

use Integra\Whatsapp\Model\ConfigFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;

class TestMessage extends Action
{
    const ADMIN_RESOURCE = 'Integra_Whatsapp::config';

    private $configFactory;
    private $curl;
    private $json;

    public function __construct(
        Context $context,
        ConfigFactory $configFactory,
        Curl $curl,
        Json $json
    ) {
        parent::__construct($context);
        $this->configFactory = $configFactory;
        $this->curl = $curl;
        $this->json = $json;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = (int)$this->getRequest()->getParam('entity_id');
        if (!$id) {
            $this->messageManager->addErrorMessage(__('Configuração inválida.'));
            return $resultRedirect->setPath('*/*/');
        }

        $config = $this->configFactory->create();
        $config->load($id);
        if (!$config->getId()) {
            $this->messageManager->addErrorMessage(__('Configuração não encontrada.'));
            return $resultRedirect->setPath('*/*/');
        }

        $provider = (string)($config->getData('api_provider') ?: 'facebook');
        $testPhone = $this->normalizePhone((string)$config->getData('test_phone'));
        $testMessage = (string)($config->getData('test_message') ?: 'Teste de integração WhatsApp');

        if ($testPhone === '') {
            $this->messageManager->addErrorMessage(__('Informe o Telefone para Teste.'));
            return $resultRedirect->setPath('*/*/edit', ['entity_id' => $id]);
        }

        try {
            if ($provider === 'zapi') {
                $this->sendZapiTest($config->getData(), $testPhone, $testMessage);
            } else {
                $this->sendFacebookTest($config->getData(), $testPhone, $testMessage);
            }
            $this->messageManager->addSuccessMessage(__('Mensagem de teste enviada para %1.', $testPhone));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('*/*/edit', ['entity_id' => $id]);
    }

    private function sendFacebookTest(array $configData, string $to, string $message): void
    {
        $phoneNumberId = (string)($configData['phone_number_id'] ?? '');
        $accessToken = (string)($configData['access_token'] ?? '');
        $apiVersion = (string)($configData['facebook_api_version'] ?? 'v23.0');

        if ($phoneNumberId === '' || $accessToken === '') {
            throw new \RuntimeException(__('Para Facebook, preencha Phone Number ID e Access Token.'));
        }

        $url = sprintf('https://graph.facebook.com/%s/%s/messages', rawurlencode($apiVersion), rawurlencode($phoneNumberId));
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'text',
            'text' => [
                'body' => $message,
            ],
        ];

        $this->curl->setHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
        ]);
        $this->curl->post($url, $this->json->serialize($payload));

        $this->assertOkResponse($this->curl->getStatus(), (string)$this->curl->getBody(), 'Facebook');
    }

    private function sendZapiTest(array $configData, string $to, string $message): void
    {
        $instanceId = (string)($configData['zapi_instance_id'] ?? '');
        $token = (string)($configData['zapi_token'] ?? '');
        $clientToken = (string)($configData['zapi_client_token'] ?? '');

        if ($instanceId === '' || $token === '') {
            throw new \RuntimeException(__('Para Z-API, preencha Instance ID e Token.'));
        }

        $url = sprintf(
            'https://api.z-api.io/instances/%s/token/%s/send-text',
            rawurlencode($instanceId),
            rawurlencode($token)
        );
        $payload = [
            'phone' => $to,
            'message' => $message,
        ];

        $headers = [
            'Content-Type' => 'application/json',
        ];
        if ($clientToken !== '') {
            $headers['Client-Token'] = $clientToken;
        }

        $this->curl->setHeaders($headers);
        $this->curl->post($url, $this->json->serialize($payload));

        $this->assertOkResponse($this->curl->getStatus(), (string)$this->curl->getBody(), 'Z-API');
    }

    private function assertOkResponse(int $status, string $body, string $providerLabel): void
    {
        if ($status >= 200 && $status < 300) {
            return;
        }
        $body = trim($body);
        if (mb_strlen($body) > 600) {
            $body = mb_substr($body, 0, 600) . '...';
        }
        if ($body !== '') {
            throw new \RuntimeException(__('%1 retornou HTTP %2: %3', $providerLabel, $status, $body));
        }
        throw new \RuntimeException(__('%1 retornou HTTP %2.', $providerLabel, $status));
    }

    private function normalizePhone(string $phone): string
    {
        return preg_replace('/\D+/', '', $phone) ?: '';
    }
}
