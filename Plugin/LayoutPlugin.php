<?php
namespace Hackathon\VarnishImprovement\Plugin;

use Magento\Framework\App\ResponseInterface;

/**
 * Class LayoutPlugin
 */
class LayoutPlugin
{
    /**
     * @var \Magento\PageCache\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $response;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\ResponseInterface $response
     * @param \Magento\PageCache\Model\Config $config
     */
    public function __construct(
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\PageCache\Model\Config $config
    ) {
        $this->response = $response;
        $this->config = $config;
    }

    /**
     * Retrieve all identities from blocks for further cache invalidation
     *
     * @param \Magento\Framework\View\Layout $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterGetOutput(\Magento\Framework\View\Layout $subject, $result)
    {
        if ($subject->isCacheable() && $this->config->isEnabled()) {
            $tags = [];
            foreach ($subject->getAllBlocks() as $block) {
                if ($block instanceof \Magento\Framework\DataObject\IdentityInterface) {
                    $isEsiBlock = $block->getTtl() > 0;
                    $isVarnish = $this->config->getType() == \Magento\PageCache\Model\Config::VARNISH;
                    if ($isVarnish && $isEsiBlock) {
                        continue;
                    }
                    $identities = $block->getIdentities();
                    $tags = array_merge($tags, array_diff($identities, array('cms_block')));
                }
            }
            $tags = array_unique($tags);
            $this->response->setHeader('X-Magento-Tags', implode(',', $tags));
        }
        return $result;
    }
}
