<?php

namespace TeaAndCode\WorldPayXML\Message;

use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * WorldPay XML Redirect Response
 */
class RedirectResponse extends Response implements RedirectResponseInterface
{
    public function getRedirectCookie()
    {
        $cookieJar = $this->request->getCookiePlugin()->getCookieJar();

        foreach ($cookieJar->all() as $cookie)
        {
            if ($cookie->getName() == 'machine')
            {
                return $cookie->getValue();
            }
        }
    }

    public function getRedirectEcho()
    {
        return $this->data->echoData;
    }

    public function getRedirectData()
    {
        return array(
            'PaReq'   => $this->data->requestInfo->request3DSecure->paRequest,
            'TermUrl' => $this->request->getTermUrl()
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