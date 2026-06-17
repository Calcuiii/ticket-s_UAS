<?php

namespace App\GraphQL\Mutations;

use App\Models\Event;
use Illuminate\Support\Str;

class EventMutations
{
    public function create($root, array $args): Event
    {
        $input = $args['input'];

        return Event::create([
            'category_id'     => $input['category_id'],
            'title'           => $input['title'],
            'slug'            => Str::slug($input['title']) . '-' . time(),
            'description'     => $input['description'] ?? null,
            'location'        => $input['location'],
            'event_date'      => $input['event_date'],
            'event_end_date'  => $input['event_end_date'] ?? null,
            'ticket_price'    => $input['ticket_price'],
            'total_stock'     => $input['total_stock'],
            'available_stock' => $input['total_stock'],
            'organizer_name'  => $input['organizer_name'],
            'status'          => $input['status'] ?? 'draft',
        ]);
    }

    public function update($root, array $args): Event
    {
        $event = Event::findOrFail($args['id']);
        $event->update($args['input']);
        return $event;
    }

    public function delete($root, array $args): Event
    {
        $event = Event::findOrFail($args['id']);
        $event->delete();
        return $event;
    }
}