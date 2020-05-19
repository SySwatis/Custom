<?php
namespace Boeki\Custom\Block;
class CategoriesList extends \Magento\Framework\View\Element\Template
{

    protected $_category_id;
    protected $_limit = 6;

    protected $_storeManager;
    protected $_categoryCollection;
    protected $_categoryRepository;
    protected $_categoryObject;
    protected $_registry;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,        
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Catalog\Model\Category $categoryObject,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Request\Http $request,
        array $data = []
    )
    {        
        $this->_storeManager = $storeManager;
        $this->_categoryCollection = $categoryCollection;
        $this->_categoryRepository = $categoryRepository;
        $this->_categoryObject = $categoryObject;
        $this->_registry = $registry;
        $this->_request = $request;
        parent::__construct($context, $data);
    }
    
	public function getStoreManagerData()
    {
        return $this->_storeManager->getStore();
    }

    public function setCategoryId()
    {
    	return $this->_category_id = $this->getData('category_id');
    }

    public function setLimit()
    {
    	return $this->_limit = $this->getData('limit');
    }

    public function isCategoryPage()
    {
    	return $this->_request->getFullActionName() == 'catalog_category_view';
    }

    public function debugRequest()
    {
    	return $this->_request->getFullActionName();
    }

    public function getCurrentCategoryId()
    {        
        $category = $this->_registry->registry('current_category');
        return $category->getId();
    }

    public function getCategoryUrl()
    {
        $this->setCategoryId();

		$cat = $this->_categoryObject->getCollection()->addAttributeToFilter('parent_id',['eq'=>$this->_category_id])->getFirstItem();

        if( !$cat->getId() ) {
        	return;
        }

        $category = $this->_categoryRepository->get($this->_category_id, $this->_storeManager->getStore()->getId());

        return $category->getUrl();
    }

	public function getCategories()
	{
	    $this->setCategoryId();
	    $this->setLimit();

	    if(!$this->_category_id) {
        	return;
        }

        $categoriesCollection = $this->_categoryCollection->create()                              
	         ->addAttributeToSelect('*')->addAttributeToFilter('parent_id',['eq'=>$this->_category_id])->setPageSize($this->_limit)->setOrder('position','asc')
	         ->setStore($this->getStoreManagerData()); //categories from current store will be fetched

	    return  $categoriesCollection;
	}

}
?>