<?php


namespace Makaira\Headless\Model;

use Makaira\Headless\Api\UserInterface;

class User implements UserInterface
{
    protected $sessionModel;

    public function __construct(
        \Magento\Customer\Model\Session $sessionModel
    )
    {
        $this->sessionModel = $sessionModel;
    }

    /**
     * GET Method
     *
     * @return mixed
     * @api
     */
    public function get()
    {
        if($this->sessionModel->isLoggedIn()) {
            $customer = $this->sessionModel->getCustomer();

            //return ALL data related to the user
            //$data = $customer->getData();

            //get only selected Data
            $data = [];
            $data['id'] = $customer->getId();
            $data['name'] = $customer->getName();
            $data['email'] = $customer->getEmail();

            $response = [
                "success" => true,
                "user" => $data
            ];
        } else {
            $response = [
                "success" => false,
                "message" => "Kunde ist nicht angemeldet."
            ];
        }

        return json_encode($response);
    }
}
