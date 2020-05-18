<?php
namespace Boeki\Custom\Block;
class Custom extends \Magento\Framework\View\Element\Template
{
    protected $_storeManager;
    protected $_storeInfo;    
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,        
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Store\Model\Information $storeInfo,
        \Magento\Customer\Model\SessionFactory $customerSession,        
        array $data = []
    )
    {        
        $this->_storeManager = $storeManager;
        $this->_storeInfo = $storeInfo;
        $this->_customerSession = $customerSession->create();       
        parent::__construct($context, $data);
    }
    
    /**
     * Get store identifier
     *
     * @return  int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
    
    /**
     * Get website identifier
     *
     * @return string|int|null
     */
    public function getWebsiteId()
    {
        return $this->_storeManager->getStore()->getWebsiteId();
    }
    
    /**
     * Get Store code
     *
     * @return string
     */
    public function getStoreCode()
    {
        return $this->_storeManager->getStore()->getCode();
    }
    
    /**
     * Get Store name
     *
     * @return string
     */
    public function getStoreName()
    {
        return $this->_storeManager->getStore()->getName();
    }

    /**
     * Get Store information object
     *
     * @return object
     */
    public function getStoreInformation()
    {
        return $this->_storeInfo->getStoreInformationObject($this->_storeManager->getStore());
    }

    /**
     * Get Store information phone number
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->getStoreInformation()->getPhone();
    }

    /**
     * Get Store information phone number
     *
     * @return string
     */
    public function getHours()
    {
        return $this->getStoreInformation()->getHours();
    }
    
    /**
     * Get current url for store
     *
     * @param bool|string $fromStore Include/Exclude from_store parameter from URL
     * @return string     
     */
    public function getStoreUrl($fromStore = true)
    {
        return $this->_storeManager->getStore()->getCurrentUrl($fromStore);
    }
    
    /**
     * Check if store is active
     *
     * @return boolean
     */
    public function isStoreActive()
    {
        return $this->_storeManager->getStore()->isActive();
    }


    /**
    * Url
    *
    */

    public function getBaseUrl() {
        return $this->_storeManager->getStore()->getBaseUrl();
    }
 
    public function getLinkUrl() {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_DIRECT_LINK);
    }
 
    public function getMediaUrl() {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
 
    public function getStaticUrl() {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_STATIC);
    }
 
    public function getCurrentUrl() {
        return $this->_storeManager->getStore()->getCurrentUrl(false);
    }
 
    public function getBaseMediaDir() {
        return $this->_storeManager->getStore()->getBaseMediaDir();
    }
 
    public function getBaseStaticDir() {
        return $this->_storeManager->getStore()->getBaseStaticDir();
    }

    /**
    * Customer
    *
    */

    public function isLoggedIn() {
        return $this->_customerSession->isLoggedIn();
    }

    public function getLoggedinCustomerId() {
        if ($this->_customerSession->isLoggedIn()) {
            return $this->_customerSession->getId();
        }
        return false;
    }
 
    public function getCustomerData() {
        if ($this->_customerSession->isLoggedIn()) {
            return $this->_customerSession->getCustomerData();
        }
    }

   
}
?>