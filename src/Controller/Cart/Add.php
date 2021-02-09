<?php

namespace Makaira\Headless\Controller\Cart;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Quote\Model\ResourceModel\Quote;

class Add implements HttpPostActionInterface
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

        $requestBody = $this->request->getBodyParams();
        $sku = $requestBody["sku"];
        $quantity = $requestBody["quantity"];

        try {
            $quote = $this->sessionModel->getQuote();

            $product = $this->productInterface->get($sku);
            $product->setQty($quantity);
            $quote->addProduct($product, $quantity);
            $this->helperQuote->save($quote);

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
                "message" => "Artikel konnte nicht hinzugefügt werden.",
                "error" => $e->getMessage()
            ];
        }
        return $result->setData($response);
    }
}
