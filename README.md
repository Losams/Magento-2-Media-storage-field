# Media Storage field [Admin]

## Explain

This code can really be improve, I know, that's juste a first try.

This field type provide à media explorer alone, without wysiwyg. This is quite a replacement for 'image' field. 

1 Button "add image" to show Magento 2 admin media explorer.

1 Button "delete image" for non required field (like checkbox on file / image field)

## How to use

Nothing required, just put this file anywhere on one of your module, and then use it as type of field :


    $fieldset->addField(
    'myimage',
    'Mynamespace\Mymodule\Block\Adminhtml\Mediastorage\Helper\Mediastorage',
    [
    'name' => 'myimage',
    'label' => __('Image with super media explorer'),
    'title' => __('Image with super media explorer'),
    ]
    );


