<?php

/**
 * @property $id
 * @property $CompanyNum
 * @property $rating_google
 * @property $rating_own
 * @property $rating_client
 * @property $rating_total
 * @property $reviews_count
 *
 * Class CompanyInfo
 */
class CompanyInfo extends \Hazzard\Database\Model
{
    protected $table = 'boostapp.company_info_explore';

    /**
     * @return void
     */
    public function recalculateTotalRating()
    {
        $rating = 5; // default rating

        if ($this->rating_google > 0) {
            $rating = $this->rating_google;
        }

        if ($this->rating_client > 0) {
            if ($this->rating_google > 0) {
                $rating = ($this->rating_google + $this->rating_client) / 2;
            } else {
                $rating = $this->rating_client;
            }
        }

        if ($this->rating_own > 0 && $this->rating_own > $rating) {
            $rating = $this->rating_own;
        }

        $this->rating_total = $rating;
        $this->save();
    }
}
