<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Razoyo\AnimalProfile\Model\Customer\Attribute\Source;

class AnimalProfile extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    /**
     * getAllOptions
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['value' => 'cat', 'label' => __('Cat')],
                ['value' => 'dog', 'label' => __('Dog')],
                ['value' => 'llama', 'label' => __('Llama')],
                ['value' => 'anteater', 'label' => __('Anteater')]
            ];
        }
        return $this->_options;
    }
}

