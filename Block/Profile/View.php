<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Razoyo\AnimalProfile\Block\Profile;

use Razoyo\AnimalProfile\Model\Customer\Attribute\Source\AnimalProfile;
use Magento\Customer\Api\CustomerRepositoryInterface;

class View extends \Magento\Framework\View\Element\Template
{
    /**
     * @var ScustomerSession
     */
    private $customerSession;
    /**
     * @var AnimalProfile
     */
    private $animalProfile;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        AnimalProfile $animalProfile,
        CustomerRepositoryInterface $customerRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerSession = $customerSession;
        $this->animalProfile = $animalProfile;
        $this->_customerRepository = $customerRepository;
    }

    public function getGreeting()
    {
        return 'Hello ' . $this->customerSession->getCustomer()->getFirstname() . '!';
    }

    public function getPhotoUrl()
    {
        return $this->getUrl('animalid/profile/photo');
    }

    /**
     * Get all animal profiles options
     */
    public function getAnimalProfiles()
    {
        return $this->animalProfile->getAllOptions();
    }

    /**
     * Get Already saved profile
     */
    public function getProfile()
    {
        $customerId = $this->customerSession->getCustomer()->getId();
        $saveProfileVal = '';
        if ($customerId) {
            $customer = $this->_customerRepository->getById($customerId);
            $saveProfile = $customer->getCustomAttribute('animal_profile');
            if ($saveProfile) {
                $saveProfileVal = $saveProfile->getValue();
            }
        }

        return $saveProfileVal;
    }
}

