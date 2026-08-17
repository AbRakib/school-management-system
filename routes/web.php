<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\BookCategoryController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookIssueController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExamSubjectController;
use App\Http\Controllers\ExamTypeController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FeeCollectionController;
use App\Http\Controllers\FeeDueController;
use App\Http\Controllers\FeeInvoiceController;
use App\Http\Controllers\FeeStructureController;
use App\Http\Controllers\FeeTypeController;
use App\Http\Controllers\GradingScaleController;
use App\Http\Controllers\GuardianController;
use App\Http\Controllers\MarksEntryController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\ReportCardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StaffAttendanceController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentAttendanceController;
use App\Http\Controllers\StudentCategoryController;
use App\Http\Controllers\StudentTransportController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\TimetablePeriodController;
use App\Http\Controllers\TransportRouteController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::prefix('dashboards')->name('dashboard.')->group(function () {
        Route::get('admin', [DashboardController::class, 'admin'])
            ->middleware('role:super-admin|admin')
            ->name('admin');
        Route::get('teacher', [DashboardController::class, 'teacher'])
            ->middleware('role:teacher')
            ->name('teacher');
        Route::get('student', [DashboardController::class, 'student'])
            ->middleware('role:student')
            ->name('student');
        Route::get('parent', [DashboardController::class, 'parent'])
            ->middleware('role:parent')
            ->name('parent');
        Route::get('accountant', [DashboardController::class, 'accountant'])
            ->middleware('role:accountant')
            ->name('accountant');
    });

    Route::middleware('role:super-admin|admin|teacher')->group(function () {
        Route::get('attendance/students', [StudentAttendanceController::class, 'create'])
            ->name('attendance.students.create');
        Route::post('attendance/students', [StudentAttendanceController::class, 'store'])
            ->name('attendance.students.store');
        Route::get('attendance/students/report', [StudentAttendanceController::class, 'report'])
            ->name('attendance.students.report');
        Route::get('attendance/staff/report', [StaffAttendanceController::class, 'report'])
            ->name('attendance.staff.report');
        Route::get('students/id-cards', [StudentController::class, 'idCards'])
            ->name('students.id-cards');
        Route::resource('students', StudentController::class)
            ->only(['index', 'show'])
            ->whereNumber('student');
    });

    Route::get('notices', [NoticeController::class, 'index'])->name('notices.index');

    Route::middleware('role:super-admin|admin|accountant')->group(function () {
        Route::get('reports/attendance', [ReportController::class, 'attendance'])->name('reports.attendance');
        Route::get('reports/results', [ReportController::class, 'results'])->name('reports.results');
        Route::get('reports/fee-collection', [ReportController::class, 'feeCollection'])->name('reports.fee-collection');
        Route::get('reports/financial', [ReportController::class, 'financial'])->name('reports.financial');
    });

    Route::middleware('role:super-admin|admin')->group(function () {
        Route::resource('notices', NoticeController::class)->except(['index', 'show']);
        Route::resource('classes', SchoolClassController::class)->except('show');
        Route::resource('subjects', SubjectController::class)->except('show');
        Route::post('exams/{exam}/publish', [ExamController::class, 'publish'])->name('exams.publish');
        Route::get('exams/{exam}/subjects', [ExamSubjectController::class, 'show'])->name('exams.subjects.show');
        Route::post('exams/{exam}/subjects', [ExamSubjectController::class, 'store'])->name('exams.subjects.store');
        Route::get('exams/{exam}/marks-entry/{examSubject}', [MarksEntryController::class, 'show'])->name('exams.marks-entry.show');
        Route::post('exams/{exam}/marks-entry/{examSubject}', [MarksEntryController::class, 'store'])->name('exams.marks-entry.store');
        Route::get('exams/{exam}/report-card/{student}', [ReportCardController::class, 'show'])->name('exams.report-card.show');
        Route::resource('exam-types', ExamTypeController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('grading-scales', GradingScaleController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('exams', ExamController::class);
        Route::post('fees/generate-invoices', [FeeInvoiceController::class, 'generateInvoices'])->name('fees.generate-invoices');
        Route::get('fees/collect', [FeeCollectionController::class, 'search'])->name('fees.collect');
        Route::post('fees/invoices/{invoice}/pay', [FeeCollectionController::class, 'pay'])->name('fees.invoices.pay');
        Route::get('fees/due', [FeeDueController::class, 'index'])->name('fees.due');
        Route::resource('fee-types', FeeTypeController::class)->only(['store', 'update', 'destroy']);
        Route::resource('fee-structures', FeeStructureController::class)->only(['index', 'store', 'destroy']);
        Route::resource('expense-categories', ExpenseCategoryController::class)->only(['store', 'update', 'destroy']);
        Route::resource('expenses', ExpenseController::class)->only(['index', 'store', 'destroy']);
        Route::get('accounts/balance', [AccountController::class, 'balance'])->name('accounts.balance');
        Route::get('accounts/transactions', [AccountController::class, 'transactions'])->name('accounts.transactions');
        Route::get('timetable/periods', [TimetablePeriodController::class, 'index'])->name('timetable.periods.index');
        Route::post('timetable/periods', [TimetablePeriodController::class, 'store'])->name('timetable.periods.store');
        Route::put('timetable/periods/{period}', [TimetablePeriodController::class, 'update'])->name('timetable.periods.update');
        Route::delete('timetable/periods/{period}', [TimetablePeriodController::class, 'destroy'])->name('timetable.periods.destroy');
        Route::get('timetable/builder', [TimetableController::class, 'builder'])->name('timetable.builder');
        Route::post('timetable/builder', [TimetableController::class, 'saveCell'])->name('timetable.builder.save');
        Route::get('timetable/teacher/{staff?}', [TimetableController::class, 'teacherSchedule'])->name('timetable.teacher-schedule');
        Route::get('library/issue', [BookIssueController::class, 'issue'])->name('library.issue');
        Route::post('library/issue', [BookIssueController::class, 'store'])->name('library.issue.store');
        Route::get('library/returns', [BookIssueController::class, 'returns'])->name('library.returns');
        Route::post('library/issues/{issue}/return', [BookIssueController::class, 'return'])->name('library.issues.return');
        Route::get('library/due', [BookIssueController::class, 'due'])->name('library.due');
        Route::resource('book-categories', BookCategoryController::class)->only(['store', 'update', 'destroy']);
        Route::resource('books', BookController::class)->except(['create', 'edit', 'show']);
        Route::resource('vehicles', VehicleController::class)->except(['create', 'edit', 'show']);
        Route::resource('transport-routes', TransportRouteController::class)->except(['create', 'edit', 'show']);
        Route::resource('student-transports', StudentTransportController::class)->only(['index', 'store', 'destroy']);
        Route::resource('teachers', TeacherController::class)->except('show');
        Route::get('attendance/staff', [StaffAttendanceController::class, 'create'])
            ->name('attendance.staff.create');
        Route::post('attendance/staff', [StaffAttendanceController::class, 'store'])
            ->name('attendance.staff.store');
        Route::get('staff/{staff}/assignments', [AssignmentController::class, 'show'])
            ->name('staff.assignments.show');
        Route::post('staff/{staff}/assignments', [AssignmentController::class, 'store'])
            ->name('staff.assignments.store');
        Route::resource('staff', StaffController::class);
        Route::resource('departments', DepartmentController::class)
            ->only(['index', 'store', 'update', 'destroy']);
        Route::resource('designations', DesignationController::class)
            ->only(['index', 'store', 'update', 'destroy']);
        Route::resource('guardians', GuardianController::class)->except('show');
        Route::resource('student-categories', StudentCategoryController::class)
            ->only(['index', 'store', 'update', 'destroy']);
        Route::resource('students', StudentController::class)
            ->except(['index', 'show'])
            ->whereNumber('student');
    });
});

require __DIR__.'/settings.php';
