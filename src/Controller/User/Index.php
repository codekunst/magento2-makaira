<?php

namespace Makaira\Headless\Controller\User;

use Magento\Customer\Model\Session;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;

class Index implements ActionInterface
{
    protected $jsonFactory;
    protected $sessionModel;

    /**
     * Index constructor.
     * @param JsonFactory $jsonFactory
     * @param Session $sessionModel
     */
    public function __construct(
        JsonFactory $jsonFactory,
        Session $sessionModel
    )
    {
        $this->jsonFactory = $jsonFactory;
        $this->sessionModel = $sessionModel;
    }

    /**
     * @return Json
     */
    public function execute()
    {
        $result = $this->jsonFactory->create();

        $customer = $this->sessionModel->getCustomer();
        $data = $customer->getData();

        $response = [
            "success" => true,
            "user" => empty($data) ? null : $data
        ];
        return $result->setData($response);
    }
}
