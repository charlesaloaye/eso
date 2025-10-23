<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Course extends Model
{
    use UuidTrait;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'title',
        'name',
        'description',
        'price',
        'duration',
        'time',
        'thumbnail',
        'capacity',
        'original_price',
        'discount_id',
        'slug',
        'location',
        'instructor'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($course) {
            // Always generate slug on creation
            $course->slug = $course->generateSlug($course->title);
        });

        static::updating(function ($course) {
            // Update slug if title changed or slug is empty
            if ($course->isDirty('title') || empty($course->slug)) {
                $course->slug = $course->generateSlug($course->title);
            }
        });
    }


    /**
     * Generate a unique slug from the title
     */
    private function generateSlug($title)
    {
        $slug = Str::slug($title, '-', 'en');

        // Ensure slug is unique
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)
            ->when(isset($this->id), function ($query) {
                $query->where('id', '!=', $this->id);
            })
            ->exists()
        ) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function keyPoints(): HasMany
    {
        return $this->hasMany(CourseKeyPoint::class)->orderBy('order');
    }

    public function units(): HasMany
    {
        return $this->hasMany(CourseUnit::class)->orderBy('order');
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    public function videos(): HasMany
    {
        return $this->hasMany(CourseVideo::class)->orderBy('order');
    }

    public function courseDates(): HasMany
    {
        return $this->hasMany(CourseDate::class)->orderBy('start_date');
    }

    public function addOns(): HasMany
    {
        return $this->hasMany(CourseAddOn::class)->orderBy('order');
    }

    public function applyDiscount($discountCode)
    {
        $discount = Discount::where('code', $discountCode)
            ->where('active', true)
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now())
            ->where(function ($query) {
                $query->whereNull('max_uses')
                    ->orWhereRaw('times_used < max_uses');
            })
            ->first();

        if (!$discount) {
            return $this->price;
        }

        return $this->price * (1 - ($discount->percent_off / 100));
    }

    public function getDiscountedPriceAttribute()
    {
        if ($this->discount) {
            return $this->price * (1 - ($this->discount->percent_off / 100));
        }
        return $this->price;
    }

    public function getTotalPriceWithAddOnsAttribute()
    {
        $addOnsTotal = $this->addOns->sum('price');
        return $this->price + $addOnsTotal;
    }

    /**
     * Get the name attribute, fallback to title if name is not set
     */
    public function getNameAttribute($value)
    {
        return $value ?: $this->title;
    }
}
