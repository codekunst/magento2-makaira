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
        $data = [];
        foreach($items as $item) {
            $data['Id'] = $item->getProductId();
            $data['Name'] = $item->getName();
            $data['Quantity'] = $item->getQty();
            $data['Price'] = $item->getPrice();

            array_push($returnedData, $data);
        }

        $subTotal = $cart->getQuote()->getSubtotal();
        $grandTotal = $cart->getQuote()->getGrandTotal();

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
     * @return string
     */
    public function addToCart($variantId, $quantity)
    {
        return 'Test Return';
    }

    /**
     * PUT Method
     *
     * @api
     * @param int $quantity
     * @return string
     */
    public function updateCart($quantity)
    {
        return 'Test Return';
    }

    /**
     * DELETE Method
     *
     * @api
     * @param string $param
     * @return string
     */
    public function deleteCart()
    {
        return 'Test Return';
    }
}
