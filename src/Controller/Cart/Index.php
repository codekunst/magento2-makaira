<?php

namespace Makaira\Headless\Controller\Cart;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Quote\Model\ResourceModel\Quote;

class Index implements CsrfAwareActionInterface
{
    protected $jsonFactory;
    protected $productInterface;
    protected $sessionModel;
    protected $request;
    protected $helperQuote;

    /**
     * Index constructor.
     * @param JsonFactory $jsonFactory
     * @param ProductRepositoryInterface $productInterface
     * @param Session $sessionModel
     * @param Quote $helperQuote
     * @param Http $request
     */
    public function __construct(
        JsonFactory $jsonFactory,
        ProductRepositoryInterface $productInterface,
        Session $sessionModel,
        Quote $helperQuote,
        Http $request
    )
    {
        $this->jsonFactory = $jsonFactory;
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
        $result = $this->jsonFactory->create();

        switch ($this->request->getMethod()) {
            case "POST":
                $result->setData($this->add());
                break;
            case "GET":
                $result->setData($this->get());
                break;
            case "PUT":
                $result->setData($this->update());
                break;
            case "DELETE":
                $result->setData($this->delete());
                break;
        }

        return $result;
    }

    protected function add()
    {
        $requestBody = json_decode($this->request->getContent(), true);

        if (!$requestBody) {
            return self::getParseError();
        }

        $sku = $requestBody["sku"];
        $quantity = $requestBody["quantity"];

        try {
            $quote = $this->sessionModel->getQuote();

            $product = $this->productInterface->get($sku);
            $product->setQty($quantity);
            $quote->addProduct($product, $quantity);

            $quote->collectTotals();
            $quote->setTriggerRecollect('1');
            $this->helperQuote->save($quote);

            $items = $quote->getAllItems();

            $returnedData = [];
            $subTotal = 0;
            foreach ($items as $item) {
                $data = [];
                $data['sku'] = $item->getSku();
                $data['name'] = $item->getName();
                $data['quantity'] = $item->getQty();
                $data['price'] = $item->getPrice();

                //must recalculate the subtotal manually
                $subTotal += $item->getPrice() * $item->getQty();

                $returnedData[] = $data;
            }

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
        return $response;
    }

    // TODO: returns price as string AND float, should be a single format every single time
    protected function get()
    {
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

        return $response;
    }

    // TODO: put sometimes does not add up the total correctly!
    protected function update()
    {
        $requestBody = json_decode($this->request->getContent(), true);

        if (!$requestBody) {
            return self::getParseError();
        }

        $sku = $requestBody["sku"];
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

        return $response;
    }

    protected function delete()
    {
        $requestBody = json_decode($this->request->getContent(), true);

        if (!$requestBody) {
            return self::getParseError();
        }

        $sku = $requestBody["sku"];

        try {
            $product = $this->productInterface->get($sku);
            $quote = $this->sessionModel->getQuote();

            $cartItem = $quote->getItemByProduct($product);
            // TODO: check if product exists in cart
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
        return $response;
    }

    protected static function getParseError()
    {
        return [
            "success" => false,
            "error" => "Invalid body, expected JSON.",
        ];
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
