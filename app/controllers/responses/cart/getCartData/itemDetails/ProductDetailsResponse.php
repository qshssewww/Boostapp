<?php

require_once __DIR__ . '/ProductVariantsOptionsResponse.php';
require_once __DIR__ . '/VariantResponse.php';
require_once __DIR__ . '/../../../../../../office/Classes/cartClasses/ProductDetails.php';

class ProductDetailsResponse
{
    /** @var VariantResponse[] $variants */
    public $variants;
    /** @var ProductVariantsOptionsResponse[] $options */
    public $options;

    /**
     * ProductDetailsResponse constructor.
     */
    public function __construct()
    {
        $this->variants = [];
        $this->options = [];
    }

    public function addProductDetail(ProductDetails $ProductDetails): void
    {
        $this->variants[] = new VariantResponse($ProductDetails->itemDetailsId, $ProductDetails->getInventoryNow());
        if(is_numeric($ProductDetails->sizesId) && $ProductDetails->sizesId > 0) {
            if(empty($this->options['sizes'])) {
                $number = count($this->options)+1;
                $this->options['sizes'] = new ProductVariantsOptionsResponse('sizes', lang('size_shop_render'), $number);
            } else{
                $number = $this->options['sizes']->number;
            }
            end($this->variants)->{'option'.$number} = $ProductDetails->sizesName;
        }
        if(is_numeric($ProductDetails->colorsId) && $ProductDetails->colorsId > 0) {
            if(empty($this->options['color'])) {
                $number = count($this->options)+1;
                $this->options['color'] = new ProductVariantsOptionsResponse('colors', lang('color_shop_render'), $number);
            } else{
                $number = $this->options['color']->number;
            }
            end($this->variants)->{'option'.$number} = $ProductDetails->getColorNameAndHex();
        }

    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $response['options'] = array_values($this->options);
        $response['variants'] = $this->variants;
        return $response;
    }


}
