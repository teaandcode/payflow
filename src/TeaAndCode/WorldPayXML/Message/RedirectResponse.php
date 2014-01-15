<?php

namespace TeaAndCode\WorldPayXML\Message;

use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * WorldPay XML Redirect Response
 */
class RedirectResponse extends Response implements RedirectResponseInterface
{
    public function getRedirectData()
    {
        return array(
            'PaReq' => $this->data->requestInfo->request3DSecure->paRequest
        );
    }

    public function getRedirectMethod()
    {
        return 'POST';
    }

    public function getRedirectUrl()
    {
        return $this->data->requestInfo->request3DSecure->issuerURL;
    }
}