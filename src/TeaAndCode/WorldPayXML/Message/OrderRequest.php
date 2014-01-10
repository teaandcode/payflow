<?php

namespace TeaAndCode\WorldPayXML\Message;

use DOMDocument;
use Omnipay\Common\CreditCard;
use Omnipay\Common\Message\AbstractRequest;
use SimpleXMLElement;

/**
 * WorldPay XML Purchase Request
 */
class PurchaseRequest extends AbstractRequest
{
    const EP_LIVE = 'https://secure.worldpay.com' .
                    '/jsp/merchant/xml/paymentService.jsp';
    const EP_TEST = 'https://secure-test.worldpay.com' .
                    '/jsp/merchant/xml/paymentService.jsp';

    const VERSION = '1.4';

    public function getInstallation()
    {
        return $this->getParameter('installation');
    }

    public function setInstallation($value)
    {
        return $this->setParameter('installation', $value);
    }

    public function getMerchant()
    {
        return $this->getParameter('merchant');
    }

    public function setMerchant($value)
    {
        return $this->setParameter('merchant', $value);
    }

    public function getPassword()
    {
        return $this->getParameter('password');
    }

    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    public function getIP()
    {
        return $this->getParameter('ip');
    }

    public function setIP($value)
    {
        return $this->setParameter('ip', $value);
    }

    public function getSession()
    {
        return $this->getParameter('session');
    }

    public function setSession($value)
    {
        return $this->setParameter('session', $value);
    }

    public function getData()
    {
        $this->validate('amount', 'card');
        $this->getCard()->validate();

        $data = new SimpleXMLElement('<paymentService />');
        $data->addAttribute('merchantCode', $this->getMerchant());
        $data->addAttribute('version', self::VERSION);

        $order = $data->addChild('submit')->addChild('order');
        $order->addAttribute('installationId', $this->getInstallation());
        $order->addAttribute('orderCode', $this->getTransactionId());

        $order->addChild('description', $this->getDescription());

        $amount = $order->addChild('amount');
        $amount->addAttribute('currencyCode', $this->getCurrency());
        $amount->addAttribute('exponent', $this->getCurrencyDecimalPlaces());
        $amount->addAttribute('value', $this->getAmountInteger());

        $payment = $order->addChild('paymentDetails');

        $codes = array(
            CreditCard::BRAND_VISA        => 'VISA-SSL',
            CreditCard::BRAND_MASTERCARD  => 'ECMC-SSL',
            CreditCard::BRAND_DISCOVER    => 'DISCOVER-SSL',
            CreditCard::BRAND_AMEX        => 'AMEX-SSL',
            CreditCard::BRAND_DINERS_CLUB => 'DINERS-SSL',
            CreditCard::BRAND_JCB         => 'JCB-SSL',
            CreditCard::BRAND_DANKORT     => 'DANKORT-SSL',
            CreditCard::BRAND_MAESTRO     => 'MAESTRO-SSL',
            CreditCard::BRAND_LASER       => 'LASER-SSL'
        );

        $card = $payment->addChild($codes[$this->getCard()->getBrand()]);
        $card->addChild('cardNumber', $this->getCard()->getNumber());

        $expiry = $card->addChild('expiryDate')->addChild('date');
        $expiry->addAttribute('month', $this->getCard()->getExpiryDate('m'));
        $expiry->addAttribute('year', $this->getCard()->getExpiryDate('Y'));

        $card->addChild('cardHolderName', $this->getCard()->getName());
        $card->addChild('cvc', $this->getCard()->getCvv());

        $address = $card->addChild('cardAddress')->addChild('address');
        $address->addChild('street', $this->getCard()->getAddress1());
        $address->addChild('postalCode', $this->getCard()->getPostcode());
        $address->addChild('countryCode', $this->getCard()->getCountry());

        $session = $payment->addChild('session');
        $session->addAttribute('shopperIPAddress', $this->getUserIP());
        $session->addAttribute('id', $this->getSession());

        $shopper = $order->addChild('shopper');
        $shopper->addChild('shopperEmailAddress', $this->getCard()->getEmail());

        $browser = $shopper->addChild('browser');
        $browser->addChild('acceptHeader', $this->getAcceptHeader());
        $browser->addChild('userAgentHeader', $this->getUserAgentHeader());

        return $data;
    }

    public function sendData($data)
    {
        // the PHP SOAP library sucks, and SimpleXML can't append element trees
        // TODO: find PSR-0 SOAP library
        $document = new DOMDocument('1.0', 'utf-8');
        $envelope = $document->appendChild(
            $document->createElementNS('http://schemas.xmlsoap.org/soap/envelope/', 'soap:Envelope')
        );
        $envelope->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $envelope->setAttribute('xmlns:xsd', 'http://www.w3.org/2001/XMLSchema');
        $body = $envelope->appendChild($document->createElement('soap:Body'));
        $body->appendChild($document->importNode(dom_import_simplexml($data), true));

        // post to Cardsave
        $headers = array(
            'Content-Type' => 'text/xml; charset=utf-8',
            'SOAPAction' => $this->namespace.$data->getName());

        $httpResponse = $this->httpClient->post($this->endpoint, $headers, $document->saveXML())->send();

        return $this->response = new Response($this, $httpResponse->getBody());
    }

    protected function getEndpoint()
    {
        if ($this->getTestMode())
        {
            return self::EP_TEST;
        }

        return self::EP_LIVE;
    }
}