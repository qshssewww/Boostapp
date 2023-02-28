<?php

require_once __DIR__ . '/../../../BaseResponse.php';
require_once __DIR__ . '/../../CartResponse.php';
require_once __DIR__ . '/../itemDetails/ProductDetailsResponse.php';
require_once __DIR__ . '/../itemDetails/ProductDetailsResponse.php';
require_once __DIR__ . '/../../getCartData/lessons/LessonsResponse.php';
require_once __DIR__ . '/../../../../../../office/Classes/ClassStudioDate.php';


class ItemDetailsResponse extends BaseResponse implements CartResponse
{
    //todo-change-To-enum?
    public const PRODUCT = 'product';
    public const LESSON = 'lesson';

    public $item;

    /**
     * ItemDetailsResponse constructor.
     * @param string $type
     */
    public function __construct(string $type)
    {
        switch ($type) {
            case self::PRODUCT:
                /** @var ProductDetailsResponse[] $item */
                $this->item = new ProductDetailsResponse();
                break;
            case self::LESSON:
                /** @var LessonsResponse[] $item */
                $this->item = [];
                break;
            default:
                $this->item= [];
                break;
        }
    }

    /**
     * @param ClassStudioDate $ClassStudioDate
     */
    public function addLesson(ClassStudioDate $ClassStudioDate): void
    {
        $this->item[] = new LessonsResponse($ClassStudioDate);
    }

    /**
     * @param false $itemIsArray
     * @return bool
     */
    public function getData($itemIsArray = false): bool
    {
        $response = $this->returnData();
        if($itemIsArray) {
            $response['items'] = $this->item;
        } else {
            $response['item'] = $this->item->getData();
        }
        echo json_encode($response);
        return true;
    }

    /**
     * @param string $message
     * @param int $status
     * @return bool
     */
    public function returnError(string $message = '', int $status = 400): bool
    {
        $this->setError($message, $status);
        return $this->getData();
    }

}
