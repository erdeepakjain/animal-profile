<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Razoyo\AnimalProfile\Controller\Profile;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;
use Razoyo\AnimalProfile\Animal;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;

class Photo implements HttpGetActionInterface
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var Json
     */
    protected $serializer;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var Http
     */
    protected $http;

    /**
     * Constructor
     *
     * @param PageFactory $resultPageFactory
     * @param Json $json
     * @param LoggerInterface $logger
     * @param Http $http
     */
    public function __construct(
        PageFactory $resultPageFactory,
        Json $json,
        LoggerInterface $logger,
        Http $http,
        HttpRequest $httpRequest,
        CustomerRepositoryInterface $customerRepository,
        Session $customerSession
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->serializer = $json;
        $this->logger = $logger;
        $this->http = $http;
        $this->httpRequest = $httpRequest;
        $this->_customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
    }

    /**
     * Execute view action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $profile = $this->httpRequest->getParam('animal_profile');
        $customerId = $this->customerSession->getCustomer()->getId();
        $photo = new Animal\Cat();
        if ($profile) {
            switch ($profile) {
                case "cat":
                    $photo = new Animal\Cat();
                    break;
                case "dog":
                    $photo = new Animal\Dog();
                    break;
                case "llama":
                    $photo = new Animal\Llama();
                    break;
                case "anteater":
                    $photo = new Animal\Anteater();
                    break;
                default:
                    $photo = new Animal\Cat();
            }
        }

        try {
            if ($customerId) {
                $customer = $this->_customerRepository->getById($customerId);
                $saveProfile = $customer->getCustomAttribute('animal_profile');
                $saveProfileVal = '';
                if ($saveProfile) {
                    $saveProfileVal = $saveProfile->getValue();
                }

                // If both value are not same then save the new profile
                if ($saveProfile != $profile) {
                    $customer->setCustomAttribute('animal_profile',$profile);
                    $this->_customerRepository->save($customer);
                }
            }

            return $this->jsonResponse(['photo' => $photo->getContent()]);
        } catch (LocalizedException $e) {
            return $this->jsonResponse($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return $this->jsonResponse($e->getMessage());
        }
    }

    /**
     * Create json response
     *
     * @return ResultInterface
     */
    public function jsonResponse($response = '')
    {
        $this->http->getHeaders()->clearHeaders();
        $this->http->setHeader('Content-Type', 'application/json');
        return $this->http->setBody(
            $this->serializer->serialize($response)
        );
    }
}

