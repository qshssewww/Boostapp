<?php

/**
 * Class PaymentStatusList to keep status list for payment system's response codes
 */
class PaymentStatusList
{
    public const DEFAULT_ERROR_CODE = 999;

    /**
     * @return array
     */
    public static function getList()
    {
        return [
            0 => lang('transaction_approved_meshulam'),
            1 => lang('doc_meshulam_1'),
            2 => lang('doc_meshulam_2'),
            3 => lang('doc_meshulam_3'),
            4 => lang('doc_meshulam_4'),
            5 => lang('doc_meshulam_5'),
            6 => lang('doc_meshulam_6'),
            7 => lang('doc_meshulam_7'),
            19 => lang('doc_meshulam_19'),
            33 => lang('doc_meshulam_33'),
            34 => lang('doc_meshulam_34'),
            35 => lang('doc_meshulam_35'),
            36 => lang('doc_meshulam_36'),
            37 => lang('doc_meshulam_37'),
            38 => lang('doc_meshulam_38'),
            39 => lang('doc_meshulam_39'),
            57 => lang('doc_meshulam_57'),
            58 => lang('doc_meshulam_58'),
            69 => lang('doc_meshulam_69'),
            101 => lang('doc_meshulam_101'),
            106 => lang('doc_meshulam_106'),
            107 => lang('doc_meshulam_107'),
            110 => lang('doc_meshulam_110'),
            111 => lang('doc_meshulam_111'),
            112 => lang('doc_meshulam_112'),
            113 => lang('doc_meshulam_113'),
            114 => lang('doc_meshulam_114'),
            118 => lang('doc_meshulam_118'),
            119 => lang('doc_meshulam_119'),
            124 => lang('doc_meshulam_124'),
            125 => lang('doc_meshulam_125'),
            127 => lang('doc_meshulam_127'),
            129 => lang('doc_meshulam_129'),
            133 => lang('doc_meshulam_133'),
            138 => lang('doc_meshulam_138'),
            146 => lang('doc_meshulam_146'),
            150 => lang('doc_meshulam_150'),
            151 => lang('doc_meshulam_151'),
            156 => lang('doc_meshulam_156'),
            160 => lang('doc_meshulam_160'),
            161 => lang('doc_meshulam_161'),
            162 => lang('doc_meshulam_162'),
            163 => lang('doc_meshulam_163'),
            164 => lang('doc_meshulam_164'),
            169 => lang('doc_meshulam_169'),
            171 => lang('doc_meshulam_171'),
            172 => lang('doc_meshulam_172'),
            173 => lang('doc_meshulam_173'),
            200 => lang('doc_meshulam_200'),
            251 => lang('doc_meshulam_19'),
            260 => lang('doc_meshulam_260'),
            280 => lang('doc_meshulam_280'),
            902 => lang('doc_meshulam_902'),
            920 => lang('doc_meshulam_920'),
            997 => lang('doc_meshulam_997'),
            998 => lang('doc_meshulam_998'),
            999 => lang('doc_meshulam_999'),
        ];
    }

    /**
     * @param $code
     * @return mixed|null
     */
    public static function getErrorMessage($code)
    {
        $errorsList = self::getList();

        if (isset($errorsList[$code])) {
            return $errorsList[$code];
        }

        return null;
    }
}
