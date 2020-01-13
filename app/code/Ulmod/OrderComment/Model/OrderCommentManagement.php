<?php
/**
 * Copyright © Ulmod. All rights reserved.
 * See LICENSE.txt for license details.
 */
 
namespace Ulmod\OrderComment\Model;

use Ulmod\OrderComment\Api\OrderCommentManagementInterface;
use Ulmod\OrderComment\Model\Data\OrderComment;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Quote\Api\CartRepositoryInterface;
use Ulmod\OrderComment\Api\Data\OrderCommentInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\ValidatorException;
use Magento\Store\Model\ScopeInterface;

class OrderCommentManagement implements OrderCommentManagementInterface
{
    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     * @param CartRepositoryInterface $quoteRepository
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param int $cartId
     * @param OrderCommentInterface $orderComment
     * @return null|string
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function saveOrderComment(
        $cartId,
        OrderCommentInterface $orderComment
    ) {
         $quote = $this->quoteRepository->getActive($cartId);
         
        if (!$quote->getItemsCount()) {
              throw new NoSuchEntityException(
                  __('Cart %1 doesn\'t contain products', $cartId)
              );
        }
        
        $comment = $orderComment->getComment();

        $this->validateComment($comment);
        
        try {
             $quote->setData(OrderComment::COMMENT_FIELD_NAME, strip_tags($comment));
            
             $this->quoteRepository->save($quote);
        } catch (\Exception $e) {
               throw new CouldNotSaveException(
                   __('The order comment could not be saved')
               );
        }

         return $comment;
    }

    /**
     * @param string $comment
     * @throws ValidatorException
     */
    protected function validateComment($comment)
    {
        $maxCommentLength = $this->scopeConfig->getValue(
            OrderCommentConfig::XML_PATH_GENERAL_MAX_LENGTH,
            ScopeInterface::SCOPE_STORE
        );
        
        if ($maxCommentLength && (mb_strlen($comment) > $maxCommentLength)) {
            throw new ValidatorException(
                __('The order comment entered exceeded the limit')
            );
        }
    }
}
