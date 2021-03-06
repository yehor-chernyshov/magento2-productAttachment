<?php

/**
 * Productattach data helper
 */
namespace Prince\Productattach\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Path to store config where count of productattach posts per page is stored
     *
     * @var string
     */
    const XML_PATH_ITEMS_PER_PAGE     = 'productattach/view/items_per_page';
    
    /**
     * Media path to extension images
     *
     * @var string
     */
    const MEDIA_PATH    = 'productattach';


    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    
    /**
     * File Uploader factory
     *
     * @var \Magento\Core\Model\File\UploaderFactory
     */
    protected $_fileUploaderFactory;
    
    
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    protected $_backendUrl;
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\File\Size $fileSize,
        \Magento\Framework\HTTP\Adapter\FileTransferFactory $httpFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_backendUrl = $backendUrl;
        $this->filesystem = $filesystem;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }
    
    /**
     * Upload image and return uploaded image file name or false
     *
     * @throws Mage_Core_Exception
     * @param string $scope the request key for file
     * @return bool|string
     */
    public function uploadFile($scope)
    {   
       try {
            $uploader = $this->_fileUploaderFactory->create(['fileId' => $scope]);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $uploader->setAllowCreateFolders(true);
            
            if ($uploader->save($this->getBaseDir())) {
                return $uploader->getUploadedFileName();    
            }

        } catch (Exception $e) {
            throw new \Exception('The file was not uploaded.', $code);
        }
        return '';
    }
    
    /**
     * Return the base media directory for Productattach Item images
     *
     * @return string
     */
    public function getBaseDir()
    {
        $path = $this->filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath(self::MEDIA_PATH);
        return $path;
    }
    
    /**
     * Return the Base URL for Productattach Item images
     *
     * @return string
     */
    public function getBaseUrl()
    { 
        return $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ) . self::MEDIA_PATH;
    }
    
    /**
     * Return the number of items per page
     *
     * @return int
     */
    public function getProductattachPerPage()
    {
        return abs((int)$this->getScopeConfig()->getValue(self::XML_PATH_ITEMS_PER_PAGE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
    }

    /**
     * Return current store Id
     *
     * @return Int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    public function getProductsGridUrl()
    {
        return $this->_backendUrl->getUrl('productattach/index/products', ['_current' => true]);
    }

    /**
     * Return customer groups
     */
    public function getCustomerGroup($customers)
    {
        $customers = implode(',', $customers);
        return $customers;
    }

    /**
     * Return stores
     */
    public function getStores($store)
    {
        $store = implode(',', $store);
        return $store;
    }
}
