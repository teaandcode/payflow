<?php

namespace TeaAndCode\WorldPayXML\Message;

use DOMDocument;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;

/**
 * WorldPay XML Response
 */
class Response extends AbstractResponse
{
    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;
error_log(print_r((string) $data, true));
        $responseDom = new DOMDocument;
        $responseDom->loadXML($data);

        $this->data = simplexml_import_dom(
            $responseDom->documentElement->firstChild->firstChild
        );
    }

    public function isSuccessful()
    {
        if (!empty($this->data->payment->lastEvent))
        {
            if (strtoupper($this->data->payment->lastEvent) == 'AUTHORISED')
            {
                return true;
            }
        }

        return null;
    }

    public function getTransactionReference()
    {
        $attributes = $this->data->attributes();

        if (isset($attributes['orderCode']))
        {
            if ($this->request->getTransactionId() == $attributes['orderCode'])
            {
                return $attributes['orderCode'];
            }
        }

        return null;
    }

    public function getMessage()
    {
        $code  = -1;
        $codes = array(
            -1 => 'PENDING',
            0  => 'AUTHORISED',
            2  => 'REFERRED',
            3  => 'INVALID ACCEPTOR',
            4  => 'HOLD CARD',
            5  => 'REFUSED',
            8  => 'APPROVE AFTER IDENTIFICATION',
            12 => 'INVALID TRANSACTION',
            13 => 'INVALID AMOUNT',
            14 => 'INVALID ACCOUNT',
            15 => 'INVALID CARD ISSUER',
            17 => 'ANNULATION BY CLIENT',
            19 => 'REPEAT OF LAST TRANSACTION',
            20 => 'ACQUIRER ERROR',
            21 => 'REVERSAL NOT PROCESSED, MISSING AUTHORISATION',
            24 => 'UPDATE OF FILE IMPOSSIBLE',
            25 => 'REFERENCE NUMBER CANNOT BE FOUND',
            26 => 'DUPLICATE REFERENCE NUMBER',
            27 => 'ERROR IN REFERENCE NUMBER FIELD',
            28 => 'ACCESS DENIED',
            29 => 'IMPOSSIBLE REFERENCE NUMBER',
            30 => 'FORMAT ERROR',
            31 => 'UNKNOWN ACQUIRER ACCOUNT CODE',
            33 => 'CARD EXPIRED',
            34 => 'FRAUD SUSPICION',
            38 => 'SECURITY CODE EXPIRED',
            40 => 'REQUESTED FUNCTION NOT SUPPORTED',
            41 => 'LOST CARD',
            43 => 'STOLEN CARD, PICK UP',
            51 => 'LIMIT EXCEEDED',
            55 => 'INVALID SECURITY CODE',
            56 => 'UNKNOWN CARD',
            57 => 'ILLEGAL TRANSACTION',
            58 => 'TRANSACTION NOT PERMITTED',
            62 => 'RESTRICTED CARD',
            63 => 'SECURITY RULES VIOLATED',
            64 => 'AMOUNT HIGHER THAN PREVIOUS TRANSACTION AMOUNT',
            68 => 'TRANSACTION TIMED OUT',
            75 => 'SECURITY CODE INVALID',
            76 => 'CARD BLOCKED',
            80 => 'AMOUNT NO LONGER AVAILABLE, AUTHORISATION EXPIRED',
            85 => 'REJECTED BY CARD ISSUER',
            91 => 'CREDITCARD ISSUER TEMPORARILY NOT REACHABLE',
            92 => 'CREDITCARD TYPE NOT PROCESSED BY ACQUIRER',
            94 => 'DUPLICATE REQUEST ERROR',
            97 => 'SECURITY BREACH'
        );

        if (!empty($this->data->payment->ISO8583ReturnCode))
        {
            $attributes = $this->data->payment->ISO8583ReturnCode->attributes();

            if (array_key_exists('code', (array) $attributes))
            {
                $returned = (int) $attributes['code'];

                if (isset($codes[$returned]))
                {
                    $code = $returned;
                }
            }
        }

        if ($this->isSuccessful())
        {
            $code = 0;
        }

        return $codes[$code];
    }
}