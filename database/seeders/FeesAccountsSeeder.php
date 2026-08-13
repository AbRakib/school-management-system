<?php

namespace Database\Seeders;

use App\Models\AcademicSession;
use App\Models\ExpenseCategory;
use App\Models\FeeInvoice;
use App\Models\FeeStructure;
use App\Models\FeeType;
use App\Models\SchoolClass;
use App\Models\User;
use App\Services\ExpenseService;
use App\Services\FeeService;
use Illuminate\Database\Seeder;

class FeesAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $session = AcademicSession::query()->first();
        $user = User::query()->first();
        if (! $session || ! $user) {
            return;
        }

        $types = collect([
            ['name' => 'Tuition Fee', 'is_recurring' => true],
            ['name' => 'Admission Fee', 'is_recurring' => false],
            ['name' => 'Exam Fee', 'is_recurring' => false],
        ])->map(fn ($type) => FeeType::query()->updateOrCreate(['name' => $type['name']], $type));

        foreach (SchoolClass::query()->take(5)->get() as $class) {
            foreach ($types as $type) {
                FeeStructure::query()->updateOrCreate([
                    'class_id' => $class->id,
                    'fee_type_id' => $type->id,
                    'academic_session_id' => $session->id,
                ], [
                    'amount' => $type->is_recurring ? 1500 + ($class->grade_level * 100) : 2500,
                    'due_day' => $type->is_recurring ? 10 : null,
                ]);
            }
            app(FeeService::class)->generateMonthlyInvoices($class->id, $session->id, now()->format('Y-m'));
        }

        FeeInvoice::query()->where('due_amount', '>', 0)->take(8)->get()
            ->each(fn ($invoice) => app(FeeService::class)->recordPayment($invoice->id, min(1000, (float) $invoice->due_amount), 'cash', $user->id));

        $categories = collect(['Salary', 'Utilities', 'Maintenance', 'Supplies'])
            ->map(fn ($name) => ExpenseCategory::query()->updateOrCreate(['name' => $name]));

        foreach ($categories as $category) {
            app(ExpenseService::class)->recordExpense([
                'expense_category_id' => $category->id,
                'amount' => rand(1500, 12000),
                'description' => $category->name.' expense',
                'expense_date' => now()->subDays(rand(1, 20))->toDateString(),
                'paid_by' => $user->id,
            ]);
        }
    }
}
