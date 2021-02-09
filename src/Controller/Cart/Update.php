<?php

namespace Makaira\Headless\Controller\Cart;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\HttpPutActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Quote\Model\ResourceModel\Quote;

class Update implements HttpPutActionInterface
{
    protected $resultJsonFactory;
    protected $helperQuote;
    protected $productInterface;
    protected $sessionModel;
    protected $request;

    /**
     * @param JsonFactory $resultJsonFactory
     * @param ProductRepositoryInterface $productInterface
     * @param Session $sessionModel
     * @param Request $request
     * @param Quote $helperQuote
     */
    public function __construct(
        JsonFactory $resultJsonFactory,
        Quote $helperQuote,
        ProductRepositoryInterface $productInterface,
        Session $sessionModel,
        Request $request
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helperQuote = $helperQuote;
        $this->productInterface = $productInterface;
        $this->sessionModel = $sessionModel;
        $this->request = $request;
    }

    /**
     * @return Json
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        $sku = $this->request->get('sku');
        $requestBody = $this->request->getBodyParams();
        $quantity = $requestBody["quantity"];

        try {

            $quote = $this->sessionModel->getQuote();

            $product = $this->productInterface->get($sku);
            $params = [
                'qty' => $quantity
            ];

            $cartItem = $quote->getItemByProduct($product);
            $cartItemId = $cartItem->getId();

            $quote->updateItem($cartItemId, $params);

            $quote->collectTotals();
            $this->helperQuote->save($quote);

            $items = $quote->getAllItems();

            $returnedData = [];
            foreach ($items as $item) {
                $data = [];
                $data['sku'] = $item->getSku();
                $data['name'] = $item->getName();
                $data['quantity'] = $item->getQty();
                $data['price'] = $item->getPrice();

                $returnedData[] = $data;
            }

            $subTotal = $quote->getSubtotal();

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
