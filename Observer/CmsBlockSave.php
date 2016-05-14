<?php

namespace Hackathon\VarnishImprovement\Observer;
use Magento\Framework\App\PageCache\Cache;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Widget\Model\ResourceModel\Widget\Instance\Collection;

class CmsBlockSave implements ObserverInterface
{
    protected $widgetCollection = null;
    protected $cache = null;

    public function __construct (
        Collection $widgetCollection,
        Cache $cache
    ) {
        $this->widgetCollection = $widgetCollection;
        $this->cache = $cache;
    }

    public function execute(Observer $observer)
    {
        /** @var \Magento\Cms\Model\Block $cmsBlock */
        $cmsBlock = $observer->getObject();
        /** @var \Magento\Widget\Model\Widget\Instance $widget */
        $needFlush = false;
        // Dirty prototype double foreach loop
        foreach ( $this->widgetCollection as $widget ) {
            $param = $widget->getWidgetParameters();
            if ( $param['block_id'] == $cmsBlock->getId() ) {
                $widget->load( $widget->getId() );
                foreach ( $widget->getPageGroups() as $pageGroup ) {
                    if ( 'wrapped_cms_footer_links_container' == $pageGroup['block_reference'] ) {
                        $needFlush = true;
                    }
                }

            }
        }
        if ($needFlush) {
            $this->cache->clean( 'footer_links_wrapper' );
        }
    }

}