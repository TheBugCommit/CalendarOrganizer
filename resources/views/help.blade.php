@extends('layouts.main')

@section('card-header')
    <div class="card-header">
        <h1 class="title">Help</h1>
    </div>
@endsection

@section('content')
    <div class="container" id="help">
        <h2>Dashboard</h2>
        <p>In the dashboard view, you can create, delete and modify your own calendars, using the button at the bottom of
            the screen.</p>
        <p>The button will open a modal, in which to enter the calendar information. Once the calendar is added, it will
            appear on the screen. On it, there are two buttons. Trash is for deleting i brush icon for modifying calendar
            data.</p>
        <p>In this same view you can also see the calendars to which you belong as a helper.</p>
        <p>If you click on it, the calendar will open, where you can edit, delete and create events.</p>

        <hr>

        <h2>Edit Calendar</h2>

        <p>In this view you can create, delete, modify and publish your events. In case you are a calendar assistant, you
            will
            not be able to publish or edit events that are not yours.</p>

        <ul>
            <li>To create an event click on a day on the calendar.</li>
            <li>To edit an event click on the event and click on the edit button.</li>
            <li>To delete an event click on the event and click on the delete button.</li>
            <li>To publish an event click on the event and click on the publish button.</li>
        </ul>

        <p>At the bottom there is a button, in which 3 options will open:</p>
        <ul>
            <li>Publish (publish the calendar to your google calendar)</li>
            <li>Upload calendar targets</li>
            <li>Edit calendar helpers</li>
        </ul>

        <hr>

        <h2>Edit calendar helpers</h2>
        <p>In this view you will select the calendar helpers. Once selected, an email will be sent to them to join.</p>

        <hr>

        <h2>Categories</h2>
        <p>Here you can edit, create and delete categories, to be used for calendar events.</p>
        <p>Using the button at the bottom of the screen, you can create categories</p>

        <hr>
        <h2>Export Events</h2>
        <p>In this view, you can export the events (within a date range) of any calendar, to which you belong.</p>
        <p>Once you enter your password, rank and calendar. A list with the results will be shown.</p>

        <p>If you click on the button at the bottom of the screen, the download of the events will start.</p>

        <hr>
        <h2>Jasper report</h2>

        <p>In this view you will be able to visualize the events that you have created. Being able to filter by category.
        </p>

    </div>
@endsection
