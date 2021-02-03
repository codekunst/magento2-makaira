<?php


namespace Makaira\Headless\Model;

class Cart
{

    /**
     * GET Method
     *
     * @api
     * @return mixed
     */
    public function getCart()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart');

        // get quote items array
        $items = $cart->getQuote()->getAllItems();

        $returnedData = [];
        foreach($items as $item) {
            $data = [];
            $data['id'] = $item->getProductId();
            $data['name'] = $item->getName();
            $data['quantity'] = $item->getQty();
            $data['price'] = $item->getPrice();

            $returnedData[] = $data;
        }

        $subTotal = $cart->getQuote()->getSubtotal();

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
     * @api
     * @param string $variantId
     * @param int $quantity
     * @return mixed
     */
    public function addToCart($variantId, $quantity)
    {
        try {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

            $product = $objectManager->create('Magento\Catalog\Model\Product')->load($variantId);
            $params = array(
                'product' => $variantId,
                'qty' => $quantity
            );

            $cart = $objectManager->get('\Magento\Checkout\Model\Cart');
            $cart->addProduct($product, $params);
            $cart->save();

            $quote = $objectManager->get('\Magento\Checkout\Model\Session')->getQuote();

            // Calculate the new Cart total and Save Quote
            $quote->collectTotals()->save();

            $items = $cart->getQuote()->getAllItems();

            $returnedData = [];
            foreach($items as $item) {
                $data = [];
                $data['id'] = $item->getProductId();
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
                "error" => "123"
            ];
        }

        return json_encode($response);
    }

    /**
     * PUT Method
     *
     * @api
     * @param int $quantity
     * @return mixed
     */
    public function updateCart($quantity)
    {
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
     * @api
     * @return string
     */
    public function deleteCart()
    {
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
