<?php

namespace Makaira\Headless\Model;

use Makaira\Headless\Api\CartInterface;

class Cart implements CartInterface
{
    protected $cartModel;
    protected $productModel;
    protected $sessionModel;
    protected $request;

    public function __construct(
        \Magento\Checkout\Model\Cart $cartModel,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Checkout\Model\Session $sessionModel,
        \Magento\Framework\Webapi\Rest\Request $request
    )
    {
        $this->cartModel = $cartModel;
        $this->productModel = $productModel;
        $this->sessionModel = $sessionModel;
        $this->request = $request;
    }

    /**
     * GET Method
     *
     * @return string
     * @api
     */
    public function get()
    {
        // get quote items array
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

        return json_encode($response);
    }

    /**
     * POST Method
     *
     * @return mixed
     * @api
     */
    public function add()
    {
        $requestBody = $this->request->getBodyParams();
        $variantId = $requestBody["variantId"];
        $quantity = $requestBody["quantity"];

        try {
            $product = $this->productModel->load($variantId);
            $params = array(
                'product' => $variantId,
                'qty' => $quantity
            );

            $this->cartModel->addProduct($product, $params);
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
                "message" => "Artikel konnte nicht hinzugefügt werden.",
                "error" => $e->getMessage()
            ];
        }

        return json_encode($response);
    }

    /**
     * PUT Method
     *
     * @param string $variantId
     * @return mixed
     * @api
     */
    public function update($variantId)
    {
        // TODO
        /* $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
         $cart = $objectManager->get('\Magento\Checkout\Model\Cart');

         // get quote items array
         $items = $cart->getQuote()->getAllItems();

         $subTotal = $cart->getQuote()->getSubtotal();
         $grandTotal = $cart->getQuote()->getGrandTotal();

         $response = [
             "success" => true,
             "cart" => [
                 "items" => $returnedData,
                 "total" => $subTotal
             ]
         ];

         return json_encode($response);*/
    }

    /**
     * DELETE Method
     *
     * @param string $variantId
     * @return string
     * @api
     */
    public function delete($variantId)
    {
        // TODO
        /* try {
             $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
             $cart = $objectManager->get('\Magento\Checkout\Model\Cart');

             // get quote items array
             $items = $cart->getQuote()->getAllItems();

             $response = [
                 "success" => true,
                 "message" => "Der Artikel wurde entfernt.",
                 //"variantId" => $variantId
             ];
         } catch(Exception $e){
             $response = [
                 "success" => false,
                 "message" => "Der Artikel konnte nicht entfernt werden.",
                 "error" => "789"
             ];
         }
         return json_encode($response);*/
    }
}
