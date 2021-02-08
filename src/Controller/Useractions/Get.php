<?php

namespace Makaira\Headless\Controller\Useractions;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Webapi\Rest\Request;

class Get extends Action
{
	protected $resultJsonFactory;
    protected $sessionModel;
	/**
	 * @param Context $context
	 */
	public function __construct(Context $context,
                                JsonFactory $resultJsonFactory,
                                Session $sessionModel) {
		$this->resultJsonFactory = $resultJsonFactory;
        $this->sessionModel = $sessionModel;
		parent::__construct($context);
	}

	/**
	 * @return JsonFactory
	 */
	public function execute() {
        $result = $this->resultJsonFactory->create();

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
                "success" => true,
                "message" => "Kunde ist nicht angemeldet."
            ];
        }
        return $result->setData($response);
	}
}
