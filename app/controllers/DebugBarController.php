<?php

use App\Utils\DebugBar;

require_once __DIR__ . '/BaseController.php';

class DebugBarController extends BaseController
{
    /**
     * @param $mode
     * @return bool
     */
    public function setDebugMode($mode)
    {
        if ($mode) {
            DebugBar::enable();
            DebugBar::init();
        } else {
            DebugBar::disable();
        }

        return $this->json(['success' => true]);
    }

    /**
     * @param $key
     * @return bool|string
     */
    public function getDebugInfo($key)
    {
        $data = DebugBar::getInfo($key);

        if (empty($data)) {
            return 'No data found.';
        }

        return $this->json([
            'table' => $this->renderAsString('debugbar/_table', [
                'dbQueries' => $data,
            ]),
            'count' => count($data),
        ]);
    }
}