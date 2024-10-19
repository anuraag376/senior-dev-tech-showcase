<?php

namespace Demo\PromotionalProducts\Block\Adminhtml\Form;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class SaveButton
 *
 * Provides the configuration for the Save button in the admin panel.
 */
class SaveButton implements ButtonProviderInterface
{
    /**
     * Get button configuration data for the Save button.
     *
     * @return array Configuration data for the Save button.
     */
    public function getButtonData()
    {
        $data = [
            "label" => __("Save"),
            "class" => "save primary",
            "sort_order"     => 90,
            "data_attribute" => [
                "mage-init"  => ["button" => ["event" => "save"]],
                "form-role"  => "save",
            ]
        ];
        return $data;
    }
}
