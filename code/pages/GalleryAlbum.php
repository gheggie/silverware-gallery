<?php

/**
 * An extension of the page class for a gallery album.
 */
class GalleryAlbum extends Page implements ListSource
{
    private static $singular_name = "Gallery Album";
    private static $plural_name   = "Gallery Albums";
    
    private static $description = "An album of images within a gallery";
    
    private static $icon = "silverware-gallery/images/icons/GalleryAlbum.png";
    
    private static $default_child = "GalleryImage";
    
    private static $can_be_root = false;
    
    private static $max_file_number = 20;
    
    private static $db = array(
        'ImagesPerPage' => 'Int',
        'PaginateImages' => 'Boolean',
        'ShowImageTitles' => 'Boolean'
    );
    
    private static $has_one = array(
        'Folder' => 'Folder'
    );
    
    private static $has_many = array(
        'UploadedImages' => 'Image'
    );
    
    private static $defaults = array(
        'ImagesPerPage' => 12,
        'PaginateImages' => 1,
        'ShowImageTitles' => 1
    );
    
    private static $allowed_children = array(
        'GalleryImage'
    );
    
    private static $extensions = array(
        'ImageDefaultsExtension'
    );
    
    /**
     * Answers a collection of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Obtain Field Objects (from parent):
        
        $fields = parent::getCMSFields();
        
        // Modify Meta Image Field:
        
        $this->setMetaImageFolder($fields, $this->getFolderPath());
        
        // Add Hidden Field for CMS Tree Refresh:
        
        $fields->addFieldToTab(
            'Root.Main',
            HiddenField::create('CMSTreeRefreshID', '', $this->ID)->addExtraClass('cms-tree-refresh')
        );
        
        // Insert Upload Tab:
        
        $fields->insertAfter(
            Tab::create(
                'Upload',
                _t('GalleryAlbum.UPLOAD', 'Upload')
            ),
            'Main'
        );
        
        // Define Upload Message:
        
        $message = _t(
            'GalleryAlbum.UPLOADMESSAGE',
            'Please be patient when saving the album, as it may take a few minutes to process uploaded images.'
        );
        
        // Obtain Maximum File Number:
        
        $max_file_number = $this->config()->max_file_number;
        
        // Obtain Image Noun:
        
        $image_noun = $max_file_number == 1 ? _t('GalleryAlbum.IMAGE', 'Image') : _t('GalleryAlbum.IMAGES', 'Images');
        
        // Create Upload Fields:
        
        $fields->addFieldsToTab(
            'Root.Upload',
            array(
                LiteralField::create(
                    'UploadedImagesWarning',
                    "<p class=\"message warning\"><i class=\"fa fa-fw fa-warning\"></i> {$message}</p>"
                ),
                $upload = UploadField::create(
                    'UploadedImages',
                    _t('GalleryAlbum.UPLOADIMAGESTOALBUM', 'Upload images to album')
                )->setRightTitle(
                    _t(
                        'GalleryAlbum.UPLOADIMAGESTOALBUMRIGHTTITLE',
                        'Uploaded images will be added to the album after saving. Maximum of {max} {noun} at a time.',
                        '',
                        array(
                            'max' => $max_file_number,
                            'noun' => strtolower($image_noun)
                        )
                    )
                )
            )
        );
        
        // Define Upload Field:
        
        $upload->setAllowedFileCategories('image');
        $upload->setAllowedMaxFileNumber($max_file_number);
        $upload->setFolderName($this->getFolderPath());
        
        // Create Options Tab:
        
        $fields->findOrMakeTab('Root.Options', _t('GalleryAlbum.OPTIONS', 'Options'));
        
        // Create Options Fields:
        
        $fields->addFieldToTab(
            'Root.Options',
            ToggleCompositeField::create(
                'GalleryAlbumOptions',
                $this->i18n_singular_name(),
                array(
                    CheckboxField::create(
                        'PaginateImages',
                        _t('GalleryAlbum.PAGINATEIMAGES', 'Paginate images')
                    ),
                    NumericField::create(
                        'ImagesPerPage',
                        _t('GalleryAlbum.IMAGESPERPAGE', 'Images per page')
                    )->displayIf('PaginateImages')->isChecked()->end(),
                    CheckboxField::create(
                        'ShowImageTitles',
                        _t('GalleryAlbum.SHOWIMAGETITLES', 'Show image titles')
                    )
                )
            )
        );
        
        // Answer Field Objects:
        
        return $fields;
    }
    
    /**
     * Event method called after the receiver is written to the database.
     */
    public function onBeforeWrite()
    {
        // Call Parent Event:
        
        parent::onBeforeWrite();
        
        // Create File Folder:
        
        $this->createFolder();
    }
    
