<?php

namespace Makaira\Headless\Api;

interface CartInterface {

    /**
     * GET Method
     *
     * @api
     * @return string
     */
    public function getCart();

    /**
     * POST Method
     *
     * @api
     * @param string $variantId
     * @param int $quantity
     * @return string
     */
    public function addToCart($variantId, $quantity);

    /**
     * PUT Method
     *
     * @api
     * @param int $quantity
     * @return string
     */
    public function updateCart($quantity);

    /**
     * DELETE Method
     *
     * @api
     * @return string
     */
    public function deleteCart();

}
