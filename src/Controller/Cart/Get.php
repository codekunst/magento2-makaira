<?php

namespace Makaira\Headless\Controller\Cart;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;

class Get implements HttpGetActionInterface
{
    protected $resultJsonFactory;
    protected $sessionModel;

    /**
     * @param JsonFactory $resultJsonFactory
     * @param Session $sessionModel
     */
    public function __construct(
        JsonFactory $resultJsonFactory,
        Session $sessionModel
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->sessionModel = $sessionModel;
    }

    /**
     * @return Json
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        try {
            $quote = $this->sessionModel->getQuote();

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
                "message" => "Der Warenkorb kann nicht angezeigt werden.",
                "error" => $e->getMessage()
            ];
        }
        return $result->setData($response);
    }
}
