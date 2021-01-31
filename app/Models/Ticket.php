<?php
declare(strict_types=1);

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Ticket
 * @package App\Models
 * @property int $id
 * @property string $subject
 * @property string $content
 * @property bool $status
 * @property DateTime $created_at
 * @property DateTime $updated_at
 */
class Ticket extends Model
{
    use HasFactory;

    public $fillable = [
        'subject',
        'content',
        'status',
    ];

    public function scopeOpened(Builder $query): Builder
    {
        return $query->where('status', false);
    }

    public function scopeClosed(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
