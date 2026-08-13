<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(private readonly ReportService $reports) {}

    public function attendance(Request $request): Response|StreamedResponse|HttpResponse
    {
        $filters = [
            'class_id' => $request->integer('class_id') ?: null,
            'section_id' => $request->integer('section_id') ?: null,
            'start_date' => $request->query('start_date', now()->subDays(30)->toDateString()),
            'end_date' => $request->query('end_date', now()->toDateString()),
        ];
        $report = $this->reports->attendanceReport($filters['class_id'], $filters['section_id'], $filters['start_date'], $filters['end_date']);

        if ($export = $request->query('export')) {
            return $this->export($export, 'attendance-report', $report['rows']->toArray());
        }

        return Inertia::render('Reports/Attendance', [
            'report' => $report,
            'filters' => $filters,
            'options' => $this->options(),
        ]);
    }

    public function results(Request $request): Response|StreamedResponse|HttpResponse
    {
        $filters = [
            'exam_id' => $request->integer('exam_id') ?: null,
            'class_id' => $request->integer('class_id') ?: null,
        ];
        $report = $this->reports->resultReport($filters['exam_id'], $filters['class_id']);

        if ($export = $request->query('export')) {
            return $this->export($export, 'result-report', $report['rows']->toArray());
        }

        return Inertia::render('Reports/Results', [
            'report' => $report,
            'filters' => $filters,
            'options' => [
                ...$this->options(),
                'exams' => Exam::query()->latest('start_date')->get(['id', 'title', 'class_id', 'status']),
            ],
        ]);
    }

    public function feeCollection(Request $request): Response|StreamedResponse|HttpResponse
    {
        $filters = [
            'class_id' => $request->integer('class_id') ?: null,
            'start_date' => $request->query('start_date', now()->startOfMonth()->toDateString()),
            'end_date' => $request->query('end_date', now()->toDateString()),
        ];
        $report = $this->reports->feeCollectionReport($filters['start_date'], $filters['end_date'], $filters['class_id']);

        if ($export = $request->query('export')) {
            return $this->export($export, 'fee-collection-report', $report['transactions']->toArray());
        }

        return Inertia::render('Reports/FeeCollection', [
            'report' => $report,
            'filters' => $filters,
            'options' => $this->options(),
        ]);
    }

    public function financial(Request $request): Response|StreamedResponse|HttpResponse
    {
        $filters = [
            'start_date' => $request->query('start_date', now()->startOfMonth()->toDateString()),
            'end_date' => $request->query('end_date', now()->toDateString()),
        ];
        $report = $this->reports->financialReport($filters['start_date'], $filters['end_date']);

        if ($export = $request->query('export')) {
            return $this->export($export, 'financial-report', $report['transactions']->toArray());
        }

        return Inertia::render('Reports/Financial', [
            'report' => $report,
            'filters' => $filters,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function options(): array
    {
        return [
            'classes' => SchoolClass::query()->orderBy('grade_level')->get(['id', 'name', 'grade_level']),
            'sections' => Section::query()->orderBy('name')->get(['id', 'school_class_id', 'name']),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function export(mixed $type, string $filename, array $rows): StreamedResponse|HttpResponse
    {
        if ($type === 'excel') {
            return response()->streamDownload(function () use ($rows) {
                $handle = fopen('php://output', 'w');
                $headers = array_keys($rows[0] ?? ['message' => 'No rows']);
                fputcsv($handle, $headers);

                foreach ($rows as $row) {
                    fputcsv($handle, array_map(fn ($value) => is_scalar($value) || $value === null ? $value : json_encode($value), $row));
                }

                fclose($handle);
            }, $filename.'.csv', ['Content-Type' => 'text/csv']);
        }

        $html = '<!doctype html><html><head><meta charset="utf-8"><title>'.e($filename).'</title></head><body>';
        $html .= '<h1>'.e(str_replace('-', ' ', $filename)).'</h1><table border="1" cellspacing="0" cellpadding="6">';
        $headers = array_keys($rows[0] ?? ['message' => 'No rows']);
        $html .= '<thead><tr>'.collect($headers)->map(fn ($header) => '<th>'.e($header).'</th>')->implode('').'</tr></thead><tbody>';

        foreach ($rows as $row) {
            $html .= '<tr>'.collect($headers)->map(fn ($header) => '<td>'.e((string) ($row[$header] ?? '')).'</td>')->implode('').'</tr>';
        }

        $html .= '</tbody></table></body></html>';

        return response($html, 200, [
            'Content-Type' => 'text/html',
            'Content-Disposition' => 'attachment; filename="'.$filename.'-print.html"',
        ]);
    }
}
