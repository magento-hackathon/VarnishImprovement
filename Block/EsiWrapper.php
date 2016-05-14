<?php

namespace Hackathon\VarnishImprovement\Block;

use Magento\Framework\View\Element\Text\ListText;
use Magento\Framework\DataObject\IdentityInterface;

class EsiWrapper extends ListText
    implements IdentityInterface
{
    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return ['footer_links_wrapper'];
    }
}