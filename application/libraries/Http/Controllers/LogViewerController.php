<?php

declare(strict_types=1);

namespace NaassonTeam\LogViewer\Http\Controllers;

use NaassonTeam\LogViewer\Contracts\LogViewer as LogViewerContract;
use NaassonTeam\LogViewer\Entities\{LogEntry, LogEntryCollection};
use NaassonTeam\LogViewer\Exceptions\LogNotFoundException;
use NaassonTeam\LogViewer\Tables\StatsTable;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;
use Illuminate\Support\{Arr, Collection, Str};
use NaassonTeam\LogViewer\Contracts\Utilities\Filesystem as FilesystemContract;

/**
 * Class     LogViewerController
 *
 * @package  LogViewer\Http\Controllers
 * @author   NaassonTeam <info@naasson.com>
 */
class LogViewerController extends Controller
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * The log viewer instance
     *
     * @var \NaassonTeam\LogViewer\Contracts\LogViewer
     */
    protected $logViewer;

    /** @var int */
    protected $perPage = 30;

    /** @var string */
    protected $showRoute = 'log-viewer::logs.show';

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * LogViewerController constructor.
     *
     * @param  \NaassonTeam\LogViewer\Contracts\LogViewer  $logViewer
     */
    public function __construct(LogViewerContract $logViewer)
    {
        $this->logViewer = $logViewer;
        $this->perPage = config('log-viewer.per-page', $this->perPage);
    }
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Show the dashboard.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function index()
    {

        return redirect('/log-viewer/logs-fpm-fcgi');

        // $stats = $this->logViewer->statsTable();
        // $chartData = $this->prepareChartData($stats);
        // $percents = $this->calcPercentages($stats->footer(), $stats->header());
        // return $this->view('dashboard', compact('chartData', 'percents'));
    }

    /**
     * List all logs.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @param $type
     * @return \Illuminate\View\View
     */
    public function listLogs(Request $request, $type)
    {
        $this->settingsLogs($type);

        $stats = $this->logViewer->statsTable();
        $headers = $stats->header();
        $rows = $this->paginate($stats->rows(), $request);

        //dd($type, $request, $stats, $headers, $rows);

        return $this->view('logs', compact('headers', 'rows', 'type'));
    }

    /**
     * Show the log.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $date
     *
     * @param $type
     * @return \Illuminate\View\View
     */
    public function show(Request $request, $type, $date)
    {

        $this->settingsLogs($type);

        $level = 'all';
        $log = $this->getLogOrFail($date);
        $query = $request->get('query');
        $levels = $this->logViewer->levelsNames();
        $entries = $log->entries($level)->paginate($this->perPage);

        //dd($log);
        //dd($log->menu($type));


        return $this->view('show', compact('level', 'log', 'query', 'levels', 'entries', 'type'));
    }

    /**
     * Filter the log entries by level.
     *
     * @param \Illuminate\Http\Request $request
     * @param $type
     * @param string $date
     * @param string $level
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showByLevel(Request $request, $type, $date, $level)
    {

        $this->settingsLogs($type);

        if ($level === 'all')
            return redirect()->route($this->showRoute, [$type, $date]);

        $log = $this->getLogOrFail($date);
        $query = $request->get('query');
        $levels = $this->logViewer->levelsNames();
        $entries = $this->logViewer->entries($date, $level)->paginate($this->perPage);

        return $this->view('show', compact('level', 'log', 'query', 'levels', 'entries', 'type'));
    }

    /**
     * Show the log with the search query.
     *
     * @param \Illuminate\Http\Request $request
     * @param $type
     * @param string $date
     * @param string $level
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function search(Request $request, $type, $date, $level = 'all')
    {

        $this->settingsLogs($type);

        $query = $request->get('query');

        if (is_null($query))
            return redirect()->route($this->showRoute, [$type, $date]);

        $log = $this->getLogOrFail($date);
        $levels = $this->logViewer->levelsNames();
        $needles = array_map(function ($needle) {
            return Str::lower($needle);
        }, array_filter(explode(' ', $query)));
        $entries = $log->entries($level)
            ->unless(empty($needles), function (LogEntryCollection $entries) use ($needles) {
                return $entries->filter(function (LogEntry $entry) use ($needles) {
                    return Str::containsAll(Str::lower($entry->header), $needles);
                });
            })
            ->paginate($this->perPage);

        return $this->view('show', compact('level', 'log', 'query', 'levels', 'entries', 'type'));
    }

    /**
     * Download the log
     *
     * @param string $date
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($date, $type)
    {

        $this->settingsLogs($type);

        return $this->logViewer->download($date);
    }

    /**
     * Delete a log.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        abort_unless($request->ajax(), 405, 'Method Not Allowed');

        $date = $request->input('date');
        $type = $request->input('type');

        $this->settingsLogs($type);

        return response()->json([
            'result' => $this->logViewer->delete($date) ? 'success' : 'error'
        ]);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param string $view
     * @param array $data
     * @param array $mergeData
     *
     * @return \Illuminate\View\View
     */
    protected function view($view, $data = [], $mergeData = [])
    {
        $theme = config('log-viewer.theme');

        //dd(
        //    $view,
        //    $data,
        //    //$data['log'],
        //    $mergeData
        //);

        return view()->make("log-viewer::{$theme}.{$view}", $data, $mergeData);
    }

    /**
     * Paginate logs.
     *
     * @param array $data
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    protected function paginate(array $data, Request $request)
    {
        $data = new Collection($data);
        $page = $request->get('page', 1);
        $path = $request->url();

        return new LengthAwarePaginator(
            $data->forPage($page, $this->perPage),
            $data->count(),
            $this->perPage,
            $page,
            compact('path')
        );
    }

    /**
     * Get a log or fail
     *
     * @param string $date
     *
     * @return \NaassonTeam\LogViewer\Entities\Log|null
     */
    protected function getLogOrFail($date)
    {
        $log = null;

        try {
            $log = $this->logViewer->get($date);
        } catch (LogNotFoundException $e) {
            abort(404, $e->getMessage());
        }

        return $log;
    }

    /**
     * Prepare chart data.
     *
     * @param \NaassonTeam\LogViewer\Tables\StatsTable $stats
     *
     * @return string
     */
    protected function prepareChartData(StatsTable $stats)
    {
        $totals = $stats->totals()->all();

        return json_encode([
            'labels' => Arr::pluck($totals, 'label'),
            'datasets' => [
                [
                    'data' => Arr::pluck($totals, 'value'),
                    'backgroundColor' => Arr::pluck($totals, 'color'),
                    'hoverBackgroundColor' => Arr::pluck($totals, 'highlight'),
                ],
            ],
        ]);
    }

    /**
     * Calculate the percentage.
     *
     * @param array $total
     * @param array $names
     *
     * @return array
     */
    protected function calcPercentages(array $total, array $names)
    {
        $percents = [];
        $all = Arr::get($total, 'all');

        foreach ($total as $level => $count) {
            $percents[$level] = [
                'name' => $names[$level],
                'count' => $count,
                'percent' => $all ? round(($count / $all) * 100, 2) : 0,
            ];
        }

        return $percents;
    }

    protected function settingsLogs($type)
    {
        $this->logViewer->setPattern(
            $type . "-laravel-",
            FilesystemContract::PATTERN_DATE,
            FilesystemContract::PATTERN_EXTENSION
        );
    }
}
