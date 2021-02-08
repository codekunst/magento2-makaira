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

class Update extends Action
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
        $requestBody = $this->request->getBodyParams();
        $quantity = $requestBody["quantity"];

        try {
            $product = $this->productModel->load($variantId);
            $params = array(
                'qty' => $quantity
            );

            $cartItem = $this->cartModel->getQuote()->getItemByProduct($product);
            $cartItemId = $cartItem->getId();

            $this->cartModel->updateItem($cartItemId, $params);
            $this->cartModel->save();

            $items = $this->cartModel->getQuote()->getAllItems();

            $returnedData = [];
            foreach ($items as $item) {
                $data = [];
                $data['id'] = $item->getProductId();
                $data['name'] = $item->getName();
                $data['quantity'] = $item->getQty();
                $data['price'] = $item->getPrice();

                $returnedData[] = $data;
            }

            $subTotal = $this->cartModel->getQuote()->getSubtotal();

            $response = [
                "success" => true,
                "cart" => [
                    "items" => $returnedData,
                    "total" => $subTotal
                ]
            ];
        } catch (\Exception $e) {
            $response = [
                "success" => false,
                "message" => "Artikel konnte nicht geändert werden.",
                "error" => $e->getMessage()
            ];
        }
        return $result->setData($response);
	}
}
