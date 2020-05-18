<?php
namespace Boeki\Custom\Block;
class FilterList extends \Magento\Catalog\Block\Product\AbstractProduct implements
    \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * Default value for products count that will be shown
     */
    const DEFAULT_PRODUCTS_COUNT = 4;

    /**
     * Products count
     *
     * @var int
     */
    protected $_productsCount;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_catalogProductVisibility;

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * Product collection registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_registry;


    /**
     * @param Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_registry = $registry;
        $this->httpContext = $httpContext;
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Initialize block's cache
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addColumnCountLayoutDepend('empty', 6)
            ->addColumnCountLayoutDepend('1column', 5)
            ->addColumnCountLayoutDepend('2columns-left', 4)
            ->addColumnCountLayoutDepend('2columns-right', 4)
            ->addColumnCountLayoutDepend('3columns', 3);

        $this->addData(
            ['cache_lifetime' => 86400, 'cache_tags' => [\Magento\Catalog\Model\Product::CACHE_TAG]]
        );
    }

    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return [
           'CATALOG_PRODUCT_FILTERLIST',
           $this->_storeManager->getStore()->getId(),
           $this->_design->getDesignTheme()->getId(),
           $this->httpContext->getValue(CustomerContext::CONTEXT_GROUP),
           'template' => $this->getTemplate(),
           $this->getProductsCount()
        ];
    }

    /**
     * Prepare and return product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|Object|\Magento\Framework\Data\Collection
     */
    protected function _getProductCollection()
    {

        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */


        $collection = $this->_productCollectionFactory->create();
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        

        $collection = $this->_addProductAttributesAndPrices(
            $collection
        )->addCategoriesFilter(
            array('in' => $this->getCurrentCategoryIds())
        )->addAttributeToSort(
            'position',
            'asc'
        );

        $dataAttributeFilter = $this->setDataAttributeFilter();

        if(count($dataAttributeFilter)) {
            $collection->addAttributeToFilter(
                [
                    $dataAttributeFilter,
                ]
            );
        }
         $_product = $this->getCurrentProduct();
        $collection->addAttributeToFilter('sku', array('neq' =>  $_product->getSku()));

        $collection->setPageSize(
            $this->getProductsCount()
        )->setCurPage(
            1
        );

        return $collection;
    }

    /**
     * Prepare collection with new products
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _beforeToHtml()
    {
        $this->setProductCollection($this->_getProductCollection());
        return parent::_beforeToHtml();
    }

    /**
     * Set how much product should be displayed at once.
     *
     * @param int $count
     * @return $this
     */
    public function setProductsCount($count)
    {
        $this->_productsCount = $count;
        return $this;
    }

    /**
     * Get how much products should be displayed at once.
     *
     * @return int
     */
    public function getProductsCount()
    {
        if (null === $this->_productsCount) {
            $this->_productsCount = self::DEFAULT_PRODUCTS_COUNT;
        }
        return $this->_productsCount;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Magento\Catalog\Model\Product::CACHE_TAG];
    }

    /**
     * Get current product
     *
     * @return object
     */
    public function getCurrentProduct()
    {        
        return $this->_registry->registry('current_product');
    }

    /**
     * Return type for list items
     *
     * @return string
     */
    public function getType() 
    {
        return "new";
    }

    /**
     * Return category ids from current product page
     *
     * @return array
     */
    public function getCurrentCategoryIds()
    {
        $product = $this->getCurrentProduct();
        return $product->getCategoryIds();
    }

    /**
     * Get name (key) and value to filter
     *
     * @return array
     */
    public function setDataAttributeFilter()
    {
        $datafilter = $this->getDataAttributeFilter();
        $filter = array();

        if(isset($datafilter) && is_array($datafilter)) {
           
            $_product = $this->getCurrentProduct();

            if( count($datafilter) && $_product ){
                foreach($datafilter as $name => $value) {
                    if($_product->getData($value)){
                        $filter = [$name => $value, 'eq' => $_product->getData($value)];
                    }
                }
            }
        }
        
        return $filter;
    }

}

?>