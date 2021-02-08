<?php

namespace Makaira\Headless\Controller\Cartactions;

use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Webapi\Rest\Request;

class Delete extends Action
{
    protected $resultJsonFactory;
    protected $cartModel;
    protected $productModel;
    protected $sessionModel;
    protected $request;
    /**
     * @param Context $context
     */
    public function __construct(Context $context,
                                JsonFactory $resultJsonFactory,
                                Cart $cartModel,
                                Product $productModel,
                                Session $sessionModel,
                                Request $request) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->cartModel = $cartModel;
        $this->productModel = $productModel;
        $this->sessionModel = $sessionModel;
        $this->request = $request;
        parent::__construct($context);
    }

	/**
	 * @return JsonFactory
	 */
	public function execute() {
        $result = $this->resultJsonFactory->create();

        $variantId = $this->getRequest()->getParam('variantid');

        try {
            $product = $this->productModel->load($variantId);

            $cartItem = $this->cartModel->getQuote()->getItemByProduct($product);
            $cartItemId = $cartItem->getId();

            $this->cartModel->removeItem($cartItemId);
            $this->cartModel->save();

            $response = [
                "success" => true,
                "message" => "Der Artikel wurde entfernt.",
                "variantId" => $variantId
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
