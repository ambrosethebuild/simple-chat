<?php

namespace SimpleChat\Drivers;

use SimpleChat\Contracts\ChatDriver;

class SupabaseDriver extends EloquentDriver
{
    // Supabase uses standard Eloquent for storage.
    // Realtime subscriptions are handled purely on the frontend.
    // This class exists to allow the driver selection and potential future backend logic.
}
