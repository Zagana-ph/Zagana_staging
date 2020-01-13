<?php
/**
 * Copyright © Ulmod. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Ulmod\OrderComment\Block\Order;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Ulmod\OrderComment\Model\Data\OrderComment;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Ulmod\OrderComment\Model\OrderCommentConfig;

class Comment extends Template
{
    /**
     * @var OrderCommentConfig
     */
    protected $orderCommentConfig;
    
    /**
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @param    Context $context
     * @param    Registry $registry
     * @param    OrderCommentConfig $orderCommentConfig
     * @param   array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        OrderCommentConfig $orderCommentConfig,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->orderCommentConfig = $orderCommentConfig;
        $this->_isScopePrivate = true;
        $this->_template = 'order/view/comment.phtml';
        parent::__construct($context, $data);
    }
    
    /**
     * Check if show order comment to customer account
     *
     * @return bool
     */
    public function isShowCommentInAccount()
    {
        return $this->orderCommentConfig->isShowCommentInAccount();
    }
    
    /**
     * Get Order
     *
     * @return array|null
     */
    public function getOrder()
    {
        return $this->coreRegistry->registry('current_order');
    }

    /**
     * Get Order Comment
     *
     * @return string
     */
    public function getOrderComment()
    {
        return trim($this->getOrder()->getData(OrderComment::COMMENT_FIELD_NAME));
    }

    /**
     * Retrieve html comment
     *
     * @return string
     */
    public function getOrderCommentHtml()
    {
        return nl2br($this->escapeHtml($this->getOrderComment()));
    }

    /**
     * Check if has order comment
     *
     * @return bool
     */
    public function hasOrderComment()
    {
        return strlen($this->getOrderComment()) > 0;
    }
}
