<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enrollment extends Model
{
    use UuidTrait;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'guest_email',
        'guest_name',
        'first_name',
        'last_name',
        'phone_number',
        'company_name',
        'country',
        'street_address',
        'building_type',
        'town_city',
        'state',
        'order_notes',
        'deliver_to_different_address',
        'different_address',
        'total_amount',
        'discounted_amount',
        'discount_id',
        'course_id',
        'status'
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class)
            ->withPivot('original_price', 'price_paid', 'selected_date')
            ->withTimestamps();
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    // Update add-ons relationship
    public function addOns(): HasMany
    {
        return $this->hasMany(EnrollmentAddOn::class);
    }

    public function getCourseDatesAttribute()
    {
        return $this->courses->flatMap->courseDates;
    }

    // Calculate total amount including add-ons
    public function getTotalWithAddOnsAttribute()
    {
        $addOnsTotal = $this->addOns->sum('price_paid');
        return $this->discounted_amount + $addOnsTotal;
    }
}
