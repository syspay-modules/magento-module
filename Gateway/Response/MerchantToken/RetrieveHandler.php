<?php

namespace SysPay\Payment\Gateway\Response\MerchantToken;

use SysPay\Payment\Api\Data\CardTokenInterface;
use SysPay\Payment\Gateway\Response\Data\ResolverFactory as ResponseResolverFactory;
use SysPay\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;

class RetrieveHandler implements HandlerInterface
{

    private $responseResolverFactory;

    private $subjectReader;

    /**
     * @param ResponseResolverFactory $responseResolverFactory
     * @param SubjectReader $subjectReader
     */
    public function __construct(
        ResponseResolverFactory $responseResolverFactory,
        SubjectReader           $subjectReader
    )
    {
        $this->responseResolverFactory = $responseResolverFactory;
        $this->subjectReader = $subjectReader;
    }

    /**
     * @param array $handlingSubject
     * @param array $response
     * @return array|void
     */
    public function handle(array $handlingSubject, array $response)
    {
        $responseResolver = $this->responseResolverFactory->create(['response' => $response]);

        /** @var CardTokenInterface $tokenDO */
        $tokenDO = $this->subjectReader->readTokenDo($handlingSubject);

        $responseTokenDO = $responseResolver->getToken();
        $responseMandateDO = $responseResolver->getMandate();
        $responsePaymentMethodDO = $responseResolver->getPaymentMethod();
        $responsePaymentMethodDetailsDO = $responseResolver->getPaymentMethodDetails();

        $tokenDO->setCardDisplay($responsePaymentMethodDO->getDisplay());
        $tokenDO->setCardType($responsePaymentMethodDetailsDO->getScheme());

        $tokenDO->setStatus($responseTokenDO->getStatus());
        $tokenDO->setCreatingDate($responseTokenDO->getCreationDate());
        $tokenDO->setExpirationDate($responseTokenDO->getExpirationDate());

        $tokenDO->setMandateStatus($responseMandateDO->getStatus());
        $tokenDO->setMandateCurrency($responseMandateDO->getCurrency());
        $tokenDO->setMandateStartDate($responseMandateDO->getStartDate());
        $tokenDO->setMandateEndDate($responseMandateDO->getEndDate());

        return $response;
    }
}
