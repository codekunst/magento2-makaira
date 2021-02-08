<?php

namespace Makaira\Headless\Controller\User;

use Magento\Customer\Model\Session;
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

        $customer = $this->sessionModel->getCustomer();

        //return ALL data related to the user
        $data = $customer->getData();

        //get only selected Data
        //$data = [];
        //$data['id'] = $customer->getId();
        //$data['name'] = $customer->getName();
        //$data['email'] = $customer->getEmail();

        $response = [
            "success" => true,
            "user" => $data
        ];
        return $result->setData($response);
    }
}
