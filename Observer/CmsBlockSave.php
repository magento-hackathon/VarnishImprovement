<?php

namespace Hackathon\VarnishImprovement\Observer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CmsBlockSave implements ObserverInterface
{
    protected $_moduleData;
    protected $_registry = null;
    protected $_widgetCollection = null;

    public function __construct (
        \Magento\Framework\Registry $registry,
        \Magento\Widget\Model\ResourceModel\Widget\Instance\Collection $widgetCollection
    ) {
        $this->_registry = $registry;
        $this->_widgetCollection = $widgetCollection;
    }

    public function execute(Observer $observer)
    {
        /** @var \Magento\Cms\Model\Block $cmsBlock */
        $cmsBlock = $observer->getObject();
        $ident = $cmsBlock->getIdentifier();
        /** @var \Magento\Widget\Model\Widget\Instance $widget */
        foreach ( $this->_widgetCollection as $widget ) {
            $param = $widget->getWidgetParameters();
            if ( $param['block_id'] == $cmsBlock->getId() ) {
                $widget->load( $widget->getId() );
                foreach ( $widget->getPageGroups() as $pageGroup ) {
                    if ( 'cms_footer_links_container' == $pageGroup['block_reference'] ) {
                        // Flush in Varnish
                    }
                }

            }
        }
    }

}