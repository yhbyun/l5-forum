<?php

namespace App\Http\Controllers;

class NotificationsController extends Controller
{

    /**
     * Display a listing of notifications
     *
     * @return Response
     */
    public function index()
    {
        $notifications = auth()->user()->notifications();

        auth()->user()->notification_count = 0;
        auth()->user()->save();

        return view('notifications.index', compact('notifications'));
    }

    public function count()
    {
        return auth()->user()->notification_count;
    }
}
