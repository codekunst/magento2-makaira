<?php

namespace Makaira\Headless\Api;

interface CartInterface
{
    /**
     * GET Method
     *
     * @return string
     * @api
     */
    public function get();

    /**
     * POST Method
     *
     * @return string
     * @api
     */
    public function add();

    /**
     * PUT Method
     *
     * @param string $variantId
     * @return string
     * @api
     */
    public function update($variantId);

    /**
     * DELETE Method
     *
     * @param string $variantId
     * @return string
     * @api
     */
    public function delete($variantId);
}
