<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Playlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'bio',
        'instagram_url',
        'youtube_url',
        'facebook_url',
        'soundcloud_url',
        'image',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($playlist) {
            if (empty($playlist->slug)) {
                $playlist->slug = Str::slug($playlist->name);
            }
        });
    }

    public function tracks()
    {
        return $this->hasMany(PlaylistTrack::class)->orderBy('position');
    }

    public function vinylMasters()
    {
        return $this->belongsToMany(VinylMaster::class, 'playlist_tracks')
                    ->withPivot(['position', 'trackable_type', 'trackable_id'])
                    ->orderBy('playlist_tracks.position')
                    ->withTimestamps();
    }

    public function getImageUrlAttribute()
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : asset('assets/images/placeholder.jpg');
    }

    public function addTrack(VinylMaster $vinylMaster, $trackable)
    {
        // Check if playlist already has 10 tracks
        if ($this->tracks()->count() >= 10) {
            return false;
        }

        // Get the next position
        $position = $this->tracks()->max('position') + 1;

        // Add the track with polymorphic relation
        $this->tracks()->create([
            'vinyl_master_id' => $vinylMaster->id,
            'trackable_type' => get_class($trackable),
            'trackable_id' => $trackable->id,
            'position' => $position
        ]);

        return true;
    }

    public function removeTrack($trackId)
    {
        $track = $this->tracks()->find($trackId);
        if ($track) {
            $track->delete();

            // Reorder remaining tracks
            $this->tracks()
                ->where('position', '>', $track->position)
                ->update(['position' => \DB::raw('position - 1')]);
        }
    }

    public function reorderTrack($trackId, int $newPosition)
    {
        $track = $this->tracks()->find($trackId);
        if (!$track) return;

        $currentPosition = $track->position;

        if ($currentPosition === $newPosition) {
            return;
        }

        // Update positions of other tracks
        if ($currentPosition < $newPosition) {
            $this->tracks()
                ->where('position', '>', $currentPosition)
                ->where('position', '<=', $newPosition)
                ->update(['position' => \DB::raw('position - 1')]);
        } else {
            $this->tracks()
                ->where('position', '>=', $newPosition)
                ->where('position', '<', $currentPosition)
                ->update(['position' => \DB::raw('position + 1')]);
        }

        // Set new position for the track
        $track->update(['position' => $newPosition]);
    }
}
