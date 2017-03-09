<?php
namespace PDP\Integration\Model\Sales\Order\Pdf;

class Creditmemo extends \Magento\Sales\Model\Order\Pdf\Creditmemo{
	
	/**
     * Draw lines with image
     *
     * Draw items array format:
     * lines        array;array of line blocks (required)
     * shift        int; full line height (optional)
     * height       int;line spacing (default 10)
     *
     * line block has line columns array
     *
     * column array format
     * text         string|array; draw text (required)
     * feed         int; x position (required)
     * font         string; font style, optional: bold, italic, regular
     * font_file    string; path to font file (optional for use your custom font)
     * font_size    int; font size (default 7)
     * align        string; text align (also see feed parametr), optional left, right
     * height       int;line spacing (default 10)
     *
     * @param  \Zend_Pdf_Page $page
     * @param  array $draw
     * @param  array $pageSettings
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Zend_Pdf_Page
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function drawLineBlocksNew(\Zend_Pdf_Page $page, array $draw, array $pageSettings = [])
    {
        foreach ($draw as $itemsProp) {
            if (!isset($itemsProp['lines']) || !is_array($itemsProp['lines'])) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('We don\'t recognize the draw line data. Please define the "lines" array.')
                );
            }
            $lines = $itemsProp['lines'];
            $height = isset($itemsProp['height']) ? $itemsProp['height'] : 10;

            if (empty($itemsProp['shift'])) {
                $shift = 0;
                foreach ($lines as $line) {
                    $maxHeight = 0;
                    foreach ($line as $column) {
						if(isset($column['image'])) {
							if(isset($column['height']))
							$shift += $column['height'];
						} else {
							$lineSpacing = !empty($column['height']) ? $column['height'] : $height;
							$top = 0;
							if (!is_array($column['text'])) {
								$column['text'] = [$column['text']];
							}
							foreach ($column['text'] as $part) {
								$top += $lineSpacing;
							}
							$maxHeight = $top > $maxHeight ? $top : $maxHeight;
							$shift += $maxHeight;
						}
                    }
                }
                $itemsProp['shift'] = $shift;
            }

            if ($this->y - $itemsProp['shift'] < 15) {
                $page = $this->newPage($pageSettings);
            }

            foreach ($lines as $line) {
                $maxHeight = 0;
                foreach ($line as $column) {
					if(isset($column['image'])) {
						$top = $this->y;
						$feed = $column['feed'];
						$urlImage = $column['image'];
						//$image = \Zend_Pdf_Image::imageWithPath($this->_mediaDirectory->getAbsolutePath($urlImage));
						$image = \Zend_Pdf_Image::imageWithPath($urlImage);
						$height = isset($column['height'])?$column['height']:143;
						$width = isset($column['width'])?$column['width']:143;
						$y1 = $top - $height;
						$y2 = $top;
						$x1 = 25;
						$x2 = $x1 + $width;

						//coordinates after transformation are rounded by Zend
						$page->drawImage($image, $x1, $y1, $x2, $y2);
						$maxHeight = $height+1;
					} else {
						$fontSize = empty($column['font_size']) ? 10 : $column['font_size'];
						if (!empty($column['font_file'])) {
							$font = \Zend_Pdf_Font::fontWithPath($column['font_file']);
							$page->setFont($font, $fontSize);
						} else {
							$fontStyle = empty($column['font']) ? 'regular' : $column['font'];
							switch ($fontStyle) {
								case 'bold':
									$font = $this->_setFontBold($page, $fontSize);
									break;
								case 'italic':
									$font = $this->_setFontItalic($page, $fontSize);
									break;
								default:
									$font = $this->_setFontRegular($page, $fontSize);
									break;
							}
						}

						if (!is_array($column['text'])) {
							$column['text'] = [$column['text']];
						}

						$lineSpacing = !empty($column['height']) ? $column['height'] : $height;
						$top = 0;
						foreach ($column['text'] as $part) {
							if ($this->y - $lineSpacing < 15) {
								$page = $this->newPage($pageSettings);
							}

							$feed = $column['feed'];
							$textAlign = empty($column['align']) ? 'left' : $column['align'];
							$width = empty($column['width']) ? 0 : $column['width'];
							switch ($textAlign) {
								case 'right':
									if ($width) {
										$feed = $this->getAlignRight($part, $feed, $width, $font, $fontSize);
									} else {
										$feed = $feed - $this->widthForStringUsingFontSize($part, $font, $fontSize);
									}
									break;
								case 'center':
									if ($width) {
										$feed = $this->getAlignCenter($part, $feed, $width, $font, $fontSize);
									}
									break;
								default:
									break;
							}
							$page->drawText($part, $feed, $this->y - $top, 'UTF-8');
							$top += $lineSpacing;
						}

						$maxHeight = $top > $maxHeight ? $top : $maxHeight;
					}
                }
                $this->y -= $maxHeight;
            }
        }

        return $page;
    }	
}