<?php

use Hazzard\Database\Model;

require_once __DIR__ . '/Docs.php';

/**
 * @property $id
 * @property $InvoiceId
 * @property $DocId
 * @property $CreatedAt
 */
class DocsLinkToInvoice extends Model
{
    protected $table = 'boostapp.docs_link_to_invoice';

    public const CREATE_RULES = [
        'InvoiceId' => 'required|exists:boostapp.docs,id',
        'DocId' => 'required|exists:boostapp.docs,id',
    ];

    /**
     * @param int $invoiceId
     * @return DocsLinkToInvoice[]
     */
    public static function getAllDocsLinkToInvoice(int $invoiceId): array
    {
         return self::where('InvoiceId', $invoiceId)->orderBy('CreatedAt')->get();
    }

    /**
     * @param int $invoiceId
     * @return int[]
     */
    public static function getAllDocsLinkToInvoiceIds(int $invoiceId): array
    {
        $docsIdArray = [];
        $docsLinkToInvoiceArray = self::getAllDocsLinkToInvoice($invoiceId);
        foreach ($docsLinkToInvoiceArray as $docsLinkToInvoice) {
            if($invoiceId !== (int)$docsLinkToInvoice->DocId) {
                $docsIdArray[] = (int)$docsLinkToInvoice->DocId;
            }
        }
        return $docsIdArray;
    }

    /**
     * @param int $docId
     * @return int $docId - if not found
     */
    public static function getInvoiceIdFromDoc(int $docId): int
    {
       return self::where('DocId',$docId)->pluck('InvoiceId') ?? $docId;
    }

    /**
     * @throws Exception
     */
    public function saveAfterValidation(): void
    {
        $validator = Validator::make($this->getAttributes(), self::CREATE_RULES);
        if ($validator->passes()) {
            $this->save();
        } else {
            throw new Exception(json_encode($validator->errors()->toArray()));
        }
        if($this->id === 0 ) {
            throw new Exception('error in add DocsLinkToInvoice - InvoiceId: '. $this->InvoiceId ?? '0' . ' docId:  ' . $this->DocId ?? '0');
        }

    }

    /**
     * @param $docInvoiceId
     * @param $docsId
     * @return int
     * @throws Exception
     */
    public static function createDocsLinkToInvoice($docInvoiceId, $docsId): int
    {
        $DocsLinkToInvoice = new DocsLinkToInvoice();
        $DocsLinkToInvoice->DocId = $docsId;
        $DocsLinkToInvoice->InvoiceId = $docInvoiceId;
        $DocsLinkToInvoice->saveAfterValidation();
        return $DocsLinkToInvoice->id ?? 0;

    }

    /**
     * @param $id
     * @return int|null
     */
    public static function removeById($id): ?int
    {
        return self::where('id', $id)->delete();
    }




}
