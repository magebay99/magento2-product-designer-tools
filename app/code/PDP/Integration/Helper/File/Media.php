<?php
namespace PDP\Integration\Helper\File;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Media extends AbstractHelper{

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $_fileIo;	
	
	/**
	 * @var \Magento\Framework\ObjectManagerInterface
	 */
	protected $_objectmanager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
	
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;
	
    /**
     * Permissions for new sub-directories
     *
     * @var int
     */
    protected $permissions = 0777;
	
	/**
	 * @var \Magento\Framework\Filesystem\Directory\WriteFactory
	 */
	protected $_writeFactory;
	
	
    /**
     * 
	 * @param Context $context
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Framework\ObjectManagerInterface $objectmanager
	 * @param \Magento\Framework\Filesystem\Directory\WriteFactory $writeFactory
	 * @param \Magento\Framework\Filesystem\Io\File $fileIo
	 * @param \Magento\Framework\Filesystem $filesystem
     */	
	public function __construct(
		Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\ObjectManagerInterface $objectmanager,
		\Magento\Framework\Filesystem\Io\File $fileIo,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Framework\Filesystem\Directory\WriteFactory $writeFactory
	) {
		parent::__construct($context);
		$this->_storeManager = $storeManager;
		$this->_objectmanager = $objectmanager;
		$this->_fileIo = $fileIo;
		$this->_filesystem = $filesystem;
        $this->_writeFactory = $writeFactory;		
	}
	
	/**
	 * @param string $oldUrl
	 * @return string|null
	 */
	public function uploadImage($oldUrl) {
		$path = 'pdp/mydesign';
		$fullPath = $this->_filesystem->getDirectoryRead('media')
		->getAbsolutePath($path);
		if(!is_dir($fullPath)) {
			$this->_fileIo->mkdir($fullPath, $this->permissions);
		}
		$url = $this->_objectmanager->get('PDP\Integration\Helper\PdpOptions')->getUrlToolDesign();
		$pdpFolder = parse_url($url);
		$destDir = $this->_writeFactory->create(realpath('pub/media/pdp/mydesign'));
		$tmpPath = $this->_writeFactory->create(realpath(getcwd().'/../'.$pdpFolder['path'].'/data/pdp/previewimage'));
		$tmpppName = pathinfo($oldUrl,PATHINFO_FILENAME). '.' .pathinfo($oldUrl,PATHINFO_EXTENSION);
		$newImg = $tmpPath->copyFile($tmpppName,$tmpppName, $destDir);
		if($newImg) {
			return $fullPath.'/'.$tmpppName;
		} else {
			return null;
		}
	}	
}