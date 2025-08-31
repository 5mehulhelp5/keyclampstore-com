<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_SecurityPro
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\SecurityPro\Plugin;

use Magento\Framework\Data\Form\Element\Label;

/**
 * Class Description
 * @package Mageplaza\SecurityPro\Plugin
 */
class Description
{
    /**
     * @param Label $label
     * @param $html
     *
     * @return string
     */
    public function afterGetElementHtml(Label $label, $html)
    {
        if ($label->getName() === 'log[mp_description]') {
            $result = $this->generateArrayHtml(json_decode($label->getValue()));
            $result = '{ ' . $result . '<p>}</p>';

            return $result;
        }

        return $html;
    }

    /**
     * @param $arr
     * @param int $level
     * @param string $result
     *
     * @return string
     */
    public function generateArrayHtml($arr, $level = 0, $result = '')
    {
        $level++;
        foreach ($arr as $key => $value) {
            $result .= '<p style="margin-left:' . (30 * $level) . 'px">' . '"' . $key . '" => ';
            if (is_object($value)) {
                $result .= ' {</p>';
                $result = $this->generateArrayHtml($value, $level, $result);
                $result .= '<p style="margin-left: ' . (30 * $level) . 'px">} ,</p>';
            } else {
                $result .= '"' . $value . '" ,</p>';
            }
        }

        return $result;
    }
}
