<?php
/**
 * Copyright © 2016 Divido. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Divido\DividoFinancing\Model\Adminhtml\Source;

/**
 * Class PlansDisplayed
 */
class PlansDisplayed implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 'plans_all',
                'label' => __('Display all plans'),
            ],
            [
                'value' => 'plans_selected',
                'label' => __('Only display selected plans'),
            ],
        ];
    }
}
