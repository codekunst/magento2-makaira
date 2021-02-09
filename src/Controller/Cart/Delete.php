<?php

namespace Makaira\Headless\Controller\Cart;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\HttpDeleteActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Quote\Model\ResourceModel\Quote;

class Delete implements HttpDeleteActionInterface
{
    protected $resultJsonFactory;
    protected $productInterface;
    protected $sessionModel;
    protected $request;
    protected $helperQuote;

    /**
     * @param JsonFactory $resultJsonFactory
     * @param ProductRepositoryInterface $productInterface
     * @param Session $sessionModel
     * @param Request $request
     * @param Quote $helperQuote
     */
    public function __construct(
        JsonFactory $resultJsonFactory,
        ProductRepositoryInterface $productInterface,
        Session $sessionModel,
        Request $request,
        Quote $helperQuote
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->productInterface = $productInterface;
        $this->sessionModel = $sessionModel;
        $this->request = $request;
        $this->helperQuote = $helperQuote;
    }

    /**
     * @return Json
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        try {
            $sku = $this->request->get('sku');
            $product = $this->productInterface->get($sku);
            $quote = $this->sessionModel->getQuote();

            $cartItem = $quote->getItemByProduct($product);
            $cartItemId = $cartItem->getId();

            $quote->removeItem($cartItemId);

            $quote->collectTotals();
            $this->helperQuote->save($quote);

            $response = [
                "success" => true,
                "message" => "Der Artikel wurde entfernt.",
                "sku" => $sku
            ];
        } catch (\Exception $e) {
            $response = [
                "success" => false,
                "message" => "Der Artikel konnte nicht entfernt werden.",
                "error" => $e->getMessage()
            ];
        }
        return $result->setData($response);
    }
}
