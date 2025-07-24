<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\TimeEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TimeEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TimeEntry::with(['project', 'project.client'])
            ->where('user_id', auth()->id());

        // Apply date range filter
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        // Apply project filter
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Apply client filter (via project relationship)
        if ($request->filled('client_id')) {
            $query->whereHas('project', function ($q) use ($request) {
                $q->where('client_id', $request->client_id);
            });
        }

        // Apply description search
        if ($request->filled('description')) {
            $query->where('description', 'like', '%'.$request->description.'%');
        }

        $timeEntries = $query->orderBy('date', 'desc')
            ->paginate(15)
            ->withQueryString(); // This preserves the query parameters in pagination links

        foreach ($timeEntries as $entry) {
            if (! isset($entry->duration_minutes) && isset($entry->start_time) && isset($entry->end_time)) {
                $start = Carbon::parse($entry->start_time);
                $end = Carbon::parse($entry->end_time);

                // If end time is earlier than start time, it means the time range spans midnight
                if ($end->lt($start)) {
                    $end->addDay();
                }

                $entry->duration_minutes = $start->diffInMinutes($end);
            }
        }

        // Get projects and clients for filter dropdowns
        $projects = Project::where('status', '!=', 'archive')
            ->where('user_id', auth()->id())
            ->orderBy('name')
            ->get();

        $clients = Client::where('user_id', auth()->id())->orderBy('name')->get();

        return view('time-entries.index', compact('timeEntries', 'projects', 'clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $projects = Project::where('status', '!=', 'archive')
            ->where('user_id', auth()->id())
            ->orderBy('name')
            ->get();

        $selectedProjectId = $request->query('project_id');

        return view('time-entries.create', compact('projects', 'selectedProjectId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'date' => 'required|date',
            'entry_type' => 'required|in:duration,time_range',
            'duration_hours' => 'required_if:entry_type,duration|nullable|integer|min:0',
            'duration_minutes' => 'required_if:entry_type,duration|nullable|integer|min:0|max:59',
            'start_time' => 'required_if:entry_type,time_range|nullable|date_format:H:i',
            'end_time' => 'required_if:entry_type,time_range|nullable|date_format:H:i|after:start_time',
            'description' => 'nullable|string',
        ]);

        // Verify that the project belongs to the authenticated user
        $project = Project::where('id', $validated['project_id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $timeEntry = new TimeEntry;
        $timeEntry->project_id = $validated['project_id'];
        $timeEntry->user_id = auth()->id();
        $timeEntry->date = $validated['date'];
        $timeEntry->description = $validated['description'] ?? null;

        if ($validated['entry_type'] === 'duration') {
            $timeEntry->duration_minutes = ($validated['duration_hours'] * 60) + $validated['duration_minutes'];
        } else {
            $timeEntry->start_time = $validated['start_time'];
            $timeEntry->end_time = $validated['end_time'];

            // Calculate duration in minutes
            $start = Carbon::parse($validated['start_time']);
            $end = Carbon::parse($validated['end_time']);

            if ($end->lt($start)) {
                $end->addDay();
            }

            $timeEntry->duration_minutes = $end->diffInMinutes($start);
        }

        $timeEntry->save();

        return redirect()->route('time-entries.index')
            ->with('success', 'Temps enregistré avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $timeEntry = TimeEntry::with(['project', 'project.client', 'user'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return view('time-entries.show', compact('timeEntry'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $timeEntry = TimeEntry::where('user_id', auth()->id())->findOrFail($id);

        $projects = Project::where('status', '!=', 'archive')
            ->orderBy('name')
            ->get();

        return view('time-entries.edit', compact('timeEntry', 'projects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $timeEntry = TimeEntry::where('user_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'date' => 'required|date',
            'entry_type' => 'required|in:duration,time_range',
            'duration_hours' => 'required_if:entry_type,duration|nullable|integer|min:0',
            'duration_minutes' => 'required_if:entry_type,duration|nullable|integer|min:0|max:59',
            'start_time' => 'required_if:entry_type,time_range|nullable|date_format:H:i',
            'end_time' => 'required_if:entry_type,time_range|nullable|date_format:H:i|after:start_time',
            'description' => 'nullable|string',
        ]);

        // Verify that the project belongs to the authenticated user
        $project = Project::where('id', $validated['project_id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $timeEntry->project_id = $validated['project_id'];
        $timeEntry->date = $validated['date'];
        $timeEntry->description = $validated['description'] ?? null;

        if ($validated['entry_type'] === 'duration') {
            $timeEntry->start_time = null;
            $timeEntry->end_time = null;
            $timeEntry->duration_minutes = ($validated['duration_hours'] * 60) + $validated['duration_minutes'];
        } else {
            $timeEntry->start_time = $validated['start_time'];
            $timeEntry->end_time = $validated['end_time'];

            // Calculate duration in minutes
            $start = Carbon::parse($validated['start_time']);
            $end = Carbon::parse($validated['end_time']);

            // If end time is earlier than start time, it means the time range spans midnight
            // Add a day to end time to calculate the correct duration
            if ($end->lt($start)) {
                $end->addDay();
            }

            $timeEntry->duration_minutes = $start->diffInMinutes($end);
        }

        $timeEntry->save();

        return redirect()->route('time-entries.index')
            ->with('success', 'Temps mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $timeEntry = TimeEntry::where('user_id', auth()->id())->findOrFail($id);

        $timeEntry->delete();

        return redirect()->route('time-entries.index')
            ->with('success', 'Temps supprimé avec succès.');
    }

    /**
     * Display time entries in a calendar view
     */
    public function calendar(Request $request)
    {
        $query = TimeEntry::with(['project', 'project.client'])
            ->where('user_id', auth()->id());

        // Apply the same filters as in the index method
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('client_id')) {
            $query->whereHas('project', function ($q) use ($request) {
                $q->where('client_id', $request->client_id);
            });
        }

        if ($request->filled('description')) {
            $query->where('description', 'like', '%'.$request->description.'%');
        }

        // Get the month to display
        $month = $request->input('month', now()->format('Y-m'));
        $startOfMonth = Carbon::parse($month.'-01');
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // Get time entries for the selected month
        $query->whereBetween('date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')]);
        $timeEntries = $query->get();

        // Group time entries by date
        $groupedEntries = $timeEntries->groupBy(function ($entry) {
            return $entry->date->format('Y-m-d');
        });

        // Get projects and clients for filter dropdowns
        $projects = Project::where('status', '!=', 'archive')
            ->where('user_id', auth()->id())
            ->orderBy('name')
            ->get();

        $clients = Client::where('user_id', auth()->id())->orderBy('name')->get();

        // Generate calendar data
        $calendar = [];
        $currentDay = $startOfMonth->copy();

        // Add days from previous month to start the calendar on Monday
        $firstDayOfWeek = $currentDay->dayOfWeek; // 0 (Sunday) to 6 (Saturday)
        $daysToSubtract = $firstDayOfWeek == 0 ? 6 : $firstDayOfWeek - 1; // Adjust to start on Monday

        if ($daysToSubtract > 0) {
            $prevMonth = $currentDay->copy()->subDays($daysToSubtract);
            for ($i = 0; $i < $daysToSubtract; $i++) {
                $calendar[] = [
                    'date' => $prevMonth->format('Y-m-d'),
                    'day' => $prevMonth->day,
                    'isCurrentMonth' => false,
                    'entries' => [],
                ];
                $prevMonth->addDay();
            }
        }

        // Add days from current month
        while ($currentDay->month == $startOfMonth->month) {
            $dateStr = $currentDay->format('Y-m-d');
            $calendar[] = [
                'date' => $dateStr,
                'day' => $currentDay->day,
                'isCurrentMonth' => true,
                'entries' => $groupedEntries->get($dateStr, []),
            ];
            $currentDay->addDay();
        }

        // Add days from next month to complete the last week
        $lastDayOfWeek = $currentDay->copy()->subDay()->dayOfWeek; // 0 (Sunday) to 6 (Saturday)
        $daysToAdd = $lastDayOfWeek == 0 ? 0 : 7 - $lastDayOfWeek;

        for ($i = 0; $i < $daysToAdd; $i++) {
            $calendar[] = [
                'date' => $currentDay->format('Y-m-d'),
                'day' => $currentDay->day,
                'isCurrentMonth' => false,
                'entries' => [],
            ];
            $currentDay->addDay();
        }

        // Previous and next month links
        $prevMonth = $startOfMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $startOfMonth->copy()->addMonth()->format('Y-m');

        return view('time-entries.calendar', compact(
            'calendar',
            'month',
            'startOfMonth',
            'prevMonth',
            'nextMonth',
            'projects',
            'clients'
        ));
    }

    /**
     * Export time entries to CSV
     */
    public function export(Request $request)
    {
        $query = TimeEntry::with(['project', 'project.client'])
            ->where('user_id', auth()->id());

        // Apply the same filters as in the index method
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('client_id')) {
            $query->whereHas('project', function ($q) use ($request) {
                $q->where('client_id', $request->client_id);
            });
        }

        if ($request->filled('description')) {
            $query->where('description', 'like', '%'.$request->description.'%');
        }

        $timeEntries = $query->orderBy('date', 'desc')->get();

        // Create CSV file
        $filename = 'time_entries_'.now()->format('Y-m-d_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($timeEntries) {
            $file = fopen('php://output', 'w');

            // Add CSV header
            fputcsv($file, [
                'Date',
                'Projet',
                'Client',
                'Durée (minutes)',
                'Heure de début',
                'Heure de fin',
                'Description',
            ]);

            // Add data rows
            foreach ($timeEntries as $entry) {
                fputcsv($file, [
                    $entry->date->format('Y-m-d'),
                    $entry->project->name,
                    $entry->project->client->name,
                    $entry->duration_minutes,
                    $entry->start_time ? $entry->start_time->format('H:i') : '',
                    $entry->end_time ? $entry->end_time->format('H:i') : '',
                    $entry->description,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show the stopwatch interface for time tracking.
     */
    public function stopwatch(Request $request)
    {
        $projects = Project::where('status', '!=', 'archive')
            ->where('user_id', auth()->id())
            ->orderBy('name')
            ->get();

        $selectedProjectId = $request->query('project_id');

        return view('time-entries.stopwatch', compact('projects', 'selectedProjectId'));
    }

    /**
     * Store a time entry created with the stopwatch.
     */
    public function storeStopwatch(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'description' => 'nullable|string',
            'start_time' => 'required|date_format:Y-m-d H:i:s',
            'end_time' => 'required|date_format:Y-m-d H:i:s|after:start_time',
        ]);

        // Verify that the project belongs to the authenticated user
        $project = Project::where('id', $validated['project_id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Create a new time entry
        $timeEntry = new TimeEntry;
        $timeEntry->project_id = $validated['project_id'];
        $timeEntry->user_id = auth()->id();
        $timeEntry->date = Carbon::parse($validated['start_time'])->format('Y-m-d');
        $timeEntry->description = $validated['description'] ?? null;
        $timeEntry->start_time = Carbon::parse($validated['start_time'])->format('H:i');
        $timeEntry->end_time = Carbon::parse($validated['end_time'])->format('H:i');

        // Calculate duration in minutes
        $start = Carbon::parse($validated['start_time']);
        $end = Carbon::parse($validated['end_time']);
        $timeEntry->duration_minutes = $start->diffInMinutes($end);

        $timeEntry->save();

        return redirect()->route('time-entries.index')
            ->with('success', 'Temps enregistré avec succès.');
    }
}
