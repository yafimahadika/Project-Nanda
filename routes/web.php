<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\ClusteringResultController;
use App\Http\Controllers\ClusteringRunController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\KMeansController;
use App\Http\Controllers\NormalizationController;
use App\Http\Controllers\PreprocessingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SilhouetteEvaluationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('employees/check-employee-id', [EmployeeController::class, 'checkEmployeeId'])->name('employees.check-employee-id');
    Route::resource('employees', EmployeeController::class)->except(['create', 'show', 'edit']);
    Route::post('attendances/import', [AttendanceController::class, 'import'])->name('attendances.import');
    Route::delete('attendances/bulk-destroy', [AttendanceController::class, 'bulkDestroy'])->name('attendances.bulk-destroy');
    Route::resource('attendances', AttendanceController::class)->except(['create', 'show', 'edit']);
    Route::get('preprocessing', PreprocessingController::class)->name('preprocessing.index');
    Route::get('normalization', NormalizationController::class)->name('normalization.index');
    Route::get('kmeans', [KMeansController::class, 'index'])->name('kmeans.index');
    Route::post('kmeans/process', [KMeansController::class, 'process'])->name('kmeans.process');
    Route::delete('clustering-runs/{clusteringRun}', [ClusteringRunController::class, 'destroy'])->name('clustering-runs.destroy');
    Route::get('clustering-results', [ClusteringResultController::class, 'index'])->name('clustering-results.index');
    Route::get('silhouette', [SilhouetteEvaluationController::class, 'index'])->name('silhouette.index');
    Route::get('charts', [ChartController::class, 'index'])->name('charts.index');
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