    /**
     * Event method called after the receiver is written to the database.
     */
    public function onAfterWrite()
    {
        // Call Parent Event:
        
        parent::onAfterWrite();
        
        // Update File Folder:
        
        $this->updateFolder();
        
        // Create Image Objects:
        
        $this->createImages();
    }
    
    /**
     * Answers the images within the receiver.
     *
     * @return DataList
     */
    public function getImages()
    {
        return GalleryImage::get()->filter(
            array(
                'ParentID' => $this->ID
            )
        );
    }
    
    /**
     * Answers a list of images within the album.
     *
     * @return SS_List
     */
    public function getListItems()
    {
        return $this->getImages();
    }
    
    /**
     * Answers the asset folder path for the receiver.
     *
     * @return string
     */
    public function getFolderPath()
    {
        if ($Gallery = $this->Gallery()) {
            return $Gallery->getFolderPath() . '/' . $this->getFolderName();
        }
    }
    
    /**
     * Answers the asset folder name for the receiver.
     *
     * @return string
     */
    public function getFolderName()
    {
        return $this->URLSegment;
    }
    
    /**
     * Answers true if the album contains an image record associated with the given image file.
     *
     * @return boolean
     */
    public function containsImage(Image $Image)
    {
        return GalleryImage::get()->filter(
            array(
                'ParentID' => $this->ID,
                'MetaImageFileID' => $Image->ID
            )
        )->exists();
    }
    
    /**
     * Answers the parent gallery of the receiver.
     *
     * @return Gallery
     */
    public function Gallery()
    {
        if ($this->Parent() instanceof Gallery) {
            return $this->Parent();
        }
    }
    
    /**
     * Answers a list component for the images within the receiver.
     *
     * @return ListComponent
     */
    public function ImageList()
    {
        $list = ListComponent::create();
        
        $list->addExtraClass('gallery');
        
        $list->setStyleIDFrom($this);
        
        $list->HideTitle  = 1;
        $list->HeadingTag = "h3";
        
        $list->ImageLinksTo = "File";
        $list->TitleLinksTo = "File";
        
        $list->ShowTitles    = $this->ShowImageTitles;
        $list->ItemsPerPage  = $this->ImagesPerPage;
        $list->PaginateItems = $this->PaginateImages;
        
        $list->ImageWidth  = $this->getDefaultThumbnailWidth();
        $list->ImageHeight = $this->getDefaultThumbnailHeight();
        $list->ImageResize = $this->getDefaultThumbnailResize();
        
        $list->setSource($this);
        
        return $list;
    }
    
    /**
     * Answers the meta image for the receiver.
     *
     * @return Image
     */
    public function MetaImage()
    {
        if ($this->MetaImageFileExists()) {
            return $this->MetaImageFile();
        }
        
        if ($Image = $this->getImages()->first()) {
            if ($Image->HasMetaImage()) {
                return $Image->MetaImage();
            }
        }
        
        return parent::MetaImage();
    }
    
    /**
     * Creates a folder for the album to store files.
     */
    protected function createFolder()
    {
        if (!$this->FolderID) {
            
            if ($path = $this->getFolderPath()) {
                
                $Folder = Folder::find_or_make($path);
                
                $this->FolderID = $Folder->ID;
                
            }
            
        }
    }
    
    /**
     * Updates the name of the folder for the album.
     */
    protected function updateFolder()
    {
        if ($Folder = Folder::get()->byID($this->FolderID)) {
            
            $Name = $this->getFolderName();
            
            if ($Folder->Name != $Name) {
                
                $Folder->Name = $Name;
                
                $Folder->write();
                
            }
            
        }
    }
    
    /**
     * Creates the corresponding gallery image objects for any uploaded image files.
     */
    protected function createImages()
    {
        // This Might Take a While:
        
        increase_time_limit_to(3600);
        increase_memory_limit_to('512M');
        
        // Process Uploaded Images:
        
        foreach ($this->UploadedImages() as $File) {
            
            if (!$this->containsImage($File)) {
                
                $Image = GalleryImage::create();
                
                $Image->Title = $File->Title;
                $Image->ParentID = $this->ID;
                $Image->MetaImageFileID = $File->ID;
                
                $Image->write();
                $Image->publish('Stage', 'Live');
                $Image->flushCache();
                
            }
            
            $this->UploadedImages()->removeByID($File->ID);
            
        }
    }
}

/**
 * An extension of the page controller class for a gallery album.
 */
class GalleryAlbum_Controller extends Page_Controller
{
    /**
     * Defines the URLs handled by this controller.
     */
    private static $url_handlers = array(
        
    );
    
    /**
     * Defines the allowed actions for this controller.
     */
    private static $allowed_actions = array(
        
    );
    
    /**
     * Performs initialisation before any action is called on the receiver.
     */
    public function init()
    {
        // Initialise Parent:
        
        parent::init();
        
        // Load Requirements:
        
        Requirements::themedCSS('silverware-gallery', SILVERWARE_GALLERY_DIR);
    }
}
