<?php

namespace TeaAndCode\WorldPayXML;

use Omnipay\Common\AbstractGateway;
use TeaAndCode\WorldPayXML\Message\OrderRequest;

/**
 * WorldPay XML Class
 *
 * @link http://www.worldpay.com/support/bg/xml/kb/dxml_inv.pdf
 */
class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'WorldPayXML';
    }

    public function getDefaultParameters()
    {
        return array(
            'installation' => '',
            'merchant'     => '',
            'password'     => '',
            'testMode'     => false,
        );
    }

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

    public function purchase(array $parameters = array())
    {
        return $this->createRequest(
            '\TeaAndCode\WorldPayXML\Message\PurchaseRequest',
            $parameters
        );
    }
}