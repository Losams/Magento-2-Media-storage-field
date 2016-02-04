<?php
namespace Mynamspace\Mymodule\Block\Adminhtml\Mediastorage\Helper;

use Magento\Framework\Escaper;
use Magento\Cms\Model\Wysiwyg\Config;

class Mediastorage extends \Magento\Framework\Data\Form\Element\Textarea
{
    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        Config $wysiwygConfig,
        Escaper $escaper,
        $data = []
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);

        $this->setType('textarea');
        $this->setExtType('textarea');

        $config =$wysiwygConfig->getConfig();
        $config->setEncodeDirectives(false);
        $config->setNoDisplay(true);
        $config->setFilesBrowserWindowWidth(100);
        $this->setConfig($config);
    }

    /**
     * @return array
     */
    protected function getButtonTranslations()
    {
        $buttonTranslations = [
            'Insert Image...' => $this->translate('Insert Image...'),
            'Insert Media...' => $this->translate('Insert Media...'),
            'Insert File...' => $this->translate('Insert File...'),
        ];

        return $buttonTranslations;
    }

    /**
     * @return string
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getElementHtml()
    {
        $htmlId = $this->getHtmlId();

        $js = '<script type="text/javascript">
                //<![CDATA[
                require(["jquery"], function(jQuery){
                    (function($) {
                        var input = $("#'.$htmlId.'");
                        var container = $("#thumb_'.$htmlId.'");

                        $(input).on("change", function(e) {
                            $(container).html("");
                            $(container).html("<img src=\'" + $(this).val() + "\' height=\'50\' width=\'50\' style=\'float:left;margin-right:20px;\' />");
                            return e.preventDefault();
                        });
                    })(jQuery);
                });
        //]]>
        </script>';

        // Display only buttons to additional features
        if ($this->getConfig('widget_window_url')) {
            $html = '';
            $thumb = '';
            $this->addClass('input-text admin__control-text');

            $html .= '<div id="thumb_'.$htmlId.'">';
            if ($this->getEscapedValue()) {
                $html .= '<img id="thumb_'.$htmlId.'" src="'. $this->getEscapedValue() .'" width="50" height="50" style="float:left;margin-right:20px;" />';
            }
            $html .= '</div>';

            $html .= $this->_getButtonsHtml();

            $html .= '<input id="' .
                $htmlId .
                '" name="' .
                $this->getName() . '" ' .
                $this->_getUiId() .
                ' value="' .
                $this->getEscapedValue() .
                '" ' .
                $this->serialize(
                    $this->getHtmlAttributes()
                ) . ' readonly />';

            $html .= $js;

            return $html;
        }


        return $html;
    }

    /**
     * Return Editor top Buttons HTML
     *
     * @return string
     */
    protected function _getButtonsHtml()
    {
        $buttonsHtml = '<div id="buttons' . $this->getHtmlId() . '" class="buttons-set">';
        $buttonsHtml .= $this->_getPluginButtonsHtml(true);
        $buttonsHtml .= '</div>';

        return $buttonsHtml;
    }

    /**
     * Prepare Html buttons for additional WYSIWYG features
     *
     * @param bool $visible Display button or not
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _getPluginButtonsHtml($visible = true)
    {
        $buttonsHtml = '';

        // Button to media images insertion window
        if ($this->getConfig('add_images')) {
            $buttonsHtml .= $this->_getButtonHtml(
                [
                    'title' => $this->translate('Insert Image...'),
                    'onclick' => "MediabrowserUtility.openDialog('" . $this->getConfig(
                        'files_browser_window_url'
                    ) . "target_element_id/" . $this->getHtmlId() . "/" . (null !== $this->getConfig(
                        'store_id'
                    ) ? 'store/' . $this->getConfig(
                        'store_id'
                    ) . '/' : '') . "')",
                    'class' => 'action-add-image plugin',
                    'style' => $visible ? '' : 'display:none',
                ]
            );


            $htmlId = $this->getHtmlId();
            $buttonsHtml .= $this->_getButtonHtml(
                [
                    'title' => $this->translate('Delete Image...'),
                        'onclick' => "if (confirm('Are you sûre?')){
                            document.getElementById('".$htmlId."').value = '';
                        }",
                        'class' => 'action-add-image plugin',
                        'style' => $visible ? '' : 'display:none',
                    ]
                );

        }

        return $buttonsHtml;
    }

    /**
     * Return custom button HTML
     *
     * @param array $data Button params
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _getButtonHtml($data)
    {
        $html = '<button type="button"';
        $html .= ' class="scalable ' . (isset($data['class']) ? $data['class'] : '') . '"';
        $html .= isset($data['onclick']) ? ' onclick="' . $data['onclick'] . '"' : '';
        $html .= isset($data['style']) ? ' style="' . $data['style'] . '"' : '';
        $html .= isset($data['id']) ? ' id="' . $data['id'] . '"' : '';
        $html .= '>';
        $html .= isset($data['title']) ? '<span><span><span>' . $data['title'] . '</span></span></span>' : '';
        $html .= '</button>';

        return $html;
    }

    /**
     * Editor config retriever
     *
     * @param string $key Config var key
     * @return mixed
     */
    public function getConfig($key = null)
    {
        if (!$this->_getData('config') instanceof \Magento\Framework\DataObject) {
            $config = new \Magento\Framework\DataObject();
            $this->setConfig($config);
        }
        if ($key !== null) {
            return $this->_getData('config')->getData($key);
        }
        return $this->_getData('config');
    }

    /**
     * Translate string using defined helper
     *
     * @param string $string String to be translated
     * @return \Magento\Framework\Phrase
     */
    public function translate($string)
    {
        return (string)new \Magento\Framework\Phrase($string);
    }

    /**
     * Check whether Wysiwyg is enabled or not
     *
     * @return bool
     */
    public function isEnabled()
    {
        return false;
    }

    /**
     * Check whether Wysiwyg is loaded on demand or not
     *
     * @return bool
     */
    public function isHidden()
    {
        return $this->getConfig('hidden');
    }
}
