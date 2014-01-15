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
            'PaReq'   => $this->data->requestInfo->request3DSecure->paRequest,
            'TermUrl' => 'http://www.davenash.com' // This must be changed!
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