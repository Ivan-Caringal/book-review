<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder; // adding this is for safety only

class Book extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'author'];

    
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function scopeTitle(Builder $query, string $title): Builder
    {
        return $query->where('title', 'LIKE', '%' . $title . '%');
    }
                                                                                            //adding QueryBuilder is for safety only
     public function scopeWithReviewsCount(Builder $query, $from = null, $to = null): Builder|QueryBuilder
    {
        return $query->withCount([
            'reviews' => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)
        ]);
    }

    public function scopeWithAvgRating(Builder $query, $from = null, $to = null): Builder|QueryBuilder
    {
        return $query->withAvg([
            'reviews' => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)
        ], 'rating');
    }


    public function scopePopular(Builder $query, $from = null, $to = null): Builder|QueryBuilder
    {
        return $query->withReviewsCount($from, $to)
            ->orderBy('reviews_count', 'desc');
    }

    //SELECT books.*, (SELECT AVG(rating) FROM reviews WHERE reviews.book_id = books.id) AS reviews_avg_rating FROM books ORDER BY reviews_avg_rating DESC;

      public function scopeHighestRated(Builder $query, $from = null, $to = null): Builder|QueryBuilder   //
    {
        return $query->withAvgRating($from, $to)
            ->orderBy('reviews_avg_rating', 'desc');
    }

    public function scopeMinReviews(Builder $query, int $minReviews): Builder|QueryBuilder
    {
        return $query->having('reviews_count', '>=', $minReviews);
    }
                                      
     private function dateRangeFilter(Builder $query, $from = null, $to = null)
    {
        if ($from && !$to) {
            $query->where('created_at', '>=', $from);
        } elseif (!$from && $to) {
            $query->where('created_at', '<=', $to);
        } elseif ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }
    }

    public function scopePopularLastMonth(Builder $query): Builder|QueryBuilder
    {
        return $query->popular(now()->subMonth(), now())
            ->highestRated(now()->subMonth(), now())
            ->minReviews(2);
    }
    public function scopePopularLast6Months(Builder $query): Builder|QueryBuilder
    {
        return $query->popular(now()->subMonths(6), now())
            ->highestRated(now()->subMonths(6), now())
            ->minReviews(5);
    }

    public function scopeHighestRatedLastMonth(Builder $query): Builder|QueryBuilder
    {
        return $query->highestRated(now()->subMonth(), now())
            ->popular(now()->subMonth(), now())
            ->minReviews(2);
    }

    public function scopeHighestRatedLast6Months(Builder $query): Builder|QueryBuilder
    {
        return $query->highestRated(now()->subMonths(6), now())
            ->popular(now()->subMonths(6), now())
            ->minReviews(5);
    }

     protected static function booted()
{
    static::updated(function (Book $book) {
        cache()->forget('book:' . $book->id);

        // Forget all known filtered lists
        
        cache()->forget('books:'); // if you have a default no-filter key
    });

    static::created(function (Book $book) {
        cache()->forget('book:' . $book->id);
        cache()->forget('books:');
    });

    static::deleted(function (Book $book) {
        cache()->forget('book:' . $book->id);
        cache()->forget('books:');
    });
}


}
