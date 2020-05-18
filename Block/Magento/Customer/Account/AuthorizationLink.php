<?php

namespace Boeki\Custom\Block\Magento\Customer\Account;

class AuthorizationLink extends \Magento\Customer\Block\Account\AuthorizationLink
{
    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
	protected $_customerSession;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param array $data
     */
	public function __construct(
        \Magento\Customer\Model\Session $customerSession,
		\Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        array $data = []
    ) {
        $this->_customerSession = $customerSession;
        parent::__construct($context, $httpContext, $customerUrl, $postDataHelper, $data);
    }

    /**
     * @return string
     */
    public function getLabel()
    {
       return $this->isLoggedIn() ? __('Sign Out') : __('My Account');
    }

    /**
     * @return string
     */
    public function getCustomerName()
    {
       return $this->_customerSession->getCustomer()->getFirstname();
    }

    /**
     * @return string
     */
    public function getAccountUrl()
    {
        return $this->_customerUrl->getAccountUrl();
    }
}