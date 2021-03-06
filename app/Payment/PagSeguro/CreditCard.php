<?php
/**
 * Created by PhpStorm.
 * User: gabriella
 * Date: 20/12/20
 * Time: 11:37
 */
namespace App\Payment\PagSeguro;
class CreditCard
{
    private $items;
    private $cardInfo;
    private $user;
    private $reference;
    public function __construct($items, $user, $cardInfo,$reference)
    {
        $this->items = $items;
        $this->user  = $user;
        $this->cardInfo = $cardInfo;
        $this->reference = $reference;
    }
    public function doPayment()
    {

        $creditCard = new \PagSeguro\Domains\Requests\DirectPayment\CreditCard();

        $creditCard->setReceiverEmail(env('PAGSEGURO_EMAIL'));
        $creditCard->setReference(base64_encode($this->reference));
        $creditCard->setCurrency("BRL");

        foreach ($this->items as $item) {
            $creditCard->addItems()->withParameters(
                $this->reference,
                $item['name'],
                $item['amount'],
                $item['price']
            );
        }


        $user = $this->user;
        $email = env('PAGSEGURO_ENV') == 'sandbox' ? 'text@sandbox.pagseguro.com.br' : $user->email;
        $creditCard->setSender()->setName($user->name);
        $creditCard->setSender()->setEmail($email);

        $creditCard->setSender()->setPhone()->withParameters(
            11,
            56273440
        );

        $creditCard->setSender()->setDocument()->withParameters(
            'CPF',
            '33629734006'
        );

        $creditCard->setSender()->setHash($this->cardInfo['hash']);

        $creditCard->setSender()->setIp('127.0.0.0');

        $creditCard->setShipping()->setAddress()->withParameters(
            'Av. Brig. Faria Lima',
            '1384',
            'Jardim Paulistano',
            '01452002',
            'São Paulo',
            'SP',
            'BRA',
            'apto. 114'
        );

        $creditCard->setBilling()->setAddress()->withParameters(
            'Av. Brig. Faria Lima',
            '1384',
            'Jardim Paulistano',
            '01452002',
            'São Paulo',
            'SP',
            'BRA',
            'apto. 114'
        );

        $creditCard->setToken($this->cardInfo['card_token']);
        list($quantity,$installmentamount) = explode('|',$this->cardInfo['installment']);

        $installmentamount = number_format($installmentamount,2,'.','');
        $creditCard->setInstallment()->withParameters($quantity, $installmentamount);

        $creditCard->setHolder()->setBirthdate('01/10/1979');
        $creditCard->setHolder()->setName($this->cardInfo['card_name']); // Equals in Credit Card

        $creditCard->setHolder()->setPhone()->withParameters(
            11,
            56273440
        );

        $creditCard->setHolder()->setDocument()->withParameters(
            'CPF',
            '33629734006'
        );
        $creditCard->setMode('DEFAULT');
        $result = $creditCard->register(
            \PagSeguro\Configuration\Configure::getAccountCredentials()
        );


        return $result;
    }
}